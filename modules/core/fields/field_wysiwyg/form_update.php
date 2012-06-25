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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
app::$request->page->addJSFile(BASE_PATH . 'lib/HTML5editor/HTML5editor.js');
app::$request->page->addCSSFile(BASE_PATH . 'lib/HTML5editor/HTML5editor.css');
?>

<div class="placeholder">
    <label for="<?php echo $this->name ?>">
	<?php echo $this->label ?>
	<?php if (!empty($this->text_help)): ?>
    	<span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
	<?php endif; ?>
    </label>
    <div style="padding-top: 24px;">
	<textarea cols="50" rows="8" id="<?php echo $this->name.'_'.$row->getId()->value; ?>" class="<?php echo $this->name.'_'.$row->getId()->value; ?>" name="<?php echo $this->name ?>" <?php if (!empty($this->regex)) echo 'pattern="' . $this->regex . '"' ?> ><?php echo s($this->value) ?></textarea>
    </div>
</div>
<script>
	var HTML5editor<?php echo $row->getId()->value; ?> = new wysiwyg();
	HTML5editor<?php echo $row->getId()->value; ?>.init("#<?php echo $this->name.'_'.$row->getId()->value; ?>",["<?php echo str_replace(',','","',$this->wysiwygModules); ?>"]);
</script>
