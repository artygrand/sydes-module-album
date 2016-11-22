<?php
/**
 * @package SyDES
 *
 * @copyright 2011-2015, ArtyGrand <artygrand.ru>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

class AlbumModel extends Model{
	public function create($title){
		$stmt = $this->db->prepare("INSERT INTO album (title) VALUES (:title)");
		$stmt->execute(array('title' => $title));

		return $this->db->lastInsertId();
	}

	public function read($id, $tag = false){
		$stmt = $this->db->prepare("SELECT * FROM album WHERE id = :id");
		$stmt->execute(array('id' => $id));
		$album['data'] = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($tag){
			$stmt = $this->db->prepare("SELECT * FROM album_pictures WHERE album_id = :id AND id IN (SELECT picture_id FROM album_tags WHERE tag = :tag) ORDER BY position ASC");
			$stmt->execute(array('id' => $id, 'tag' => $tag));
		} else {
			$stmt = $this->db->prepare("SELECT * FROM album_pictures WHERE album_id = :id ORDER BY position ASC");
			$stmt->execute(array('id' => $id));
		}
		$pictures = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$ids = array();
		$album['pictures'] = array();
		foreach ($pictures as $pic){
			$album['pictures'][$pic['id']] = $pic;
			$ids[] = $pic['id'];
		}

		$stmt = $this->db->query("SELECT * FROM album_tags WHERE picture_id IN (" . implode(',', $ids) . ")");
		$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($tags as $tag){
			$album['pictures'][$tag['picture_id']]['tags'][] = $tag['tag'];
		}

		return $album;
	}

	public function update($data){
		$stmt = $this->db->prepare("UPDATE album SET title = :title, thumb_width = :thumb_width, thumb_height = :thumb_height, status = :status WHERE id = :id");
		$stmt->execute($data);
	}

	public function delete($id){
		$stmt = $this->db->prepare("DELETE FROM album WHERE id = :id");
		$stmt->execute(array('id' => $id));
		$stmt = $this->db->prepare("DELETE FROM album_pictures WHERE album_id = :id");
		$stmt->execute(array('id' => $id));
	}

	public function getList(){
		$stmt = $this->db->query("SELECT * FROM album");
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function addpic($id, $files){
		$stmt = $this->db->prepare("INSERT INTO album_pictures (album_id, file, position) VALUES (:id, :file, (SELECT IFNULL(MAX(position), 0) + 1 FROM album_pictures))");
		foreach ($files as $file){
			$stmt->execute(array('id' => $id, 'file' => $file));
		}
	}

	public function getpic($id){
		$stmt = $this->db->prepare("SELECT * FROM album_pictures WHERE id = :id");
		$stmt->execute(array('id' => $id));
		$pic = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $this->db->query("SELECT * FROM album_tags WHERE picture_id = {$pic['id']}");
		$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($tags as $tag){
			$pic['tags'][] = $tag['tag'];
		}

		return $pic;
	}

	public function updpic($data){
		$tags = explode(',', $data['tags']);
		unset($data['tags']);

		$stmt = $this->db->prepare("UPDATE album_pictures SET title = :title, caption = :caption WHERE id = :id");
		$stmt->execute($data);
		
		$stmt = $this->db->prepare("DELETE FROM album_tags WHERE picture_id = :id");
		$stmt->execute(array('id' => $data['id']));
		$stmt = $this->db->prepare("INSERT INTO album_tags (picture_id, tag) VALUES (:id, :tag)");
		foreach ($tags as $tag){
			$tag = trim($tag);
			if (empty($tag)){
				continue;
			}
			$stmt->execute(array('id' => $data['id'], 'tag' => $tag));
		}
	}

	public function delpic($id){
		$stmt = $this->db->prepare("DELETE FROM album_pictures WHERE id = :id");
		$stmt->execute(array('id' => $id));
		$stmt = $this->db->prepare("DELETE FROM album_tags WHERE picture_id = :id");
		$stmt->execute(array('id' => $id));
	}

	public function sort($pics){
		$stmt = $this->db->prepare("UPDATE album_pictures SET position = :position WHERE id = :id");
		foreach ($pics as $index => $pic){
			$stmt->execute(array('id' => $pic, 'position' => $index+1));
		}
	}

	public function gettag($term){
		mb_regex_encoding('UTF-8');
		mb_internal_encoding('UTF-8');

		$this->db->sqliteCreateFunction('tolower', 'lower', 1);
		$stmt = $this->db->prepare("SELECT tag FROM album_tags WHERE tolower(tag) LIKE :term GROUP BY tag LIMIT 10");
		$stmt->execute(array('term' => '%' . lower($term) . '%'));
		$result = $stmt->fetchAll(PDO::FETCH_COLUMN);

		foreach ($result as $item){
			$out[] = array(
				'label' => $item,
				'value' => $item
			);
		}
		return $out;
	}
}
