<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_users extends CI_Model
{
  private $table_name = 'users';
  public function  __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->dbforge();
  }

  function get($condition = array())
  {
    $this->db->select('*');
    $this->db->from($this->table_name);
    $this->db->where($condition);
    $this->db->limit(1);
    $details = $this->db->get()->result();
    if (!empty($details)) {
      return (array)$details[0];
    } else {
      return array();
    }
  }


  function getAll($condition = array())
  {
    $this->db->select('*');
    $this->db->from($this->table_name);
    $this->db->where($condition);
    $details = $this->db->get()->result();
    if (!empty($details)) {
      return (array)$details;
    } else {
      return array();
    }
  }


  //Save and update common function : PP
  public function saveData($data)
  {
    $response = array();
    if (isset($data['id']) && $data['id'] > 0) {
      $data['updated_at'] = date('Y-m-d H:i:s');
      $update = $this->db->update($this->table_name, $data, array('id' => $data['id']));
      if ($update) {
        $response = $this->get(array('id' => $data['id']));
      }
    } else {
      $data['created_at'] = date('Y-m-d H:i:s');
      $data['updated_at'] = date('Y-m-d H:i:s');
      $insert = $this->db->insert($this->table_name, $data);
      if ($insert) {
        $id = $this->db->insert_id();
        if ($id > 0) {
          $response = $this->get(array('id' => $id));
        }
      }
    }
    return $response;
  }

  //Authenticate access token : PP
  function authToken($id = null, $access_token = null)
  {
    $user_details = $this->get(array('id' => $id));
    if (!empty($user_details)) {
      if ($user_details['is_deleted'] == 1) {
        $this->session->set_flashdata('error', 'User Account is deleted.');
        return false;
      }
      if ($user_details['is_active'] == 0) {
        $this->session->set_flashdata('error', 'User Account is inactive.');
        return false;
      }
      if ($user_details['access_token'] == $access_token) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }


  public function delete($id = null)
  {
    $this->db->where('id', $id);
    if ($this->db->delete($this->table_name))
      return true;
    else
      return false;
  }

  // get dynamic record : Parth Patel
  public function graphqlQuery($params)
  {
    $fields = isset($params['fields']) ? $params['fields'] : '*';
    $where = isset($params['where']) ? $params['where'] : array();
    $group_by = isset($params['group_by']) ? $params['group_by'] : '';
    $limit = isset($params['limit']) ? $params['limit'] : null;
    $order_by = isset($params['order_by']) ? $params['order_by'] : '';

    $this->db->select($fields);
    $this->db->from($this->table_name);

    if (!empty($where)) {
      $this->db->where($where);
    }

    if (!empty($group_by)) {
      $this->db->group_by($group_by);
    }

    if (!empty($order_by)) {
      $this->db->order_by($order_by);
    }

    if (!is_null($limit)) {
      $this->db->limit($limit);
    }

    $query = $this->db->get()->result();
    return $query;
  }


  public function incrementFailedLoginAttempts($username)
  {
    $this->db->where('username', $username);
    $this->db->set('failed_attempts', 'failed_attempts + 1', FALSE);
    $this->db->update('users');
  }

  public function getFailedLoginAttempts($username)
  {
    $this->db->select('failed_attempts');
    $this->db->where('username', $username);
    $query = $this->db->get('users');
    $row = $query->row();
    return $row->failed_attempts;
  }

  public function lockAccount($username)
  {
    $this->db->where('username', $username);
    $this->db->update('users', array('locked' => 1));
  }
}
