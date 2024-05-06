<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Exam extends CI_Controller
{
    /**
     * Constructor
     *
     * Loads necessary libraries, helpers, and models for the Blogs controller.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model("web/Exam_model", "", true);
        $this->load->library("Utility");
    }

    public function getExamNotificationForClg()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
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
        if ($data) {
            $collegeid = $data->collegeid;
            $result = $this->Exam_model->getExamNotificationForClg($collegeid);
            foreach ($result as $key => $img) {
                $result[$key]->imageName = $img->image;

                $result[$key]->image =
                    base_url() . "/uploads/blogs/" . $img->image;
            }
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

    public function getExamList()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
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
        if ($data) {
            $value = isset($data->value) ? $data->value : "";
            $result = $this->Exam_model->getExamList($value);
            foreach ($result as $key => $img) {
                $result[$key]->imageName = $img->image;
                $result[$key]->image =
                    base_url() . "/uploads/exams/" . $img->image;
                $result[$key]->questionpaper_Name = $img->questionpaper;
                $result[$key]->questionpaper =
                    base_url() .
                    "/uploads/questionpaper/" .
                    $img->questionpaper;
                $result[$key]->preparationName = $img->preparation;
                $result[$key]->preparation =
                    base_url() . "/uploads/preparation/" . $img->preparation;
                $result[$key]->syllabusName = $img->syllabus;
                $result[$key]->syllabus =
                    base_url() . "/uploads/syllabus/" . $img->syllabus;
            }
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
            $response["response_message"] = "Failed";
        }
        echo json_encode($response);
        exit();
    }

    public function getExamDetails()
    {
        $data = json_decode(file_get_contents("php://input"));

        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
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
        if ($data) {
            $examId = $data->examId;
            $result = $this->Exam_model->getExamDetails($examId);
            // print_r($result);exit;
            
            $addView = $this->Exam_model->increment_view($examId);
            foreach ($result as $key => $img) {
                $result[$key]->imageName = $img->image;

                $result[$key]->image =
                    base_url() . "/uploads/exams/" . $img->image;
            }

            $relatedExams = $this->Exam_model->relatedExams(
                $result[0]->categoryid
            );
            $chunkedRelatedExams = array_chunk($relatedExams, 3);
            $groupedRelatedExams = [];
            foreach ($chunkedRelatedExams as $chunk) {
                $groupedRelatedExams[] = ["relatedExamsSub" => $chunk];
            }
            //print_r($chunkedRelatedExams);exit;
            foreach ($relatedExams as $key => $img) {
                $relatedExams[$key]->imageName = $img->image;

                $relatedExams[$key]->image =
                    base_url() . "/uploads/exams/" . $img->image;
            }
            if ($result || $relatedExams) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["examdetails"] = $result;
                $response["relatedExams"] = $groupedRelatedExams;
            } else {
                $response["response_code"] = "400";
                $response["response_message"] = "Failed";
            }
        } else {
            $response["response_code"] = "500";
            $response["response_message"] = "data is null";
        }
        echo json_encode($response);
        exit();
    }
}
