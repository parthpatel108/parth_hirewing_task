<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // Login function
    public function login()
    {
        // If user is already authenticated, redirect to home
        if ($this->authenticateSession()) {
            redirect(base_url('home'));
        }

        $viewData = array();
        $viewData['title'] = 'Login';
        $viewData['username'] = "";

        // Handle form submission
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $viewData['username'] = $username;
            $conditions = array(
                'username' => $username,
                'is_deleted' => 0,
                'is_active' => 1
            );
            $login_details = $this->my_users->get($conditions);

            // Check if user exists
            if (!empty($login_details)) {
                // Check if account is locked
                if ($login_details['locked'] == 1) {
                    $this->session->set_flashdata('error', 'Your account is locked due to failed login attempts. <a href="#">Contact Administrator</a>');
                    redirect(base_url() . 'login');
                }
                // Verify password
                if (password_verify($password, $login_details['password'])) {
                    $user_details = array(
                        'username' => $username,
                        'fullname' => $login_details['first_name'] . " " . $login_details['last_name'],
                        'first_name' => $login_details['first_name'],
                        'last_name' => $login_details['last_name'],
                        'email' => $login_details['email'],
                        'id' => $login_details['id'],
                        'role_id' => $login_details['role_id'],
                        'is_admin' => $login_details['role_id'] == 1 ? 1 : 0
                    );
                    // Set user session
                    if ($this->setUser($user_details)) {
                        $this->session->set_flashdata('success', 'Login Success.');
                        redirect(base_url('home'));
                    } else {
                        $this->session->set_flashdata('error', 'Something went wrong, Please try again later.');
                        redirect(base_url() . 'login');
                    }
                } else {
                    // Handle failed login attempts
                    $this->handleFailedLogin($username);
                    $this->session->set_flashdata('error', 'Password is incorrect.');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid username.');
            }
        }

        $this->load->view('auth/login', $viewData);
    }

    // Authenticate user session
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

    // Register function
    public function register()
    {
        $view_data = array();
        $view_data['title'] = 'Register';
        $this->load->view('auth/register', $view_data);
    }

    // Save user details
    public function save_user_details()
    {
        $request = $this->input->post();
        // Check if username and email already exist
        $condition = array('username' => trim($request['username']));
        if (!empty($this->my_users->get($condition))) {
            echo json_encode(['status' => 'ERROR', 'message' => 'User details already registered with this username.']);
            die;
        }

        $condition = array('email' => trim($request['email']));
        if (!empty($this->my_users->get($condition))) {
            echo json_encode(['status' => 'ERROR', 'message' => 'User details already registered with this email.']);
            die;
        }

        $data = array(
            'first_name' => htmlentities($request['first_name']),
            'last_name' => htmlentities($request['last_name']),
            'email' => htmlentities($request['email']),
            'username' => htmlentities($request['username']),
            'password' => password_hash($request['password'], PASSWORD_DEFAULT),
            'role_id' => 3
        );

        if (!empty($this->my_users->saveData($data))) {
            echo json_encode(['status' => 'SUCCESS', 'message' => 'User details registered successfully.']);
        } else {
            echo json_encode(['status' => 'ERROR', 'message' => 'User details failed to register in the system.']);
        }
    }

    // Handle failed login attempts
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

    // Password strength check
    public function wing_password_helth()
    {
        $request = $this->input->post();
        if (!empty($request['password'])) {
            $password = $_POST['password'];
            $strength = $this->check_password_health($password);
            echo $strength;
        }
    }

    // Check password strength
    public function check_password_health($password)
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

    // Logout function
    public function logout($is_expired = false)
    {
        if ($is_expired) {
            $this->session->set_flashdata('error', 'Session expired.');
        } else {
            $this->session->set_flashdata('info', 'Logged Out Success.');
        }

        $this->session->unset_userdata(SESSION_HANDLER);
        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'ERROR', 'message' => 'Session expired.', 'hooks' => true]);
            die();
        }
        redirect(base_url() . 'login');
    }

    // Set user session
    public function setUser($user_details = array())
    {
        if (!empty($user_details)) {
            $token = $this->generateAccessToken();
            $update_data = array(
                'id' => $user_details['id'],
                'access_token' => $token,
                'last_login' => strtotime("now"),
                'ip_address' => $_SERVER['REMOTE_ADDR']
            );

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

    // Generate access token
    public function generateAccessToken()
    {
        $token = bin2hex(random_bytes(64));
        return $token;
    }
}
