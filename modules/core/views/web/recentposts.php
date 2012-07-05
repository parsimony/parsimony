<h1><?php echo t('Recent posts',false) ;?></h1>
<ul>
<?php foreach ($view as $key => $line) : ?>
	<li>
		<a href="<?php echo BASE_PATH.$line->url ?>" style="overflow: hidden;text-overflow: ellipsis;"><?php echo $line->title ?></a>
	</li>
<?php endforeach; ?>
</ul>