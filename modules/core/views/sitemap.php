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
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package core
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

\app::$response->setHeader('Content-type', 'text/xml;charset=utf-8');

$urls = array();
foreach (\app::$config['modules']['active'] AS $module => $type) {
    foreach (\app::getModule($module)->getPages() AS $page) {
	if(!strstr($page->getMeta('robots'), 'noindex')){
	    if (count($page->getURLcomponents()) == 0) {
		$urls[] = 'http://' . DOMAIN . '/' . $module . '/' . $page->getURL();
	    } else {
		$nb = 0;
		foreach ($page->getURLcomponents() AS $urlRegex)
		    if (isset($urlRegex['modelProperty']))
			$nb++;
		if($nb == 1) {
		    foreach ($page->getURLcomponents() AS $urlRegex) {
			if (isset($urlRegex['modelProperty'])) {
			    $prop = explode('.', $urlRegex['modelProperty']);
			    $table = explode('_', $prop[0], 2);
			    $entity = \app::getModule($table[0])->getEntity($table[1]);

			    foreach ($entity as $row) {
				$url = $page->getRegex();
				$url = str_replace('(?<' . $urlRegex['name'] . '>' . $urlRegex['regex'] . ')', $row->$prop[1], substr($page->getRegex(), 2, -2));
				$urls[] = 'http://' . DOMAIN . '/' . $module . '/' . $url;
			    }
			}
		    }
		}
	    }
	}
    }
}

echo '<?xml version="1.0" encoding="UTF-8" ?> '; // in PHP to avoid confusion with short_opentag
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls AS $url): ?>
    <url>
        <loc><?php echo $url; ?></loc>
    </url>
    <?php endforeach; ?>
</urlset>