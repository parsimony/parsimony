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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if($this->getConfig('items'))
$items = $this->getConfig('items');
else $items = 5;
$recposts = \PDOconnection::getDB()->query('select core_post.id_post,core_post.title,core_post.url from core_post order by core_post.id_post desc LIMIT 0 , '.$items.'')->fetchAll(\PDO::FETCH_ASSOC);


?> 

<h1><?php echo t('Recent posts',false) ;?></h1>
<ul>
<?php foreach ($recposts as $key => $recentpost) : ?>
	<li class="recentposts">
            <a href="<?php echo BASE_PATH .$recentpost['url'] ?>"  style="overflow: hidden;text-overflow: ellipsis;"><?php echo $recentpost['title'] ?></a>
	</li>
<?php endforeach; ?>
</ul>

