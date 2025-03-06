<?php
 $a = check_email($_POST['email'],$_POST['ph_number']);

 if(!$a['isValid']){
     echo json_encode(["isValid" => false, "msg" => $a['msg']]);
     return false;
 }
$host = "localhost";
$dbname = "dbs17gptfgbqba";
$username = "u4hhoiy7gab9q"; // Change if needed
$password = "22g&@1{ofje@"; // Change if needed

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "❌ Database Connection Failed", "error" => $e->getMessage()]));
}

    $stmt = $db->prepare("SELECT COUNT(*) AS total, leads.ph_number as ph_number FROM leads WHERE ph_number = :phone");
    $stmt->bindParam(":phone", $_POST['ph_number'], PDO::PARAM_STR);
    $stmt->execute();

    // Fetch result
    $result = $stmt->fetch(PDO::FETCH_ASSOC); 
    if($result['total'] != 0){
        echo json_encode(["status" => "success", "message" => "Data saved successfully!", "lead_id" => $_POST['lead_id'],"OTP" => $_POST['wp_otp']]);
        return false;
    }if($result['ph_number'] == $_POST['ph_number']){
       
        if(isset($_POST['lead_id']) && $_POST['lead_id'] != ''){
            echo json_encode(["status" => "success", "message" => "Data saved successfully!", "lead_id" => $_POST['lead_id'],"OTP" => $_POST['wp_otp']]);
            return false;
        }

    }

$data = $_POST;
unset($data['a']); 

$leadFields = ["form_type", "source_url", "firstname", "ph_number", "email"];
$leadData = array_intersect_key($data, array_flip($leadFields));

foreach ($leadFields as $field) {
    if (!isset($leadData[$field]) || empty($leadData[$field])) {
        die(json_encode(["status" => "error", "message" => "❌ Missing required field: $field"]));
    }
}

try {
    $stmt = $db->prepare("INSERT INTO leads (form_type, source_url, firstname, ph_number, email) 
                          VALUES (:form_type, :source_url, :firstname, :ph_number, :email)");
    $stmt->execute($leadData);
    $leadId = $db->lastInsertId(); // Get inserted lead ID

  
    $extraFields = array_diff_key($data, array_flip($leadFields));
    unset($extraFields['user_otp'], $extraFields['wp_otp'], $extraFields['lead_id']);

    if (!empty($extraFields)) {
        $stmt = $db->prepare("INSERT INTO lead_details (lead_id, lead_form_key, lead_form_value) 
                              VALUES (:lead_id, :lead_form_key, :lead_form_value)");
        if($_POST['form_type'] == 'landed'){                      
            foreach ($extraFields as $key => $value) {
                if($key == 'like_to_know'){
                    $stmt->execute([
                        ':lead_id' => $leadId,
                        ':lead_form_key' => $key,
                        ':lead_form_value' => implode('| ', $value)
                    ]);
                }else{
                    $stmt->execute([
                        ':lead_id' => $leadId,
                        ':lead_form_key' => $key,
                        ':lead_form_value' => $value
                    ]);
                }
            }
        }else{
            foreach ($extraFields as $key => $value) {
                $stmt->execute([
                    ':lead_id' => $leadId,
                    ':lead_form_key' => $key,
                    ':lead_form_value' => $value
                ]);
            }
        }    
    }
    $otp = rand(100000, 999999);
    $message = 'Your OTP code is: '.$otp;
    send_wp_message('+971551120500', $message);
    // $otp = rand(100000, 999999);
    // $otp_stmt = $db->prepare("INSERT INTO user_otp (lead_id, OTP, is_expired)  VALUES (:lead_id, :otp, :is_expired)");
    // $otp_stmt->execute([
    //     ':lead_id'   => $leadId, 
    //     ':otp'       => $otp,
    //     ':is_expired' => 0
    // ]);


   
    echo json_encode(["status" => "success", "message" => "Data saved successfully!", "lead_id" => $leadId,"OTP" => $otp]);

} catch (PDOException $e) {
    
    echo json_encode(["status" => "error", "message" => "Database Insert Failed", "errorInfo" => $e->getMessage()]);
}
function send_wp_message($client_number, $message) {
    $curl = curl_init();
    $api_key = 'UAK32c243e8-e2ca-417a-ba7a-b3e1ee7b3d4c'; 

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.p.2chat.io/open/whatsapp/send-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array(
            "to_number" => $client_number,
            "from_number" => "+6580832500",
            "text" => $message
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'X-User-API-Key: ' . $api_key
        ),
        CURLOPT_SSL_VERIFYPEER => false
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
    }

    curl_close($curl);
    // return $response;
}

function check_email($email,$ph_number){
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://janicez87.sg-host.com/check_time_email_round.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
        "email":"'.$email.'",
        "ph_number":"'.$ph_number.'"
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    CURLOPT_SSL_VERIFYPEER => false
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}

?>