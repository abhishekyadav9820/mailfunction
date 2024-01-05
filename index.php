<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader

header('Access-Control-Allow-Origin: *');

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';



$servername = "localhost";
$username = ""; 
$password = "";


$accUserName = $_POST['accUserName'];
$leadName =  $_POST['leadName'];
$leadMobile =$_POST['leadMobile'];
$leadEmail = $_POST['leadEmail'];
$leadMessage = $_POST['leadMessage'];
$mailBody = $_POST['mailBody'];




try {
    $conn = new PDO("mysql:host=$servername;dbname=mail_functions", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select account_id from accounts based on account_username
    $query = "SELECT account_id, account_username FROM accounts WHERE account_username = ?";
    $accountStm = $conn->prepare($query);
    $accountStm->bindParam(1, $accUserName, PDO::PARAM_STR, 255);

    if ($accountStm->execute()) {
        $accountResult = $accountStm->fetchAll();

        if ($accountStm->rowCount() > 0) {
            foreach ($accountResult as $accountResults) {
                $accountId = $accountResults['account_id'];

                // Insert into account_leads table
                $queryLeads = "INSERT INTO account_leads (account_lead_name, account_lead_mobile, account_lead_email, account_lead_message, account_id_reff, added_date)
                               VALUES (:names, :mobile, :email, :messages, :account_id, current_timestamp())";

                $accountLeadsStm = $conn->prepare($queryLeads);

                // Set parameters for the account_leads table
                $accountLeadsStm->bindParam(':names', $leadName, PDO::PARAM_STR);
                $accountLeadsStm->bindParam(':mobile', $leadMobile, PDO::PARAM_STR);
                $accountLeadsStm->bindParam(':email', $leadEmail, PDO::PARAM_STR);
                $accountLeadsStm->bindParam(':messages', $leadMessage, PDO::PARAM_STR);
                $accountLeadsStm->bindParam(':account_id', $accountId, PDO::PARAM_INT);

                // Execute the account_leads statement
               if($accountLeadsStm->execute()){
                $subject = 'Lead Mail';
                sendEmail($leadEmail, $subject, $mailBody);
               }else{
                echo $accountLeadsStm->$error;
               }
                echo "Lead added successfully!";
            }
        } else {
            echo "Username Not found...!";
        }
    } else {
        echo $accountStm->errorInfo();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}






function sendEmail($to, $subject, $body) {
    // Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                    // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';               // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                           // Enable SMTP authentication
        $mail->Username   = 'example@gmail.com';           // SMTP username
        $mail->Password   = 'genrate key';          // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Enable implicit TLS encryption
        $mail->Port       = 465;                            // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        // Recipients
        $mail->setFrom('from@gmail.com',"mailler name");
        $mail->addAddress($to);                             // Add a recipient

        //  $mail->addReplyTo('from@gamil.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name


        // Content
        $mail->isHTML(true);                                // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);                 // Convert HTML to plain text for non-HTML mail clients

        // Send email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
