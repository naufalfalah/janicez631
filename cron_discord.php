<?php

require_once 'database.php';
require_once 'discord_webhook.php';

$stmt = $pdo->prepare("SELECT * FROM leads WHERE is_verified = 0 AND is_send = 0 LIMIT 1");
$stmt->execute();
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

if ($lead) {
    $leadId = $lead['id'];

    $detailStmt = $pdo->prepare("SELECT lead_form_key, lead_form_value FROM lead_details WHERE lead_id = :lead_id");
    $detailStmt->execute(['lead_id' => $leadId]);
    $leadDetails = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($leadDetails as $detail) {
        if ($detail['lead_form_key'] === 'town') {
            $lead['town'] = $detail['lead_form_value'];
            continue;
        }
        if ($detail['lead_form_key'] === 'street_name') {
            $lead['street_name'] = $detail['lead_form_value'];
            continue;
        }
        if ($detail['lead_form_key'] === 'blk') {
            $lead['blk'] = $detail['lead_form_value'];
            continue;
        }
        if ($detail['lead_form_key'] === 'flat_type') {
            $lead['flat_type'] = $detail['lead_form_value'];
            continue;
        }
        if ($detail['lead_form_key'] === 'sellCheck') {
            $lead['sellCheck'] = $detail['lead_form_value'];
            continue;
        }
        if ($detail['lead_form_key'] === 'floor_number') {
            $lead['floor_number'] = $detail['lead_form_value'];
            continue;
        }
        if ($detail['lead_form_key'] === 'unit_number') {
            $lead['unit_number'] = $detail['lead_form_value'];
            continue;
        }
        if ($detail['lead_form_key'] === 'wp_otp') {
            $lead['wp_otp'] = $detail['lead_form_value'];
            continue;
        }
    }
    
    if (sendLeadToDiscord($lead)) {
        $updateStmt = $pdo->prepare("UPDATE leads SET is_send = 1 WHERE id = :id");
        $updateStmt->execute(['id' => $leadId]);

        echo "Lead $leadId has been sent.\n";
    }
}
