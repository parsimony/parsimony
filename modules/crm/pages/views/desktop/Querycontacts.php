<?php if (!$view->isEmpty()) : ?>
<table class="table">
	<?php foreach ($view as $nb => $row) :
	 if ($nb == 0) : ?>
	<thead>
		<th class="thead"><?php echo $row->name()->label ?></th>
		<th class="thead"><?php echo $row->firstname()->label ?></th>
		<th class="thead"><?php echo $row->title()->label ?></th>
		<th class="thead"><?php echo $row->status()->label ?></th>
		<th class="thead"><?php echo $row->mobile()->label ?></th>
		<th class="thead">Company</th>
		<th class="thead">Edit</th>
	</thead>
	<tbody>
		<?php endif; ?>
		<tr class="itemscope">
			<td class="itemprop name"><?php echo $row->name(); ?></td>
			<td class="itemprop firstname"><?php echo $row->firstname(); ?></td>
			<td class="itemprop title"><?php echo $row->title() ; ?></td>
			<td class="itemprop status"><?php echo $row->status() ; ?></td>
			<td class="itemprop mobile"><?php echo $row->mobile(); ?></td>
			<td class="itemprop name"><?php echo $row->name_crm_company(); ?></td>
			<td class="itemprop edit"><a href="/updatecontact/<?php echo $row->id_contact ?>"></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	<div class="noResults"><?php echo t('No contact for the moment'); ?></div>
<?php endif; ?>


