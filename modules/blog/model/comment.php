<?php
namespace blog\model;
/**
* Description of entity comment
* @author Parsimony
* @top 389px
* @left 584px
*/
class comment extends \entity {

	protected $id_comment;
	protected $id_post;
	protected $author;
	protected $author_url;
	protected $author_email;
	protected $content;
	protected $status;
	protected $id_user;
	protected $id_parent;
	protected $type;
	protected $author_ip;
	protected $date;


	public function __construct(\field_ident $id_comment,\field_foreignkey $id_post,\field_string $author,\field_string $author_url,\field_mail $author_email,\field_textarea $content,\field_state $status,\field_user $id_user,\field_foreignkey $id_parent,\field_state $type,\field_ip $author_ip,\field_date $date) {
		parent::__construct();
		$this->id_comment = $id_comment;
		$this->id_post = $id_post;
		$this->author = $author;
		$this->author_url = $author_url;
		$this->author_email = $author_email;
		$this->content = $content;
		$this->status = $status;
		$this->id_user = $id_user;
		$this->id_parent = $id_parent;
		$this->type = $type;
		$this->author_ip = $author_ip;
		$this->date = $date;

	}



// DON'T TOUCH THE CODE ABOVE ##########################################################

	public function olderComments($rowdate, $days) {
		if (isset($days) && !empty($days)) {
			$days = '+ ' . $days . ' days';
			$date = new \DateTime('now');
			$date =  $date->format('Y-m-d H:i:s');
			
			$rowdate = new \DateTime($rowdate);
			$rowdate = $rowdate->modify($days);
			$rowdate =  $rowdate->format('Y-m-d H:i:s');
			if (strtotime($date) < strtotime($rowdate))
				return TRUE;
			else
				return FALSE;
		}
		else
			return TRUE;
	}

	public function beforeInsert($vars) {
		// call blog configs
		$configs = \app::getModule('blog')->getConfigs();
		if ($configs['allowComments'] === '1'){
			$days = $configs['closeAfterDays'];
			$params = \app::$request->getParams();
			$params['id_post'] = $vars['id_post'];
			$idpage = $params['id_post'];	
			$selpage = 'select ' . PREFIX . 'blog_post.has_comment, ' . PREFIX . 'blog_post.publicationGMT from ' . PREFIX . 'blog_post where ' . PREFIX . 'blog_post.id_post = "'.$idpage.'"';
			$qpage = \PDOconnection::getDB()->query($selpage);
			$result = $qpage->fetch();
			$has_comment = $result['has_comment'];	
			$rowdate = $result['publicationGMT'];
			// if comments are open in blog configs 
			if (\app::getModule('blog')->getEntity('comment')->olderComments($rowdate, $days) == TRUE){
				// if blog configs allow comments
				if ($has_comment == '1') {
					// If user is connected, we define variables login and mail with session_login
					if(isset($params['session_login']) && $params['session_login'] != ''){ 
						$selmail = 'select ' . PREFIX . 'core_user.mail from ' . PREFIX . 'core_user where ' . PREFIX . 'core_user.id_user ="'.$params['session_id_user'] . '"';
						$qmail = \PDOconnection::getDB()->query($selmail);
						$resmail = $qmail->fetch();
						$mail = $resmail['mail'];
						$login = $params['session_login'];
						$vars['author_email'] = $mail;
					}	
					if(isset($params['session_login']) && $params['session_login'] != '') $vars['author'] = $params['session_login'];
					$content = $vars['content'];
					// hold = '0'; approve = '1'; spam = '2'; trash = '3';
					// By default status = 1
					$vars['status'] = '1';

					// alwaysApprove
					if($configs['alwaysApprove'] == '1') {
						$vars['status'] = '0';
					}
					//previousComment
					if($configs['previousComment']== '1') {
							if(isset($vars['author'])) {	
								$previousComment = 'SELECT count(' . PREFIX . 'blog_comment.id_comment) from ' . PREFIX . 'blog_comment where ' . PREFIX . 'blog_comment.status = "1" and ' . PREFIX . 'blog_comment.author ="' . $vars['author']. '"';
								$res = \PDOconnection::getDB()->query($previousComment)->fetch();
								if ($res[0] > 0) $vars['status'] = '1';
							}
					}
					// moderationWord
					if($configs['moderationWord'] != '') {
						// moderation
						$words = explode(",", $configs['moderationWord']);
						foreach ($words as $word) {
							if (preg_match('@' . $word . '@Usi', $content) == true) {
								$vars['status'] = '0';
							}
						}
					}
					// trashWord
					if($configs['trashWord'] != '') {
						$words = explode(",", $configs['trashWord']);
						foreach ($words as $word) {
							if (preg_match('@' . $word . '@Usi', $content) == true) {
								$vars['status'] = '2';	
							}
						}
					}
					// linkspam
					if(is_numeric($configs['linkspam'])) {
						$count = '';
						$content = str_replace("\\r","\r",$content);
						$content = str_replace("\\n","\n<BR>",$content);
						$content = str_replace("\\n\\r","\n\r",$content);
						$in=array('`((?:https?|ftp)://\S+[[:alnum:]]/?)`si','`((?<!//)(www\.\S+[[:alnum:]]/?))`si');
						$out=array('<a href="$1" rel="nofollow">$1</a> ','<a href="http://$1" rel="nofollow">$1</a>');
						$content = preg_replace($in,$out,$content, -1, $count);
						$nblink = $configs['linkspam']; 
						if ($count >= $nblink) {
							// Comment = spam
							$vars['status'] = '2';
						}		
					}		
					return $vars;
				}
				
			}
		}return FALSE;
	}

	public function afterInsert($vars) {
		
		$configs = \app::getModule('blog')->getConfigs();
		$reqhold = 'SELECT count(' . PREFIX . 'blog_comment.id_comment) from ' . PREFIX . 'blog_comment where ' . PREFIX . '.blog_comment.status = 0';
		$hold = \PDOconnection::getDB()->query($reqhold)->fetch();
		$holdcomments = $hold[0]; 
		if($holdcomments == '') $holdcomments = 0;
		if(isset($vars[':author'])) $author = $vars[':author'];
		if(isset($vars[':author_email'])) $author_email = $vars[':author_email'];
		$id_comment = $vars[':id_comment'];
		$id_post = $vars[':id_post'];
		$author_ip = $vars[':author_ip'];
		$author_url = $vars[':author_url'];
		$content = $vars[':content'];
		$remote = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $author_ip;
		$postquery = "SELECT blog_post.title, blog_post.url from blog_post where id_post =" . $id_post;

		$posttitle = '';
		$posturl = '';

		$params = \PDOconnection::getDB()->query($postquery);
		
		
		foreach ($params as $param) {
			$posttitle = $param['title'];
			$posturl = $param['url'];
		}
		// Email me when anyone posts a comment mailForAnyPost or a comment is held for moderation
		if ($configs['mailForAnyPost'] == '1' || ($configs['heldModeration'] == '1' && $vars[':status'] == '0')) {

			// if block config provides mailing before moderation

			$titre = utf8_decode('Comment Moderation for ' . $posttitle . ' (' . $vars[':date'] . ')');

			$message = t('A new comment on', FALSE) . ' ' . $posttitle . ' ' . t('is held for moderation', FALSE);
			$message .= '<br><A href="' . $posturl . '">' . $posturl . '</A><br>';

			$message .= '<br>' .(isset($vars[':author'])) ? t('Author :') . $author : '' . t('(IP :') . $author_ip . ',' . $remote . ' )';
			if(isset($vars[':author_email'])) $message .= '<br>' . t('E-mail :') . '<A href="mailto:' . $author_email . '">' . $author_email . '</A>';
			$message .= '<br>' . t('Website :') . $author_url;
			$message .= '<br>' . t('Whois :') . '<A href="' . 'http://whois.arin.net/rest/ip/' . $author_ip . '">' . 'http://whois.arin.net/rest/ip/' . $author_ip . '</a>';
			$message .= '<br>' . t('Comment :') . $content.'<br>'.t('ID Comment').$id_comment;
			$message .= '<br>' . '<A href="' . BASE_PATH . 'index#modules/model/blog/comment">' . BASE_PATH . 'index#modules/model/blog/comment</a>';
			$message .= '<br>' . t('Right now, '.$holdcomments.' comments await your approval',false).'.';
			$message = utf8_decode($message);
			$adminmail = \app::$config['mail']['adminMail'];

			ob_start();
			include('blog/views/mail/moderationmail.php');
			$body = ob_get_clean();
			if (\tools::sendMail($adminmail, '' . $adminmail . '', '' . $adminmail . '', $titre, $body)) {
				return true;
			} else {
				return false;
			}
		}
		// else config provides mailing after previous approved comment  
		if($configs['previousComment']== '1' && $vars[':status'] == '1'){
			$titre = utf8_decode('Approved comment for ' . $posttitle . ' (' . $vars[':date'] . ')');
			$message = t('New approved comment on', FALSE) . ' ' . $posttitle;
			$message .= '<br><A href="' . $posturl . '">' . $posturl . '</A><br>';
			$message .= '<br>' . (isset($vars[':author'])) ? t('Author :') . $author : '' . t('(IP :') . $author_ip . ',' . $remote . ' )';
			if(isset($vars[':author_email'])) $message .= '<br>' . t('E-mail :') . '<A href="mailto:' . $author_email . '">' . $author_email . '</A>';
			$message .= '<br>' . t('Website :') . $author_url;
			$message .= '<br>' . t('Whois :') . '<A href="' . 'http://whois.arin.net/rest/ip/' . $author_ip . '">' . 'http://whois.arin.net/rest/ip/' . $author_ip . '</a>';
			$message .= '<br>' . t('Comment :') . $content.'<br>'.t('ID Comment').$id_comment;
			$message .= '<br>' . '<A href="' . BASE_PATH . 'index#modules/model/blog/comment">' . BASE_PATH . 'index#modules/model/blog/comment</a>';
			$message .= '<br>' . t('Right now, '.$holdcomments.' comments await your approval',false).'.';
			$message = utf8_decode($message);
			$adminmail = \app::$config['mail']['adminMail'];

			ob_start();
			include('blog/views/mail/moderationmail.php');
			$body = ob_get_clean();
			if (\tools::sendMail($adminmail, '' . $adminmail . '', '' . $adminmail . '', $titre, $body)) {
				return true;
			} else {
				return false;
			}
		}
	}
}
?>