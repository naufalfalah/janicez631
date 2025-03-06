<?php 
require_once 'db_config.php';


if ($_POST['town'] && !empty($_POST['town'])) {
    
    $town_id = get_town($pdo,$_POST['town']);
    $flat_types = get_flat_types($pdo,$town_id);
    $blks = get_blks($pdo,$town_id);
    $street_names = get_street_names($pdo,$town_id);

    // $flat_type_html = "";
    // foreach ($flat_types as $f => $val) {
    //     $flat_type_html .= '<div class="form-group col-md-4 mb-0">
    //                             <div class="radio">
    //                                 <input id="radio-'.$f.'" name="flat_type" class="gridCheck"
    //                                     type="radio" value="'.$val['flat_type'].'">
    //                                 <label for="radio-'.$f.'" class="radio-label">
    //                                     '.$val['flat_type'].'
    //                                 </label>
    //                             </div>
    //                         </div>';
    // }

    $flat_type_options = [];
    foreach ($flat_types as $f => $val) {
        $flat_type_options[] = [
            'value' => $val['flat_type'],
            'text' => $val['flat_type'],
        ];
    }
 
    $blk_options = [];  
    foreach ($blks as $b => $val) {
        $blk_options[] = [
            'value' => $val['blocks'],
            'text' => $val['blocks'],
        ];
    } 

    $street_name_options = []; 
    foreach ($street_names as $s => $val) {
        $street_name_options[] = [
            'value' => $val['street_names'],
            'text' => $val['street_names'],
        ];
    }

    echo json_encode([
        'flat_types' => $flat_type_options,
        'blks' => $blk_options,
        'street_names' => $street_name_options,
    ]);

    exit();
}


function get_flat_types($pdo,$town_id){ 
    $checkIfExists = $pdo->prepare("SELECT flat_type FROM flat_types WHERE town_id = :town_id ORDER BY flat_type");
    $checkIfExists->bindParam(':town_id', $town_id);
    $checkIfExists->execute();

    $rows = $checkIfExists->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}

function get_blks($pdo,$town_id){ 
    $checkIfExists = $pdo->prepare("SELECT blocks FROM blocks WHERE town_id = :town_id  ORDER BY blocks");
    $checkIfExists->bindParam(':town_id', $town_id);
    $checkIfExists->execute();

    $rows = $checkIfExists->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}

function get_street_names($pdo,$town_id){ 
    $checkIfExists = $pdo->prepare("SELECT street_names FROM street_names WHERE town_id = :town_id ORDER BY street_names");
    $checkIfExists->bindParam(':town_id', $town_id);
    $checkIfExists->execute();

    $rows = $checkIfExists->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}

function get_town($pdo,$town) {  

    $checkIfExists = $pdo->prepare("SELECT id FROM towns WHERE town = :town");
    $checkIfExists->bindParam(':town', $town);
    $checkIfExists->execute();

    $row = $checkIfExists->fetch(PDO::FETCH_ASSOC);

    return $row['id'];
}