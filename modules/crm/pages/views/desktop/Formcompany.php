<?php
if(isset($_POST['add'])){
	$res = $entity->insertInto($_POST);
	if($res === TRUE || is_numeric($res)){ /* TRUE in update context or last insert id for insert */
		echo '<div class="notify positive">'.t($this->getConfig('success')).'</div>';
	}else{
		echo '<div class="notify negative">'.t($this->getConfig('fail')).'</div>';
	}
}
?>
<form method="post" action="">
	<input type="hidden" name="TOKEN" value="<?php echo TOKEN; ?>" />
		<?php echo $entity->name()->form(); ?>
		<?php echo $entity->street()->form(); ?>
		<?php echo $entity->city()->form(); ?>
		<?php echo $entity->state()->form(); ?>
		<?php echo $entity->code()->form(); ?>
		<?php echo $entity->country()->form(); ?>
		<?php echo $entity->phone()->form(); ?>
		<?php echo $entity->websiteurl()->form(); ?>
		<?php echo $entity->employees()->form(); ?>
		<?php echo $entity->accounttype()->form(); ?>
		<?php echo $entity->ownership()->form(); ?>
		<?php echo $entity->industry()->form(); ?>
		<?php echo $entity->annualrevenue()->form(); ?>
		<div class="description"><?php echo $entity->description()->form(); ?></div>
	<br><br>
	<input type="submit" value="<?php echo t('Save', FALSE); ?>" name="add" class="submit">
</form>