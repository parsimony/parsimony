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
 * @authors Julien Gras et BenoÃ®t Lorillot
 * @copyright  Julien Gras et BenoÃ®t Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package core/fields
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
\app::$response->page->addJSFile('lib/jquery-ui/jquery-ui-1.10.3.min.js');
\app::$response->page->addCSSFile('core/fields/formasso/css.css');

echo $this->displayLabel($fieldName);

$foreignEntity = \app::getModule($this->entity->getModule())->getEntity($this->entity_foreign);
$titleForeignEntity = $foreignEntity->getBehaviorTitle();

$words = array();
foreach ($foreignEntity as $foreignRow) {
	$obj = new stdClass;
	$obj->label = $foreignRow->$titleForeignEntity;
	$obj->value = $foreignEntity->getId()->value;
	$words[] = $obj; 
}

$values = json_decode($value, TRUE);

if($this->mode === 'tag'): ?>

<script>
	function addATag<?php echo $fieldName; ?>(id,label){
		if(label.length > 0) $('<div><span class="ui-icon ui-icon-circle-close"></span>' + label + '<input type="hidden" name="<?php echo $tableName ?>[<?php echo $this->name; ?>][' + id + ']" value="' + label + '" /></div>').appendTo("#log<?php echo $fieldName; ?>");
	}
	$(function() {
		var availableTags<?php echo $fieldName; ?> = <?php echo json_encode($words); ?>;
		$( "#<?php echo $fieldName; ?>").autocomplete({
			source: availableTags<?php echo $fieldName; ?>,
			select: function( event, ui ) {
			addATag<?php echo $fieldName; ?>(ui.item.value, ui.item.label);
			ui.item.value = '';
			}
		});
		$(document).on('click', ".ui-icon-circle-close", function(){
			$(this).parent().remove();
		})
		.on("keydown","#<?php echo $fieldName; ?>",function(event){
			if(event.keyCode == 13){
				event.preventDefault();
				event.stopPropagation();
				$("#<?php echo $fieldName; ?>_ok").trigger("click");
			}
		})
		.on("click","#<?php echo $fieldName; ?>_ok",function(){
			var val = $("#<?php echo $fieldName; ?>").val();
			if(val.indexOf(",")){
				var cut = val.split(",");
				for(i=0;i < cut.length;i++){
					addATag<?php echo $fieldName; ?>("new" + (new Date()).getTime(),cut[i]);
				}
			}else{
				addATag<?php echo $fieldName; ?>("new" + (new Date()).getTime(),val);
			}
			$("#<?php echo $fieldName; ?>").val("");
		});
	});
</script>

<div class="fixedzindex<?php echo $fieldName; ?>"><input type="text" id="<?php echo $fieldName; ?>" /><input type="button" id="<?php echo $fieldName; ?>_ok"  value="<?php echo t('Add'); ?>" /></div>
<div id="log<?php echo $fieldName; ?>" class="listasso">
<input type="hidden" name="<?php echo $tableName . '[' . $this->name . ']' ?>" value="empty" />
<?php
if (!empty($values)) {
	reset($values);
	foreach ($values as $id => $title) {
		echo '<div><span class="ui-icon ui-icon-circle-close"></span>' . $title . '<input type="hidden" name="' . $tableName . '[' . $this->name . '][' . $id . ']" value="' . $title . '" /></div>';
	}
}
?>
</div>

<?php else : ?>

<script>
	$(function() {
		$(document).on("click", "#<?php echo $fieldName . '_add'; ?>", function(){
			
			var params = new FormData(document.getElementById("<?php echo $fieldName . '_iframe'; ?>").contentWindow.document.body.querySelector("form"));
			$.ajax({
				url: '<?php echo BASE_PATH; ?>admin/action',
				data: params,
				processData: false,
				contentType: false,
				type: 'POST',
				success: function(data){
					try {
						data = JSON.parse(data);
						$("#<?php echo $fieldName; ?>_list").append('<div class="itemasso"><input type="checkbox" name="<?php echo $tableName; ?>[<?php echo $foreignEntity->getName(); ?>][' + data.id + ']"> ' + data.title + '</div>');
						document.getElementById("<?php echo $fieldName . '_form'; ?>").classList.add("none");
					} catch (e) {
						top.ParsimonyAdmin.execResult(data);
					}
				}
			  });
			
		})
		.on("click", "#<?php echo $fieldName . '_addbtn'; ?>", function(){
			$.post(BASE_PATH + "admin/action", "TOKEN=" + TOKEN + "&action=displayInsertFormAsso&popup=yes&entity=<?php echo $this->entity->getModule(); ?> - <?php echo $this->entity_foreign; ?>", function(data) {
				$("#<?php echo $fieldName . '_insertAddForm'; ?>").empty();	
				$('<iframe id="<?php echo $fieldName . '_iframe'; ?>" seamless onload="this.style.height = this.contentWindow.document.documentElement.offsetHeight + \'px\';">').appendTo("#<?php echo $fieldName . '_insertAddForm'; ?>").attr('srcdoc', data);
			});
			this.nextSibling.classList.toggle("none");
			this.nextSibling.classList.add("formassonew");
			document.getElementById("<?php echo $fieldName . '_add'; ?>").classList.toggle("none");
			
		});
	});
</script>
<?php
	echo '<div id="' . $fieldName . '_list" style="height:150px;overflow-y:scroll;border: 1px solid #DFDFDF;">';
	foreach ($words as $word) {
		echo '<div class="itemasso"><input type="checkbox" name="' . $tableName . '[' . $this->name . '][' . $word->value . ']" ' . (isset($values[$word->value]) ? ' checked="checked"' : '') .' /> ' . $word->label . '</div>';
	}
	echo '</div>';

	echo '<div id="' . $fieldName . '_addbtn" style="color:#777;line-height:25px;cursor:pointer">+ Add New</div><div id="' . $fieldName . '_form" class="none formassonew">';
	echo '<div id="' . $fieldName . '_insertAddForm"></div><div><input type="button" value="Add new" id="' . $fieldName . '_add" class="none"></div></div>';

endif;
