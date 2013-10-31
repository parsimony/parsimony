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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$blockquery = $this->getConfig('blockquery') ? $this->getConfig('blockquery') : 'rapports';
$block = \app::getModule(MODULE)->getPage(\app::$request->getParam('IDPage'))->searchBlock($blockquery);
if ($block) {
	$propeties = $this->getConfig('properties');
	$selected = $block->getConfig('selected');
	foreach ($selected as $key => $value) {
		if (isset($value['filter'])) {
			$name = $value['table'] . '.' . $value['property'];
			echo $name . ' : ';
			?>
			<select name="properties[<?php echo $name ?>]">
				<option <?php if(isset($propeties[$name]) && $propeties[$name] === 'string') echo 'selected="selected"'; ?>>string</option>
				<option <?php if(isset($propeties[$name]) && $propeties[$name] === 'range') echo 'selected="selected"'; ?>>range</option>
				<option <?php if(isset($propeties[$name]) && $propeties[$name] === 'choice') echo 'selected="selected"'; ?>>choice</option>
				<option <?php if(isset($propeties[$name]) && $propeties[$name] === 'daterange') echo 'selected="selected"'; ?>>daterange</option>
				<option <?php if(isset($propeties[$name]) && $propeties[$name] === 'datetimerange') echo 'selected="selected"'; ?>>datetimerange</option>
			</select><br>
			<?php
		}
	}
}
?>
			Block ident:<br>
<input type="text" name="blockquery" value="<?php if($this->getConfig('blockquery')) echo $this->getConfig('blockquery'); ?>">