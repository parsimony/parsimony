<?php
if($_SESSION['behavior'] === 0){
	\app::$request->page->addJSFile('lib/HTML5editor/HTML5editor.js');
	\app::$request->page->addCSSFile('lib/HTML5editor/HTML5editor.css');
}
\app::$request->page->addJSFile('core/fields/field_wysiwyg/script.js');
?>
<div class="parsieditinline fieldwysiwyg" spellcheck="false" data-click="fieldwysiwyg" data-wysiwygplugins="<?php echo $this->wysiwygModules; ?>" data-module="<?php echo $this->entity->getModule(); ?>" data-entity="<?php echo $this->entity->getName(); ?>" data-property="<?php echo $this->name; ?>" data-id="<?php echo $row->getId()->value; ?>"><?php echo $this->value; ?></div>