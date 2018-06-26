<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Admin_Controller {

	public function index()
	{
		$this->load->model('user_model', 'users');
		$this->load->model('book_post_model', 'books');
		$this->load->model('users_group_model', 'users_groups');
		$user_ids = $this->users->get_where('type','1');
		$pilot_ids = $this->users->get_where('type', '2');
		$book_ids = $this->books->get_confirmed_books();
		$this->mViewData['count'] = array(
			'users' => count($user_ids), //$this->users->count_all(),
			'therapists' => count($pilot_ids),
			'books' => count($book_ids),//$this->books->count_all(),
		);
		$this->render('home');
	}
}
