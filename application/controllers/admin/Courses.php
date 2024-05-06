<?php

/**
 * Courses Controller
 *
 * @category   Controllers
 * @package    Admin
 * @subpackage Courses
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    31 JAN 2024
 *
 * Class Courses handles all the operations related to displaying list, creating Courses, update, and delete.
 */

if (!defined("BASEPATH")) {
	exit("No direct script access allowed");
}
class Courses extends CI_Controller
{
	/**
	 * Constructor
	 *
	 * Loads necessary libraries, helpers, and models for the Courses controller.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model("admin/Courses_model", "", true);
		$this->load->library('Utility');
	}

	public function getCourseList()
	{
		$data = json_decode(file_get_contents('php://input'));

		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data->status = 'ok';
			echo json_encode($data);
			exit;
		}

		if ($data) {

			$headers = apache_request_headers();

			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);

			$columns = array(
				0 => 'id',
				1 => 'name',
				2 => 'type',
			);

			$limit = $data->length;
			$start = ($data->draw - 1) * $limit;
			$orderColumn = $columns[$data->order[0]->column];
			$orderDir = $data->order[0]->dir;
			$totalData = $this->Courses_model->countAllCourse();
			$totalFiltered = $totalData;

			if (!empty($data->search->value) or !empty($data->search->category)) {

				$search = $data->search->value;
				$cat = $data->search->category;
				$totalFiltered = $this->Courses_model->countFilteredCourse($search, $cat);
				$courseList = $this->Courses_model->getFilteredCourse($search, $start, $limit, $orderColumn, $orderDir, $cat);
			} else {
				$courseList = $this->Courses_model->getAllCourse($start, $limit, $orderColumn, $orderDir);
			}

			$datas = array();
			foreach ($courseList as $crs) {

				$nestedData = array();
				$nestedData['id'] = $crs->id;
				$nestedData['name'] = $crs->name;
				$nestedData['category'] = $crs->category;
				$nestedData['type'] = $crs->type;
				$nestedData['status'] = $crs->status;
				if($crs->image != NULL)
				{
				$nestedData['image'] = base_url().'uploads/courses/'.$crs->image;
				}
				else
				{
				    $nestedData['image'] = ""; 
				}
				$datas[] = $nestedData;
			}

			$json_data = array(
				'draw' => intval($data->draw),
				'recordsTotal' => intval($totalData),
				'recordsFiltered' => intval($totalFiltered),
				'data' => $datas
			);

			echo json_encode($json_data);
		} else {
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			echo json_encode($response);
			exit();
		}
	}


	public function getPGCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if($data)
		{
		$headers = apache_request_headers();
		$token = str_replace("Bearer ", "", $headers['Authorization']);
		$kunci = $this->config->item('jwt_key');
		$userData = JWT::decode($token, $kunci);
		Utility::validateSession($userData->iat, $userData->exp);
		$tokenSession = Utility::tokenSession($userData);
		$searchPg = isset($data->searchPg)?$data->searchPg:'';

		$result = $this->Courses_model->getPGCourses($searchPg);
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
		echo json_encode($response);
		exit();
	}
		echo json_encode($response);
		exit();
	}

	public function getUGCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if($data)
		{
		$searchUg = isset($data->searchUg)?$data->searchUg:'';
		$headers = apache_request_headers();
		$token = str_replace("Bearer ", "", $headers['Authorization']);
		$kunci = $this->config->item('jwt_key');
		$userData = JWT::decode($token, $kunci);
		Utility::validateSession($userData->iat, $userData->exp);
		$tokenSession = Utility::tokenSession($userData);

		$result = $this->Courses_model->getUGCourses($searchUg);
		if ($result) {
			$response["response_code"] = "200";
			$response["response_message"] = "Success";
			$response["response_data"] = $result;
		} else {
			$response["response_code"] = "400";
			$response["response_message"] = "Failed";
		}
	}else
	{		$response["response_code"] = "500";
			$response["response_message"] = "Failed";
	}
		echo json_encode($response);
		exit();
	}

	public function insertCourseDetails()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if ($data) {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);

			$courseName = $data->name;
			$duration = $data->duration;
			$academicCategory = $data->academicCategory;
			$courseCategory = $data->courseCategory;
			$scope = $data->scope;
			$jobProfile = $data->jobProfile;
			$certification =  $data->certification;
			$description = $data->description;
			$crs_image = $data->imageName;
			$sub_category = $data->sub_category;
			$topmenu = $data->topmenu;

			$str = preg_replace('/[^A-Za-z0-9\. -]/', ' ', $courseName);
			$slug = preg_replace('/\s+/', '-', strtolower($str));
			$Arr = [
				'name' => $courseName, 'slug' => $slug, 'course_description' => $description,
				'duration' => $duration, 'academic_category' => $academicCategory,
				'course_category' => $courseCategory, 'scope' => $scope, 'job_profile' => $jobProfile,
				'certification' => $certification,'image' => $crs_image,'sub_category'=>$sub_category,'view_in_menu'=>$topmenu
			];

			$chkIfExists = $this->Courses_model->chkIfExists($slug);
			if ($chkIfExists > 0) {
				$response["response_code"] = 300;
				$response["response_message"] = 'Course is already exists.Please try another one.';
			} else {
				$result = $this->Courses_model->insertCourseDetails($Arr);
				if ($result) {
					$response["response_code"] = "200";
					$response["response_message"] = "Success";
					$response["response_data"] = $result;
				} else {
					$response["response_code"] = "400";
					$response["response_message"] = "Failed";
				}
			}
		} else {
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			echo json_encode($response);
			exit();
		}
		echo json_encode($response);
		exit;
	}

	public function updateCourseDetails()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if ($data) {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);

			$courseId = $data->course_id;
			$courseName = $data->name;
			$duration = $data->duration;
			$academicCategory = $data->academicCategory;
			$courseCategory = $data->courseCategory;
			$scope = $data->scope;
			$jobProfile = $data->jobProfile;
			$certification =  $data->certification;
			$description = $data->description;
			$crs_image = $data->imageName;
			$sub_category = $data->sub_category;
			$topmenu = $data->topmenu;

			// $keyword = $data->keyword;
			$str = preg_replace('/[^A-Za-z0-9\. -]/', ' ', $courseName);
			$slug = preg_replace('/\s+/', '-', strtolower($str));
			$Arr = [
				'name' => $courseName, 'slug' => $slug, 'course_description' => $description,
				'duration' => $duration, 'academic_category' => $academicCategory,
				'course_category' => $courseCategory, 'scope' => $scope, 'job_profile' => $jobProfile,
				'certification' => $certification,'image'=>$crs_image,'sub_category'=>$sub_category,'view_in_menu'=>$topmenu
			];

			$chkIfExists = $this->Courses_model->chkWhileUpdate($courseId, $slug);
			if ($chkIfExists > 0) {
				$response["response_code"] = 300;
				$response["response_message"] = 'Course is already exists.Please try another one.';
			} else {
				$result = $this->Courses_model->updateCourseDetails($courseId, $Arr);
				if ($result) {
					$response["response_code"] = "200";
					$response["response_message"] = "Success";
					$response["response_data"] = $result;
				} else {
					$response["response_code"] = "400";
					$response["response_message"] = "Failed";
				}
			}
		} else {
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			echo json_encode($response);
			exit();
		}
		echo json_encode($response);
		exit;
	}


	public function deleteCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if ($data) {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);
			$UserRole = $userData->data->type;
			if(strtoupper($UserRole)=='EMPLOYEE')
			{
				$response["response_code"] = 300;
				$response["response_message"] = "Sorry, you do not have permission to modify the courses.";
				echo json_encode($response);
        		exit();
			}
			$Id = $data->id;
			$result = $this->Courses_model->deleteCourses($Id);
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

	public function getCourseDetailsById()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if ($data) {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);

			$Id = $data->id;
			$result = $this->Courses_model->getCourseDetailsById($Id);
			$result[0]->filepath = base_url().'uploads/courses/'.$result[0]->image;
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

	public function coursesOfferedInSameGrp()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if ($data) {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);

			$columns = array(
				0 => 'id'
			);

			$limit = $data->length;
			$start = ($data->draw - 1) * $limit;
			$orderColumn = $columns[$data->order[0]->column];
			$orderDir = $data->order[0]->dir;
			$totalData = $this->Courses_model->countAllCourseOffered();

			$totalFiltered = $totalData;

			if (!empty($data->search->value)) {
				$search = $data->search->value;
				$totalFiltered = $this->Courses_model->countFilteredCourseOffered($search);
				$courseList = $this->Courses_model->getFilteredCourseOffered($search, $start, $limit, $orderColumn, $orderDir);
			} else {
				$courseList = $this->Courses_model->getAllCourseOffered($start, $limit, $orderColumn, $orderDir);
			}

			$datas = array();
			// echo"ww";exit;
			foreach ($courseList as $crs) {

				$nestedData = array();
				$nestedData['id'] = $crs->id;
				$nestedData['college_name'] = $crs->college_name;
				$nestedData['courses'] = $crs->courses;

				$datas[] = $nestedData;
			}

			$json_data = array(
				'draw' => intval($data->draw),
				'recordsTotal' => intval($totalData),
				'recordsFiltered' => intval($totalFiltered),
				'data' => $datas
			);

			echo json_encode($json_data);
		} else {
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			echo json_encode($response);
			exit();
		}
	}

	public function getDiplomaCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
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
		Utility::validateSession($userData->iat, $userData->exp);
		$tokenSession = Utility::tokenSession($userData);
		$searchDp = isset($data->searchDp)?$data->searchDp:'';
		$result = $this->Courses_model->getDiplomaCourses($searchDp);
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
		$response["response_code"] = "500";
		$response["response_message"] = "Data is null";
	}
		echo json_encode($response);
		exit();
	}

	public function getDocCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
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
		Utility::validateSession($userData->iat, $userData->exp);
		$tokenSession = Utility::tokenSession($userData);
		$searchDoc = isset($data->searchDoc)?$data->searchDoc:'';
		$result = $this->Courses_model->getDocCourses($searchDoc);
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
		$response["response_code"] = "500";
		$response["response_message"] = "Data is null";
	}
		echo json_encode($response);
		exit();
	}

	public function getOtherCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
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
		Utility::validateSession($userData->iat, $userData->exp);
		$tokenSession = Utility::tokenSession($userData);
		$searchOther = isset($data->searchOther)?$data->searchOther:'';
		$result = $this->Courses_model->getOtherCourses($searchOther);
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
		$response["response_code"] = "500";
		$response["response_message"] = "Data is null";
	}
		echo json_encode($response);
		exit();
	}

	public function updateCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
		$headers = apache_request_headers();
		$token = str_replace("Bearer ", "", $headers['Authorization']);
		$kunci = $this->config->item('jwt_key');
		$userData = JWT::decode($token, $kunci);
		Utility::validateSession($userData->iat, $userData->exp);
		$tokenSession = Utility::tokenSession($userData);
		if($data)
		{
			$fieldName = $data->fieldName;
			$collegeId = $data->collegeId;
			$courseId = $data->courseId;
			$data = $data->fieldDetails;
			if(strtoupper($fieldName) == 'HIGHLIGHT')
			{
			$total_fees = $data->totalFees;
			$total_intake = isset($data->total_intake) ? $data->total_intake:'';
			$median_salary = isset($data->median_salary) ? $data->median_salary:'';
			$rank = $data->rank;
			$duration = $data->duration;
			$level = $data->level;
			$website = $data->website;
			$description = $data->description;
			$Arr = ['median_salary'=>$median_salary,'total_intake'=>$total_intake,'total_fees'=>$total_fees,'rank'=>$rank,'duration'=>$duration,'level'=>$level,'website'=>$website,'description'=>$description];
			$result = $this->Courses_model->updateCourses($collegeId,$courseId,$Arr);
			if ($result) {
				$response["response_code"] = "200";
				$response["response_message"] = "Success";
				$response["response_data"] = $result;
			} else {
				$response["response_code"] = "400";
				$response["response_message"] = "Failed";
			}
			}
			else if(strtoupper($fieldName) == 'ELIGIBILITY')
			{
				$data = $data->eligibility;
				$Arr = [];
				$other_eligibility = $data[0]->other_eligibility;
				foreach ($data as $key => $val) {
    			$qualification = $val->qualification;
    			$cut_off = $val->cut_off;
    			$Arr[] = ['qualification' => $qualification, 'cut_off' => $cut_off, 'other_eligibility' => $other_eligibility];
				}

				$NewArr = ['eligibility' => json_encode($Arr)];
				$result = $this->Courses_model->updateCourses($collegeId,$courseId,$NewArr);
				if ($result) {
					$response["response_code"] = "200";
					$response["response_message"] = "Success";
					$response["response_data"] = $result;
				} else {
					$response["response_code"] = "400";
					$response["response_message"] = "Failed";
				}
				
			}
			else if(strtoupper($fieldName == 'EXAMS'))
			{
				$exams = $data->exams;
				$NewArr = ['entrance_exams' => $exams];
				$result = $this->Courses_model->updateCourses($collegeId,$courseId,$NewArr);
				if ($result) {
					$response["response_code"] = "200";
					$response["response_message"] = "Success";
					$response["response_data"] = $result;
				} else {
					$response["response_code"] = "400";
					$response["response_message"] = "Failed";
				}
			}
			else if (strtoupper($fieldName == 'PLACEMENT'))
			{
				$placed_student = $data->placed_student;
				$salary = $data->salary;
				$companies = $data->companies;
				$Arr = ['placed_student'=>$placed_student, 'salary'=>$salary,'companies'=>$companies];
				$NewArr = ['placement' => json_encode($Arr)];
				$result = $this->Courses_model->updateCourses($collegeId,$courseId,$NewArr);
				if ($result) {
					$response["response_code"] = "200";
					$response["response_message"] = "Success";
					$response["response_data"] = $result;
				} else {
					$response["response_code"] = "400";
					$response["response_message"] = "Failed";
				}
			}
			else if (strtoupper($fieldName == 'BROCHURE'))
			{
				$FileName = $data->fileName;
				$NewArr = ['brochure' => $FileName];
				$result = $this->Courses_model->updateCourses($collegeId,$courseId,$NewArr);
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
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			
		}

		echo json_encode($response);
		exit();
	}

	public function getCollegeCourseDetail()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$response["response_code"] = "401";
			$response["response_message"] = "Unauthorized";
			echo json_encode($response);
			exit();
		}
		$headers = apache_request_headers();
		$token = str_replace("Bearer ", "", $headers['Authorization']);
		$kunci = $this->config->item('jwt_key');
		$userData = JWT::decode($token, $kunci);
		Utility::validateSession($userData->iat, $userData->exp);
		$tokenSession = Utility::tokenSession($userData);
		if($data)
		{
			$id = $data->id;
			$clgid = $data->clgid;
			$result = $this->Courses_model->getCollegeCourseDetail($id,$clgid);
			if(!empty($result[0]['eligibility']) && $result[0]['eligibility'])
			{
			$result[0]['eligibility'] = json_decode($result[0]['eligibility']);
			$result[0]['placement'] = json_decode($result[0]['placement']);
			}
			// print_r($result[0]['eligibility']);exit;
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
			$response["response_code"] = "500";
			$response["response_message"] = "Data is null";
			
		}

		echo json_encode($response);
		exit();
}

/**
     * upload documents of courese.
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
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat,$userData->exp);
        	$tokenSession = Utility::tokenSession($userData);
			
		$folder = 'uploads/courses';
		if(!is_dir($folder)) {
			mkdir($folder, 0777, TRUE);
			}
			if(isset($_FILES["file"]) && $_FILES["file"]["error"] == 0)
			{
				$allowed = array(
					"jpg" => "image/jpg",
					"jpeg" => "image/jpeg",
					"png" => "image/png",
					"JPG" => "image/jpeg",
					"JPEG" => "image/jpeg",
					"PNG" => "image/png"
				);
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


		public function getPostDocCourses()
		{
			$data = json_decode(file_get_contents('php://input'));
			if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
				$data['status'] = 'ok';
				echo json_encode($data);
				exit;
			}
			if($data)
			{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);
			$searcPostDoc = isset($data->searcPostDoc)?$data->searcPostDoc:'';
			$result = $this->Courses_model->getPostDocCourses($searcPostDoc);
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
			$response["response_message"] = "Data is null !";
		}
			echo json_encode($response);
			exit();
		}	


		public function getAdvMasterCourses()
		{
			$data = json_decode(file_get_contents('php://input'));
			if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
				$data['status'] = 'ok';
				echo json_encode($data);
				exit;
			}
			if($data)
			{
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);
			$searcAdvMas = isset($data->searcAdvMas)?$data->searcAdvMas:'';
			$result = $this->Courses_model->getAdvMasterCourses($searcAdvMas);
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
			$response["response_message"] = "Data is null !";
		}
			echo json_encode($response);
			exit();
		}

		public function getCourses()
		{
			$data = json_decode(file_get_contents('php://input'));
			if($this->input->server('REQUEST_METHOD')=='OPTIONS')
			{
				$data['status']='ok';
				echo json_encode($data);exit;
			}

				if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
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
				$search_courses = isset($data->search_courses)?$data->search_courses:'';
				$result = $this->Courses_model->getCourses($search_courses);
				if ($result) {
					$response["response_code"] = "200";
					$response["response_message"] = "Success";
					$response["coursedata"] = $result;
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
			
			echo json_encode($response);
			exit();
		}


		public function deleteClgCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if ($data) {
			$headers = apache_request_headers();
			$token = str_replace("Bearer ", "", $headers['Authorization']);
			$kunci = $this->config->item('jwt_key');
			$userData = JWT::decode($token, $kunci);
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);

			$courseid = $data->courseid;
			$collegeid = $data->collegeid;

			$result = $this->Courses_model->deleteClgCourses($courseid,$collegeid);
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

    public function saveExamForSubCat()
    {
        $data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
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
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);
			$subcat = isset($data->subcat) ? $data->subcat :'';
			$examids = isset($data->examids) ? $data->examids :'';
			$collegeid = isset($data->collegeid) ? $data->collegeid :'';
            $NewArr = ['entrance_exams' => $exams];
			$result = $this->Courses_model->saveExamForSubCat($NewArr,$subcat,$collegeid);
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
			$response["response_code"] = "500";
			 $response["response_message"] = "Data is null !";
		}
		
		echo json_encode($response);
		exit();
    }
	public function getTrendingCourses()
	{
		$data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
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
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);
			$fromdate = isset($data->fromdate) ? $data->fromdate :'';
			$todate = isset($data->todate) ? $data->todate :'';
			$result = $this->Courses_model->getTrendingCourses($fromdate,$todate);
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
			$response["response_code"] = "500";
			 $response["response_message"] = "Data is null !";
		}
		
		echo json_encode($response);
		exit();
	}
	
	public function getCollegeSubCat()
    {
        $data = json_decode(file_get_contents('php://input'));
		if ($this->input->server('REQUEST_METHOD') == 'OPTIONS') {
			$data['status'] = 'ok';
			echo json_encode($data);
			exit;
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
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
			Utility::validateSession($userData->iat, $userData->exp);
			$tokenSession = Utility::tokenSession($userData);
			
			$collegeid = isset($data->collegeid) ? $data->collegeid :'';
			$result = $this->Courses_model->getCollegeSubCat($collegeid);
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
			$response["response_code"] = "500";
			 $response["response_message"] = "Data is null !";
		}
		
		echo json_encode($response);
		exit();
    }
}
