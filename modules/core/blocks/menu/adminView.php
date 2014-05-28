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
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package core/blocks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
?>
<script src="<?php echo BASE_PATH; ?>lib/jquery-ui/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH; ?>lib/nestedSortable/jquery.ui.nestedSortable.js"></script>
<style>
    .placeholdermenu {background-color: #fff;border: 1px #ccc dashed}
    .ui-nestedSortable-error {background:#fbe3e4;color:#8a1f11;}
    ol {margin: 0;padding: 0;padding-left: 30px;}
    ol.sortable, ol.sortable ol {padding: 0;list-style-type: none;margin: 0 0 0 15px;}
    .sortable li {margin: 7px 0 0 0;padding: 0;}
    .sortable li > div {background: #fff url(img/arrow_move.png) no-repeat 7px 6px;border: 1px solid #ccc;font-weight: bold;color: #222;padding: 3px;margin: 2px;cursor: move;line-height: 30px;padding-left:40px;border-radius: 4px;}
    .sortable li > div  input{background: transparent;}
    .sortable li > div > div{vertical-align: middle}
    .ui-icon-closethick{margin-top: 7px;}
	
    #linkmenu, #previewmenu{border :1px solid #ccc;margin-top:10px; padding-bottom: 10px;border-radius: 3px;padding-left: 15px;background: #F9F9F9;}
    #design-menu{font-family: sans-serif;font-size: 12px;color:#585858}
	#addPageItem li {float : left ;position : relative ;list-style : none ;}
	#addPageItem a, #addPageItem > li {color : #444 ;text-decoration : none ;display : block ;padding : 8px 20px ;background: #EEE;position: relative}
	#addPageItem a:hover {background : #2DC1EE ;color: #f5f5f5;}
	#addPageItem .current a {background : #eee ;}
	#addPageItem .current a:hover {background : #2DC1EE ;}
	#addPageItem ul {z-index : 2 ;display : none ;margin : 0 ;padding : 0 ;min-width : 100px ;position : absolute ;left : 0 ;}
	#addPageItem ul li {float : none;}
	#addPageItem ul li a {background : #eee ;}
	#addPageItem ul li a:hover {background : #2DC1EE;}
	#addPageItem li:hover > ul {display : block ;}
	#addPageItem .parent > a::after {content: ' \25BE';}
	
	#design-menu h2{padding: 20px 0 0 5px;font-size: 19px;}
	#design-menu h3{font-size: 13px;}

</style>
<div id="item-menu-template" class="none">
    <?php
    echo $this->drawAdminMenu(array(array('id' => '', 'title' => '', 'url' => '')));
    ?>
</div>
<div id="item-menu-page-template" class="none">
    <?php
    echo $this->drawAdminMenu(array(array('id' => '', 'module' => '', 'page' => '')));
    ?>
</div>
<div id="design-menu">
    <div class="placeholder">
		<label><?php echo t('Menu orientation'); ?></label>
		<select name="position"><option value="0">Horizontal</option><option value="1" <?php if ($this->getConfig('position') == 1) echo ' selected="selected"'; ?>>Vertical</option></select>
    </div>
	
	<h2><?php echo t('Add Links'); ?></h2>
    <div id="linkmenu">
		<h3><?php echo t('Add A Link Manually'); ?></h3>
			<input type="text" id="input_title" placeholder="<?php echo t('Title'); ?>" />
			<input type="text" id="input_url"  placeholder="http://" />
			<input type="button" value="<?php echo t('Add'); ?>" id="add-menu-item">
		<h3><?php echo t('Choose An Existing Link'); ?></h3>
		<ul id="addPageItem">
			<?php
			foreach (\app::$activeModules as $module => $mode) {
				$moduleObj = \app::getModule($module);
				$pagesMod = $moduleObj->getPages();
				if (!empty($pagesMod)) {
					?>
					<li><?php echo $module; ?><ul>
							<?php
							foreach ($moduleObj->getPages() as $key => $page) {
								$nb = 0;
								foreach ($page->getURLcomponents() as $tab) {
									if(isset($tab['regex'])) $nb++; 
								}
								if ($nb <= 1)
									echo '<li><a data-title="' . s($page->getTitle()) . '" data-module="' . $module . '" data-page="' . $key . '" href="' . $page->getURL() . '">' . $page->getTitle() . '</a></li>';
							}
							?>
						</ul>
					</li>
					<?php
				}
			}
			?>
		</ul>
		<div class="clearboth"></div>
    </div>
	
	<h2><?php echo t('Preview Menu'); ?></h2>
    <div id="previewmenu">
		<ol class="sortable">
			<?php
			$menu = json_decode($this->getConfig('menu'), true);
			if (is_array($menu)) {
				$this->drawadminmenu($menu);
			}
			?>
		</ol>
		<input type="hidden" name="toHierarchy" id="toHierarchy">
    </div>
</div>

<script>
    function refreshPos(){
		$('#toHierarchy').val(JSON.stringify($('ol.sortable').nestedSortable('toHierarchy')));
    }
    $(document).ready(function() {
		function getMaxId(){
			var maxnb = 0;
			$("ol.sortable li").each(function(i) {
				var tab = $(this).attr("id").split(/itemlist_/);
				if(parseInt(tab[1]) > maxnb) maxnb = parseInt(tab[1]);
			});
			return maxnb;
		}
		function addLink(title,url){
			var maxnb = getMaxId() + 1;
			var obj = $('#item-menu-template > li').clone().attr("id","itemlist_" + maxnb);
			obj.find(".input_title").val(title).attr("name","title[" + maxnb + "]");
			obj.find(".input_url").val(url).attr("name","url[" + maxnb + "]");
			obj.find(".input_active").attr("name","active[" + maxnb + "]");
			$("#input_title").val('');
			$("#input_url").val('');
			$("ol.sortable").append(obj);
			refreshPos();
		}
		function addPage(module, page, title){
			var maxnb = getMaxId() + 1;
			var obj = $('#item-menu-page-template > li').clone().attr("id","itemlist_" + maxnb);
			obj.find(".module").val(module).attr("name","module[" + maxnb + "]");
			obj.find(".page").val(page).attr("name","page[" + maxnb + "]");
			obj.find(".titlePage").text(title);
			$("ol.sortable").append(obj);
			refreshPos();
		}
		$("#add-menu-item").on("click",function(){
			addLink($("#input_title").val(),$("#input_url").val());
		});
		$("#addPageItem a").on("click", function(){
			addPage($(this).data('module'), $(this).data('page'), $(this).data('title'));
			return false;
		});
		$('ol.sortable').nestedSortable({
			forcePlaceholderSize: true,
			handle: 'div',
			helper:	'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholdermenu',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div',
			update:function(){
				refreshPos();
			}
		});
		refreshPos();
    });
</script>