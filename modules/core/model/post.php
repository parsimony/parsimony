<?php
namespace core\model;
/**
* Description of entity post
* @author Parsimony
* @top 450px
* @left 492px
*/
class post extends \entity {

    protected $id_post;
    protected $title;
    protected $url;
    protected $content;
    protected $excerpt;
    protected $author;
    protected $publicationGMT;
    protected $tag;
    protected $category;
    protected $has_comment;
    protected $status;
    protected $post_password;
    protected $is_sticky;
    protected $ping_status;


public function __construct(\field_ident $id_post,\field_textarea $title,\field_url_rewriting $url,\field_wysiwyg $content,\field_textarea $excerpt,\field_user $author,\field_publication $publicationGMT,\field_formasso $tag,\field_formasso $category,\field_state $has_comment,\field_state $status,\field_password $post_password,\field_state $is_sticky,\field_state $ping_status) {
        $this->id_post = $id_post;
        $this->title = $title;
        $this->url = $url;
        $this->content = $content;
        $this->excerpt = $excerpt;
        $this->author = $author;
        $this->publicationGMT = $publicationGMT;
        $this->tag = $tag;
        $this->category = $category;
        $this->has_comment = $has_comment;
        $this->status = $status;
        $this->post_password = $post_password;
        $this->is_sticky = $is_sticky;
        $this->ping_status = $ping_status;

}
// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>