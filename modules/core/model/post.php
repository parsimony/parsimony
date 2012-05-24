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
    protected $publicationGMT;
    protected $author;
    protected $tag;
    protected $category;
    protected $has_comment;
    protected $ping_status;


public function __construct(\field_ident $id_post,\field_textarea $title,\field_url_rewriting $url,\field_wysiwyg $content,\field_textarea $excerpt,\field_publication $publicationGMT,\field_user $author,\field_formasso $tag,\field_formasso $category,\field_state $has_comment,\field_state $ping_status) {
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

}
// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

// DON'T TOUCH THE CODE ABOVE ##########################################################

}
?>