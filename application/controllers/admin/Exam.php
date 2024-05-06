<?php

/**
 * Exam Controller
 *
 * @category   Controllers
 * @package    Admin
 * @subpackage Exam
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    26 JAN 2024
 *
 * Class Exam handles all the operations related to displaying list, creating Exam, update, and delete.
 */
date_default_timezone_set('Asia/Kolkata');

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Exam extends CI_Controller
{
    /**
     * Constructor
     *
     * Loads necessary libraries, helpers, and models for the Exam controller.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/Exam_model", "", true);
		$this->load->model("admin/Common_model", "", true);
		$this->load->library('Utility');

    }

	/*** Get list of Exam */
	public function getExamList()
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
		$userId = $userData->data->userId;
		$userType = $userData->data->user_type;
		$columns = array(
			0 => 'title',
			1 => 'id',
			2 => 'category',
			3 => 'status',


		);
		$limit = $data->length;
		$start = ($data->draw - 1) * $limit;
		$orderColumn = $columns[$data->order[0]->column];
		$orderDir = $data->order[0]->dir;
		$totalData = $this->Exam_model->countAllExam($userId,$userType);
		$totalFiltered = $totalData;
		if (!empty($data->search->value)) {

			$search = $data->search->value;
			$totalFiltered = $this->Exam_model->countFilteredExam($search,$userId,$userType);
			$Exam = $this->Exam_model->getFilteredExam($search, $start, $limit, $orderColumn, $orderDir,$userId,$userType);

		   } else {
			$Exam = $this->Exam_model->getAllExam($start, $limit, $orderColumn, $orderDir,$userId,$userType);
		}

		$datas = array();
		foreach ($Exam as $e) {
		   
			$nestedData = array();
			$nestedData['id'] = $e->id;
			$nestedData['title'] = $e->title;
			$nestedData['category'] = $e->category;
			$nestedData['status'] = $e->status;
			$nestedData['created_by'] = $e->created_by;
			$nestedData['created_date'] = $e->create_date;
			$nestedData['updated_by'] = $e->updated_by;
			$nestedData['updated_date'] = $e->updated_date;
			$nestedData['created_by_name'] = $e->created_by_name;
			$nestedData['updated_by_name'] = $e->updated_by_name;

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

	/*** insert details of Exam */
	public function insertExamDetails()
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
		$created_by = $userData->data->userId;
		$userType = $userData->data->user_type;
		$create_date = date('Y-m-d H:i:s');
		$name = $data->name;
		$category = $data->category;
		$status = $data->status;
		$view_in_menu = $data->view_in_menu;
		$description = $data->description;
		$criteria = $data->criteria;
		$process = $data->process;
		$pattern = $data->pattern;
		$notification = $data->notification;
		$Arr = ['created_by'=>$created_by,'create_date'=>$create_date,'notification'=>$notification,'title'=>$name,'categoryid'=>$category,'status'=>$status,'view_in_menu'=>$view_in_menu,'description'=>$description,'criteria'=>$criteria,'process'=>$process,'pattern'=>$pattern];
		
		$chkIfExists = $this->Exam_model->chkIfExists($name);
		if($chkIfExists > 0)
		{
			$response["response_code"] = 300;
			$response["response_message"] = 'Exam is already exists.Please try another one.';
		}
		else
		{
			$result = $this->Exam_model->insertExamDetails($Arr);
			$checkTeamReport = $this->Common_model->checkTeamReport($created_by,$userType,$create_date);
			$TeamArr = ['userid'=>$created_by,'usertype'=>$userType,'no_of_colleges_added'=>0,'no_of_exams_added'=>1,'no_of_events_added'=>0,'no_of_articles_added'=>0,'updated_date'=>date('Y-m-d H:i:s')];
			if($checkTeamReport > 0)
			{
			 $updateTeamReport = $this->Common_model->updateTeamReport($created_by,$TeamArr,$create_date);

			}
			else
			{
			$saveTeamReport = $this->Common_model->saveTeamReport($TeamArr);
			}
			$id['id'] = $result ; 
			if ($result) {
				$response["response_code"] = "200";
				$response["response_message"] = "Success";
				$response["response_data"] = $id;
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

	/*** update details of Exam */
	public function updateExamDetails()
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
		$updated_by = $userData->data->userId;
		$updated_date = date('Y-m-d H:i:s');
		$id = $data->id;
		$name = $data->name;
		$category = $data->category;
		$status = $data->status;
		$view_in_menu = $data->view_in_menu;
		$description = $data->description;
		$criteria = $data->criteria;
		$process = $data->process;
		$pattern = $data->pattern;
		$notification = $data->notification;
		$Arr = ['updated_by'=>$updated_by,'updated_date'=>$updated_date,'notification'=>$notification,'title'=>$name,'categoryid'=>$category,'status'=>$status,'view_in_menu'=>$view_in_menu,'description'=>$description,'criteria'=>$criteria,'process'=>$process,'pattern'=>$pattern];
		
		$chkIfExists = $this->Exam_model->chkWhileUpdate($name,$id);
		if($chkIfExists > 0)
		{
			$response["response_code"] = 300;
			$response["response_message"] = 'Exam is already exists.Please try another one.';
		}
		else
		{
			$result = $this->Exam_model->updateExamDetails($id,$Arr);
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
     * get the details of Exam using Exam id.
     */
    public function getExamDetailsById()
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
            $result = $this->Exam_model->getExamDetailsById($Id);
			$result1 = $this->Exam_model->getExamImgDetailsById($Id);
			foreach ($result1 as $key => $img) {
				$result1[$key]->imageName = $img->image;

				$result1[$key]->image = base_url().'/uploads/exams/'.$img->image;

			}
			$result->questionpaperPath = base_url().'/uploads/questionpaper/'.$result->questionpaper;
			$result->preparationPath = base_url().'/uploads/preparation/'.$result->preparation;
			$result->syllabusPath = base_url().'/uploads/syllabus/'.$result->syllabus;

			
            if ($result) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["exam_data"] = $result;
				$response["image_data"] = $result1;

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
     * delete the details of Exam using Exam id.
     */

	 public function deleteExam()
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
			 $UserRole = $userData->data->type;
			 $userId = $userData->data->userId;
			if($UserRole=='Employee' && $Id != $userId)
			{
				$response["response_code"] = 300;
				$response["response_message"] = "Sorry, you do not have permission to modify another user's Exams.";
				echo json_encode($response);
        		exit();
			}
			 $result = $this->Exam_model->deleteExam($Id);
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
     * upload documents of Exam.
     */
    public function uploadDocs()
    {
        $data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS')
		{
		$data["status"] = "ok";
		echo json_encode($data);
		exit;
		}
		// print_r($_POST['type']);exit;
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);
		// $type = $data->type;
		$folder = ''; // Initialize $folder variable

		$allowed = []; // Initialize $allowed array
		$type = $_POST['type'];
		switch ($type) {
			case 'IMAGE':
				$folder = 'uploads/exams';
				$allowed = [
					"jpg" => "image/jpeg",
					"jpeg" => "image/jpeg",
					"png" => "image/png"
				];
				break;
			case 'QUESTIONPAPER':
				$folder = 'uploads/questionpaper';
				$allowed = [
					"pdf" => "application/pdf"
				];
				break;
			case 'SYLLABUS':
				$folder = 'uploads/syllabus';
				$allowed = [
					"pdf" => "application/pdf"
				];
				break;
			case 'PREPARATION':
				$folder = 'uploads/preparation';
				$allowed = [
					"pdf" => "application/pdf"
				];
				break;
			default:
				// Handle invalid $type
				break;
		}

		if(!is_dir($folder)) {
			mkdir($folder, 0777, TRUE);
			}
			if(isset($_FILES["file"]) && $_FILES["file"]["error"] == 0)
			{
			
				$filename = $_FILES["file"]["name"];
				$filesize = $_FILES["file"]["size"];
				$file_ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$maxsize = 6 * 1024 * 1024;
				if(!array_key_exists($file_ext, $allowed))
				{
				$response['status'] = 'false';
				$response['response_code'] = 1;
				$response['response_message'] = " Please select a valid file format.";
				}
				else if($filesize > $maxsize)
				{
				$response['status'] = 'false';
				$response['response_code'] = 2;
				$response['response_message'] = "File size is larger than the allowed limit";
				}
				else
				{
				$fileNameFinal = time()."_".$filename."";
				$finalFile = $folder."/". $fileNameFinal;
				$upload = move_uploaded_file($_FILES["file"]["tmp_name"], $finalFile);
				if($upload)
				{	
					$response['File'] = $fileNameFinal;
					$response['FileDir'] = base_url().$finalFile;
					$response["response_code"] = "200";
                	$response["response_message"] = "success";
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

		/**
     * delete the details of Exam using Exam id.
     */

	

	  /**
     * delete the details of event Doc using image id.
     */

	 public function deleteDoc()
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
 
			 $Id = $data->imageId;
			 $result = $this->Exam_model->deleteDoc($Id);
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

	 public function getExams()
	{
		$data = json_decode(file_get_contents('php://input'));
		 if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		 {
			 $data['status']='ok';
			 echo json_encode($data);exit;
		 }
		 
			 $headers = apache_request_headers();
			 $token = str_replace("Bearer ", "", $headers['Authorization']);
			 $kunci = $this->config->item('jwt_key');
			 $userData = JWT::decode($token, $kunci);
			 Utility::validateSession($userData->iat,$userData->exp);
			 $tokenSession = Utility::tokenSession($userData);

			 $searchExam = isset($data->searchExam) ? $data->searchExam : "";

			 $result = $this->Exam_model->getExams($searchExam);
			 if ($result) {
				 $response["response_code"] = "200";
				 $response["response_message"] = "Success";
				 $response["response_data"] = $result;
			 } else {
				 $response["response_code"] = "400";
				 $response["response_message"] = "Failed";
			 }
		
		 echo json_encode($response);
		 exit();
	}


	public function updateExamsDocs()
	{
		$data = json_decode(file_get_contents('php://input'));
		 if($this->input->server('REQUEST_METHOD')=='OPTIONS')
		 {
			 $data['status']='ok';
			 echo json_encode($data);exit;
		 }
		 
			 $headers = apache_request_headers();
			 $token = str_replace("Bearer ", "", $headers['Authorization']);
			 $kunci = $this->config->item('jwt_key');
			 $userData = JWT::decode($token, $kunci);
			 Utility::validateSession($userData->iat,$userData->exp);
			 $tokenSession = Utility::tokenSession($userData);
		 	if($data)
			{
			$examId = $data->examId;
			$questionpaper = $data->questionpaper;
			$preparation = $data->preparation;
			$syllabus = $data->syllabus;
			$Arr = ['questionpaper'=>$questionpaper,'preparation'=>$preparation,'syllabus'=>$syllabus];
			 $result = $this->Exam_model->updateExamsDocs($examId,$Arr);
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
