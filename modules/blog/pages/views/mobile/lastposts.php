<?php if (!$view->isEmpty()) : ?>
	<?php foreach ($view as $key => $row) : ?>
		<div class="itemscope">
			<a href="<?php echo BASE_PATH.$row->url; ?>">	
				<div class="itemprop publicationGMT" style="float:left"><?php echo strftime('%b %d %Y', strtotime($row->publicationGMT));?></div>		
				<div class="itemprop title"><?php echo $row->title; ?></div>
			</a>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="noResults"><?php echo t('No results'); ?></div>
<?php endif; ?>

<?php $view->getPagination(); ?>
