<?php

$reader = new XMLReader();
$reader->open('mess.xml', null, 1<<19);

$limit =  999999;
$posts = array();
$categories = array();
$tags = array();
$comments = array();
$users = array();

$post = array();
$categorie = array();
$tag = array();
$comment = array();
$user = array();

$category_post = array();
$tag_post = array();

$comments = array();

$current = '';

while ($limit > 0) {
	while ($reader->read()) {
		if ($reader->nodeType == XMLReader::ELEMENT) {
			switch ($reader->name) {
				//AUTHORS
				case 'wp:author':
					$current = 'author';
					$user = array();
					break;
				case 'wp:author_id':
					$reader->read();
					$user['id_user'] = $reader->value;
					break;
				case 'wp:author_login':
					$reader->read();
					$user['pseudo'] = $reader->value;
					break;
				case 'wp:author_email':
					$reader->read();
					$user['mail'] = $reader->value;
					break;
				case 'wp:author_display_name':
					$reader->read();
					$user['display_name'] = $reader->value;
					break;
				case 'wp:author_first_name':
					$reader->read();
					$user['first_name'] = $reader->value;
					break;
				case 'wp:author_last_name':
					$reader->read();
					$user['last_name'] = $reader->value;
					break;
				//CATEGORIES
				case 'wp:category':
					$current = 'category';
					$category = array('id_category'=>'');
					break;
				case 'wp:category_nicename':
					$reader->read();
					$category['url'] = $reader->value;
					break;
				case 'wp:category_parent':
					$reader->read();
					$category['id_parent'] = $reader->value;
					break;
				case 'wp:cat_name':
					$reader->read();
					$category['name'] = $reader->value;
					break;
				// TAGS
				case 'wp:tag':
					$current = 'tag';
					$tag = array();
					break;
				case 'wp:term_id':
					if($current=='tag'){
						$reader->read();
						$tag['id_tag'] = $reader->value;
					}
					if($current=='category'){
						$reader->read();
						$category['id_category'] = $reader->value;
					}
					break;
				case 'wp:tag_slug':
					$reader->read();
					$tag['url'] = $reader->value;
					break;
				case 'wp:tag_name':
					$reader->read();
					$tag['name'] = $reader->value;
					break;
				// POSTS && PAGES
				case 'item':
					$current = 'post';
					$post = array();
					break;
				case 'wp:post_id':
					$reader->read();
					$post['id_post'] = $reader->value;
					break;
				case 'title':
					$reader->read();
					$post['title'] = $reader->value;
					break;
				case 'dc:creator':
					$reader->read();
					$post['author'] = $users[$reader->value]['id_user'];
					break;
				case 'wp:post_date':
					$reader->read();
					$post['publicationGMT'] = $reader->value;
					break;
				case 'wp:post_type':
					$reader->read();
					$post['type'] = $reader->value;
					break;
				case 'wp:status':
					$reader->read();
					if($reader->value == 'publish'){
						$value = 1;
					}elseif($reader->value == 'draft'){
						$value = 0;
					}elseif($reader->value == 'pending'){
						$value = 2;
					}else{
                                            $value = 3;
                                        }
					$post['status'] = $value;
					break;
				case 'wp:ping_status':
					$reader->read();
					if($reader->value == 'open'){
						$value = 1;
					}elseif($reader->value == 'closed'){
						$value = 0;
					}
					$post['ping_status'] = $value;
					break;
				case 'wp:is_sticky':
					$reader->read();
					$post['is_sticky'] = $reader->value;
					break;
				case 'wp:post_password':
					$reader->read();
					$post['post_password'] = $reader->value;
					break;
				case 'content:encoded':
					$reader->read();
					$post['content'] = $reader->value;
					break;
				case 'excerpt:encoded':
					$reader->read();
					$post['excerpt'] = $reader->value;
					break;
				case 'wp:post_name':
					$reader->read();
					if(strlen($reader->value) == 0) $value = '?p='.$post['id_post'];
					else $value = $reader->value;
					$post['url'] = $value;
					break;
				case 'wp:comment_status':
					$reader->read();
					if($reader->value == 'open'){
						$value = 1;
					}elseif($reader->value == 'closed'){
						$value = 0;
					}elseif($reader->value == 'registered_only'){
						$value = 2;
					}
					$post['has_comment'] = $value;
					break;
				case 'category':
					if ($reader->getAttribute('domain') == 'category') {
						$category_post[] = array('id_category_post' => '','id_category' => $categories[$reader->getAttribute('nicename')]['id_category'],'id_post' => $post['id_post']);
						$reader->read();
					} elseif ($reader->getAttribute('domain') == 'post_tag') {
						$tag_post[] = array('id_tag_post' => '','id_tag' => $tags[$reader->getAttribute('nicename')]['id_tag'],'id_post' => $post['id_post']);
						$reader->read();
					}
					break;
				// COMMENTS
				case 'wp:comment':
					$comment = array('id_post' => $post['id_post']);
					break;
				case 'wp:comment_id':
					$reader->read();
					$comment['id_comment'] = $reader->value;
					break;
				case 'wp:comment_author':
					$reader->read();
					if(isset($users[$reader->value]['id_user'])) $value = $users[$reader->value]['id_user'];
					else $value = $reader->value;
					$comment['author'] = $value;
					break;
				case 'wp:comment_author_email':
					$reader->read();
					$comment['author_email'] = $reader->value;
					break;
				case 'wp:comment_author_url':
					$reader->read();
					$comment['author_url'] = $reader->value;
					break;
				case 'wp:comment_author_IP':
					$reader->read();
					$comment['author_IP'] = $reader->value;
					break;
				case 'wp:comment_date':
					$reader->read();
					$comment['dateGMT'] = $reader->value;
					break;
				case 'wp:comment_content':
					$reader->read();
					$comment['content'] = $reader->value;
					break;
				case 'wp:comment_approved':
					$reader->read();
					$comment['status'] = $reader->value;
					break;
				case 'wp:comment_type':
					$reader->read();
					$comment['type'] = $reader->value;
					break;
				case 'wp:comment_parent':
					$reader->read();
					$comment['id_parent'] = $reader->value;
					break;
				case 'wp:comment_user_id':
					$reader->read();
					$comment['id_user'] = $reader->value;
					break;
			}
		}
		if($reader->nodeType == XMLReader::END_ELEMENT) {
			switch ($reader->name) {
				case 'wp:author':
					$users[$user['pseudo']] = $user;
					unset($user);
					break;
				case 'wp:category':
					$categories[$category['url']] = $category;
					unset($category);
					break;
				case 'wp:comment':
					$comments[$comment['id_post']] = $comment;
					unset($comment);
					break;
				case 'wp:tag':
					$tags[$tag['url']] = $tag;
					unset($tag);
					break;
				case 'item':
					if($post['type'] == 'post') {
						unset($post['type']);
						$posts[] = $post;
					}
					unset($post);
					break;
			}
		}
	}
	$limit--;
	break;
}
/*
print_r($comments);
print_r($category_post);
print_r($posts);
print_r($tag_post);
print_r($tags);
print_r($users);
print_r($categories);
print_r($tags);
*/
//print_r($categories);
foreach ($categories as $category) {
    $tt = \app::getModule('blog')->getEntity('category')->insertInto($category);
        var_dump($tt);
}
foreach ($category_post as $category_postchild) {
    $tt = \app::getModule('blog')->getEntity('category_post')->insertInto($category_postchild);
        var_dump($tt);
}
foreach ($tag_post as $tag_postchild) {
    $tt = \app::getModule('blog')->getEntity('tag_post')->insertInto($tag_postchild);
        var_dump($tt);
}
foreach ($tags as $tag) {
    $tt = \app::getModule('blog')->getEntity('tag')->insertInto($tag);
        var_dump($tt);
}
foreach ($posts as $post) {
    $tt = \app::getModule('blog')->getEntity('post')->insertInto($post);
        var_dump($tt);
}
echo count($comments);
foreach ($comments as $comment) {
    $tt = \app::getModule('blog')->getEntity('comment')->insertInto($comment);
        var_dump($tt);echo $comment['id_comment'];
}
?>