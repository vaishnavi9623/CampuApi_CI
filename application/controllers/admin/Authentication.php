<?php
/**
 * Authentication Controller
 *
 * @category   Controllers
 * @package    Admin
 * @subpackage Authentication
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    08 JAN 2024
 * 
 * Class Authentication handles secure login methods for administrators.
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Authentication extends CI_Controller{

    /**
     * Constructor.
     * 
     * Loads necessary libraries, helpers, and models for the Authentication controller.
     */
    function __construct() {
        parent::__construct();
        
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('admin/auth_model', '', TRUE);
    }

    /**
     * Login Method
     * 
     * Handles the administrator login functionality.
     *
     * @access public
     * @return void
     */
    public function Login()
    {
        $data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if($data)
		{
			$username = $data->username;
			$password = md5($data->password);

			$result = $this->auth_model->verifyUser($username, $password);
			if($result)
			{
				$response['response_code'] = '200';
				$response['response_message'] = 'Success';
				$response['response_data'] = $result;
			}
			else
			{
				$response['response_code'] = '400';
				$response['response_message'] = 'Failed';
			}
		}
		else
		{
				$response['response_code'] = '500';
				$response['response_message'] = 'data is null';
		}

		echo json_encode($response);exit;

    }
}
