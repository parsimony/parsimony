<?php if (!$view->isEmpty()) : ?>
	<?php foreach ($view as $key => $line) : ?>
		<div class="itemscope">
			<div class="itemprop publicationGMT"><?php echo strftime('%b %d %Y', strtotime($line->publicationGMT)); ?></div>
			<div class="itemprop title"><a href="<?php echo BASE_PATH.$line->url; ?>"><?php echo $line->title(); ?></a></div>
			<div class="itemprop author"><?php echo t('By').' '.$line->pseudo; ?></div>
			<div class="itemprop content"><?php echo $line->content(); ?></div>
			
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="noResults"><?php echo t('No results'); ?></div>
<?php endif; ?>

<?php $view->getPagination(); ?>
