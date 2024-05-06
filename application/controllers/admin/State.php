<?php

/**
 * State Controller
 *
 * @category   Controllers
 * @package    Admin
 * @subpackage State
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    24 JAN 2024
 *
 * Class State handles all the operations related to displaying list, creating State, update, and delete.
 */

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class State extends CI_Controller
{
    /**
     * Constructor
     *
     * Loads necessary libraries, helpers, and models for the State controller.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/State_model", "", true);
		$this->load->library('Utility');

    }
		/*** Get list of state */
		public function getStateList()
		{
			$data = json_decode(file_get_contents('php://input'));
			 
			if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
				$data->status = 'ok';
				echo json_encode($data);
				exit;
			}
			if($data)
			{
				

			$headers = apache_request_headers();	
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
			$tokenSession = Utility::tokenSession($userData);
	
			$columns = array(
				0 => 'country',
				1 => 'id',
			);
			$limit = $data->length;
			$start = ($data->draw - 1) * $limit;
			$orderColumn = $columns[$data->order[0]->column];
			$orderDir = $data->order[0]->dir;
			$totalData = $this->State_model->countAllState();
			$totalFiltered = $totalData;
			if (!empty($data->search->value)) {

				$search = $data->search->value;
				$totalFiltered = $this->State_model->countFilteredState($search);
				$state = $this->State_model->getFilteredState($search, $start, $limit, $orderColumn, $orderDir);
	
			   } else {
				$state = $this->State_model->getAllState($start, $limit, $orderColumn, $orderDir);
			}
	
			$datas = array();
			foreach ($state as $st) {
			   
				$nestedData = array();
				$nestedData['id'] = $st->id;
				$nestedData['state'] = $st->statename;
				$nestedData['country'] = $st->country;
				
				$datas[] = $nestedData;
			}
	
			$json_data = array(
				'draw' => intval($data->draw),
				'recordsTotal' => intval($totalData),
				'recordsFiltered' => intval($totalFiltered),
				'data' => $datas
			);
			
			echo json_encode($json_data);
		}
		else{
		   $response["response_code"] = "500";
		   $response["response_message"] = "Data is null";
		   echo json_encode($response);
		   exit();
		}
	   
		}

		/**
     * get the details of state using country id.
     */
    public function getStateDetailsByCntId()
    {
        $data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if($data)
		{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);
            $CountryId = $data->CountryId;
            $result = $this->State_model->getStateDetailsByCntId($CountryId);
            if ($result) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["response_data"] = $result;
            } else {
                $response["response_code"] = "400";
                $response["response_message"] = "Failed";
            }
        } else {
            $response["response_code"] = "500";
            $response["response_message"] = "Data is null";
        }
        echo json_encode($response);
        exit();
    }

	/*** insert details of state */
	public function insertStateDetails()
	{
		$data = json_decode(file_get_contents('php://input'));
		 
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data->status = 'ok';
			echo json_encode($data);
			exit;
		}

		$headers = apache_request_headers();	
		$token = str_replace("Bearer ", "", $headers['Authorization']);
		$kunci = $this->config->item('jwt_key');
		$userData = JWT::decode($token, $kunci);
		Utility::validateSession($userData->iat,$userData->exp);
        $tokenSession = Utility::tokenSession($userData);
		if($data)
		{
		$stateName = $data->stateName;
		$countryid = $data->countryId;
		$view_in_menu = $data->view_in_menu;
		$str = preg_replace('/[^A-Za-z0-9\. -]/', ' ', $stateName);
		$slug = preg_replace('/\s+/', '-', strtolower($str));
		$Arr = ['stateName'=>$stateName,'countryid'=>$countryid,'view_in_menu'=>$view_in_menu,'slug'=>$slug];
		$chkIfExists = $this->State_model->chkIfExists($stateName,$countryid);
		if($chkIfExists > 0)
		{
			$response["response_code"] = 300;
			$response["response_message"] = 'state is already exists.Please try another one.';
		}
		else
		{
			$result = $this->State_model->insertStateDetails($Arr);
			if ($result) {
				$response["response_code"] = "200";
				$response["response_message"] = "Success";
				$response["response_data"] = $result;
			} else {
				$response["response_code"] = "400";
				$response["response_message"] = "Failed";
			}
		}
		}
		else{
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			echo json_encode($response);
			exit();
		 }
		echo json_encode($response);exit;
	}

	/*** update details of state */
	public function updateStateDetails()
	{
		$data = json_decode(file_get_contents('php://input'));
		 
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data->status = 'ok';
			echo json_encode($data);
			exit;
		}

		$headers = apache_request_headers();	
		$token = str_replace("Bearer ", "", $headers['Authorization']);
		$kunci = $this->config->item('jwt_key');
		$userData = JWT::decode($token, $kunci);
		Utility::validateSession($userData->iat,$userData->exp);
        $tokenSession = Utility::tokenSession($userData);
		if($data)
		{
		$id = $data->id;
		$stateName = $data->stateName;
		$countryid = $data->countryId;
		$view_in_menu = $data->view_in_menu;
		$str = preg_replace('/[^A-Za-z0-9\. -]/', ' ', $stateName);
		$slug = preg_replace('/\s+/', '-', strtolower($str));
		$Arr = ['stateName'=>$stateName,'countryid'=>$countryid,'view_in_menu'=>$view_in_menu,'slug'=>$slug];
		$chkIfExists = $this->State_model->chkWhileUpdate($id,$stateName,$countryid);
		if($chkIfExists > 0)
		{
			$response["response_code"] = 300;
			$response["response_message"] = 'state is already exists.Please try another one.';
		}
		else
		{
			$result = $this->State_model->updateStateDetails($id,$Arr);
			if ($result) {
				$response["response_code"] = "200";
				$response["response_message"] = "Success";
				$response["response_data"] = $result;
			} else {
				$response["response_code"] = "400";
				$response["response_message"] = "Failed";
			}
		}
		}
		else{
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			echo json_encode($response);
			exit();
		 }
		echo json_encode($response);exit;
	}

	/**
     * get the details of state using state id.
     */
    public function getStateDetailsById()
    {
        $data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if($data)
		{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);
            $Id = $data->id;
            $result = $this->State_model->getStateDetailsById($Id);
            if ($result) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["response_data"] = $result;
            } else {
                $response["response_code"] = "400";
                $response["response_message"] = "Failed";
            }
        } else {
            $response["response_code"] = "500";
            $response["response_message"] = "Data is null";
        }
        echo json_encode($response);
        exit();
    }

	/**
     * delete the details of state using state id.
     */

	 public function deleteState()
	 {
		 $data = json_decode(file_get_contents('php://input'));
		 if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		 {
			 $data['status']='ok';
			 echo json_encode($data);exit;
		 }
		 if($data)
		 {
			 $headers = apache_request_headers();
			 $token = str_replace("Bearer ", "", $headers['Authorization']);
			 $kunci = $this->config->item('jwt_key');
			 $userData = JWT::decode($token, $kunci);
			 Utility::validateSession($userData->iat,$userData->exp);
			 $tokenSession = Utility::tokenSession($userData);
 
			 $Id = $data->id;
			 $result = $this->State_model->deleteState($Id);
			 if ($result) {
				 $response["response_code"] = "200";
				 $response["response_message"] = "Success";
				 $response["response_data"] = $result;
			 } else {
				 $response["response_code"] = "400";
				 $response["response_message"] = "Failed";
			 }
		 } else {
			 $response["response_code"] = "500";
			 $response["response_message"] = "Data is null";
		 }
		 echo json_encode($response);
		 exit();
	 }
}
