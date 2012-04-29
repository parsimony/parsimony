
<?php foreach ($view as $key => $line) : ?>
	<div class="clearboth howtos">
		<div class="title howtostitle"><a style="text-decoration:none;font-size:22px;color: rgba(255, 255, 255, 0.347656);line-height: 30px;text-shadow: -2px -2px 0px #0070A1; font-weight:bold" href="<?php echo BASE_PATH.$line->url; ?>"><?php echo $line->title; ?></a></div>
		<div class="content howtosdescript"><?php echo $line->content; ?></div>

	</div>
<?php endforeach; ?>

