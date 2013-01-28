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
<script type="text/javascript">
<?php
if (isset($this->unique) && $this->unique):
    ?>
        $(document).on("keyup", "#<?php echo $this->name . '_' . $row->getId()->value ?>", function(){
            $.post(BASE_PATH + '<?php echo $this->module; ?>/callField',{module:"<?php echo $this->module; ?>", entity:"<?php echo $this->entity; ?>", fieldName:"<?php echo $this->name; ?>", method:'checkUnique', args:'chars=' + this.value + "&id=<?php echo $row->getId()->value ?>"}, function(data){
                if(data == 1){
                    $(".info_<?php echo $this->name . '_' . $row->getId()->value ?>").empty();
                }else{
                    $(".info_<?php echo $this->name . '_' . $row->getId()->value ?>").text("<?php echo t('It already exist, please choose another') ?>");
                }
            });
        });
    <?php
endif;
?>
    $(document).on ('blur','textarea[name="<?php echo $this->propertyToURL ?>"],input[name="<?php echo $this->propertyToURL ?>"]',function() {
        if($(this).val().length > 0 && $('#<?php echo $this->name . '-' . $row->getId()->value ?>').val().length == 0){
            $('#<?php echo $this->name . '-' . $row->getId()->value ?>').addClass('active');
            $.post(BASE_PATH + "admin/titleToUrl", {TOKEN: TOKEN ,url: $(this).val()},
            function(data) {
                $('#<?php echo $this->name . '-' . $row->getId()->value ?>').val(data);
            });
        }
    });
</script>
<style>
     #<?php echo $this->name . '_' . $row->getId()->value ?>{border: 0;background: none;width: 90%;box-shadow: none;height: 21px;line-height: 1px;margin: 7px 0;color: #555;}
     #<?php echo $this->name . '_' . $row->getId()->value ?>:focus{background: #fff;}
</style>
<div>
    <label><?php echo ucfirst($this->label) ?> : </label>
    <input type="text" autocomplete="off" id="<?php echo $this->name . '_' . $row->getId()->value ?>" name="<?php echo $this->name ?>" class="<?php echo $this->name ?>" value="<?php echo s($value) ?>" <?php if (!empty($this->regex)) echo 'pattern="' . $this->regex . '"' ?> <?php if ($this->required) echo 'required' ?> />
    <?php if (isset($this->unique) && $this->unique): ?>
        <div class="infoUnique info_<?php echo $this->name . '_' . $row->getId()->value ?>"></div>
    <?php endif; ?>
</div>