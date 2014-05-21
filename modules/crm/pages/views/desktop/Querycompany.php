<?php if (!$view->isEmpty()) : ?>
<table class="table">
	<?php foreach ($view as $nb => $row) :
	 if ($nb == 0) : ?>
	<thead>
		<th class="thead"><?php echo $row->name()->label ?></th>
		<th class="thead"><?php echo $row->state()->label ?></th>
		<th class="thead"><?php echo $row->phone()->label ?></th>
		<th class="thead"><?php echo $row->websiteurl()->label ?></th>
		<th class="thead"><?php echo $row->accounttype()->label ?></th>
		<th class="thead"><?php echo $row->ownership()->label ?></th>
		<th class="thead"><?php echo $row->industry()->label ?></th>
		<th class="thead">Edit</th>
	</thead>
	<tbody>
		<?php endif; ?>
		<tr class="itemscope">
			<td class="itemprop name"><?php echo $row->name(); ?></td>
			<td class="itemprop firstname"><?php echo $row->state(); ?></td>
			<td class="itemprop title"><?php echo $row->phone() ; ?></td>
			<td class="itemprop status"><?php echo $row->websiteurl() ; ?></td>
			<td class="itemprop mobile"><?php echo $row->accounttype(); ?></td>
			<td class="itemprop name"><?php echo $row->ownership(); ?></td>
			<td class="thead"><?php echo $row->industry() ?></td>
			<td class="itemprop edit"><a href="/updatecompany/<?php echo $row->id_company ?>"></a></td>

		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	<div class="noResults"><?php echo t('No company for the moment'); ?></div>
<?php endif; ?>