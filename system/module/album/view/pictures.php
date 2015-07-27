<div id="album">
	<div class="pic empty" id="add" title="<?=t('add_picture_title');?>" data-toggle="tooltip">
		<span class="glyphicon glyphicon-plus-sign"></span>
	</div>

<?php foreach ($result as $item){ ?>
	<div class="pic">
		<a href="?route=album/getpic&id=<?=$item['id'];?>" data-toggle="modal" data-target="#modal">
			<img src="/cache/img/150_150_c<?=$item['file'];?>">
		</a>
		<div class="control">
			<button type="button" class="btn btn-danger btn-xs delete" data-id="<?=$item['id'];?>"><?=t('delete');?></button>
		</div>
	</div>
<?php } ?>
</div>
