<?php
/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@parsimony.mobi so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package blog/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<style>
.pubstatus input {padding: 5px;cursor: pointer;background: #44C5EC;color : #fff;border: none;margin: 4px 0;display: block;text-align: center;width:57px;}
.pubstatus input.active, .pubstatus input:hover {background: #259BDB;}
.pubstatus{left : -60px;}
label{display: block; margin: 10px 0 2px;text-transform: capitalize;}
li{list-style: none;position: relative;padding: 5px 15px;}
h4{margin: 5px 0;}
.pendingcomment a{white-space: nowrap;width: 150px; overflow: hidden;text-transform: capitalize; text-overflow: ellipsis;text-decoration: none;color: #777}
</style>

<?php \app::getModule('blog')->initConfig();
/* VISITORS */
$configs = \app::getModule('blog')->getConfigs();

$params = \app::$request->getParams();
	
if(\app::$request->page->getModule() == 'blog') {
	// params title or url
	if ((isset($params['url']) && is_string($params['url'])) || (isset($params['title']) )) {
		$url = (isset($params['title'])) ? $params['title'] : $params['url'];
		// If config allows to comment this post or posts
		if ($configs['allowComments'] == '1') { 
			// define url	
			$selpage = 'select ' . PREFIX . 'blog_post.id_post, ' . PREFIX . 'blog_post.publicationGMT, ' . PREFIX . 'blog_post.url, ' . PREFIX . 'blog_post.has_comment from ' . PREFIX . 'blog_post where ' . PREFIX . 'blog_post.url =  "' . $url . '"';
			$qpage = \PDOconnection::getDB()->query($selpage);
			$result = $qpage->fetch();
			$has_comment = $result['has_comment'];	
			$rowdate = $result['publicationGMT'];
			$idpage = $result['id_post'];
			$entity = \app::getModule('blog')->getEntity('comment');			
			$desc = '';
			// Number of items
			$desc = ' order by ' . PREFIX . 'blog_comment.id_comment ' . $configs['commentOrder'];
			$qcomment = 'select ' . PREFIX . 'blog_comment.id_comment, ' . PREFIX . 'blog_comment.author, ' . PREFIX . 'blog_comment.author_ip, ' . PREFIX . 'blog_comment.date, ' . PREFIX . 'blog_comment.content, ' . PREFIX . 'blog_comment.status, ' . PREFIX . 'blog_comment.id_user, ' . PREFIX . 'blog_comment.id_parent, ' . PREFIX . 'blog_comment.author_email from ' .
					PREFIX . 'blog_comment where ' . PREFIX . 'blog_comment.id_post =  ' . $idpage . ' AND ' . PREFIX . 'blog_comment.status = 1 ' . $desc;
			if (is_numeric($configs['items'])) {
				$qcomment .= ' limit 0, ' . $configs['items'] . '';
			}
			// Query approved comments 
			$comments = \PDOconnection::getDB()->query($qcomment);
			// if config provides that comments must be automatically closed on articles after X days 
			$days = $configs['closeAfterDays'];
			
			if (\app::getModule('blog')->getEntity('comment')->olderComments($rowdate, $days) == TRUE) {

				if ($has_comment == '1') {
					$heldFormoderation = $configs['alwaysApprove'];
					if($heldFormoderation == '1') $notify = t('Your comment is held for moderation');
					else $notify = t('Your comment is approved');
					if (isset($_POST['add'])) {
						$res = $entity->insertInto($_POST);
						if ($res === TRUE || is_numeric($res)) {
							echo '<div class="notify positive">' . $notify . '</div>';
						} else {
							echo '<div class="notify negative">' . t('Your comment cannot be send'). ': '. $res . '</div>';
						}
					}
					/* Fetch comments  
					 * Display approved comments
					 */
					?>
					<ul>
						<?php if (is_object($comments)): ?>
							<?php foreach ($comments as $key => $comment) : ?>

								<li class="comment" id="comment_<?php echo $comment['id_comment']; ?>">		
								<!--if baehaviour = 2-->
									<div>
										<span class="author"><?php if($comment['author'] == '') echo t('Anonymous'); else echo $comment['author']; ?></span> 
										<span class="said"><?php echo t('said', false); ?></span> 
										<span class="dateToTimeAgo"><?php echo \tools::dateToTimeAgo($comment['date']); ?></span> 
										<span class="datesqlformat">(<?php echo $comment['date']; ?>)</span> 
									</div>
									<div><?php echo $comment['content']; ?></div>
											<!-- NESTED COMMENTS ANSWER -->
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<?php
					
					// if config provides that user must me connected
					if ($configs['loggedin'] == "1") :

						// check if user connected
						if (\app::getClass('user')->VerifyConnexion()) :

							// Post a comment 
							?>
							<h4 class="title"><?php echo t('Post a comment') ?></h4>

							<?php // Tpl: Login as & comment form  ?>
							<div class="userInfo">
								<?php echo t('Connected as') . ' <span>'. \app::$request->getParam('session_login').'</span>'; ?>
								(<a class="logout" href="logout"><?php echo t('Logout', FALSE) ?></a>)
							</div>
							<form method="post" class="form" action="">
								<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
								<?php
							// if config provides that comment author must fill out name and e-mail 
							// we fill them with session data before insert 
								echo $entity->author_url()->form();
								echo $entity->content()->form(); ?>

								<input type="hidden" value="<?php echo $idpage; ?>" name="id_post">
								<input type="submit" value="<?php echo t('Save'); ?>" name="add" class="submit">
							</form>

							<?php
						// else displays connection form  
						else :
						?>

						<form method="POST" action="<?php echo BASE_PATH; ?>login" class="connexion">
							<div class="none error"></div>
							<div class="connectLogin"><label><?php echo t('User'); ?> : </label><input type="text" name="login" class="login" /></div>
							<div class="connectPassword"><label><?php echo t('Password'); ?> : </label><input type="password" name="password" class="password" /></div>
							<div class="connectSubmit"><input type="submit" value="<?php echo t('Login'); ?>" /></div>
						</form>
					<?php endif; ?>

					<?php
				// config doesn't provide that user must me connected  
				else :
					 ?>
					<form method="post" class="form" action="">
						<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />

						<?php
						// if config provides that comment author must fill out name and e-mail
						if ($configs['fillNameMail'] == "1") :
							echo $entity->author()->form();
							echo $entity->author_email()->form();
						endif; 
						echo $entity->author_url()->form();
						echo $entity->content()->form(); ?>
						<input type="hidden" value="<?php echo $idpage; ?>" name="id_post">
						<input type="submit" value="<?php echo t('Save'); ?>" name="add" class="submit">
					</form>

				<?php endif; 
				
				}					
			}
		}
	}
}
else echo t('Your comment block is not in a page of the blog module');
?>  


