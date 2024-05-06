<?php

/**
 * Common Controller
 *
 * @category   Controllers
 * @package    Admin
 * @subpackage Common
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    26 JAN 2024
 *
 * Class Common handles all the operations related to displaying list, creating Common, update, and delete.
 */

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Common extends CI_Controller
{
    /**
     * Constructor
     *
     * Loads necessary libraries, helpers, and models for the Common controller.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/Common_model", "", true);
		//$this->load->model("web/Common_model", "", true);

		$this->load->library('Utility');

    }
	public function saveDoc()
	 {
		 $data = json_decode(file_get_contents('php://input'));
		 if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		 {
			 $data['status']='ok';
			 echo json_encode($data);exit;
		 }
		 if($data)
		 {
			if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
				$response["response_code"] = "401";
				$response["response_message"] = "Unauthorized";
				echo json_encode($response);
				exit();
			}
			 $headers = apache_request_headers();
			 $token = str_replace("Bearer ", "", $headers['Authorization']);
			 $kunci = $this->config->item('jwt_key');
			 $userData = JWT::decode($token, $kunci);
			 Utility::validateSession($userData->iat,$userData->exp);
			 $tokenSession = Utility::tokenSession($userData);
			 $postId = $data->postId;
             $type = $data->type;
             $images = $data->image;
            $resultArray = array();
			foreach ($images as $image) {
                if (!empty($image->id)) {
                    $updateArr = ['postid' => $postId, 'type' => $type, 'image' => $image->imageName];
                    $this->Common_model->updateDocsDetails($image->id,$postId, $updateArr);
					 $resultArray['imageId'] = $image->id;
                } else {
                    $insertArr = ['postid' => $postId, 'type' => $type, 'image' => $image->imageName];
                    $result = $this->Common_model->insertDocsDetails($insertArr);
                    if ($result) {
                        $resultArray['imageId'] = $result;
                    }
                }
            }
			 if (!empty($resultArray)) {
				 $response["response_code"] = "200";
				 $response["response_message"] = "Success";
				 $response["response_data"] = $resultArray;
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


	 public function getPageCategory()
	 {
		$data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);

			$type = 'college';
			$result = $this->Common_model->getPageCategory($type);
			if ($result) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["response_data"] = $result;
            } else {
                $response["response_code"] = "400";
                $response["response_message"] = "Failed";
            }

			echo json_encode($response);exit;
	 }

	 public function saveCounselingFees()
	 {
		$data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
		if($data){
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);

			$sub_category = $data->sub_category;
			$categoryid = $data->categoryid;
			$collegeType = $data->collegeType;
			$exam_id = $data->exam_id;
			$fees = $data->fees;

			$Arr = ['sub_category'=>$sub_category,'college_type'=>$collegeType,'category'=>$categoryid,'fees'=>$fees,'exam_id'=>$exam_id];
			$chkIsExists = $this->Common_model->chkIsExists($sub_category,$collegeType,$categoryid);
			if($chkIsExists > 0) {
				$response["response_code"] = "301";
                $response["response_message"] = "The combination of counselling already exists. Please try using different data.";
				echo json_encode($response);exit;

			}
			else
			{
			$result = $this->Common_model->saveCounselingFees($Arr);
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
		else
		{
			$response['status'] = 'false';
			$response['response_code'] = 3;
			$response['response_message'] = "please Upload the image";
		}
			echo json_encode($response);exit;
	 }

	 public function updateCounselingFees()
	 {
		$data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
		if($data){
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);

			$sub_category = $data->sub_category;
			$categoryid = $data->categoryid;
			$collegeType = $data->collegeType;
			$exam_id = $data->exam_id;

			$fees = $data->fees;
			$id = $data->id;
			$Arr = ['sub_category'=>$sub_category,'college_type'=>$collegeType,'category'=>$categoryid,'fees'=>$fees,'exam_id'=>$exam_id];
			$chkIsExists = $this->Common_model->chkIsExistsWhileUpdate($sub_category,$collegeType,$categoryid,$id);
			if($chkIsExists > 0) {
				$response["response_code"] = "301";
                $response["response_message"] = "The combination of counselling already exists. Please try using different data.";
				echo json_encode($response);exit;

			}
			$result = $this->Common_model->updateCounselingFees($Arr,$id);
			if ($result) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["response_data"] = $result;
            } else {
                $response["response_code"] = "400";
                $response["response_message"] = "Failed";
            }

		}
		else
		{
			$response['status'] = 'false';
			$response['response_code'] = 3;
			$response['response_message'] = "please Upload the image";
		}
			echo json_encode($response);exit;
	 }

	 public function deleteCounselingFees()
	 {
		$data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
		if($data)
		{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);

			$id = $data->id;
			$result = $this->Common_model->deleteCounselingFees($id);
			if ($result) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["response_data"] = $result;
            } else {
                $response["response_code"] = "400";
                $response["response_message"] = "Failed";
            }
		}
		else
		{
			$response['status'] = 'false';
			$response['response_code'] = 3;
			$response['response_message'] = "please Upload the image";
		}

			echo json_encode($response);exit;
	 }


	 /**
     	* get server side datatable data of counseling fees.
     	*/
		 public function getCounselingFeesList()
		 {
			 $data = json_decode(file_get_contents('php://input'));
		 
			 if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
				 $data->status = 'ok';
				 echo json_encode($data);
				 exit;
			 }
		 
			 if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
				$response["response_code"] = "401";
				$response["response_message"] = "Unauthorized";
				echo json_encode($response);
				exit();
			}
			 if ($data) {
			
				$headers = apache_request_headers();
					
				$token = str_replace("Bearer ", "", $headers['Authorization']);
				$kunci = $this->config->item('jwt_key');
				$userData = JWT::decode($token, $kunci);
				Utility::validateSession($userData->iat,$userData->exp);
        		$tokenSession = Utility::tokenSession($userData);
				
				 $columns = array(
					 0 => 'id',
					 1 => 'subCategoryName',
				 );
				 $limit = $data->length;
				 $start = ($data->draw - 1) * $limit;
				 $orderColumn = $columns[$data->order[0]->column];
				 $orderDir = $data->order[0]->dir;
				 $totalData = $this->Common_model->countAllCounselingFees();
				 
				 $totalFiltered = $totalData;
		 
				 if (!empty($data->search->value)) {
					 $search = $data->search->value;
					 $totalFiltered = $this->Common_model->countFilteredCounselingFees($search);
					 $CounselingFees = $this->Common_model->getFilteredCounselingFees($search, $start, $limit, $orderColumn, $orderDir);

					} else {
					 $CounselingFees = $this->Common_model->getAllCounselingFees($start, $limit, $orderColumn, $orderDir);
				 }
		 
				 $datas = array();
				 foreach ($CounselingFees as $cnf) {
					
					 $nestedData = array();
					 $nestedData['id'] = $cnf->id;
					 $nestedData['category'] = $cnf->categoryName;  
					 $nestedData['sub_category'] = $cnf->subCategoryName;
					 $nestedData['college_type'] = $cnf->collegeTypeName; 
					 $nestedData['fees'] = $cnf->fees; 
					 $nestedData['exam_name'] = $cnf->exam_name;

					

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

		 public function getCounselingFeesById()
		 {
			$data = json_decode(file_get_contents('php://input'));
			if($this->input->server('REQUEST_METHOD')=='OPTIONS')
			{
				$data['status']='ok';
				echo json_encode($data);exit;
			}
			if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
				$response["response_code"] = "401";
				$response["response_message"] = "Unauthorized";
				echo json_encode($response);
				exit();
			}
			if($data)
			{
				$headers = apache_request_headers();
				$token = str_replace("Bearer ", "", $headers['Authorization']);
				$kunci = $this->config->item('jwt_key');
				$userData = JWT::decode($token, $kunci);
				Utility::validateSession($userData->iat,$userData->exp);
				$tokenSession = Utility::tokenSession($userData);

				$id = $data->id;
				$result = $this->Common_model->getCounselingFeesById($id);
				if ($result) {
					$response["response_code"] = "200";
					$response["response_message"] = "Success";
					$response["response_data"] = $result;
				} else {
					$response["response_code"] = "400";
					$response["response_message"] = "Failed";
				}
			}
			else
			{
				$response['status'] = 'false';
				$response['response_code'] = 3;
				$response['response_message'] = "please Upload the image";
			}

				echo json_encode($response);exit;
		 }


		 public function getUserActivity()
		 {
			$data = json_decode(file_get_contents('php://input'));
			if($this->input->server('REQUEST_METHOD')=='OPTIONS')
			{
				$data['status']='ok';
				echo json_encode($data);exit;
			}
			if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
				$response["response_code"] = "401";
				$response["response_message"] = "Unauthorized";
				echo json_encode($response);
				exit();
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
					0 =>'created_date',
					1 => 'user_type',
					2 => 'username',
					3 => 'no_of_exams_added',
					4 => 'no_of_artcles_added',


				);
		
				$limit = $data->length;
				$start = ($data->draw - 1) * $limit;
				$orderColumn = $columns[$data->order[0]->column];
				$orderDir = $data->order[0]->dir;
				$totalData = $this->Common_model->countAllUserActivity();
				$totalFiltered = $totalData;
				if (!empty($data->search->value)) {
					$search = $data->search->value;
					$totalFiltered = $this->Common_model->countFilteredUserActivity($search);
					$useractivity = $this->Common_model->getFilteredUserActivity($search, $start, $limit, $orderColumn, $orderDir);

				   } else {
					$useractivity = $this->Common_model->getUserActivity($start, $limit, $orderColumn, $orderDir);
				}
				$datas = array();
				foreach ($useractivity as $clg) {
				   
					$nestedData = array();
					$nestedData['id'] = $clg->id;
					$nestedData['user_name'] = $clg->fullname;  
					$nestedData['user_id'] = $clg->user_name;  
					$nestedData['email'] = $clg->email; 
					$nestedData['location'] = $clg->location; 
					$nestedData['latest_activity'] = $clg->latest_activity;
					$nestedData['created_date'] = $clg->created_date;
					$nestedData['updated_date'] = $clg->updated_date;

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

		 public function getTeamReport()
		 {
			$data = json_decode(file_get_contents('php://input'));
			if($this->input->server('REQUEST_METHOD')=='OPTIONS')
			{
				$data['status']='ok';
				echo json_encode($data);exit;
			}
			if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
				$response["response_code"] = "401";
				$response["response_message"] = "Unauthorized";
				echo json_encode($response);
				exit();
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
					0 =>'id',
					1 => 'user_type',
					2 => 'username',
					3 => 'no_of_exams_added',
					4 => 'no_of_artcles_added',


				);
		
				$limit = $data->length;
				$start = ($data->draw - 1) * $limit;
				$orderColumn = $columns[$data->order[0]->column];
				$orderDir = $data->order[0]->dir;
				$totalData = $this->Common_model->countAllTeamReport();
				$totalFiltered = $totalData;
				if (!empty($data->search->value) || !empty($data->search->usertype) || !empty($data->search->fromdate) || !empty($data->search->todate) ) {
					$search = $data->search->value;
					$usertype = isset($data->search->usertype) ? $data->search->usertype :'';
					$fromdate = isset($data->search->fromdate) ? $data->search->fromdate :'';
					$todate = isset($data->search->todate) ? $data->search->todate :'';

					$totalFiltered = $this->Common_model->countFilteredTeamReport($search,$usertype,$fromdate,$todate);
					$teamreplist = $this->Common_model->getFilteredTeamReport($search,$usertype,$fromdate,$todate,$start, $limit, $orderColumn, $orderDir);

				   } else {
					$teamreplist = $this->Common_model->getTeamReport($start, $limit, $orderColumn, $orderDir);
				}
				$datas = array();
				foreach ($teamreplist as $clg) {
				   
					$nestedData = array();
					$nestedData['id'] = $clg->id;
					$nestedData['userid'] = $clg->userid;  
					$nestedData['usertype'] = $clg->usertype; 
					$nestedData['no_of_colleges_added'] = $clg->no_of_colleges_added; 
					$nestedData['no_of_exams_added'] = $clg->no_of_exams_added;
					$nestedData['no_of_events_added'] = $clg->no_of_events_added;
					$nestedData['no_of_artcles_added'] = $clg->no_of_articles_added;
					$nestedData['created_date'] = $clg->created_date;
					$nestedData['username'] = $clg->username;
					$nestedData['updated_date'] = $clg->updated_date;
					$nestedData['user_type'] = $clg->user_type;



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

		 public function getCollegeReport()
		 {
			$data = json_decode(file_get_contents('php://input'));
			if($this->input->server('REQUEST_METHOD')=='OPTIONS')
			{
				$data['status']='ok';
				echo json_encode($data);exit;
			}
			//print_r($_SERVER);exit;
			if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
				$response["response_code"] = "401";
				$response["response_message"] = "Unauthorized";
				echo json_encode($response);
				exit();
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
					0 =>'id',
					1 => 'college',
					2 => 'no_of_articles_linked',
					3 => 'no_of_brochures_download',
					4 => 'no_of_application_submitted',


				);
		
				$limit = $data->length;
				$start = ($data->draw - 1) * $limit;
				$orderColumn = $columns[$data->order[0]->column];
				$orderDir = $data->order[0]->dir;
				$totalData = $this->Common_model->countAllCollegeReport();
				$totalFiltered = $totalData;
				if (!empty($data->search->value)) {
					$search = $data->search->value;
					$totalFiltered = $this->Common_model->countFilteredCollegeReport($search);
					$clgreplist = $this->Common_model->getFilteredCollegeReport($search, $start, $limit, $orderColumn, $orderDir);

				   } else {
					$clgreplist = $this->Common_model->getCollegeReport($start, $limit, $orderColumn, $orderDir);
				}
				

				$datas = array();
				foreach ($clgreplist as $clg) {
				   
					$nestedData = array();
					$nestedData['id'] = $clg->id;
					$nestedData['college'] = $clg->college;  
					$nestedData['no_of_articles_linked'] = $clg->no_of_articles_linked; 
					$nestedData['no_of_brochures_download'] = $clg->no_of_brochures_download; 
					$nestedData['no_of_application_submitted'] = $clg->no_of_application_submitted;
					$nestedData['no_of_que_asked'] = $clg->no_of_que_asked;
					$nestedData['no_of_answeres'] = $clg->no_of_answeres;
					$nestedData['no_of_views'] = $clg->no_of_views;
					$nestedData['created_date'] = $clg->created_date;
					$nestedData['updated_date'] = $clg->updated_date;
					$nestedData['collegename'] = $clg->collegename;
					$nestedData['city'] = $clg->city;
					$nestedData['statename'] = $clg->statename;


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

		 public function viewUserActivity()
    {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
        if($data)
		{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
			$tokenSession = Utility::tokenSession($userData);
        	$userid = $data->userid;
        	$viewUserActivity = $this->Common_model->viewUserActivity($userid);

        if ($viewUserActivity) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["viewUserActivity"] = $viewUserActivity;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
    }
    else
    {
        $response["response_code"] = "500";
         $response["response_message"] = "Data is null";
    }
        echo json_encode($response);
        exit;
    }


	public function notificationCount()
    {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
        // if($data)
		// {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
			$tokenSession = Utility::tokenSession($userData);
        	$userid = $userData->data->userId;
        	$EnqnotificationCount = $this->Common_model->EnqnotificationCount($userid);
        	$ApplicationNotificationCount = $this->Common_model->ApplicationNotificationCount($userid);
        	$CourseEnquiryNotificationCount = $this->Common_model->CourseEnquiryNotificationCount($userid);
			$PredictionNotificationCount = $this->Common_model->PredictionNotificationCount($userid);
			$ReviewNotificationCount = $this->Common_model->ReviewNotificationCount($userid);
			$QuestionNotificationCount = $this->Common_model->QuestionNotificationCount($userid);

        	if (true) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
			$response ['response_data'] = array(
				 $EnqnotificationCount,
				$ApplicationNotificationCount,
				$CourseEnquiryNotificationCount,
				$PredictionNotificationCount,
				$QuestionNotificationCount,
				 $ReviewNotificationCount
			);

        	} else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
    // }
    // else
    // {
    //     $response["response_code"] = "500";
    //      $response["response_message"] = "Data is null";
    // }
        echo json_encode($response);
        exit;
    }

	public function updateLogStatus()
	{
		$data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
        if($data)
		{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
			$tokenSession = Utility::tokenSession($userData);
        	$logid = $data->logid;
			$type = $data->type;
			$seen_by = $userData->data->userId;
			$getdatabylogid = $this->Common_model->getdatabylogid($logid,$type);
			// print_r($getdatabylogid);
			if(empty($getdatabylogid[0]->seen_by)){
				$Arr  = ['seen_by'=>$seen_by,'status'=>1];
				$updateLogStatus = $this->Common_model->updateLogStatus($Arr,$logid,$type);
			}
			else
			{
				$explodeArr = explode(",", $getdatabylogid[0]->seen_by);
				if (!in_array($seen_by, $explodeArr)) {
					$explodeArr[] = $seen_by; 
				}
				$explodeArr = array_unique($explodeArr);
				$implodeArr = implode(',', $explodeArr);
				$Arr  = ['seen_by'=>$implodeArr,'status'=>1];
				$updateLogStatus = $this->Common_model->updateLogStatus($Arr,$logid,$type);
			}
        	// 

        if ($updateLogStatus) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["updateLogStatus"] = $updateLogStatus;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
    }
    else
    {
        $response["response_code"] = "500";
         $response["response_message"] = "Data is null";
    }
        echo json_encode($response);
        exit;
	}

	public function getContentCategory()
	{
		$data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
        // if($data)
		// {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
			$tokenSession = Utility::tokenSession($userData);
			$result = $this->Common_model->getContentCategory();

        if ($result) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["contentCategory"] = $result;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
    // }
    // else
    // {
    //     $response["response_code"] = "500";
    //      $response["response_message"] = "Data is null";
    // }
        echo json_encode($response);
        exit;
	}


	public function saveTblOfContent()
	{
		$data = json_decode(file_get_contents("php://input"));
		if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
			$data["status"] = "ok";
			echo json_encode($data);
			exit();
		}
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
		if ($data) {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$collegeid = $data->collegeid;
			$type = 'college';
			$titleids = explode(',', $data->titleids);
			
			// Get the existing titleids for the given collegeid
			$existingTitles = $this->Common_model->getTitlesByCollegeId($collegeid);

			// Convert existing titles to an associative array for easier checking
			$existingTitles = array_flip($existingTitles);

			foreach ($titleids as $titleid) {
				// Check if the titleid already exists for the given collegeid
				if (empty($existingTitles[$titleid])) {
					// If it doesn't exist, save a new row
					$result = $this->Common_model->saveTblOfContent(['title' => $titleid, 'type' => $type, 'college_id' => $collegeid]);
				} else {
					// If it exists, remove it from the existing titles list
					unset($existingTitles[$titleid]);
				}
			}

			// Remove any remaining titles in the existingTitles list (titles that were not in the new list)
			if (!empty($existingTitles)) {
				$titlesToDelete = array_keys($existingTitles);
				$result1 = $this->Common_model->deleteTitles($titlesToDelete, $collegeid);
			}

			if (true) {
				$response["response_code"] = "200";
				$response["response_message"] = "Success";
				// $response["response_data"] = $result;
			} else {
				$response["response_code"] = "400";
				$response["response_message"] = "Failed";
			}
		} else {
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
		}
		echo json_encode($response);
		exit;
	}


	public function getTblOfContentById()
	{
		$data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
        if($data)
		{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
			$collegeid = $data->collegeid;
			$type = 'college';
			$result = $this->Common_model->getTblOfContentById(['type' => $type, 'college_id' => $collegeid]);

        if ($result) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["contentCategory"] = $result;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
    }
    else
    {
        $response["response_code"] = "500";
         $response["response_message"] = "Data is null";
    }
        echo json_encode($response);
        exit;
	}

	public function getyear()
	 {
		$data = json_decode(file_get_contents('php://input'));
		if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		{
			$data['status']='ok';
			echo json_encode($data);exit;
		}
		if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);

			$searchyear = isset($data->searchyear)?$data->searchyear:'';
			$result = $this->Common_model->getyear($searchyear);
			if ($result) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["response_data"] = $result;
            } else {
                $response["response_code"] = "400";
                $response["response_message"] = "Failed";
            }

			echo json_encode($response);exit;
	 }

	}
