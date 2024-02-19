<?php
defined('BASEPATH') or exit('No direct script access allowed');
// TEXT LOCAL API KEY
class App extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if ($this->authenticateSession()) {
            redirect(base_url('home'));
        }
        $viewData = array();
        $viewData['title'] = 'Login';
        $viewData['username'] = "";
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $viewData['username'] = $username;
            $conditions = array();
            $conditions['username'] = $username;
            // $conditions['role_id'] = 1; //For admin
            $conditions['is_deleted'] = 0;
            $conditions['is_active'] = 1;
            $login_details = $this->my_users->get($conditions);

            if (!empty($login_details)) {
                if ($login_details['locked'] == 1) {
                    $this->session->set_flashdata('error', 'Your account is locked due to failed login attempts.<a href="#">Contact Administrator</a>');
                    redirect(base_url() . 'login');
                }
                if (password_verify($password, $login_details['password'])) {

                    $user_details = array();
                    $user_details['username'] = $username;
                    $user_details['fullname'] = $login_details['first_name'] . " " . $login_details['last_name'];
                    $user_details['first_name'] = $login_details['first_name'];
                    $user_details['last_name'] = $login_details['last_name'];
                    $user_details['email'] = $login_details['email'];
                    $user_details['id'] = $login_details['id'];
                    $user_details['role_id'] = $login_details['role_id'];
                    $user_details['is_admin'] = $login_details['role_id'] == 1 ? 1 : 0;
                    if ($this->setUser($user_details)) {
                        $this->session->set_flashdata('success', 'Login Success.');
                        redirect(base_url('home'));
                    } else {
                        $this->session->set_flashdata('error', 'Something went wrong, Please try again later.');
                        redirect(base_url() . 'login');
                    }
                } else {
                    $this->handleFailedLogin($username);
                    //Password is incorrect.
                    $this->session->set_flashdata('error', 'Password is incorrect.');
                    // redirect(base_url() . 'login');
                }
            } else {
                //Invalid user or password.
                $this->session->set_flashdata('error', 'Invalid username.');
                // redirect(base_url() . 'login');
            }
        }

        $this->load->view('auth/login', $viewData);
    }



    public function authenticateSession()
    {
        if (!empty($this->session->userdata()) && !empty($this->session->userdata[SESSION_HANDLER])) {
            if ($this->my_users->authToken($this->session->userdata[SESSION_HANDLER]['id'], $this->session->userdata[SESSION_HANDLER]['access_token'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function register()
    {
        $view_data = array();
        $view_data['title'] = 'Register';
        $this->load->view('auth/register', $view_data);
    }
    public function save_user_details()
    {
        $request = $this->input->post();
        // Check username and email is already linked with existing account : Parth Patel
        $condition = array();
        $condition['username'] = trim($request['username']);
        if (!empty($this->my_users->get($condition))) {
            echo json_encode(['status' => 'ERROR', 'message' => 'User details already register with this username.']);
            die;
        }
        $condition = array();
        $condition['email'] = trim($request['email']);
        if (!empty($this->my_users->get($condition))) {
            echo json_encode(['status' => 'ERROR', 'message' => 'User details already register with this email.']);
            die;
        }
        $data = array();
        $data['first_name'] =  htmlentities($request['first_name']);
        $data['last_name'] =  htmlentities($request['last_name']);
        $data['email'] =  htmlentities($request['email']);
        $data['username'] =  htmlentities($request['username']);
        $data['password'] =  password_hash($request['password'], PASSWORD_DEFAULT);
        $data['role_id'] = 3;
        if (!empty($this->my_users->saveData($data))) {
            echo json_encode(['status' => 'SUCCESS', 'message' => 'User details registered successfully.']);
        } else {
            echo json_encode(['status' => 'ERROR', 'message' => 'User details failed to register in system.']);
        }
    }


    private function handleFailedLogin($username)
    {
        // Update failed login attempts for the user

        $this->my_users->incrementFailedLoginAttempts($username);

        // Check if failed login attempts exceed threshold
        $failedAttempts = $this->my_users->getFailedLoginAttempts($username);
        $maxAttempts = 3; // Adjust as needed
        if ($failedAttempts >= $maxAttempts) {
            // Lock the account
            $this->my_users->lockAccount($username);
            // Optionally, notify the user about the account lockout
        }
    }

    public function wing_password_helth()
    {
        $request = $this->input->post();
        if (!empty($request['password'])) {
            $password = $_POST['password'];
            $strength = $this->check_password_helth($password);
            echo $strength;
        }
    }

    public function check_password_helth($password)
    {
        $score = 0;
        // Check for length
        if (strlen($password) >= 8) {
            $score++;
        }
        // Check for uppercase letters
        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        }
        // Check for lowercase letters
        if (preg_match('/[a-z]/', $password)) {
            $score++;
        }
        // Check for digits
        if (preg_match('/\d/', $password)) {
            $score++;
        }
        // Check for special characters
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score++;
        }
        // Return strength level
        if ($score >= 5) {
            return '<span class="badge badge-pill badge-success">Strong</span>';
        } elseif ($score >= 3) {
            return '<span class="badge badge-pill badge-info">Medium</span>';
        } else {
            return '<span class="badge badge-pill badge-danger">Weak</span>';
        }
    }
    //Logout : PP
    public function logout($is_expired = false)
    {
        if ($is_expired) {
            $this->session->set_flashdata('error', 'Session expired.');
        } else {
            $this->session->set_flashdata('info', 'Logged Out Success.');
        }

        $this->session->unset_userdata(SESSION_HANDLER);
        if ($this->input->is_ajax_request()) {
            // exit('No direct script access allowed');
            echo json_encode(['status' => 'ERROR', 'message' => 'Session expired.', 'hooks' => true]);
            die();
        }
        redirect(base_url() . 'login');
    }


    //Set user session : PP
    public function setUser($user_details = array())
    {
        if (!empty($user_details)) {
            $token = $this->generateAccessToken();
            $update_data = array();
            $update_data['id'] = $user_details['id'];
            $update_data['access_token'] = $token;
            $update_data['last_login'] = strtotime("now");
            $update_data['ip_address'] = $_SERVER['REMOTE_ADDR'];

            if ($this->my_users->saveData($update_data)) {
                $user_details['access_token'] = $token;
                $this->session->set_userdata(SESSION_HANDLER, $user_details);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //Generate access token : PP
    public function generateAccessToken()
    {
        $token = bin2hex(random_bytes(64));
        return $token;
    }
    //Return login user : PP
    public function getUser()
    {
    }
}
