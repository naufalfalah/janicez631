<?php
include('db_config.php');
$otp = $_POST['otp1'].$_POST['otp2'].$_POST['otp3'].$_POST['otp4'].$_POST['otp5'].$_POST['otp6'];
$lead_id = $_POST['lead_id'];

$sql = "SELECT * FROM user_otp 
        WHERE lead_id = :lead_id 
        AND otp = :otp 
        AND is_expired = 0 
        ORDER BY created_at DESC 
        LIMIT 1"; 

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':lead_id', $lead_id, PDO::PARAM_INT);
$stmt->bindParam(':otp', $otp, PDO::PARAM_INT);
$stmt->execute();

$otp_data = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json'); // JSON Response Type Set

$sql_lead = "SELECT * FROM leads WHERE id = :lead_id";
$stmt = $pdo->prepare($sql_lead);
$stmt->bindParam(':lead_id', $lead_id, PDO::PARAM_INT);
$stmt->execute();
$lead = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$lead) {
    die("No lead found with ID: $lead_id");
}


$sql_details = "SELECT * FROM lead_details WHERE lead_id = :lead_id";
$stmt = $pdo->prepare($sql_details);
$stmt->bindParam(':lead_id', $lead_id, PDO::PARAM_INT);
$stmt->execute();
$lead_details = $stmt->fetchAll(PDO::FETCH_ASSOC);


$response = [
    'lead' => $lead,  // Lead ka single record
    'lead_details' => $lead_details // Lead details ka array
];

// Key Mapping
$keyMappings = [
    "form_type" => "Project",
    "town" => "Town",
    "street_name" => "Street Name",
    "blk" => "Blk",
    "flat_type" => "HDB Flat Type",
    "sellCheck" => "Looking to sell your property",
];

// Final Output Structure
$formattedArray = [
    "name" => $response["lead"]["firstname"],
    "mobile_number" => $response["lead"]["ph_number"],
    "email" => $response["lead"]["email"],
    "source_url" => $response["lead"]["source_url"],
    "additional_data" => []
];

// Processing lead_details
$floorNumber = "";
$unitNumber = "";

foreach ($response["lead_details"] as $detail) {
    $key = $detail["lead_form_key"];
    $value = $detail["lead_form_value"];

    if ($key === "floor_number") {
        $floorNumber = $value;
    } elseif ($key === "unit_number") {
        $unitNumber = $value;
    } else {
        $formattedArray["additional_data"][] = [
            "key" => $keyMappings[$key] ?? ucfirst(str_replace("_", " ", $key)),
            "value" => $value
        ];
    }
}

// Add "Floor - Unit number" field
if ($floorNumber && $unitNumber) {
    $formattedArray["additional_data"][] = [
        "key" => "Floor - Unit number",
        "value" => "$floorNumber - $unitNumber"
    ];
}

$formattedArray["additional_data"][] = [
    "key" => "Whatsapp Verified",
    "value" => $response["lead"]["is_whatsapp_verified"] = "Yes"
];

if ($otp_data) {
    sendFrequencyLead($formattedArray); 
    echo json_encode([
        "status" => true,
        "msg" => "OTP verified successfully.",
        "data" => $otp_data // OTP details
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        "status" => false,
        "msg" => "Invalid OTP."
    ], JSON_PRETTY_PRINT);
}


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