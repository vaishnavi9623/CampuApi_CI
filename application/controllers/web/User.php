<?php

/**
 * User Controller
 *
 * @category   Controllers
 * @package    Web
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    30 JAN 2024
 *
 * Class User handles all the operations related to displaying list, creating User, update, and delete.
 */

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

error_reporting(E_ALL);
ini_set("display_errors", 1);
class User extends CI_Controller
{
    /*** Constructor ** Loads necessary libraries, helpers, and models for the User controller.*/
    public function __construct()
    {
        parent::__construct();
        $this->load->model("web/User_model", "", true);
        $this->load->library("Utility");
        $this->load->library("session");
    }
    
    /**
     * Create User
     *
     * This function is responsible for creating a new user.
     */
    public function createUser()
    {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
        $this->session->userdata("start");
        if ($data) {
            $email = $data->email;
            $password = md5($data->password);
            $username = isset($data->name) ? $data->name : "";

            $userExist = $this->User_model->checkUserExist($email);
            if ($userExist == true) {
                $response["response_code"] = "300";
                $response["response_message"] =
                    "This user already exists! Please try another or log in.";
            } else {
                $result = $this->User_model->createUser(
                    $email,
                    $password,
                    $username
                );
                if ($result) {
                    $otp = rand(100000, 999999);
                    $Arr = ["otp" => $otp];
                    $updateOTP = $this->User_model->updateOTP($Arr, $result);
                    $sendOTP = $this->sendOTPMailToCustomer(
                        $username,
                        $otp,
                        $email
                    );
                    $response["response_code"] = "200";
                    $response["response_message"] =
                        "The OTP has been sent via email. Please check.";
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
        exit();
    }

    /**
     * Send OTP Mail to Customer
     *
     * This function sends the OTP mail to the user for verification.
     */
    public function sendOTPMailToCustomer($username, $otp, $email)
    {
        $senderName = "OhCampus Team";
        $bccArray = "";
        $toName = $username;
        $from = "enquiryteam@ohcampus.com";
        $to = $email;
        $emailMessage =
            '<!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>OTP Email Template</title>
        </head>
        <body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
        
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="https://win.k2key.in/ohcampus/uploads/OhCampusLogo.png" alt="Company Logo" style="max-width: 200px;">
            </div>
            <div style="padding: 20px; background-color: #f0f0f0; border-radius: 5px;">
                <h2 style="color: #333333; margin-bottom: 10px;">OTP for Verification</h2>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">Dear ' .
            $username .
            ',</p>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">Your One-Time Password (OTP - valid for 5 MIN) for verification is: <strong>' .
            $otp .
            '</strong>. Please use this code to proceed with your action.</p>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">Please note that this OTP is valid for a single use and should not be shared with anyone.</p>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">If you did not request this OTP, please ignore this email or contact us immediately.</p>
                <p style="color: #666666; font-size: 16px; line-height: 1.6;">Best regards,<br>OhCampus IT Team<br>OhCampus<br><img src="https://win.k2key.in/ohcampus/uploads/OhCampusLogo.png" alt="Company Logo" style="max-width: 100px;">
                </div>
        </div>
        
        </body>
        </html>
        
        ';
        $subject = "OTP for Verification - OhCampus";
        $url = "https://api.sendinblue.com/v3/smtp/email";

        $headers = [
            "api-key: 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",
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

    /**
     * Verify OTP
     *
     * This function verifies the OTP entered by the user.
     */
    public function verifyOTP()
    {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->input->server("REQUEST_METHOD") == "OPTIONS") {
            $data["status"] = "ok";
            echo json_encode($data);
            exit();
        }
        if ($data) {
            $email = $data->email;
            $OTP = $data->Otp;
            $getOtpdata = $this->User_model->getOtpdata($email);
            if (
                !empty($getOtpdata[0]->otp) &&
                !empty($getOtpdata[0]->otp_timestamp)
            ) {
                $expiry_time = 5 * 60; // 5 minutes (in seconds)
                $current_time = time();
                $otp_timestamp_unix = strtotime($getOtpdata[0]->otp_timestamp);

                if ($current_time - $otp_timestamp_unix <= $expiry_time) {
                    if ($OTP == $getOtpdata[0]->otp) {
                        $response["response_code"] = "200";
                        $response["response_message"] =
                            "Your account has been created. Please sign in.";
                    } else {
                        $response["response_code"] = "400";
                        $response["response_message"] =
                            "Invalid OTP! Please try again.";
                    }
                } else {
                    $response["response_code"] = "300";
                    $response["response_message"] =
                        "OTP has been expired! Please try again.";
                }
            } else {
                $response["response_code"] = "600";
                $response["response_message"] = "OTP not found.";
            }
        } else {
            $response["response_code"] = "500";
            $response["response_message"] = "Data is null.";
        }
        echo json_encode($response);
        exit();
    }
}
