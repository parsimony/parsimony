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
\app::$request->page->addJSFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.js');
?>
<div>
    <label for="<?php echo $this->name ?>">
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
    <style>
        .ui-autocomplete {
            position:absolute;
            max-height: 180px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index:1005;
        }
    </style>
    <script>
        function addATag<?php echo $this->name; ?>(id,label){
            if(label.length > 0) $('<div class="floatleft" style="padding:3px"><span class="ui-icon ui-icon-circle-close floatleft" style="cursor:pointer"></span>' + label + '<input type="hidden" name="<?php echo $this->name; ?>[' + id + ']" value="' + label + '" /></div>').appendTo("#log<?php echo $this->name; ?>");
        }
        $(function() {
            var availableTags = <?php echo json_encode($words); ?>;
            $( "#<?php echo $this->name; ?>").autocomplete({
                source: availableTags, appendTo:".fixedzindex",
                select: function( event, ui ) {
                    addATag<?php echo $this->name; ?>(ui.item.value, ui.item.label);
                    ui.item.value = '';
                }
            });
            $(".ui-icon-circle-close").live('click', function(event){
                $(this).parent().remove();
            });
            $("#log<?php echo $this->name; ?>").parent().on("keydown","#<?php echo $this->name; ?>",function(event){
                if(event.keyCode == 13){
                    event.preventDefault();
                    event.stopPropagation();
                    $("#<?php echo $this->name; ?>_ok").trigger("click");
                }
            });
            $(document).on("click","#<?php echo $this->name; ?>_ok",function(event){
                var val = $("#<?php echo $this->name; ?>").val();
                if(val.indexOf(",")){
                    var cut = val.split(",")
                    for(i=0;i < cut.length;i++){
                        addATag<?php echo $this->name; ?>("new" + (new Date()).getTime(),cut[i]);
                    }
                }else{
                    addATag<?php echo $this->name; ?>("new" + (new Date()).getTime(),val);
                }
                $("#<?php echo $this->name; ?>").val("");
            });
        });
    </script>

    <div class="fixedzindex"><input type="text" id="<?php echo $this->name; ?>" /><input type="button" value="<?php echo t('Add', FALSE); ?>" id="<?php echo $this->name; ?>_ok" /></div>
    <div id="log<?php echo $this->name; ?>" style="border-color: #ddd;border-radius:4px;padding:10px;width: 100%; overflow: auto;" class="ui-widget-content"> </div>
</div>

