<?php

/**
 * Category Controller
 *
 * @category   Controllers
 * @package    Web
 * @subpackage Category
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    26 JAN 2024
 *
 * Class Category handles all the operations related to displaying list, creating Category, update, and delete.
 */

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Category extends CI_Controller
{
    /**
     * Constructor
     *
     * Loads necessary libraries, helpers, and models for the Category controller.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model("web/Category_model", "", true);
        $this->load->library("Utility");
        // $this->checkAuthorization();
    }
    /*** Get list of Category */
    public function getCategoryForMenu()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
           // $data->status = "ok";
            echo json_encode("ok");
            exit();
        }
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            if (
                !is_object($data) ||
                !property_exists($data, "defaultToken") ||
                empty($data->defaultToken)
            ) {
                $response["response_code"] = "401";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide an access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
            if ($data->defaultToken !== $this->config->item("defaultToken")) {
                $response["response_code"] = "402";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide a valid access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
        } else {
            $headers = apache_request_headers();
            $token = str_replace("Bearer ", "", $headers["Authorization"]);
            $kunci = $this->config->item("jwt_key");
            $userData = JWT::decode($token, $kunci);
            Utility::validateSession($userData->iat, $userData->exp);
            $tokenSession = Utility::tokenSession($userData);
        }
        $categories = $this->Category_model->getCategoryForMenu();
        
        // foreach ($categories as &$category) {
        //     $courseCount = $this->Category_model->getCourseCount($category->id);
        //     $category->Total_Courses = $courseCount;
        //     $courses = $this->Category_model->getCourses($category->id);
        //     $category->Courses = $courses;
        //     $exams = $this->Category_model->getExams($category->id);
        //     $category->Exams = $exams;
        // }
         
        if ($categories) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["response_data"] = $categories;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
        echo json_encode($response);
        exit();
    }

    public function getCoursesForCategory()
    {
         $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
           // $data->status = "ok";
            echo json_encode("ok");
            exit();
        }
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            if (
                !is_object($data) ||
                !property_exists($data, "defaultToken") ||
                empty($data->defaultToken)
            ) {
                $response["response_code"] = "401";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide an access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
            if ($data->defaultToken !== $this->config->item("defaultToken")) {
                $response["response_code"] = "402";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide a valid access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
        } else {
            $headers = apache_request_headers();
            $token = str_replace("Bearer ", "", $headers["Authorization"]);
            $kunci = $this->config->item("jwt_key");
            $userData = JWT::decode($token, $kunci);
            Utility::validateSession($userData->iat, $userData->exp);
            $tokenSession = Utility::tokenSession($userData);
        }
            $categoryid = $data->categoryid;
            $categories = $this->Category_model->getCourses($categoryid);
        if ($categories) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["response_data"] = $categories;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
        echo json_encode($response);
        exit();
    }
    
    public function getExamForCategory()
    {
         $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
           // $data->status = "ok";
            echo json_encode("ok");
            exit();
        }
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            if (
                !is_object($data) ||
                !property_exists($data, "defaultToken") ||
                empty($data->defaultToken)
            ) {
                $response["response_code"] = "401";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide an access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
            if ($data->defaultToken !== $this->config->item("defaultToken")) {
                $response["response_code"] = "402";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide a valid access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
        } else {
            $headers = apache_request_headers();
            $token = str_replace("Bearer ", "", $headers["Authorization"]);
            $kunci = $this->config->item("jwt_key");
            $userData = JWT::decode($token, $kunci);
            Utility::validateSession($userData->iat, $userData->exp);
            $tokenSession = Utility::tokenSession($userData);
        }
            $categoryid = $data->categoryid;

            $exams = $this->Category_model->getExams($categoryid);

        if ($exams) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["response_data"] = $exams;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
        echo json_encode($response);
        exit();
    }
    public function getCategoryForMenuNav()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data->status = "ok";
            echo json_encode($data);
            exit();
        }
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            if (
                !is_object($data) ||
                !property_exists($data, "defaultToken") ||
                empty($data->defaultToken)
            ) {
                $response["response_code"] = "401";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide an access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
            if ($data->defaultToken !== $this->config->item("defaultToken")) {
                $response["response_code"] = "402";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide a valid access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
        } else {
            $headers = apache_request_headers();
            $token = str_replace("Bearer ", "", $headers["Authorization"]);
            $kunci = $this->config->item("jwt_key");
            $userData = JWT::decode($token, $kunci);
            Utility::validateSession($userData->iat, $userData->exp);
            $tokenSession = Utility::tokenSession($userData);
        }
        $categories = $this->Category_model->getCategoryForMenus();

        //print_r($categories);exit;
        //$cat=[];
        $nav = ["id" => "20", "title" => "Home"];

        foreach ($categories as &$category) {
            $category->type = "collapsable";
            $courses = $this->Category_model->getCourse($category->id);
            foreach ($courses as $course) {
                $course->type = "basic";
            }
            $exams = $this->Category_model->getExam();
            foreach ($exams as $exam) {
                $exam->type = "basic";
            }
            $category->children = [
                ["title" => "Top Ranked Colleges", "type" => "collapsable"],
                [
                    "title" => "Popular Courses",
                    "type" => "collapsable",
                    "children" => $courses,
                ],
                [
                    "title" => "Exams",
                    "type" => "collapsable",
                    "children" => $exams,
                ],
                ["title" => "Colleges by Location", "type" => "collapsable"],
            ];
        }
        $home = [
            "id" => "",
            "title" => "Home",
            "menuorder" => "1",
            "type" => "basic",
            "link" => "/home",
        ];
        array_unshift($categories, $home);

        if (count($categories) > 4) {
            // Slice the sub-array to get the first three elements
            $more = array_slice($categories, 4);

            // Remove the elements after the first three from the original sub-array
            $subArray = array_slice($categories, 0, 4);
        } else {
            // If sub-array size is 3 or less, set $more to an empty array
            $more = [];
        }
        //$nav = array();
        //$nav = ["catname"=>"Home"];
        //$categories[] = $nav;
        //print_r($nav);exit;
        //print_r($categories);exit;
        /*if ($categories) {
            //$response["response_code"] = "200";
            //$response["response_message"] = "Success";
            //$response["response_data"] = $categories;
			$response["horizontal"] = $categories;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
        echo json_encode($response);
        exit;*/
        echo json_encode(["horizontal" => $subArray]);
    }

    public function getCategory()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data->status = "ok";
            echo json_encode($data);
            exit();
        }
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            if (
                !is_object($data) ||
                !property_exists($data, "defaultToken") ||
                empty($data->defaultToken)
            ) {
                $response["response_code"] = "401";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide an access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
            if ($data->defaultToken !== $this->config->item("defaultToken")) {
                $response["response_code"] = "402";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide a valid access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
        } else {
            $headers = apache_request_headers();
            $token = str_replace("Bearer ", "", $headers["Authorization"]);
            $kunci = $this->config->item("jwt_key");
            $userData = JWT::decode($token, $kunci);
            Utility::validateSession($userData->iat, $userData->exp);
            $tokenSession = Utility::tokenSession($userData);
        }
        $categories = $this->Category_model->getCategory();

        foreach ($categories as &$category) {
            $courseCount = $this->Category_model->getCourseCount($category->id);
            $category->Total_Courses = $courseCount;
        }
        if ($categories) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["response_data"] = $categories;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }

        echo json_encode($response);
        exit();
    }
    public function getAcadamicCategory()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data->status = "ok";
            echo json_encode($data);
            exit();
        }
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            if (
                !is_object($data) ||
                !property_exists($data, "defaultToken") ||
                empty($data->defaultToken)
            ) {
                $response["response_code"] = "401";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide an access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
            if ($data->defaultToken !== $this->config->item("defaultToken")) {
                $response["response_code"] = "402";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide a valid access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
        } else {
            $headers = apache_request_headers();
            $token = str_replace("Bearer ", "", $headers["Authorization"]);
            $kunci = $this->config->item("jwt_key");
            $userData = JWT::decode($token, $kunci);
            Utility::validateSession($userData->iat, $userData->exp);
            $tokenSession = Utility::tokenSession($userData);
        }
        $categories = $this->Category_model->getAcadamicCategory();
        if ($categories) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["response_data"] = $categories;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }

        echo json_encode($response);
        exit();
    }

    public function getPlacementCategory()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data->status = "ok";
            echo json_encode($data);
            exit();
        }
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            if (
                !is_object($data) ||
                !property_exists($data, "defaultToken") ||
                empty($data->defaultToken)
            ) {
                $response["response_code"] = "401";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide an access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
            if ($data->defaultToken !== $this->config->item("defaultToken")) {
                $response["response_code"] = "402";
                $response["response_message"] =
                    "UNAUTHORIZED: Please provide a valid access token to continue accessing the API";
                echo json_encode($response);
                exit();
            }
        } else {
            $headers = apache_request_headers();
            $token = str_replace("Bearer ", "", $headers["Authorization"]);
            $kunci = $this->config->item("jwt_key");
            $userData = JWT::decode($token, $kunci);
            Utility::validateSession($userData->iat, $userData->exp);
            $tokenSession = Utility::tokenSession($userData);
        }
        $categories = $this->Category_model->getPlacementCategory();
        if ($categories) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["response_data"] = $categories;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }

        echo json_encode($response);
        exit();
    }
}
