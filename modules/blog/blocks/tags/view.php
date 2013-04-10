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
$tags = \PDOconnection::getDB()->query('SELECT '.PREFIX.'blog_tag.name, '.PREFIX.'blog_tag.url, COUNT( '.PREFIX.'blog_tag.name ) AS nb
FROM '.PREFIX.'blog_tag INNER JOIN '.PREFIX.'blog_tag_post ON '.PREFIX.'blog_tag.id_tag = '.PREFIX.'blog_tag_post.id_tag
GROUP BY '.PREFIX.'blog_tag.name ORDER BY nb DESC LIMIT 0 , 30');
if(is_object($tags)){
    $tags = $tags->fetchAll(\PDO::FETCH_ASSOC);
    if(isset($tags[0]['nb'])){
	$nbMax = $tags[0]['nb'];
	echo '<ul>';
	foreach ($tags as $key => $tag) {
	    $percent = floor(($tag['nb'] / $nbMax) * 100);
	    if ($percent < 20):
		$size = 'xsmall';
	    elseif ($percent >= 20 and $percent < 40):
		$size = 'small';
	    elseif ($percent >= 40 and $percent < 60):
		$size = 'medium';
	    elseif ($percent >= 60 and $percent < 80):
		$size = 'large';
	    else:
		$size = 'xlarge';
	    endif;
	    echo '<li><a class="'.$size.'" href="tag/'.$tag['url'].'">'.$tag['name'].'</a></li>';
	}
	echo '</ul>';
    }
}
?>