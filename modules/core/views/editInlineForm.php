<style>
	body{overflow: hidden;margin: 0;padding: 0;background: #FFF;}
	label{display:none}
	form{margin:0}
	textarea {margin: 0;width: 100%;}
</style>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		var dimm = document.querySelector("form").getBoundingClientRect();
		window.parent.document.getElementById("editForm").style.cssText = "height:" + dimm.height + "px;width:" + dimm.width + "px";
	}); 
</script>
<?php
foreach ($this->entity as $row) {
	$name = $this->name;
	echo '<form method="POST" action="' . BASE_PATH . $this->entity->getModule() . '/callField?entity=' . $this->entity->getName() . '&fieldName=' . $this->name . '&method=saveEditInline&id=' . $id . '">'
			. '<input type="hidden" name="TOKEN" value="' . TOKEN . '" />'
			. $row->$name()->form()
			. '</form>';
}