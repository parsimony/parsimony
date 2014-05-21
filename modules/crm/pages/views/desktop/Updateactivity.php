<?php
if(isset($_POST['add'])){
	$res = $entity->update($_POST);
	if($res === TRUE || is_numeric($res)){ /* TRUE in update context or last insert id for insert */
		echo '<div class="notify positive">'.t($this->getConfig('success')).'</div>';
	}else{
		echo '<div class="notify negative">'.t($this->getConfig('fail')).'</div>';
	}
}
?>
<form method="post" action="">
	<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
		<?php echo $entity->object()->form(); ?>
		<div class="description"><?php echo $entity->duedate()->form(); ?></div>
		<div class="description"><?php echo $entity->description()->form(); ?></div>
		<?php echo $entity->type()->form(); ?>
		<?php echo $entity->id_user()->form(); ?>
	<input type="submit" value="<?php echo t('Save', FALSE); ?>" name="add" class="submit">
</form>