<div class="form-group">
	<label><?=t('album_title');?></label>
	<input type="text" name="title" class="form-control" value="<?=$result['title'];?>">
</div>

<div class="form-group">
	<label><?=t('thumb_size');?></label>
	<div class="row">
		<div class="col-sm-6">
			<input type="text" name="thumb_width" class="form-control" value="<?=$result['thumb_width'];?>" placeholder="<?=t('width');?>">
		</div>
		<div class="col-sm-6">
			<input type="text" name="thumb_height" class="form-control" value="<?=$result['thumb_height'];?>" placeholder="<?=t('height');?>">
		</div>
	</div>
</div>

<div class="form-group">
	<label><?=t('active');?></label>
	<?=H::yesNo('status', $result['status']);?>
</div>

<input type="hidden" name="id" id="album_id" value="<?=$result['id'];?>">
