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
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<div class="placeholder">
    <label for="filter_<?php echo $this->name ?>">
	<?php echo $this->label ?>
    </label>
    <input type="text" autocomplete="off" name="filter[<?php echo $this->name ?>]" class="<?php echo $this->name ?>" id="filter_<?php echo $this->name ?>" value="<?php if(isset($_POST['filter'][$this->name]))echo $_POST['filter'][$this->name] ?>" <?php if (!empty($this->regex)) echo 'pattern="' . $this->regex . '"' ?> />
</div>