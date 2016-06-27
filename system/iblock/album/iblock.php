<?php
/**
* Infoblock: Album
* Shows pictures
* Usage:
* {iblock:album?show=1} - from album with id = 1
*   tag=cloud - filter by tag
*   limit=20 - quantity per page
*   show_pagination=1 - show pagination (works if limit is set)
*/

$defaults = array(
	'show' => 0,
	'tag' => '',
	'limit' => 0,
	'show_pagination' => 0,
);
$args = array_merge($defaults, $args);

if (!$args['show']){
	return;
}

$tag = false;
if (isset($_GET['tag'])){
	$tag = $_GET['tag'];
}
if (!empty($args['tag'])){
	$tag = $args['tag'];
}

$this->load->model('album');
$result = $this->album_model->read($args['show'], $tag);

if ($result['data']['status'] == 0){
	return;
}

if ($args['limit']){
	$skip = (!empty($_GET['skip']) && $_GET['skip'] > 0) ? (int)$_GET['skip'] : 0;
	$result['pictures'] = array_slice($result['pictures'], $skip, $args['limit']);
}

$stmt = $this->db->query("SELECT tag FROM album_tags WHERE picture_id IN (SELECT id FROM album_pictures WHERE album_id = " . (int)$args['show'] . ") GROUP BY tag");
$result['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
