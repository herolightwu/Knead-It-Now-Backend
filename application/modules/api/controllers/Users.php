<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Demo Controller with Swagger annotations
 * Reference: https://github.com/zircote/swagger-php/
 */
class Users extends API_Controller {

	public function index_get()
	{
		$data = $this->users
			->select('id, username, email, active, first_name, last_name')
			->get_all();
		//$this->response($data);
	}

	public function id_get($id)
	{
		$data = $this->users
			->select('id, username, email, active, first_name, last_name')
			->get($id);
		//$this->response($data);
	}

	// TODO: user should be able to update their own account only
	public function id_put($id)
	{
		$data = elements(array('first_name', 'last_name'), $this->put());

		// proceed to update user
		$updated = $this->ion_auth->update($id, $data);

		// result
		($updated) ? $this->success($this->ion_auth->messages()) : $this->error($this->ion_auth->errors());
	}

	public function forgotpassword_post()
	{
		$email = $this->post('email');
		if($email != '') {
			$newPass = $this->generateRandomString();
			$updated = $this->ion_auth->reset_password($email, $newPass);
			if($updated){
				// TODO: send email to user
				//=========send email ======================
				$to = $email; // Send email to our user
				$subject = 'New KneadItNow Password'; // Give the email a subject
				$message = '<center>
  				<h2>New Password</h2><br>
				<p>Please find your new password below. You can use this new password to login to</p>
				<p> the KneadItNow app. Once you\'ve logged in, you can change your password</p>
				 <p>by selecting <b>Change Password</b> from the application menu.</p>

            	<br>
            	<b>New Password:</b> ' . $newPass . '<p>
            	-------------------------------------

            	<h4>Thanks for choosing KneadItNow.</h4>
				</center>';

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				$headers .= 'From: info@kneaditnow.com' . "\r\n" .
					'Reply-To: info@kneaditnow.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();// Set from headers
				mail($to, $subject, $message, $headers); // Send our email

				$result = array(
					'status' => 'success'
				);
				$this->response($result);
			} else{
				$result = array(
					'status' => 'failed',
					'error' => $this->ion_auth->errors(),
				);
				$this->response($result);
			}
		} else {
			$result = array(
				'status' => 'failed',
				'error' => "Email is not exist!",
			);
			$this->response($result);
		}
	}

	public function login_social_post(){
		$name = $this->post('username');
		$surname = $this->post('lastname');
		$email = $this->post('email');
		$fbId = $this->post('socialid');
		$password = '1234567890';
		$type = $this->post('type');
		$device_token = $this->post('token');
		$userimg = $this->post('photo');
		$this->load->model('Users_group_model', 'users_groups');

		if($type == 'fb'){
			$user_data = $this->users->where(array('fb_id'=>$fbId))->get_all();
		} else if($type == 'gl'){
			$user_data = $this->users->where(array('gl_id'=>$fbId))->get_all();
		} else{
			$user_data = $this->users->where(array('tw_id'=>$fbId))->get_all();
		}
		if(count($user_data)==1){
			unset($user_data[0]->password);
			unset($user_data[0]->salt);
			//unset($user_data[0]->ip_address);
			unset($user_data[0]->activation_code);
			unset($user_data[0]->forgotten_password_code);
			unset($user_data[0]->forgotten_password_time);
			unset($user_data[0]->remember_code);
			$this->users->update_field($user_data[0]->id, 'device_token', $device_token);
			//$ugroup = $this->users_groups->where(array('user_id'=>$user_data[0]->id))->get_all();
			//$user_data[0]->type = $ugroup[0]->group_id;
			if(!empty($userimg))
				$user_data[0]->photo = $userimg;

			$result = array(
				'status' => 'success',
				'data' =>$user_data[0]
			);
			$this->response($result);
		} else{
			$user_email = $this->users->where(array('email'=>$email))->get_all();
			if(count($user_email)==1){
				if($type == 'fb'){
					$this->users->update_field($user_email[0]->id, 'fb_id', $fbId);
				} else if($type == 'gl'){
					$this->users->update_field($user_email[0]->id, 'gl_id', $fbId);
				} else{
					$this->users->update_field($user_email[0]->id, 'tw_id', $fbId);
				}

				$this->users->update_field($user_email[0]->id, 'device_token', $device_token);
				unset($user_email[0]->password);
				unset($user_email[0]->salt);
				//unset($user_email[0]->ip_address);
				unset($user_email[0]->activation_code);
				unset($user_email[0]->forgotten_password_code);
				unset($user_email[0]->forgotten_password_time);
				unset($user_email[0]->remember_code);

				//$ugroup = $this->users_groups->where(array('user_id'=>$user_email[0]->id))->get_all();
				//$user_email[0]->type = $ugroup[0]->group_id;
				if(!empty($userimg))
					$user_email[0]->photo = $userimg;

				$result = array(
					'status' => 'success',
					'data' =>$user_email[0]
				);
				$this->response($result);
			} else {
				$additional_data = array();
				// set user to "members" group
				$group = array('1');

				// proceed to create user
				$user_id = $this->ion_auth->register($name, $password, $email, $additional_data, $group);
				// result
				if($user_id) {
					if($type == 'fb'){
						$this->users->update_field($user_id, 'fb_id', $fbId);
					} else if($type == 'gl'){
						$this->users->update_field($user_id, 'gl_id', $fbId);
					} else{
						$this->users->update_field($user_id, 'tw_id', $fbId);
					}

					$this->users->update_field($user_id, 'first_name', $name);
					$this->users->update_field($user_id, 'last_name', $surname);
					$this->users->update_field($user_id, 'device_token', $device_token);
					$this->users->update_field($user_id, 'type', '1');
					$user = $this->users->fetch_for_id($user_id);
					unset($user->password);
					unset($user->salt);
					//unset($user->ip_address);
					unset($user->activation_code);
					unset($user->forgotten_password_code);
					unset($user->forgotten_password_time);
					unset($user->remember_code);

					if(!empty($userimg))
						$user->photo = $userimg;
					$resData = array(
						'status' => 'success',
						'message' => $this->ion_auth->messages(),
						'data'=> $user
					);
					$this->response($resData);
				} else {
					$result = array(
						'status' => 'failed',
						'error' => $this->ion_auth->errors(),
					);
					$this->response($result);
				}
			}
		}
	}

	public function login_post()
	{
		$email = $this->post('email');
		$password = $this->post('password');
		$device_token = $this->post('token');

		// proceed to login user
		$logged_in = $this->ion_auth->login($email, $password, FALSE);

		// result
		if ($logged_in) {
			// get User object and remove unnecessary fields
			$user = $this->ion_auth->user()->row();
			unset($user->password);
			unset($user->salt);
			//unset($user->ip_address);
			unset($user->activation_code);
			unset($user->forgotten_password_code);
			unset($user->forgotten_password_time);
			unset($user->remember_code);

			$this->users->update_field($user->id, 'device_token', $device_token);

			if($user->type == "2"){
				$this->getBusinessInfo($user);
			}

			// return result
			//$this->response($user);
			$result = array(
				'status' => 'success',
				'data' => $user
			);
			$this->response($result);
		} else {
			$result = array(
				'status' => 'failed',
				'error' => $this->ion_auth->errors(),
			);
			$this->response($result);
		}
	}

	public function signup_to_user_post()
	{	// required fields
		$password = $this->post('password');
		$email = $this->post('email');
		$firstname = $this->post('firstname');
		$lastname = $this->post('lastname');
		$phone = $this->post('phone');
		$device_token = $this->post('token');

		if(!empty($email) && count($this->users->where(array('email'=>$email))->get_all()) > 0) {
			$result = array(
				"status" => 'false',
				"error" => "Email address already taken\ntry another email address\nor select Forgot Password to recover your account"
			);
			$this->response($result);
			return;
		}

		$additional_data = array(
			'first_name' => $firstname,
			'last_name' => $lastname,
			'phone' => $phone,
			'device_token' => $device_token,
			'type' => '1',
		);
		// set user to "members" group
		$group = array('1');

		// proceed to create user
		$user_id = $this->ion_auth->register($firstname, $password, $email, $additional_data, $group);

		// result
		if($user_id) {
			$user = $this->users->fetch_for_id($user_id);
			unset($user->password);
			unset($user->salt);
			//unset($user->ip_address);
			unset($user->activation_code);
			unset($user->forgotten_password_code);
			unset($user->forgotten_password_time);
			unset($user->remember_code);
			$this->users->update_field($user->id, 'username', $firstname);
			//$user->type = "1";//customer
			$resData = array(
				'status' => 'success',
				'data'=> $user
			);
			$this->response($resData);
		} else {
			$result = array(
				'status' => 'failed',
				'error' => $this->ion_auth->errors(),
			);
			$this->response($result);
		}
	}

	public function signup_to_seller_post()
	{	// required fields
		$password = $this->post('password');
		$email = $this->post('email');
		$firstname = $this->post('firstname');
		$lastname = $this->post('lastname');
		$phone = $this->post('phone');
		$username = $this->post('username');
		$gender = $this->post('gender');
		$birthday = $this->post('birthday');
		$bname = $this->post('business_name');
		$baddress = $this->post('business_address');
		$bzip = $this->post('business_zip');
		$blicense = $this->post('business_license');
		$bactive = $this->post('business_active');
		$bparking = $this->post('business_parking');
		$btypes = $this->post('business_types');
		$stp_id = $this->post('stp_id');
		$device_token = $this->post('token');
		$blocation = $this->post('location');

		$this->load->model('Business_info_model', 'business_infos');

		if(!empty($email) && count($this->users->where(array('email'=>$email))->get_all()) > 0) {
			$result = array(
				"status" => 'false',
				"error" => "Email address already taken\ntry another email address\nor select Forgot Password to recover your account"
			);
			$this->response($result);
			return;
		}

		$additional_data = array(
			'first_name' => $firstname,
			'last_name' => $lastname,
			'phone' => $phone,
			'gender' => $gender,
			'birthday' => $birthday,
			'device_token' => $device_token,
			'type' => '2',
			'address' => $baddress,
			'location' => $blocation,
			'stripe_id' => $stp_id,
		);
		// set user to "members" group
		$group = array('2');

		// proceed to create user
		$user_id = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

		// result
		if($user_id) {
			$business_data = array(
				'user_id' => $user_id,
				'name' => $bname,
				'address' => $baddress,
				'zipcode' => $bzip,
				'license_code' => $blicense,
				'active_year' => $bactive,
				'parking' => $bparking,
				'massage_types' => $btypes,
			);
			$this->business_infos->insert($business_data);

			$user = $this->users->fetch_for_id($user_id);
			unset($user->password);
			unset($user->salt);
			//unset($user->ip_address);
			unset($user->activation_code);
			unset($user->forgotten_password_code);
			unset($user->forgotten_password_time);
			unset($user->remember_code);

			$this->getBusinessInfo($user);

			$resData = array(
				'status' => 'success',
				'data'=> $user
			);
			$this->response($resData);
		} else {
			$result = array(
				'status' => 'failed',
				'error' => $this->ion_auth->errors(),
			);
			$this->response($result);
		}
	}

	public function logout_post(){
		$userId = $this->post('userID');
		$this->users->update_field($userId, 'device_token', "");
		$resData = array(
			'status' => 'success',
		);
		$this->response($resData);
	}

	public function getUserById_post(){
		$userId = $this->post('userID');
		$email = $this->post('email');
		$token = $this->post('token');
		$user = $this->users->fetch_for_id($userId);
		if($user){
			unset($user->password);
			unset($user->salt);
			//unset($user->ip_address);
			unset($user->activation_code);
			unset($user->forgotten_password_code);
			unset($user->forgotten_password_time);
			unset($user->remember_code);
			$this->users->update_field($user->id, 'device_token', $token);
			if($user->email == $email){
				if($user->type == "2"){
					$this->getBusinessInfo($user);
				}

				$resData = array(
					'status' => 'success',
					'data'=> $user,
				);
				$this->response($resData);
			} else{
				$resData = array(
					'status' => 'failed',
					'error'=> 'Email was invailed',
				);
				$this->response($resData);
			}

		} else{
			$result = array(
				'status' => 'failed',
				'error' => 'User is not exist',
			);
			$this->response($result);
		}
	}

	public function changepassword_post()
	{
		$email = $this->post('email');
		$new_password = $this->post('new_pass');
		$old_password = $this->post('old_pass');
		$identity = $email;
		$logged_in = $this->ion_auth->login($identity, $old_password, FALSE);
		if($logged_in){
			$this->load->model('Ion_auth_model', 'ion_auth_model');
			$status = $this->ion_auth_model->change_password($identity, $old_password, $new_password);
			if($status){
				$result = array(
					"status" => "success",
					'data' => 'Your password was changed successfully.',
				);
				$this->response($result);
			} else{
				$this->error($this->ion_auth_model->errors());
			}
		} else {
			$this->error($this->ion_auth->errors());
		}
	}

	public function updateUserInfo_post(){
		$userid = $this->post('uid');
		$uemail = $this->post('email');
		$phoneno = $this->post('phone');
		$maildata = $this->users->get_where('email', $uemail);
		if(count($maildata) == 0 || (count($maildata) == 1 && $maildata[0]->id == $userid)){
			$result = $this->users->update_field($userid, "email", $uemail);
			$result = $this->users->update_field($userid, "phone", $phoneno);

			$response = array(
				'status' => 'success',
				'data' => '',
			);
			$this->response($response);
		} else{
			$result = array(
				'status' => "failed",
				'error' => "This email is existed."
			);
			$this->response($result);
		}
	}

	public function updateSellerProfile_post(){
		$userid = $this->post('uid');
		$email = $this->post('email');
		$firstname = $this->post('firstname');
		$lastname = $this->post('lastname');
		$phone = $this->post('phone');
		$gender = $this->post('gender');
		$bname = $this->post('business_name');
		$baddress = $this->post('business_address');
		$bzip = $this->post('business_zip');
		$blicense = $this->post('business_license');
		$bactive = $this->post('business_active');
		$bparking = $this->post('business_parking');
		$btypes = $this->post('business_types');
		$bid = $this->post('bid');
		$blocation = $this->post('location');
		$this->load->model('Business_info_model', 'business_infos');

		$maildata = $this->users->get_where('email', $email);
		if(count($maildata) == 0 || (count($maildata) == 1 && $maildata[0]->id == $userid)){
			$namedata = $this->users->get_where('username', $firstname);
			if(count($namedata) == 0 || (count($namedata) == 1 && $namedata[0]->id == $userid)){
				$result = $this->users->update_field($userid, "email", $email);
				$result = $this->users->update_field($userid, "username", $firstname);
				$result = $this->users->update_field($userid, "phone", $phone);
				$result = $this->users->update_field($userid, "gender", $gender);
				$result = $this->users->update_field($userid, "last_name", $lastname);
				$result = $this->users->update_field($userid, "first_name", $firstname);
				$result = $this->users->update_field($userid, "address", $baddress);
				$result = $this->users->update_field($userid, "location", $blocation);

				//$bdata = $this->business_infos->get_where('user_id', $userid);
				$result = $this->business_infos->update_field($bid, "name", $bname);
				$result = $this->business_infos->update_field($bid, "address", $baddress);
				$result = $this->business_infos->update_field($bid, "zipcode", $bzip);
				$result = $this->business_infos->update_field($bid, "license_code", $blicense);
				$result = $this->business_infos->update_field($bid, "active_year", $bactive);
				$result = $this->business_infos->update_field($bid, "parking", $bparking);
				$result = $this->business_infos->update_field($bid, "massage_types", $btypes);


				$user = $this->users->fetch_for_id($userid);
				unset($user->password);
				unset($user->salt);
				//unset($user->ip_address);
				unset($user->activation_code);
				unset($user->forgotten_password_code);
				unset($user->forgotten_password_time);
				unset($user->remember_code);
				$this->getBusinessInfo($user);

				$response = array(
					"status" => "success",
					'data' => $user,
				);
				$this->response($response);
			}else{
				$result = array(
					'status' => "failed",
					'error' => "Username is existed."
				);
				$this->response($result);
			}
		} else{
			$result = array(
				'status' => "failed",
				'error' => "This email is existed."
			);
			$this->response($result);
		}

	}

	public function updateHomeAddr_post(){
		$userid = $this->post("uid");
		$address = $this->post("address");
		$location = $this->post("location");

		$result = $this->users->update_field($userid, "address", $address);
		$result = $this->users->update_field($userid, "location", $location);

		$response = array(
			'status' => 'success',
			'data' => '',
		);
		$this->response($response);
	}

	public function uploadPhoto_post(){
		$userid = $this->post("uid");
		// ===== Image file upload
		if (isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])) {
			$config['upload_path'] = UPLOAD_USER_PHOTO;
			$config['file_name'] = $userid.'_'.time();
			$config['overwrite'] = FALSE;
			$config['allowed_types'] = 'jpg|png|jpeg|bmp|gif';
			$config['max_size'] = PHOTO_MAX_SISE;
			$config['encrypt_name'] = FALSE;

			// Check directory
			if (!file_exists($config['upload_path']) || !is_dir($config['upload_path'])) {
				mkdir($config['upload_path']);
				chmod($config['upload_path'], DIR_WRITE_MODE);
			}

			$this->load->library('upload', $config);
			if ($this->upload->do_upload('photo')) {
				$upload_result = $this->upload->data();

				$photoname = $upload_result['file_name'];
				$this->users->update_field($userid, 'photo', $photoname);

				$resdata = array(
					'status' => 'success',
					'data' => $photoname
				);
				$this->response($resdata);
			} else {
				$resdata = array(
					'status' => 'failed',
					'error' => 'Upload photo failed!'
				);
				$this->response($resdata);
			}
		} else {
			$resdata = array(
				'status' => 'failed',
				'error' => 'Needed items is requested!'
			);
			$this->response($resdata);
		}
	}

	public function removePhoto_post(){
		$userid = $this->post("uid");
		$this->users->update_field($userid, 'photo', '');
		$resdata = array(
			'status' => 'success',
			'data' => '',
		);
		$this->response($resdata);
	}

	public function getReviews_post(){
		$userid = $this->post('uid');
		$this->load->model('Review_post_model', 'reviews');
		$retlist = $this->reviews->get_where("user_id", $userid);
		foreach($retlist as $d){
			$user_one = $this->users->fetch_for_id($d->given_id);
			$d->first_name = $user_one->first_name;
			$d->last_name = $user_one->last_name;
		}

		$udata = $this->users->fetch_for_id($userid);
		$retdata = array(
			'rate' => $udata->rate,
			'count' => $udata->rate_count,
			'reviews' => $retlist,
		);
		$resdata = array(
			'status' => 'success',
			'data' => $retdata,
		);
		$this->response($resdata);
	}

	public function getTherapistProfile_post(){
		$userid = $this->post('uid');
		$this->load->model('Review_post_model', 'reviews');
		$retlist = $this->reviews->get_where("user_id", $userid);
		foreach($retlist as $d){
			$user_one = $this->users->fetch_for_id($d->given_id);
			$d->first_name = $user_one->first_name;
			$d->last_name = $user_one->last_name;
		}

		$this->load->model('Business_info_model', 'business_infos');
		$bus_data = $this->business_infos->get_where('user_id', $userid);

		$udata = $this->users->fetch_for_id($userid);
		$retdata = array(
			'rate' => $udata->rate,
			'count' => $udata->rate_count,
			'active_year' => $bus_data[0]->active_year,
			'massage_types' => $bus_data[0]->massage_types,
			'parking' => $bus_data[0]->parking,
			'reviews' => $retlist,
		);
		$resdata = array(
			'status' => 'success',
			'data' => $retdata,
		);
		$this->response($resdata);
	}

	public function getUserRate_post(){
		$userid = $this->post('uid');
		$udata = $this->users->fetch_for_id($userid);
		$retdata = array(
			'rate' => $udata->rate,
			'count' => $udata->rate_count,
		);
		$resdata = array(
			'status' => 'success',
			'data' => $retdata,
		);
		$this->response($resdata);
	}

	public function updateStpAccount_post(){
		$userid = $this->post('uid');
		$accountid = $this->post('account_id');
		$this->users->update_field($userid, 'stripe_id', $accountid);
		$resdata = array(
			'status' => 'success',
			'data' => $accountid,
		);
		$this->response($resdata);
	}

	function generateRandomString($length = 8) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}

	public function sendmail_post(){
		$tomail = $this->post('tomail');
		$content = $this->post('content');
		$frommail = $this->post('from');
		$to = $tomail; // Send email to our user
		$subject = 'Welcome KneadItNow App'; // Give the email a subject
		$message = '<center>
				<p>'.$content.'</p>

            	<br>
            	-------------------------------------

            	<h4>'.$frommail.'</h4>
				</center>';

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= 'From:'.$frommail. "\r\n" .
			'Reply-To: '.$tomail . "\r\n" .
			'X-Mailer: PHP/' . phpversion();// Set from headers
		mail($to, $subject, $message, $headers); // Send our email

		$result = array(
			'status' => 'success',
			'data' => '',
		);
		$this->response($result);
	}

	private function getBusinessInfo(&$user){
		$this->load->model('Business_info_model', 'business_infos');
		$business_info = $this->business_infos->get_where('user_id',$user->id);
		$user->business_info = $business_info[0];
		return $user;
	}

}
