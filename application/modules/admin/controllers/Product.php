<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_builder');
	}

	// Frontend User CRUD
	public function index()
	{
		$crud = $this->generate_crud('products');
		$crud->columns('user_id', 'name', 'updatedate', 'status');

		// only webmaster and admin can change member groups
		$crud->order_by('updatedate', 'desc');
		$crud->set_relation('user_id', 'users', 'username');
		$crud->display_as('user_id','User');
		$crud->display_as('name','List Name');
		$crud->display_as('updatedate','Update');
		$crud->display_as('status','Status');

		// disable direct create / delete Frontend User
		//$crud->unset_delete();
		$crud->unset_add();

		$this->mPageTitle = 'Area Lists';
		$this->render_crud();
	}

	// Create Frontend User

}
