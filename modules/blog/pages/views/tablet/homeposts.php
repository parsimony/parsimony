<?php if (!$view->isEmpty()) : ?>
	<?php foreach ($view as $key => $row) : ?>
		<div class="itemscope">		
			<div class="itemprop publicationGMT" style="float:right"><?php echo strftime('%b %d %Y', strtotime($row->publicationGMT));?></div>		
		<div style="display:inline">
			<div class="itemprop title"><a href="<?php echo BASE_PATH.$row->url; ?>"><?php echo $row->title /*->display() */; ?></a></div>
			
			<div class="itemprop content"><?php  echo $row->content; ?></div>
			<div style="display:inline" class="itemprop author"><?php echo t('By').' '.$row->pseudo; ?></div>
		</div>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="noResults"><?php echo t('No results'); ?></div>
<?php endif; ?>

<?php $view->getPagination(); ?>