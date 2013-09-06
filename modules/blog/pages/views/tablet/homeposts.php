<?php if (!$view->isEmpty()) : ?>
	<?php foreach ($view as $key => $line) : ?>
		<div class="itemscope">		
			<div class="itemprop publicationGMT" style="float:right"><?php echo strftime('%b %d %Y', strtotime($line->publicationGMT));?></div>		
		<div style="display:inline">
			<div class="itemprop title"><a href="<?php echo BASE_PATH.$line->url; ?>"><?php echo $line->title /*->display() */; ?></a></div>
			
			<div class="itemprop content"><?php  echo $line->content; ?></div>
			<div style="display:inline" class="itemprop author"><?php echo t('By').' '.$line->pseudo; ?></div>
		</div>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="noResults"><?php echo t('No results'); ?></div>
<?php endif; ?>

<?php $view->getPagination(); ?>
