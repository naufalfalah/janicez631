<?php
// print_r($_POST); die();
// Database Connection
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
    // $stmt = $db->prepare("INSERT INTO leads (form_type, source_url, firstname, ph_number, email) 
    //                       VALUES (:form_type, :source_url, :firstname, :ph_number, :email)");
    // $stmt->execute($leadData);
    // $leadId = $db->lastInsertId(); // Get inserted lead ID

  
    // $extraFields = array_diff_key($data, array_flip($leadFields));

    // if (!empty($extraFields)) {
    //     $stmt = $db->prepare("INSERT INTO lead_details (lead_id, lead_form_key, lead_form_value) 
    //                           VALUES (:lead_id, :lead_form_key, :lead_form_value)");
    //     if($_POST['form_type'] == 'landed'){                      
    //         foreach ($extraFields as $key => $value) {
    //             if($key == 'like_to_know'){
    //                 $stmt->execute([
    //                     ':lead_id' => $leadId,
    //                     ':lead_form_key' => $key,
    //                     ':lead_form_value' => implode('| ', $value)
    //                 ]);
    //             }else{
    //                 $stmt->execute([
    //                     ':lead_id' => $leadId,
    //                     ':lead_form_key' => $key,
    //                     ':lead_form_value' => $value
    //                 ]);
    //             }
    //         }
    //     }else{
    //         foreach ($extraFields as $key => $value) {
    //             $stmt->execute([
    //                 ':lead_id' => $leadId,
    //                 ':lead_form_key' => $key,
    //                 ':lead_form_value' => $value
    //             ]);
    //         }
    //     }    
    // }

    // $otp = rand(100000, 999999);
    // $otp_stmt = $db->prepare("INSERT INTO user_otp (lead_id, OTP, is_expired)  VALUES (:lead_id, :otp, :is_expired)");
    // $otp_stmt->execute([
    //     ':lead_id'   => $leadId, 
    //     ':otp'       => $otp,
    //     ':is_expired' => 0
    // ]);


   
    echo json_encode(["status" => "success", "message" => "Data saved successfully!", "lead_id" => $_POST['lead_id']]);

} catch (PDOException $e) {
    
    echo json_encode(["status" => "error", "message" => "Database Insert Failed", "errorInfo" => $e->getMessage()]);
}

// print_r($_POST); die();
// echo json_encode($_POST); 

$Project = "";
$ProjectData = "";
$ZapierURL = "";
$commonData = array();
$additional_data = array();

if (isset($_POST)) {

    $commonData = array(
        "name" => $_POST['firstname'],
        "mobile_number" => $_POST['ph_number'],
        "email" => $_POST['email'],
        "source_url" => 'https://launchgovtest.homes/',
    );

    if ($_POST['form_type'] == 'condo') {
        $additional_data = array(
            array(
                "key" => "Project",
                "value" => "Condo " . $_POST['condo_project_name']
            ),
            array(
                "key" => "Blk",
                "value" => $_POST['blk']
            ),
            array(
                "key" => "Looking to sell your property",
                "value" => $_POST['sellCheck']
            ),
            array(
                "key" => "Floor - Unit number",
                "value" => $_POST['floor_number'] ." - ". $_POST['unit_number']
            )
        );
    } elseif ($_POST['form_type'] == 'landed') {
        $additional_data = array(
            array(
                "key" => "Project",
                "value" => "Landed"
            ),
            array(
                "key" => "Landed Street",
                "value" => $_POST['landed_street']
            ),
            array(
                "key" => "SQFT",
                "value" => $_POST['sqft']
            ),
            array(
                "key" => "Like to Know",
                "value" => implode(" | ", $_POST['like_to_know'])
            ),
            array(
                "key" => "Plans",
                "value" => $_POST['your_plans']
            )
        );
    } elseif ($_POST['form_type'] == 'hdb') {
        $additional_data = array(
            array(
                "key" => "Project",
                "value" => "HDB"
            ),
            array(
                "key" => "Town",
                "value" => $_POST['town']
            ),
            array(
                "key" => "Street Name",
                "value" => $_POST['street_name']
            ),
            array(
                "key" => "Blk",
                "value" => $_POST['blk']
            ),
            array(
                "key" => "HDB Flat Type",
                "value" => $_POST['flat_type']
            ),
            array(
                "key" => "Looking to sell your property",
                "value" => $_POST['sellCheck']
            ),
            array(
                "key" => "Floor - Unit number",
                "value" => $_POST['floor_number'] ." - ".$_POST['unit_number']
            )
        );
    }

    $commonData['additional_data'] = $additional_data;
    $LeadManagement = $commonData;

    // JSON encode the lead data
    $jsonData = json_encode($LeadManagement);

    // Check for potential junk content
    $check_junk = checkJunk($jsonData);

    //check dnc via phone or email
    $check_dnc = check_dnc($_POST['email'],$_POST['ph_number']);
    
    // Fetch the user's IP address
    $ip_address = fetchIp();

    // Prepare webhook data
    $webhook_data = array(
        'client_id' => null,
        'project_id' => null,
        'ip_address' => $ip_address,
        'is_verified' => 0
    );
    if($_POST['wp_otp'] != '' && $_POST['wp_otp'] == $_POST['user_otp']){
        $LeadManagement["additional_data"][] = [
            "key" => "Whatsapp Verified",
            "value" => $response["lead"]["is_whatsapp_verified"] = "Yes"
        ];
    }else{
        $LeadManagement["additional_data"][] = [
            "key" => "Whatsapp Verified",
            "value" => $response["lead"]["is_whatsapp_verified"] = "No"
        ];
    }
    
    // Determine status based on junk content
    if (isset($check_junk['Terms']) && !empty($check_junk['Terms']) && count($check_junk['Terms']) > 0) {
        $webhook_data['status'] = 'junk';
        $webhook_data['is_send_discord'] = 0;
    }if($check_dnc['status']){
        $webhook_data['status'] = 'DNC Registry';
        $webhook_data['is_send_discord'] = 0;
    } else {
        $webhook_data['status'] = 'clear';
        $webhook_data['is_send_discord'] = 1;
        // Assuming sendFrequencyLead() is defined elsewhere
        sendFrequencyLead($LeadManagement);
        $_SESSION['lead_sent'] = true;
    }

    // Merge $_POST data with webhook data
    $webhook_data = array_merge($webhook_data, $_POST); 
    // print_r($webhook_data); die();
    // Send data to the endpoint
    sendData($webhook_data);

    // $message = 'Your OTP code is: '.$otp;
    // send_wp_message('+923123686399', $message);

    
}


// Function to send data via cURL
function sendData($data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://janicez87.sg-host.com/endpoint.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic am9tZWpvdXJuZXl3ZWJzaXRlQGdtYWlsLmNvbTpQQCQkd29yZDA5MDIxOGxlYWRzISM='
        ),
        CURLOPT_SSL_VERIFYPEER => false
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    // return $response;
}

// Function to fetch user's IP address
function fetchIp()
{
    $url = "https://api.ipify.org/?format=json";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return false;
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if ($data !== null) {
        return $data['ip'];
    } else {
        return false;
    }
}

// Function to check for junk content
function checkJunk($data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://jomejourney.cognitiveservices.azure.com/contentmoderator/moderate/v1.0/ProcessText/Screen',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: text/plain',
            'Ocp-Apim-Subscription-Key: 453fe3c404554800bc2c22d7ef681542'
        ),
        CURLOPT_SSL_VERIFYPEER => false
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

// Function to send frequency lead
function sendFrequencyLead($data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://roundrobin.datapoco.ai/api/lead_frequency/add_lead',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode('Client Management Portal:123456')
        ),
        CURLOPT_SSL_VERIFYPEER => false
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function check_dnc($email,$phone){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://janicez87.sg-host.com/check_dnc.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "email": "'.$email.'",
            "ph_number": "'.$phone.'"
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
    return $response;
}


?>
