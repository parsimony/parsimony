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
 * @package core/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<div class="placeholder">
    <label><?php echo t('Address', false); ?></label><input type="text" name="adress" value="<?php echo $this->getConfig('adress'); ?>" />
</div>
<div class="placeholder">
    <label><?php echo t('Town', false); ?></label><input type="text" name="town" value="<?php echo $this->getConfig('town'); ?>" />
</div>
<div class="placeholder">
    <label><?php echo t('Country', false); ?></label><input type="text" name="country" value="<?php echo $this->getConfig('country'); ?>" />
</div>
<?php
if (($this->getConfig('language')) != '') {
    $lang = $this->getConfig('language');
} else {
    $lang = substr(\app::$config['localization']['default_language'], 0, 2);
}
?> 

<div class="placeholder">
    <label><?php echo t('Language (Optional)', false); ?></label><input type="text" name="language" value="<?php echo $lang ?>" />
</div>

<div>
    <label style="display:inline-block;width:60px"><?php echo t('View', false); ?></label>
    <select name="view" style="width:100px">
        <?php
                
        if (($this->getConfig('view')) == 'm') {
            $select = 'selected="selected"';
        } else {
            $select = '';
        }
        echo '<option value="m" ' . $select . ' >'. t('Map', false).'</option>';

        if (($this->getConfig('view')) == 'h') {
            $select = 'selected="selected"';
        } else {
            $select = '';
        }
        echo '<option value="h" ' . $select . ' >'. t('Hybrid', false).'</option>';
         
        if (($this->getConfig('view')) == 'k') {
        $select = 'selected="selected"';
        } else {
        $select = '';
        } echo '<option value="k" ' . $select . ' >'. t('Satellite', false).'</option>';

        if (($this->getConfig('view')) == 'p') {
        $select = 'selected="selected"';
        } else {
        $select = '';
        }
        echo '<option value="p" ' . $select . ' >'. t('Terrain', false).'</option>';
        
        
?>

    </select> 
</div>
<br>
<div>
    <label style="display:inline-block;width:60px"><?php echo t('Zoom', false); ?></label>
    <select name="zoom" style="width:100px">
        <?php
        for ($i = 1; $i <= 20; $i++) {
            if($this->getConfig('zoom')==$i){
                $select = 'selected="selected"';
            }else{
                $select = '';
            }
            echo '<option value="' . $i . '" ' . $select . '>' . $i . '</option>';
        }
        ?>
    </select>
</div>


<script>
    $('input,select').live('blur change',function(){
        var adress = $('input[name=adress]').val() +','+ $('input[name=town]').val()+','+ $('input[name=country]').val();
        var lang = $('input[name=language]').val();
        var view = $('select[name=view]').val();
        var zoom = $('select[name=zoom]').val();
        var adressjs = 'http://maps.google.com/maps?q='+ adress +'&oe=utf-8&ie=UTF8&hl='+ lang +'&hq=&hnear='+ adress +'&t='+ view +'&z='+ zoom +'&vpsrc=0&output=embed';
        $('#googlemapid').attr('src', adressjs);
        console.log(adressjs);
    });
</script>
<br><label style="display:inline-block;text-decoration: underline;width: 100%; text-align: center"><?php echo t('Preview The Map', false); ?></label><br><br>
<iframe id="googlemapid" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=<?php echo $this->getConfig('adress') . ',' . $this->getConfig('town') . ',' . $this->getConfig('country'); ?>&amp;oe=utf-8&amp;ie=UTF8&amp;hl=<?php echo $this->getConfig('language') ?>&amp;hq=&amp;hnear=<?php echo $this->getConfig('adress') . ',' . $this->getConfig('town') . ',' . $this->getConfig('country') ?>&amp;t=<?php echo $this->getConfig('view') ?>&amp;z=<?php echo $this->getConfig('zoom') ?>&amp;vpsrc=0&amp;output=embed"></iframe>
