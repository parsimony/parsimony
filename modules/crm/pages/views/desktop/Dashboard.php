<?php if (!$view->isEmpty()) : ?>
	<?php foreach ($view as $row) : ?>
<table>
	<?php foreach ($view as $nb => $row) :
	 if ($nb == 0) : ?>
	<thead>
		<th class="thead"><input type="checkbox"></th>
		<th class="thead"><?php echo $row->object()->label ?></th>
		<th class="thead"><?php echo $row->duedate()->label ?></th>
		<th class="thead"><?php echo $row->description()->label ?></th>
		<th class="thead"><?php echo $row->type()->label ?></th>
		<th class="thead"><?php echo $row->name()->label ?></th>	
	</thead><tbody>
		<?php endif; ?>

		<tr class="itemscope">
			<td class="itemprop object"><?php echo $row->object; ?></td>
			<td class="itemprop duedate"><?php echo $row->duedate; ?></td>
			<td class="itemprop description"><?php echo $row->description ?></td>
			<td class="thead"><?php echo $row->type() ?></td>
			<td class="itemprop name"><?php echo $row->name(); ?></td>
		</tr>

	<?php endforeach; ?>
	</tbody>
</table>
	

	<?php endforeach; ?>
<?php else: ?>
	<div class="noResults"><?php echo t('No results'); ?></div>
<?php endif; ?>
