<div class="titleTab ellipsis"><span style="letter-spacing: 1.1px;"><?php echo t('Tree', FALSE); ?></span><span id="treelegend" style="position: absolute;right: 6px;top: 1px;color: #444;font-weight: bold;padding-right: 10px;">?</span></div>
<div class="none" id="treelegend2"><fieldset style="text-shadow:none;color:white;">
	<legend><?php echo t('Type of blocks', FALSE); ?></legend>
	<span class="parsicontainer" style="padding-left: 30px;position: relative;left: 5px;"><?php echo t('Block Container', FALSE); ?></span> </br>
	<span class="parsiblock" style="padding-left: 39px;position: relative;left: -3px;"><?php echo t('Content Block', FALSE); ?></span></br>
	<span class="parsipage" style="padding-left: 37px;position: relative;left: -1px;"><?php echo t('Dynamic Page', FALSE); ?></span></br>
    </fieldset>
</div>  
<div id="config_tree_selector" class="none">
    <span draggable="true" class="floatleft move_block ui-icon ui-icon-arrow-4"></span>
    <span class="floatleft ui-icon ui-icon-wrench action" rel="getViewConfigBlock" title="<?php echo t('Configuration', FALSE); ?>"></span>
    <span class="ui-icon ui-icon-pencil cssblock floatleft"></span>
    <span class="ui-icon ui-icon-closethick config_destroy floatleft"></span>
</div>
<div id="tree"> 
    <?php
    echo \app::getModule('admin')->structureTree(\theme::get(THEMEMODULE, THEME, THEMETYPE));
    ?>
</div>