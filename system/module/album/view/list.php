<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title"><?=t('module_album');?></h4>
	</div>
	<table class="table table-hover table-condensed va-middle">
		<thead>
			<tr>
				<th><?=t('title');?></th>
				<th><?=t('token');?></th>
				<th style="width:150px;"><?=t('actions');?></th>
			</tr>
		</thead>
		<tbody>
	<?php foreach ($result as $item){ ?>
			<tr>
				<td><?=$item['title'];?></td>
				<td>{iblock:album?show=<?=$item['id'];?>}</td>
				<td>
					<div class="btn-group pull-right btn-group-sm">
						<a class="btn btn-default" href="?route=album/edit&id=<?=$item['id'];?>"><?=t('edit');?></a>
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu dropdown-menu-right">
							<li><a class="danger" href="?route=album/delete&id=<?=$item['id'];?>"><?=t('delete');?></a></li>
						</ul>
					</div>
				</td>
			</tr>
	<?php } ?>
		</tbody>
	</table>
	<div class="panel-footer">
	<form action="?route=album/add" method="post">
		<div class="input-group">
			<input type="text" name="title" class="form-control" data-toggle="tooltip" title="<?=t('title')?>" placeholder="<?=t('album_title_example')?>" required>
			<span class="input-group-btn"><button type="submit" class="btn btn-default"><?=t('add');?></button></span>
		</div>
	</form>
	</div>
</div>