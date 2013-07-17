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
 * @package admin
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$dirPath = PROFILE_PATH . \app::$request->getParam('dirPath');
//securise $path
$dirPath = str_replace(DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR, '', $dirPath);
echo '<div id="path">'.$dirPath.'</div><div id="dirsandfiles">';
$extOk = array(); 
$array_img= array('.jpeg', '.png', '.gif', '.jpg');
$extKo = array('.obj');
$files = glob($dirPath . '/*');
foreach ((is_array($files) ? $files : array()) as $filename) :
    if (is_dir($filename)) :
        $filename = str_replace('//','/',$filename); //fix      
        ?>
	<div class="explorer_file dir">
	    <img src="<?php echo BASE_PATH ?>admin/img/dir.png">
	    <div class="explorer_file_name" path="<?php echo str_replace(PROFILE_PATH, '', $filename) ?>"><?php echo basename($filename) ?></div>
	</div>
	<?php
    elseif ((empty($extOk) || in_array(strrchr($filename, '.'), $extOk)) &&
	    (empty($extKo) || !in_array(strrchr($filename, '.'), $extKo))) :
	?>
	<div class="explorer_file">
	    <?php if (in_array(strrchr($filename, '.'), $array_img)) : ?>
                <img onclick="" src="<?php echo BASE_PATH.'thumbnail?x=50&y=50&path='.$filename; ?>"> 
	    <?php else: ?>
	        <img src="<?php echo BASE_PATH ?>admin/img/file.png">
	     <?php endif; ?>
	    <div class="explorer_file_name" path="<?php echo str_replace(PROFILE_PATH, '', $filename) ?>"><?php echo basename($filename) ?></div>
	</div>
	<?php
    endif;
endforeach;
?>
</div>