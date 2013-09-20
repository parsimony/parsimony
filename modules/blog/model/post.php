<?php
namespace blog\model;
/**
* Description of entity post
* @author Parsimony
* @top 129px
* @left 300px
*/
class post extends \entity {

	protected $id_post;
	protected $title;
	protected $url;
	protected $content;
	protected $excerpt;
	protected $publicationGMT;
	protected $author;
	protected $tag;
	protected $category;
	protected $has_comment;
	protected $ping_status;
	protected $is_sticky;


	public function __construct(\field_ident $id_post,\field_textarea $title,\field_url_rewriting $url,\field_wysiwyg $content,\field_textarea $excerpt,\field_publication $publicationGMT,\field_user $author,\field_formasso $tag,\field_formasso $category,\field_boolean $has_comment,\field_boolean $ping_status,\field_boolean $is_sticky) {
		parent::__construct();
		$this->id_post = $id_post;
		$this->title = $title;
		$this->url = $url;
		$this->content = $content;
		$this->excerpt = $excerpt;
		$this->publicationGMT = $publicationGMT;
		$this->author = $author;
		$this->tag = $tag;
		$this->category = $category;
		$this->has_comment = $has_comment;
		$this->ping_status = $ping_status;
		$this->is_sticky = $is_sticky;

	}

// DON'T TOUCH THE CODE ABOVE ##########################################################

public function afterInsert($vars) {
	if ($vars[':ping_status'] == '0') {
		$titre = utf8_decode($vars[':title']);
		$path = 'http://' . DOMAIN . BASE_PATH . $vars[':url'];
		$content = '<?xml version="1.0"?>' .
			'<methodCall>' .
			' <methodName>weblogUpdates.ping</methodName>' .
			' <params>' .
			' <param>' .
			' <value>' . $titre . '</value>' .
			' </param>' .
			' <param>' .
			' <value>' . $path . '</value>' .
			' </param>' .
			' </params>' .
			'</methodCall>';

		$headers = "POST / HTTP/1.0\r\n" .
			"User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.19 Safari/537.31\r\n" .
			"Host: rpc.pingomatic.com\r\n" .
			"Content-Type: text/xml\r\n" .
			"Content-length: " . strlen($content);

		$request = $headers . "\r\n\r\n" . $content;
		$response = '';
		$socket = fsockopen('rpc.pingomatic.com', 80, $errno, $errstr);
		if ($socket) {
			fwrite($socket, $request);
			while (!feof($socket))
				$response .= fgets($socket);
			fclose($socket);
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

}

?>