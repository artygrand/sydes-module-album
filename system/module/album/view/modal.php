<div class="row">
	<div class="col-sm-6">
		<img src="/cache/img/268_200_r<?=$result['file'];?>" class="preview"><br>
		<?=basename($result['file']);?>
	</div>
	<div class="col-sm-6">
		<?=H::form(array(
			'title' => array(
				'label' => t('title'),
				'type' => 'string',
				'value' => $result['title'],
				'attr' => 'class="form-control"'
			),
			'caption' => array(
				'label' => t('caption'),
				'type' => 'textarea',
				'value' => $result['caption'],
				'attr' => 'class="form-control" rows="4"'
			),
		));?>
	</div>
</div>
<?=H::form(array(
	'tags' => array(
		'label' => t('tags'),
		'type' => 'string',
		'value' => empty($result['tags']) ? '' : implode(', ', $result['tags']) . ', ',
		'attr' => 'class="form-control"'
	)
));?>
<input type="hidden" name="id" value="<?=$result['id'];?>">