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
\app::$request->page->addJSFile('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/jquery-ui.js');
\app::$request->page->addCSSFile(BASE_PATH . 'core/fields/field_formasso/css.css');
?>
<script>typeof jQuery.ui != 'undefined' || document.write('<script src="' + BASE_PATH + 'lib/jquery-ui/jquery-ui-1.10.0.min.js"><\/script>')</script>
<div>
    <label for="<?php echo $this->name.'_'.$row->getId()->value ?>">
        <?php echo $this->label ?>
        <?php if (!empty($this->text_help)): ?>
            <span class="tooltip ui-icon ui-icon-info" data-tooltip="<?php echo t($this->text_help) ?>"></span>
        <?php endif; ?>
    </label>
    <?php
    $mode = '';
    $mode = \app::getModule($this->module)->getEntity($this->entity_foreign);
    $words = array();
    $tit = $mode->getBehaviorTitle();
    foreach ($mode->select() AS $line) {
        $obj = new stdClass;
        $obj->label = $line->$tit->value;
        $obj->value = $line->getId()->value;
        $words[] = $obj; 
    }

    ?>
    <script>
        function addATag<?php echo $this->name.'_'.$row->getId()->value; ?>(id,label){
            if(label.length > 0) $('<div class="floatleft" style="padding:3px"><span class="ui-icon ui-icon-circle-close floatleft" style="cursor:pointer"></span>' + label + '<input type="hidden" name="<?php echo $this->name; ?>[' + id + ']" value="' + label + '" /></div>').appendTo("#log<?php echo $this->name.'_'.$row->getId()->value; ?>");
        }
        $(function() {
            var availableTags<?php echo $this->name.'_'.$row->getId()->value; ?> = <?php echo json_encode($words); ?>;
            $( "#<?php echo $this->name.'_'.$row->getId()->value; ?>").autocomplete({
                source: availableTags<?php echo $this->name.'_'.$row->getId()->value; ?>,
                select: function( event, ui ) {
                    addATag<?php echo $this->name.'_'.$row->getId()->value; ?>(ui.item.value, ui.item.label);
                    ui.item.value = '';
                }
            });
            $(document).on('click', ".ui-icon-circle-close", function(event){
                $(this).parent().remove();
            });
            $(document).on("keydown","#<?php echo $this->name.'_'.$row->getId()->value; ?>",function(event){
                if(event.keyCode == 13){
                    event.preventDefault();
                    event.stopPropagation();
                    $("#<?php echo $this->name.'_'.$row->getId()->value; ?>_ok").trigger("click");
                }
            });
            $(document).on("click","#<?php echo $this->name.'_'.$row->getId()->value; ?>_ok",function(event){
                var val = $("#<?php echo $this->name.'_'.$row->getId()->value; ?>").val();
                if(val.indexOf(",")){
                    var cut = val.split(",")
                    for(i=0;i < cut.length;i++){
                        addATag<?php echo $this->name.'_'.$row->getId()->value; ?>("new" + (new Date()).getTime(),cut[i]);
                    }
                }else{
                    addATag<?php echo $this->name.'_'.$row->getId()->value; ?>("new" + (new Date()).getTime(),val);
                }
                $("#<?php echo $this->name.'_'.$row->getId()->value; ?>").val("");
            });
        });
    </script>
 
    <div class="fixedzindex<?php echo $this->name.'_'.$row->getId()->value; ?>"><input type="text" id="<?php echo $this->name.'_'.$row->getId()->value; ?>" /><input type="button" id="<?php echo $this->name.'_'.$row->getId()->value; ?>_ok"  value="<?php echo t('Add',FALSE); ?>" /></div>
    <div id="log<?php echo $this->name.'_'.$row->getId()->value; ?>" style="border-color: #ddd;border-radius:4px;padding-top:7px;width: 100%; overflow: auto;" class="ui-widget-content">
    <?php
    $idNameEntity = $row->getId()->name;
    $foreignEntity = \app::getModule($this->module)->getEntity($this->entity_foreign);
    $idNameForeignEntity = $foreignEntity->getId()->name;
    $titleForeignEntity = $foreignEntity->getBehaviorTitle();
    $assoEntity = \app::getModule($this->module)->getEntity($this->entity_asso);
    foreach ($assoEntity->select()->join($this->module.'_'.$this->entity_asso.'.'.$idNameForeignEntity, $this->module.'_'.$this->entity_foreign.'.'.$idNameForeignEntity)->where($idNameEntity.' = '.$row->getId()->value) AS $line) {
        echo '<div class="floatleft" style="padding:3px"><span class="ui-icon ui-icon-circle-close floatleft" style="cursor:pointer"></span>' . $line->$titleForeignEntity . '<input type="hidden" name="'.$this->name.'[' . $line->$idNameForeignEntity->value . ']" value="' . $line->$titleForeignEntity . '" /></div>';
    }

    ?>
    </div>
</div>

