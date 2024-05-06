<?php

class User_model extends CI_Model
{
    private $table = 'college';

    public function __construct()
    {

        parent::__construct(); {
            $this->load->database();
        }
    }
    public function checkUserExist($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            return true; 
        } else {
            return false;
        }
		//echo $this->db->last_query();exit;
    }

    public function createUser($email, $password, $userName)
    {
        $userData = array(
            'email' => $email,
            'password' => $password,
            'f_name' => $userName,
            'user_type' => 2,
            'user_status' => 2
        );

        $this->db->insert('users', $userData);
        //echo $this->db->last_query();        exit;
        return $this->db->insert_id();
    }

    public function getUserDetailsById($id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id',$id);
        $query = $this->db->get()->result();
        return $query;
    }

    public function updateOTP($Arr, $id)
    {
        $this->db->where("id", $id);
        $query = $this->db->update('users', $Arr);
        return $query;
    }

    public function getOtpdata($email)
    {
        $this->db->select('id,email,otp,otp_timestamp');
        $this->db->from('users');
        $this->db->where('email',$email);
        $query = $this->db->get()->result();
        return $query;
    }
}
