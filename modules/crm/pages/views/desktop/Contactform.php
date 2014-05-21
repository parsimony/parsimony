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
		<?php echo $entity->firstname()->form(); ?>
		<?php echo $entity->title()->form(); ?>
		<?php echo $entity->street()->form(); ?>
		<?php echo $entity->city()->form(); ?>
		<?php echo $entity->state()->form(); ?>
		<?php echo $entity->code()->form(); ?>
		<?php echo $entity->country()->form(); ?>
	<div class="description"><?php echo $entity->description()->form(); ?></div>
		<?php echo $entity->mail()->form(); ?>
		<?php echo $entity->skype()->form(); ?>
		<?php echo $entity->twitter()->form(); ?>
		<?php echo $entity->phone()->form(); ?>
		<?php echo $entity->mobile()->form(); ?>
		<?php echo $entity->websiteurl()->form(); ?>
		<?php echo $entity->employees()->form(); ?>
		<?php echo $entity->annualrevenue()->form(); ?>
		<?php echo $entity->type()->form(); ?>
		<?php echo $entity->leadsource()->form(); ?>
		<?php echo $entity->stage()->form(); ?>
		<?php echo $entity->status()->form(); ?>
		<?php echo $entity->assessment()->form(); ?>
		<?php echo $entity->industry()->form(); ?>
	<input type="submit" value="<?php echo t('Save', FALSE); ?>" name="add" class="submit">
</form>