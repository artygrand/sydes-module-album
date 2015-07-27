<?php
/**
 * @package SyDES
 *
 * @copyright 2011-2015, ArtyGrand <artygrand.ru>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

class AlbumController extends Controller{
	public $name = 'album';

	public function install(){
		$this->db->exec("CREATE TABLE album (
		`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
		`title` TEXT default '',
		`thumb_width` INTEGER default 150,
		`thumb_height` INTEGER default 150,
		`status` INTEGER default 1
	)");
	$this->db->exec("CREATE TABLE album_pictures (
		`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
		`album_id` INTEGER,
		`file` TEXT default '',
		`title` TEXT default '',
		`caption` TEXT default '',
		`tags` TEXT default '',
		`position` INTEGER
	)");
	$this->db->exec("CREATE TABLE album_tags (
		`picture_id` INTEGER,
		`tag` TEXT
	)");
		$this->registerModule(true);
		$this->response->notify(t('installed'));
		$this->response->redirect('?route=config/modules');
	}

	public function uninstall(){
		$this->db->exec("DROP TABLE IF EXISTS album");
		$this->db->exec("DROP TABLE IF EXISTS album_pictures");
		$this->db->exec("DROP TABLE IF EXISTS album_tags");
		$this->unregisterModule();
		$this->response->notify(t('uninstalled'));
		$this->response->redirect('?route=config/modules');
	}

	public function config(){
		$this->response->redirect('?route=album');
	}

	public function index(){
		$this->load->model('album');

		$data = array();
		$data['content'] = $this->load->view('album/list', array(
			'result' => $this->album_model->getList(),
		));

		$data['meta_title'] = t('module_' . $this->name);
		$data['breadcrumbs'] = H::breadcrumb(array(
			array('title' => t('module_' . $this->name))
		));

		$this->response->data = $data;
	}

	public function add(){
		$this->load->model('album');
		$id = $this->album_model->create($this->request->post['title']);
		$this->response->notify(t('saved'));
		$this->response->redirect('?route=' . $this->name . '/edit&id=' . $id);
	}

	public function edit(){
		if (!isset($this->request->get['id'])){
			throw new BaseException(t('error_empty_values_passed'));
		}

		$id = (int)$this->request->get['id'];
		$this->load->model('album');

		$album = $this->album_model->read($id);
		$data = array();
		$data['content'] = $this->load->view('album/pictures', array(
			'result' => $album['pictures'],
		));
		$data['sidebar_right'] = H::saveButton(DIR_SITE . $this->site . '/database.db') . $this->load->view('album/album', array(
			'result' => $album['data'],
		));
		$data['form_url'] = '?route=album/save';

		$data['meta_title'] = t('module_' . $this->name);
		$data['breadcrumbs'] = H::breadcrumb(array(
			array('url' => '?route=' . $this->name, 'title' => t('module_' . $this->name)),
			array('title' => t('editing')),
		));

		$this->response->data = $data;
		$this->response->style[] = '/system/module/album/assets/album.css';
		$this->response->script[] = '/system/module/album/assets/album.js';
	}

	public function save(){
		if (!isset($this->request->post['id'])){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$id = (int)$this->request->post['id'];
		$this->load->model('album');
		$this->album_model->update(array(
			'id' => $id,
			'title' => $this->request->post['title'],
			'thumb_width' => $this->request->post['thumb_width'],
			'thumb_height' => $this->request->post['thumb_height'],
			'status' => $this->request->post['status']
		));
		$this->response->notify(t('saved'));
		$this->response->redirect('?route=album/edit&id=' . $id);
	}

	public function delete(){
		if (!isset($this->request->get['id'])){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$this->load->model('album');
		$this->album_model->delete((int)$this->request->get['id']);

		$this->response->notify(t('deleted'));
		$this->response->redirect('?route=album');
	}

	public function addpic(){
		if (!isset($this->request->post['files']) || !IS_AJAX){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$this->load->model('album');
		$this->album_model->addpic((int)$this->request->post['id'], $this->request->post['files']);

		$this->response->notify(t('added'));
	}

	public function getpic(){
		if (!isset($this->request->get['id']) || !IS_AJAX){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$this->load->model('album');
		$body = $this->load->view('album/modal', array(
			'result' => $this->album_model->getpic((int)$this->request->get['id']),
		));
		$footer = H::button(t('save'), 'button', 'class="btn btn-primary apply-modal" data-dismiss="modal"');
		$this->response->body = H::modal(t('picture_properties'), $body, $footer, '?route=album/updpic');
	}

	public function updpic(){
		if (!isset($this->request->post['id']) || !IS_AJAX){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$this->load->model('album');
		$this->album_model->updpic(array(
			'id' => (int)$this->request->post['id'],
			'title' => $this->request->post['title'],
			'caption' => $this->request->post['caption'],
			'tags' => $this->request->post['tags']
		));

		$this->response->notify(t('saved'));
	}

	public function delpic(){
		if (!isset($this->request->post['id']) || !IS_AJAX){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$this->load->model('album');
		$this->album_model->delpic((int)$this->request->post['id']);
		
		$this->response->notify(t('deleted'));
	}

	public function sort(){
		if (!isset($this->request->post['pics']) || !IS_AJAX){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$this->load->model('album');
		$this->album_model->sort($this->request->post['pics']);
	}

	public function gettag(){
		if (!isset($this->request->post['term']) || !IS_AJAX){
			throw new BaseException(t('error_empty_values_passed'));
		}
		$this->load->model('album');
		$result = $this->album_model->gettag($this->request->post['term']);
		if (!$result){
			$result = '[]';
		}
		$this->response->body = $result;
	}
}