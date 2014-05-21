<?php if (!$view->isEmpty()) : ?>
<table class="table">
	<?php foreach ($view as $nb => $row) :
	 if ($nb == 0) : ?>
	<thead>
		<th class="thead"><?php echo $row->name()->label ?></th>
		<th class="thead"><?php echo $row->firstname()->label ?></th>
		<th class="thead"><?php echo $row->object()->label ?></th>
		<th class="thead"><?php echo $row->duedate()->label ?></th>
		<th class="thead"><?php echo $row->description()->label ?></th>
		<th class="thead">Edit</th>
	</thead>
	<tbody>
		<?php endif; ?>
		
		<tr class="itemscope">
			<td class="thead"><?php echo $row->name() ?></td>
			<td class="thead"><?php echo $row->firstname() ?></td>
			<td class="itemprop firstname"><?php echo $row->object(); ?></td>
			<td class="itemprop title"><?php echo $row->duedate() ; ?></td>
			<td class="itemprop status"><?php echo $row->description() ; ?></td>
			<td class="itemprop edit"><a href="/updateactivity/<?php echo $row->id_contact ?>"></a></td>	
		</tr>
	
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	<div class="noResults"><?php echo t('No activity for the moment'); ?></div>
<?php endif; ?>