<?php foreach ($view as $key => $line) : ?>
	<div class="clearboth">
		<div class="publicationGMT"><?php echo strftime('%b %d %Y', strtotime($line->publicationGMT)); ?></div>
	    <div class="title"><a href="<?php echo BASE_PATH.$line->url; ?>"><?php echo $line->title->displayEditInline($line); ?></a></div>
		<div class="author"><?php echo t('By').' '.$line->pseudo; ?></div>
		<div class="content"><?php echo $line->content->displayEditInline($line); ?></div>
		
	</div>
<?php endforeach; ?>

<?php echo $view->getPagination(); ?>
