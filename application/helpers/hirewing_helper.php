<?php
defined('BASEPATH') or exit('No direct script access allowed');


if (!function_exists('is_admin')) {
    function is_admin()
    {
        try {
            $CI = &get_instance();
            if (!empty($CI->session->userdata()) && !empty($CI->session->userdata[SESSION_HANDLER])) {
                $id = $CI->session->userdata[SESSION_HANDLER]['id'];
                $user_details = $CI->my_users->get(array('id' => $id));
                if (!empty($user_details)) {
                    if ($user_details['role_id'] == 1) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
        }
        return false;
    }
}


if (!function_exists('encrypt')) {
    function encrypt($string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = "KEY HIREWING EXP SRT";
        $secret_iv = "IV SRT HIREWING EXP";
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }
}

if (!function_exists('decrypt')) {
    function decrypt($string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = "KEY HIREWING EXP SRT";
        $secret_iv = "IV SRT HIREWING EXP";
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
}


if (!function_exists('format_role')) {
    function format_role($role_id = 0)
    {
        $response = '<span class="badge badge-pill badge-default">Unknown</span>';
        switch ($role_id) {
            case 1:
                $response = '<span class="badge badge-pill badge-primary">Administrator</span>';
                break;
            case 2:
                $response = '<span class="badge badge-pill badge-success">Teacher</span>';
                break;
            case 3:
                $response = '<span class="badge badge-pill badge-warning">Student</span>';
                break;
            case 4:
                $response = '<span class="badge badge-pill badge-danger">Parent</span>';
                break;
        }
        return $response;
    }
}
