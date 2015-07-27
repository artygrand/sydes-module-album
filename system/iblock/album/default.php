<div class="tags">
	<a href="<?=$page['fullpath'];?>"><?=t('all');?></a>
<?php foreach ($result['tags'] as $tag){ ?>
	<a href="<?=$page['fullpath'];?>?tag=<?=$tag;?>"><?=$tag;?></a>
<?php } ?>
</div>

<div class="pictures">
<?php foreach ($result['pictures'] as $item){ ?>
	<a href="<?=$item['file'];?>" rel="lightbox[<?=$result['data']['id'];?>]" title="<?=$item['caption'];?>">
		<img src="/cache/img/<?=$result['data']['thumb_width'];?>_<?=$result['data']['thumb_height'];?>_c<?=$item['file'];?>" alt="<?=$item['title'];?>">
	</a>
<?php } ?>
</div>

<?php
if ($args['limit'] && $args['show_pagination']){
	echo H::pagination($page['fullpath'], $count, $skip, $args['limit']);
}
?>