<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Therapist extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_builder');
	}

	// Frontend User CRUD
	public function index()
	{
		$crud = $this->generate_crud('users');
		$crud->columns('photo', 'first_name', 'last_name', 'email', 'business_name', 'address', 'zip', 'phone', 'license', 'active_year', 'rate', 'services');
		$this->unset_crud_fields('ip_address', 'last_login', 'type');
		$crud->where('type', '2');
		$crud->set_field_upload('photo', UPLOAD_USER_PHOTO);

		$crud->callback_column('services', array($this, 'callback_service_names'));
		$crud->callback_column('business_name', array($this, 'callback_business_name'));
		$crud->callback_column('zip', array($this, 'callback_zip_code'));
		$crud->callback_column('license', array($this, 'callback_license_code'));
		$crud->callback_column('active_year', array($this, 'callback_active_year'));
		$crud->callback_column('address', array($this, 'callback_address'));
		// only webmaster and admin can change member groups
		if ($crud->getState()=='list' || $this->ion_auth->in_group(array('webmaster', 'admin')))
		{
		}

		$crud->display_as('address','B. Address');
		$crud->display_as('first_name','First Name');
		$crud->display_as('last_name','Last Name');
		$crud->display_as('zip','B. Zip');
		$crud->display_as('services','Massage Types');
		$crud->display_as('rate', 'Rating');
		$crud->display_as('business_name', 'B. Name');
		$crud->display_as('active_year', 'Year of Activation');
		$crud->display_as('license', 'License #');
		$crud->display_as('phone', 'B. Phone #');
		// only webmaster and admin can reset user password
		/*if ($this->ion_auth->in_group(array('webmaster', 'admin')))
		{
			$crud->add_action('Reset Password', '', 'admin/user/reset_password', 'fa fa-repeat');
		}*/

		// disable direct create / delete Frontend User
		$crud->unset_add();
		//$crud->unset_delete();
		$crud->unset_edit();

		$this->mPageTitle = 'Therapists';
		$this->render_crud();
	}

	public function callback_address($value, $row) {
		$this->load->model('Business_info_model', 'business_infos');
		$bus_data = $this->business_infos->get_where('user_id',$row->id);
		$ret_str = $bus_data[0]->address;
		$ret_data = str_replace(',', '<br>', $ret_str);
		return $ret_data;
	}

	public function callback_active_year($value, $row) {
		$this->load->model('Business_info_model', 'business_infos');
		$bus_data = $this->business_infos->get_where('user_id',$row->id);
		return $bus_data[0]->active_year;
	}

	public function callback_license_code($value, $row) {
		$this->load->model('Business_info_model', 'business_infos');
		$bus_data = $this->business_infos->get_where('user_id',$row->id);
		return $bus_data[0]->license_code;
	}

	public function callback_zip_code($value, $row) {
		$this->load->model('Business_info_model', 'business_infos');
		$bus_data = $this->business_infos->get_where('user_id',$row->id);
		return $bus_data[0]->zipcode;
	}

	public function callback_business_name($value, $row) {
		$this->load->model('Business_info_model', 'business_infos');
		$bus_data = $this->business_infos->get_where('user_id',$row->id);
		return $bus_data[0]->name;
	}

	public function callback_service_names($value, $row) {
		$this->load->model('Business_info_model', 'business_infos');
		$this->load->model('Massage_type_model', 'massages');
		$bus_data = $this->business_infos->get_where('user_id',$row->id);
		$types = explode(",", $bus_data[0]->massage_types);
		$ret_str = "<ul>";
		foreach($types as $d){
			$one_type = $this->massages->fetch_for_id($d);
			$ret_str = $ret_str."<li>".$one_type->name."</li>";
		}
		$ret_str = $ret_str."</ul>";
		return $ret_str;
	}

	// Create Frontend User
	public function create()
	{
		$form = $this->form_builder->create_form();

		if ($form->validate())
		{
			// passed validation
			$username = $this->input->post('username');
			$email = $this->input->post('email');
			$phone = $this->input->post('phone');
			$password = $this->input->post('password');
			$identity = empty($username) ? $email : $username;
			$additional_data = array(
				'first_name'	=> $this->input->post('first_name'),
				'last_name'		=> $this->input->post('last_name'),
				'phone'			=> $phone,
			);
			//$groups = $this->input->post('groups');
			$groups = array('2');

			// [IMPORTANT] override database tables to update Frontend Users instead of Admin Users
			$this->ion_auth_model->tables = array(
				'users'				=> 'users',
				'groups'			=> 'groups',
				'users_groups'		=> 'users_groups',
				'login_attempts'	=> 'login_attempts',
			);

			// proceed to create user
			$user_id = $this->ion_auth->register($identity, $password, $email, $additional_data, $groups);
			if ($user_id)
			{
				$this->load->model('User_model', 'users');
				$this->users->update_field($user_id, 'type', '2');
				// success
				$messages = $this->ion_auth->messages();
				$this->system_message->set_success($messages);

				// directly activate user
				$this->ion_auth->activate($user_id);
			}
			else
			{
				// failed
				$errors = $this->ion_auth->errors();
				$this->system_message->set_error($errors);
			}
			refresh();
		}

		// get list of Frontend user groups
		$this->load->model('group_model', 'groups');
		$this->mViewData['groups'] = $this->groups->get_all();
		$this->mPageTitle = 'Create Therapist';

		$this->mViewData['form'] = $form;
		$this->render('user/create');
	}

	// User Groups CRUD
	public function group()
	{
		$crud = $this->generate_crud('groups');
		$this->mPageTitle = 'User Groups';
		$this->render_crud();
	}

	// Frontend User Reset Password
	public function reset_password($user_id)
	{
		// only top-level users can reset user passwords
		$this->verify_auth(array('webmaster', 'admin'));

		$form = $this->form_builder->create_form();
		if ($form->validate())
		{
			// pass validation
			$data = array('password' => $this->input->post('new_password'));

			// [IMPORTANT] override database tables to update Frontend Users instead of Admin Users
			$this->ion_auth_model->tables = array(
				'users'				=> 'users',
				'groups'			=> 'groups',
				'users_groups'		=> 'users_groups',
				'login_attempts'	=> 'login_attempts',
			);

			// proceed to change user password
			if ($this->ion_auth->update($user_id, $data))
			{
				$messages = $this->ion_auth->messages();
				$this->system_message->set_success($messages);
			}
			else
			{
				$errors = $this->ion_auth->errors();
				$this->system_message->set_error($errors);
			}
			refresh();
		}

		$this->load->model('user_model', 'users');
		$target = $this->users->get($user_id);
		$this->mViewData['target'] = $target;

		$this->mViewData['form'] = $form;
		$this->mPageTitle = 'Reset User Password';
		$this->render('user/reset_password');
	}
}
