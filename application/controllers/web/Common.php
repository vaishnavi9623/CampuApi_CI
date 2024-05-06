<?php

/**
 * Common Controller
 *
 * @category   Controllers
 * @package    Web
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    30 JAN 2024
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
        $this->load->model("web/College_model", "", true);
        $this->load->model("web/Exam_model", "", true);
        $this->load->model("web/Courses_model", "", true);
        $this->load->model("web/Common_model", "", true);
        $this->load->model("admin/common_model", "", true);
        $this->load->model("web/User_model", "", true);
        $this->load->library("Utility");
        $this->load->library("m_pdf");
    }

    public function getTotalCount()
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
        $clg = $this->College_model->countAllClg();
        $exam = $this->Exam_model->countAllExam();
        $courses = $this->Courses_model->countAllcourses();

        if ($clg || $exam || $courses) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["Clgcount"] = $clg;
            $response["Examcount"] = $exam;
            $response["Coursescount"] = $courses;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
        echo json_encode($response);
        exit();
    }

    public function getFAQ()
    {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
        $Que = $this->Common_model->getFAQ();

        if ($Que) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["Question"] = $Que;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }
        echo json_encode($response);
        exit();
    }

    public function getSubCategoryList()
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
            $collegeId = $data->collegeid;
            $SubCategory = $this->Common_model->getSubCategoryList($collegeId);

            if ($SubCategory) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["SubCategory"] = $SubCategory;
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

    public function getCourseLevel()
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
            $collegeId = $data->collegeid;
            $SubCategory = $this->Common_model->getAcademicCategory($collegeId);

            if ($SubCategory) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["SubCategory"] = $SubCategory;
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

    public function getExamAccepted()
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
            $collegeId = $data->collegeid;
            $SubCategory = $this->Common_model->getExamAccepted($collegeId);

            if ($SubCategory) {
                $response["response_code"] = "200";
                $response["response_message"] = "Success";
                $response["SubCategory"] = $SubCategory;
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

    public function saveEnquiry()
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
            $name = $data->name;
            $email = $data->email;
            $phone = $data->phone;
            $message = $data->message;
            $postid = $data->postid;
            $type = $data->type;
            $city = $data->city;
            $state = $data->state;
            $arr = [
                "name" => $name,
                "email" => $email,
                "phone" => $phone,
                "message" => $message,
                "postid" => $postid,
                "type" => $type,
                "city" => $city, "state" => $state
            ];
            $saveEnquiry = $this->Common_model->saveEnquiry($arr);

            if ($saveEnquiry) {
                $sendMailToCustomer = $this->sendMailToCustomer($name, $email);
                $logArr = ["enquiry_id" => $saveEnquiry];
                $tableName = "enquiry_log";
                $addLog = $this->Common_model->addLog($logArr, $tableName);
                $response["response_code"] = "200";
                $response["response_message"] =
                    "Your inquiry has been submitted successfully. We will get back to you soon!";
                $response["response_data"] = $saveEnquiry;
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

    public function sendMailToCustomer($name, $email)
    {
        $senderName = "OhCampus Team";
        $bccArray = "";
        $toName = $name;
        $from = "enquiry@ohcampus.comm";
        $to = $email;
        $emailMessage =
            '<!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Template</title>
        </head>
        <body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
        
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="https://win.k2key.in/ohcampus/uploads/OhCampusLogo.png" alt="Company Logo" style="max-width: 200px;">
            </div>
            <div style="padding: 20px; background-color: #f0f0f0; border-radius: 5px;">
                <h2 style="color: #333333; margin-bottom: 10px;">Thank You for Your Enquiry!</h2>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">Dear ' .
            $toName .
            ',</p>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">Thank you for contacting us regarding your enquiry. We have received your message and will get back to you as soon as possible with the information you requested.</p>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">If you have any further questions or need immediate assistance, please feel free to contact us.</p>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">Best regards,<br>OhCampus IT Team<br>OhCampus<br><img src="https://win.k2key.in/ohcampus/uploads/OhCampusLogo.png" alt="Company Logo" style="max-width: 100px;">
                </div>
        </div>
        
        </body>
        </html>
        ';
        $subject = "Response to Your Enquiry - OhCampus";
        $url = "YOUR_EMAIL_URL";

        $headers = [
            "api-key: API_KEY",
            "Content-Type: application/json",
        ];
        $custJsonData = [
            "sender" => ["name" => $senderName, "email" => $from],
            "to" => [["name" => $toName, "email" => $to]],
            "subject" => $subject,
            "htmlContent" => $emailMessage,
        ];
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($custJsonData),
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);
    }

    public function downloadBrochure()
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
            $collegeId = $data->collegeId;
            $userId = $data->userId;
            $brochures = $this->Common_model->getBrochure($collegeId);
            $clgDtl = $this->College_model->getCollegeDetailsByID($collegeId);
            $getUserDetails = $this->User_model->getUserDetailsById($userId);
            // print_r($getUserDetails);exit;
            if (!empty($brochures[0])) {
                $brochure = $brochures[0]["file"];
                $brochureName = $brochures[0]["title"];
                $fname = $getUserDetails[0]->f_name;
                $lname = $getUserDetails[0]->l_name;
                $name = $fname . " " . $lname;
                $email = $getUserDetails[0]->email;
                $senderName = "OhCampus Team";
                $bccArray = "";
                $toName = $name;
                $from = "enquiry@ohcampus.com";
                // $to = $getUserDetails[0]["email"];
                $to = $getUserDetails[0]->email;
                $emailMessage =
                    '<!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email Template</title>
                </head>
                <body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
                
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                  <div style="text-align: center; margin-bottom: 20px;">
                    <img src="https://win.k2key.in/ohcampus/uploads/OhCampusLogo.png" alt="Company Logo" style="max-width: 200px;">
                  </div>
                  <div style="padding: 20px; background-color: #f0f0f0; border-radius: 5px;">
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Hi ' .
                    $name .
                    ',</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">We are thrilled to share with you the latest brochure for ' .
                    $clgDtl[0]["title"] .
                    '! This comprehensive guide contains all the information you need to know about our offerings, programs, and facilities.</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Please feel free to explore it at your convenience, and dont hesitate to reach out if you have any questions or need further assistance.</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Thank you for your interest in ' .
                    $clgDtl[0]["title"] .
                    ' and for choosing OhCampus. We are excited to embark on this journey with you!</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Warm regards,<br>The OhCampus Team</p>
                  </div>
                </div>
                
                </body>
                </html>
                ';
                $subject =
                    "E-Brochure of " . $clgDtl[0]["title"] . " - OhCampus";
                $url = "YOUR_EMAIL_URL";

                $headers = [
                    "api-key: YOUR_API_KEY",
                    "Content-Type: application/json",
                ];
                $attachmentData = base64_encode(
                    file_get_contents("uploads/brochures/" . $brochure)
                );

                $custJsonData = [
                    "sender" => ["name" => $senderName, "email" => $from],
                    "to" => [["name" => $toName, "email" => $to]],
                    "subject" => $subject,
                    "htmlContent" => $emailMessage,
                    "attachment" => [
                        [
                            "content" => $attachmentData,
                            "name" =>
                                "e_brochure" . $clgDtl[0]["title"] . ".pdf",
                            "type" => "application/pdf", // Adjust MIME type according to your attachment
                        ],
                    ],
                ];
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($custJsonData),
                    CURLOPT_HTTPHEADER => $headers,
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
                $res = json_decode($response);
                if ($res) {
                    $Arr = [
                        "user_name" => $getUserDetails[0]->id,
                        "email" => $email,
                        "college" => $clgDtl[0]["id"],
                        "location" => $clgDtl[0]["city"],
                        "latest_activity" => "Brochure Downloaded",
                    ];
                    $addUserActivity = $this->Common_model->addUserActivity(
                        $Arr
                    );
                    $ClgRepArr = [
                        "college" => $collegeId,
                        "no_of_articles_linked" => 0,
                        "no_of_brochures_download" => 1,
                        "no_of_application_submitted" => 0,
                        "no_of_que_asked" => 0,
                        "no_of_answeres" => 0,
                    ];
                    // $this->load->model("admin/common_model", "", true);
                    $checkcollegeReport = $this->common_model->checkcollegeReport(
                        $collegeId
                    );
                    if ($checkcollegeReport > 0) {
                        $updateClgReport = $this->common_model->updateClgReport(
                            $collegeId,
                            $ClgRepArr
                        );
                    } else {
                        $saveClgReport = $this->common_model->saveClgReport(
                            $ClgRepArr
                        );
                    }
                    $response1["response_code"] = "200";
                    $response1["response_message"] =
                        "brochure sent sucessfully by mail";
                }
            } else {
                // $collegeDetails = $this->College_model->getCollegeDetailsByID($collegeId);
                $clgDtl = $this->College_model->getCollegeDetailsByID(
                    $collegeId
                );

                //print_r($collegeDetails);exit;
                $courseDetails = $this->College_model->getCoursesAndFeesOfClg(
                    $collegeId
                );
                $HighlightsDetails = $this->College_model->getCollegeHighlightByID(
                    $collegeId
                );
                $template_name = "template/brochure.html";
                $content = file_get_contents($template_name);
                // $PDFheader = SetHtmlHeader();
                // $PDFfooter = SetHtmlFooter();
                $this->m_pdf->pdf->AddPage(
                    "",
                    "",
                    "",
                    "",
                    "",
                    20, // margin_left
                    20, // margin right
                    10, // margin top
                    15, // margin bottom
                    5, // margin header
                    5
                ); // margin footer
                $content = $this->generatePDF(
                    $collegeDetails,
                    $courseDetails,
                    $HighlightsDetails
                );
                $path = dirname(dirname(__DIR__)) . "/uploads/brochures/";
                $path = str_replace("\\application", "", $path);
                $path = str_replace("/", "\\", $path);
                // print_r($path);exit;
                $filename =
                    "e_brochure_" . $collegeDetails[0]["title"] . ".pdf";
                // $filename = 'e_brochure_new_xx.pdf';
                $this->m_pdf->pdf->WriteHTML($content);
                $this->m_pdf->pdf->debug = true;
                $this->m_pdf->pdf->Output($path . $filename, "F");
                $pdf = base_url() . "/uploads/brochures/" . $filename;
                $brochuresData = [
                    "collegeid" => $collegeId,
                    "title" => "brochure pdf",
                    "file" => $filename,
                ];
                $saveBrochures = $this->College_model->saveBrochures(
                    $brochuresData
                );
                sleep(3);
                $brochures = $this->Common_model->getBrochure($collegeId);

                $brochure = $brochures[0]["file"];
                $brochureName = $brochures[0]["title"];
                $fname = $getUserDetails[0]->f_name;
                $lname = $getUserDetails[0]->l_name;
                $name = $fname . " " . $lname;
                $email = $getUserDetails[0]->email;
                $senderName = "OhCampus Team";
                $bccArray = "";
                $toName = $name;
                $from = "enquiry@ohcampus.comm";
                // $to = $getUserDetails[0]["email"];
                $to = $getUserDetails[0]->email;
                $emailMessage =
                    '<!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email Template</title>
                </head>
                <body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
                
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                  <div style="text-align: center; margin-bottom: 20px;">
                    <img src="https://win.k2key.in/ohcampus/uploads/OhCampusLogo.png" alt="Company Logo" style="max-width: 200px;">
                  </div>
                  <div style="padding: 20px; background-color: #f0f0f0; border-radius: 5px;">
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Hi ' .
                    $name .
                    ',</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">We are thrilled to share with you the latest brochure for ' .
                    $clgDtl[0]["title"] .
                    '! This comprehensive guide contains all the information you need to know about our offerings, programs, and facilities.</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Please feel free to explore it at your convenience, and dont hesitate to reach out if you have any questions or need further assistance.</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Thank you for your interest in ' .
                    $clgDtl[0]["title"] .
                    ' and for choosing OhCampus. We are excited to embark on this journey with you!</p>
                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">Warm regards,<br>The OhCampus Team</p>
                  </div>
                </div>
                
                </body>
                </html>
                ';
                $subject =
                    "E-Brochure of " . $clgDtl[0]["title"] . " - OhCampus";
                $url = "YOUR_EMAIL_URL";

                $headers = [
                    "api-key: YOUR_API_KEY",
                    "Content-Type: application/json",
                ];
                $attachmentData = base64_encode(
                    file_get_contents("uploads/brochures/" . $brochure)
                );

                $custJsonData = [
                    "sender" => ["name" => $senderName, "email" => $from],
                    "to" => [["name" => $toName, "email" => $to]],
                    "subject" => $subject,
                    "htmlContent" => $emailMessage,
                    "attachment" => [
                        [
                            "content" => $attachmentData,
                            "name" =>
                                "e_brochure" . $clgDtl[0]["title"] . ".pdf",
                            "type" => "application/pdf", // Adjust MIME type according to your attachment
                        ],
                    ],
                ];
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($custJsonData),
                    CURLOPT_HTTPHEADER => $headers,
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
                $res = json_decode($response);
                if ($res) {
                    $Arr = [
                        "user_name" => $getUserDetails[0]->id,
                        "email" => $email,
                        "college" => $clgDtl[0]["id"],
                        "location" => $clgDtl[0]["city"],
                        "latest_activity" => "Brochure Downloaded",
                    ];
                    $addUserActivity = $this->Common_model->addUserActivity(
                        $Arr
                    );
                    $ClgRepArr = [
                        "college" => $collegeId,
                        "no_of_articles_linked" => 0,
                        "no_of_brochures_download" => 1,
                        "no_of_application_submitted" => 0,
                        "no_of_que_asked" => 0,
                        "no_of_answeres" => 0,
                    ];
                    // $this->load->model("admin/common_model", "", true);
                    $checkcollegeReport = $this->common_model->checkcollegeReport(
                        $collegeId
                    );
                    if ($checkcollegeReport > 0) {
                        $updateClgReport = $this->common_model->updateClgReport(
                            $collegeId,
                            $ClgRepArr
                        );
                    } else {
                        $saveClgReport = $this->common_model->saveClgReport(
                            $ClgRepArr
                        );
                    }

                    $response1["response_code"] = "200";
                    $response1["response_message"] =
                        "brochure sent sucessfully by mail";
                }
            }
        } else {
            $response1["response_code"] = "500";
            $response1["response_message"] = "Data is null";
        }
        echo json_encode($response1);
        exit();
    }

    public function generatePDF(
        $collegeDetails,
        $courseDetails,
        $HighlightsDetails
    ) {
        // print_r($HighlightsDetails[0]['text']);exit;
        $collegeContent =
            '<h2>College Information</h2>
        <ul>
            <li style="font-weight:bold">' .
            $collegeDetails[0]["title"] .
            "," .
            $collegeDetails[0]["city"] .
            '</li>
            <li>' .
            $collegeDetails[0]["description"] .
            '</li>
        </ul>';
        $coursesContent .= ' <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Duration</th>
                <th>category</th>
                <th>academic category</th>
                <th>sub category</th>

            </tr>
        </thead>
        <tbody>';
        foreach ($courseDetails as $key => $value) {
            // Convert object to array
            $value = (array) $value;
            if (!empty($value["name"])) {
                $coursesContent .=
                    '<tr>
                <td>' .
                    $value["name"] .
                    '</td>
                <td>' .
                    $value["duration"] .
                    ' years</td>
                <td>' .
                    $value["courseCategoryName"] .
                    '</td>
                <td>' .
                    $value["academicCategoryName"] .
                    '</td>
                <td>' .
                    $value["subCategoryName"] .
                    '</td>
            </tr>';
            }
        }

        $coursesContent .= '</tbody>
        </table>';
        foreach ($HighlightsDetails as $key => $value) {
            $value = (array) $value;
            if (count($HighlightsDetails) > 1) {
                // If there are multiple highlights, wrap each one in <li> tags
                $highlightContent .= "<li>" . $value["text"] . "</li>";
            } else {
                // If there's only one highlight, don't use <ul> or <li> tags
                $highlightContent .= $value["text"];
            }
        }
        $template_name = "template/brochure.html";
        $content = file_get_contents($template_name);

        // Replace placeholders with actual content
        $content = str_replace(
            ["VAR_COLLEGE_DATA", "VAR_COURSE_DATA", "VAR_HIGHLIGHT_DATA"],
            [$collegeContent, $coursesContent, $highlightContent],
            $content
        );
        return $content;
    }

    public function savCourseApplication()
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
            $student_name = $data->student_name;
            $email = $data->email;
            $mobile_no = $data->mobile_no;
            $category = $data->category;
            $college = $data->college;
            $course = $data->course;
            $entrance_exam = $data->entrance_exam;
            $rank = $data->rank;
            $score = $data->score;
            $userId = 1;
            $arr = [
                "student_name" => $student_name,
                "email" => $email,
                "mobile_no" => $mobile_no,
                "category" => $category,
                "college" => $college,
                "course" => $course,
                "entrance_exam" => $entrance_exam,
                "rank" => $rank,
                "score" => $score,
            ];
            $application = $this->Common_model->saveCourseApplication($arr);

            if ($application) {
                $sendMailToCustomer = $this->sendMailToCustomer(
                    $student_name,
                    $email
                );
                $logArr = ["application_id" => $application];
                $tableName = "application_log";
                $addLog = $this->Common_model->addLog($logArr, $tableName);

                $clgDtl = $this->College_model->getCollegeDetailsByID($college);
                $Arr = [
                    "user_name" => $userId,
                    "email" => $email,
                    "college" => $clgDtl[0]["id"],
                    "location" => $clgDtl[0]["city"],
                    "latest_activity" => "Application Submitted.",
                ];

                // $Arr = ['user_name'=>$student_name,'email'=>$email,'location'=>'','latest_activity'=>''.$clgDtl[0]['title'].','.$clgDtl[0]['city'].' Application Submitted.'];
                $addUserActivity = $this->Common_model->addUserActivity($Arr);
                $ClgRepArr = [
                    "college" => $college,
                    "no_of_articles_linked" => 0,
                    "no_of_brochures_download" => 0,
                    "no_of_application_submitted" => 1,
                    "no_of_que_asked" => 0,
                    "no_of_answeres" => 0,
                    "no_of_review"=>0,
                ];
                $checkcollegeReport = $this->common_model->checkcollegeReport(
                    $college
                );
                if ($checkcollegeReport > 0) {
                    $updateClgReport = $this->common_model->updateClgReport(
                        $college,
                        $ClgRepArr
                    );
                } else {
                    $saveClgReport = $this->common_model->saveClgReport(
                        $ClgRepArr
                    );
                }
                $response["response_code"] = "200";
                $response["response_message"] =
                    "Thanks for submitting the details.Our counsellor will contact you shortly to provide details.";
                $response["response_data"] = $application;
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

    public function savPredictAdmission()
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
            $student_name = $data->student_name;
            $email = $data->email;
            $mobile_no = $data->mobile_no;
            $category = $data->category;
            $college = $data->college;
            $course = $data->course;
            $entrance_exam = $data->entrance_exam;
            $rank = $data->rank;
            $score = $data->score;
            $userId = 1;
            $arr = [
                "student_name" => $student_name,
                "email" => $email,
                "mobile_no" => $mobile_no,
                "category" => $category,
                "college" => $college,
                "course" => $course,
                "entrance_exam" => $entrance_exam,
                "rank" => $rank,
                "score" => $score,
            ];
            $application = $this->Common_model->savPredictAdmission($arr);

            if ($application) {
                $sendMailToCustomer = $this->sendMailToCustomer(
                    $student_name,
                    $email
                );
                $logArr = ["predict_id" => $application];
                $tableName = "predict_log";
                $addLog = $this->Common_model->addLog($logArr, $tableName);
                $clgDtl = $this->College_model->getCollegeDetailsByID($college);
                $Arr = [
                    "user_name" => $userId,
                    "email" => $email,
                    "college" => $clgDtl[0]["id"],
                    "location" => $clgDtl[0]["city"],
                    "latest_activity" => "Predcited Admission Submitted.",
                ];
                $addUserActivity = $this->Common_model->addUserActivity($Arr);
                // $ClgRepArr = ['college'=>$college,'no_of_articles_linked'=>0,'no_of_brochures_download'=>0,'no_of_application_submitted'=>1,'no_of_que_asked'=>0,'no_of_answeres'=>0];
                // $checkcollegeReport = $this->common_model->checkcollegeReport($college);
                // if($checkcollegeReport > 0)
                // {
                //     $updateClgReport = $this->common_model->updateClgReport($college,$ClgRepArr);

                // }
                // else
                // {
                //    $saveClgReport = $this->common_model->saveClgReport($ClgRepArr);
                // }
                $response["response_code"] = "200";
                $response["response_message"] =
                    "Thanks for submitting the details.Our counsellor will contact you shortly to provide details.";
                $response["response_data"] = $application;
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

    public function getTrendingSpecilization()
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
        $TrendingSpecilization = $this->Common_model->getTrendingSpecilization();

        if ($TrendingSpecilization) {
            $response["response_code"] = "200";
            $response["response_message"] = "Success";
            $response["TrendingSpecilization"] = $TrendingSpecilization;
        } else {
            $response["response_code"] = "400";
            $response["response_message"] = "Failed";
        }

        echo json_encode($response);
        exit();
    }

    public function sendContactMail()
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
            $senderName = $data->name;
            $bccArray = "vaishnavi.b@queenzend.com";
            $toName = "OhCampus Enquiry Team";
            $from = $data->email;
            $senderemail = $data->email;
            $to = "enquiry@ohcampus.com";

            $contactNo = $data->contactNo;
            $subject = $data->subject;
            $message = $data->message;

            $emailMessage =
                '<!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Template</title>
        </head>
        <body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
        
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="https://win.k2key.in/ohcampus/uploads/OhCampusLogo.png" alt="Company Logo" style="max-width: 200px;">
            </div>
            <div style="background-color: #f0f0f0; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div style="background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #333; font-size: 24px; margin-bottom: 20px;">Enquiry from OhCampus.com</h2>
        <p style="color: #666666; font-size: 16px; line-height: 1.6;">Dear ' .
                $toName .
                ',</p>
        <p style="font-size: 16px; color: #666;">
            <strong style="color: #333;">Name:</strong>' .
                $senderName .
                '<br>
            <strong style="color: #333;">Email:</strong>' .
                $senderemail .
                '<br>
            <strong style="color: #333;">Contact No:</strong>' .
                $contactNo .
                '<br>
            <strong style="color: #333;">Subject:</strong>' .
                $subject .
                '<br>
            <strong style="color: #333;">Message:</strong>' .
                $message .
                '<br>
        </p>
    </div>
</div>
        </div>
        
        </body>
        </html>
        ';
            $subject = "New Enquiry - OhCampus";
            $url = "YOUR_EMAIL_URL";

            $headers = [
                "api-key: YOUR_API_KEY",
                "Content-Type: application/json",
            ];
            $custJsonData = [
                "sender" => ["name" => $senderName, "email" => $from],
                "to" => [["name" => $toName, "email" => $to]],
                "subject" => $subject,
                "htmlContent" => $emailMessage,
            ];
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($custJsonData),
                CURLOPT_HTTPHEADER => $headers,
            ]);
            $response1 = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response1);
            if ($res) {
                $response["response_code"] = "200";
                $response["response_message"] =
                    "Your request has been successful submitted...! We will contact you as soon as possible. Thank you.";
            }
        } else {
            $response["response_code"] = "500";
            $response["response_message"] = "data is null";
        }

        echo json_encode($response);
        exit();
    }
}
