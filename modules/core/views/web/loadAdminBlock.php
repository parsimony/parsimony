<?php
if(isset($_POST['module']) && isset($_POST['page']) && isset($_POST['id'])){
$theme = \theme::get(THEMEMODULE,THEME, THEMETYPE);
$block = '';
if($theme!=FALSE)$block = &$theme->search_block($theme, $_POST['id']);
if(!is_object($block))$block = \app::getModule($_POST['module'])->getPage($_POST['page'])->getBlock($_POST['id']);
echo $block->display();
}
?>