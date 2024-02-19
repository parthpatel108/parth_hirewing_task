<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once('App.php');
class HireWingAuth extends App
{
	public function __construct()
	{
		parent::__construct();
		// If user is already logged in, redirect to home page
		if (!$this->authenticateSession()) {
			$this->logout(true);
		}
	}
	public function users()
	{
		if (!is_admin()) {
			$this->session->set_flashdata('error', 'Access denied to users list.');
			redirect(base_url('home'));
		}
		$view_data = array();
		$view_data['title'] = 'Users List';
		$id = $this->session->userdata[SESSION_HANDLER]['id'];
		$view_data['users'] = $this->my_users->getAll(['is_deleted' => 0, 'id !=' => $id]); //Exclude session user : Parth Patel
		$this->load->view('users', $view_data);
	}
	
	public function edit_user($id = null)
	{
		if (!is_admin()) {
			$this->session->set_flashdata('error', 'Access denied to edit user.');
			redirect(base_url('home'));
		}
		$id = decrypt($id);
		$details = $this->my_users->get(array('id' => $id));
		if (empty($details)) {
			$this->session->set_flashdata('error', 'User not found.');
			redirect(base_url('home'));
		}
		$view_data = array();
		$view_data['_profile'] = 0;
		$view_data['title'] = 'Edit User';
		$view_data['user'] = $this->my_users->get(['is_deleted' => 0, 'is_active' => 1, 'id =' => $id]);
		$this->load->view('edit_user', $view_data);
	}


	public function update_profile()
	{

		$id = $this->session->userdata[SESSION_HANDLER]['id'];
		$details = $this->my_users->get(array('id' => $id));
		if (empty($details)) {
			$this->session->set_flashdata('error', 'User not found.');
			redirect(base_url('home'));
		}

		$view_data = array();
		$view_data['title'] = 'Edit Profile';
		$view_data['_profile'] = 1;
		$view_data['user'] = $details;
		$this->load->view('edit_user', $view_data);
	}

	public function update_user_details()
	{
		$request = $this->input->post();
		$data = array();
		$id = decrypt($request['id']);
		$data['id'] = $id;
		// Check username and email is already linked with existing account : Parth Patel
		if (!empty($request['username'])) {
			$condition = array();
			$condition['username'] = trim($request['username']);
			$condition['id !='] = $id;
			if (!empty($this->my_users->get($condition))) {
				echo json_encode(['status' => 'ERROR', 'message' => 'User details already register with this username.']);
				die;
			}
			$data['username'] =  htmlentities($request['username']);
		}

		if (!empty($request['email'])) {
			$condition = array();
			$condition['email'] = trim($request['email']);
			$condition['id !='] = $id;
			if (!empty($this->my_users->get($condition))) {
				echo json_encode(['status' => 'ERROR', 'message' => 'User details already register with this email.']);
				die;
			}
			$data['email'] =  htmlentities($request['email']);
		}

		$data['first_name'] =  htmlentities($request['first_name']);
		$data['last_name'] =  htmlentities($request['last_name']);
		if (!empty($request['password'])) {
			$data['password'] =  password_hash($request['password'], PASSWORD_DEFAULT);
		}

		if (!empty($request['role_id'])) {
			$data['role_id'] = $request['role_id'];
		}

		if (!empty($this->my_users->saveData($data))) {
			echo json_encode(['status' => 'SUCCESS', '_profile' => $request['_profile'], 'message' => $request['_profile'] ? 'Profile updated successfully.' : 'User details updated successfully.']);
		} else {
			echo json_encode(['status' => 'ERROR', 'message' => 'User details failed to update in system.']);
		}
	}

	public function delete_user($id = null)
	{
		if (!is_admin()) {
			$this->session->set_flashdata('error', 'Access denied to delete user.');
			redirect(base_url('home'));
		}
		$id = decrypt($id);
		$details = $this->my_users->get(array('id' => $id));
		if (empty($details)) {
			$this->session->set_flashdata('error', 'User not found.');
			redirect(base_url('home'));
		}

		$data['id'] = $id;
		$data['is_deleted'] = 1;

		if ($this->my_users->saveData($data)) {
			$this->session->set_flashdata('success', 'User deleted successfully.');
			redirect(base_url('users'));
		} {
			$this->session->set_flashdata('error', 'Something went wrong, Please try again later.');
			redirect(base_url('users'));
		}
	}

	public function index()
	{
		// Load view based on user role
		if (is_admin()) {
			// Load view for admin user
			$this->load->view('admin_home');
		} else {
			// Load view for normal user
			$this->load->view('user_home');
		}
	}
	public function profile()
	{
		if (!$this->authenticateSession()) {
			$this->logout();
		}
		$view_data = array();
		$view_data['title'] = 'Profile';
		// Get user details from session
		$user_id =  $this->session->userdata[SESSION_HANDLER]['id'];
		$view_data['user'] = $this->my_users->get(['id' => $user_id]);
		$this->load->view('profile', $view_data);
	}
}
