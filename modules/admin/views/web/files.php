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
 * to contact@parsimony.mobi so we can send you a copy immediately.
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
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$dirPath = PROFILE_PATH . substr(\app::$request->getParam('dirPath'), 1);

//securise $path
$dirPath = str_replace(DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR, '', $dirPath);
$extOk = array(); //'jpeg', 'png', 'gif', 'jpg'
$extKo = array('obj');
foreach (glob($dirPath . '/*') as $filename) :
    if (is_dir($filename)) :
        $filename = str_replace('//','/',$filename); //fix
	?>
	<div class="explorer_file dir">
	    <img src="<?php echo BASE_PATH ?>admin/img/icons/DIR.jpg" style="width:60%;height:60%" >
	    <div class="explorer_file_name" path="/<?php echo str_replace(PROFILE_PATH, '', $filename) ?>"><?php echo basename($filename) ?></div>
	</div>
	<?php
    elseif ((empty($extOk) || in_array(substr(strrchr($filename, '.'), 1), $extOk)) &&
	    (empty($extKo) || !in_array(substr(strrchr($filename, '.'), 1), $extKo))) :
	?>
	<div class="explorer_file">
	    <?php if (is_file('modules/admin/img/icons/' . strtoupper(substr($filename, -3)) . '.jpg')): ?>
	        <img src="<?php echo BASE_PATH . 'admin/img/icons/' . strtoupper(substr($filename, -3)) ?>.jpg" style="width:60%;height:60%" >
	    <?php else: ?>
	        <img src="<?php echo BASE_PATH ?>admin/img/icons/FILE.jpg" style="width:60%;height:60%" >
	    <?php endif; ?>
	    <div class="explorer_file_name" file="/<?php echo str_replace(PROFILE_PATH, '', $filename) ?>"><?php echo basename($filename) ?></div>
	</div>
	<?php
    endif;
endforeach;
exit;
?>