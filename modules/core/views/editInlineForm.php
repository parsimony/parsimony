<style>
	body{overflow: hidden;margin: 0;padding: 0;background: #FFF;}
	label{display:none}
	form{margin:0}
	textarea {margin: 0;width: 100%;}
</style>
<?php
foreach ($this->entity as $row) {
	$name = $this->name;
	echo '<form method="POST" action="' . BASE_PATH . '/' . $this->entity->getModule() . '/callField?entity=' . $this->entity->getName() . '&fieldName=' . $this->name . '&method=saveEditInline&id=' . $id . '">'
			. $row->$name()->form()
			. '</form>';
}