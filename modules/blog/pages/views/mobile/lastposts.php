<?php if (!$view->isEmpty()) : ?>
	<?php foreach ($view as $key => $line) : ?>
		<div class="itemscope">
			<a href="<?php echo BASE_PATH.$line->url; ?>">	
				<div class="itemprop publicationGMT" style="float:left"><?php echo strftime('%b %d %Y', strtotime($line->publicationGMT));?></div>		
				<div class="itemprop title"><?php echo $line->title; ?></div>
			</a>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="noResults"><?php echo t('No results'); ?></div>
<?php endif; ?>

<?php $view->getPagination(); ?>
