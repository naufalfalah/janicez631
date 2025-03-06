<?php
include('db_config.php');
$lead_id = $_GET['lead_id'];
// Pehle lead ka data fetch karein
$sql_lead = "SELECT * FROM leads WHERE id = :lead_id";
$stmt = $pdo->prepare($sql_lead);
$stmt->bindParam(':lead_id', $lead_id, PDO::PARAM_INT);
$stmt->execute();
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

// Agar lead nahi mili to return karein
if (!$lead) {
    die("No lead found with ID: $lead_id");
}

// Ab lead_details ka data fetch karein
$sql_details = "SELECT * FROM lead_details WHERE lead_id = :lead_id";
$stmt = $pdo->prepare($sql_details);
$stmt->bindParam(':lead_id', $lead_id, PDO::PARAM_INT);
$stmt->execute();
$lead_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Final formatted output
$response = [
    'lead' => $lead,  // Lead ka single record
    'lead_details' => $lead_details // Lead details ka array
];
//  echo '<pre>';
// print_r($response); die();
$db = mysqli_connect("localhost", "u4hhoiy7gab9q", "22g&@1{ofje@", "dbs17gptfgbqba");
function get_town_blocks($pdo, $town)
    {
    
        $townQuery = "SELECT id FROM towns WHERE town = :town";
    
        $townStmt =  $pdo->prepare($townQuery);
        $townStmt->bindParam(':town', $town);
        $townStmt->execute();
        $townResult = $townStmt->fetch(PDO::FETCH_ASSOC);
    
        $townId = $townResult['id'];
    
        // Prepare the SQL query to search for n-1 and n+1 values
        $query = "SELECT blocks FROM blocks WHERE town_id = :townId";
    
        // Bind parameters and execute the query
        $statement = $pdo->prepare($query);
        $statement->bindParam(':townId', $townId);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        if (!empty($result)) {
    
            $data_arr = [];
    
            if (!empty($result)) {
                foreach ($result as $val) {
                    $data_arr[] = $val['blocks'];
                }
            }
    
            return $data_arr;
        }
    
        return null;
    }
if($response['lead']['form_type'] == 'hdb'){ 
    $base_url = "https://data.gov.sg/api/action/datastore_search";
    $resource_id = "f1765b54-a209-4718-8d38-a39237f502b3";
    
    $is_submit = 0;
    $search_count = 1;
    $town_blocks = '';
    $town = '';
    $flat_type = '';
    $street_name = '';
    
    $first_request_url = '';
    $second_request_url = '';
    
    if (!empty($response['lead_details']['2']['lead_form_value'])) {
    
        $blocks = get_town_blocks($pdo, $response['lead_details']['0']['lead_form_value']);
    
        if (!empty($blocks)) {
            $town_blocks = json_encode($blocks);
        }
    }
    
    if (isset($response['lead_details']['0']['lead_form_value'])) {
    
        $dateRange = [];
    
        // Get the current year and month
        $currentYear = date('Y');
        $currentMonth = date('m');
        for ($i = 11; $i >= 0; $i--) {
            $month = $currentMonth - $i;
            $year = $currentYear;
    
            if ($month <= 0) {
                $year = $currentYear - 1;
                $month = 12 + $month;
            }
    
            $paddedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
            $dateRange[] = $year . '-' . $paddedMonth;
        }
    
        $is_submit = 1;
    
        $town = strtoupper($response['lead_details']['0']['lead_form_value']);
    
        $filters = [
            "month" => $dateRange,
            "town" => strtoupper($response['lead_details']['0']['lead_form_value']),
        ];
    
        if (isset($response['lead_details']['3']['lead_form_value']) && !empty($response['lead_details']['3']['lead_form_value'])) {
            $filters['flat_type'] = strtoupper($response['lead_details']['3']['lead_form_value']);
            $flat_type = strtoupper($response['lead_details']['3']['lead_form_value']);
        }
    
        if (!empty($response['lead_details']['1']['lead_form_value'])) {
            $filters['street_name'] = strtoupper($response['lead_details']['1']['lead_form_value']);
            $street_name = strtoupper($response['lead_details']['1']['lead_form_value']);
        }
    
        // Set up the filters
        $filters = json_encode($filters);
    
    
        $queryParams = [
            'resource_id' => $resource_id,
            "limit" => 12000,
            'filters' => $filters,
            'sort' => 'month desc'
        ];
    
        $first_request_url = $base_url . '?' . http_build_query($queryParams);
    
    
        $filters = [
            "month" => $dateRange,
            "town" => strtoupper($response['lead_details']['0']['lead_form_value']),
        ];
    
        if (isset($response['lead_details']['3']['lead_form_value']) && !empty($response['lead_details']['3']['lead_form_value'])) {
            $filters['flat_type'] = strtoupper($response['lead_details']['3']['lead_form_value']);
        }
    
        if (!empty($response['lead_details']['2']['lead_form_value'])) {
    
            $blocks = get_town_blocks($pdo, $response['lead_details']['0']['lead_form_value']);
    
            if (!empty($blocks)) {
                $filters['block'] = $blocks;
            }
        }
    
        // Set up the filters
        $filters = json_encode($filters);
    
        $queryParams = [
            'resource_id' => $resource_id,
            "limit" => 12000,
            'filters' => $filters,
            'sort' => 'month desc'
        ];
    
        $second_request_url = $base_url . '?' . http_build_query($queryParams);
    }
    
        
}

if($response['lead']['form_type'] == 'condo'){
    $project_id = $response['lead_details'][1]['lead_form_value']; 
    $sales_date_q = mysqli_query($db, "SELECT DISTINCT(project_transactions.contractDate) FROM `project_transactions` LEFT JOIN projects ON projects.id = project_transactions.project_id where project_transactions.project_id = '$project_id' ORDER BY contractDate asc");
 
    $sales_date_option = '<option value="">Any Sales Year</option>'; 
    

    $sales_data_array = [];
    $j = 0;
    while ($row = mysqli_fetch_assoc($sales_date_q)) {
    
        $sales_data_array[$j] = date("Y", strtotime($row['contractDate']));
        $j++;
    }

    $uniqueValues = array_flip(array_flip($sales_data_array));
    $sales_data_array = array_intersect_key($sales_data_array, $uniqueValues);

    asort($sales_data_array);

    foreach ($sales_data_array as $key => $value) { 
        if (isset($_GET['sales_dates']) && !empty($_GET['sales_dates']) && $_GET['sales_dates'] ==  $key) {
            $sales_date_option .= '<option value="' . $value . '" selected>' . $value . '</option>';
        }

        $sales_date_option .= '<option value="' . $value . '" >' . $value . '</option>'; 
    } 
    if (isset($response['lead_details'][1]['lead_form_value'])) {
        $query = "SELECT projects.project, projects.street, projects.marketSegment, project_transactions.* FROM project_transactions
        JOIN projects on projects.id = project_transactions.project_id ";
        $searchQuery = " ";
        if (isset($response['lead_details'][1]['lead_form_value']) && !empty($response['lead_details'][1]['lead_form_value'])) {
            $searchQuery .= " and (project_transactions.project_id = '".$response['lead_details'][1]['lead_form_value']."') ";
        } 
    
        if (isset($_GET['type_of_sale']) && !empty($_GET['type_of_sale'])) {
            $searchQuery .= " and (project_transactions.typeOfSale = ".$_GET['type_of_sale']." ) ";
        }
        
        if (isset($_GET['floor_range']) && !empty($_GET['floor_range'])) {
    
            $floor_rage = $_GET['floor_range']; 
            if($floor_rage === '1-10'){
                $searchQuery .= " and (project_transactions.floorRange BETWEEN 1 and 10) ";
            }elseif($floor_rage === '10-20'){
                $searchQuery .= " and (project_transactions.floorRange BETWEEN 10 and 20) ";
            }elseif($floor_rage === '20 and above'){
                $searchQuery .= " and (project_transactions.floorRange >= 20) ";
            }
        }
        
        if (isset($_GET['sales_dates']) && !empty($_GET['sales_dates'])) { 
    
            $searchQuery .= " and (YEAR(project_transactions.contractDate) = '".$_GET['sales_dates']."' ) ";
    
        }
        
        if (isset($_GET['area_sqft']) && !empty($_GET['area_sqft'])) {
            $area_sqft = $_GET['area_sqft'];
            $in_array = ['400-600','600-700','700-1300'];
            if (in_array($area_sqft,$in_array)) {
                 
                $area_range = explode('-', $_GET['area_sqft']);
                $min_area =  ceil(number_format(($area_range[0] / 10.76391042),2,'.',''));
                $max_area =  ceil(number_format(($area_range[1] / 10.76391042),2,'.',''));
            
                $searchQuery .= " and (project_transactions.area BETWEEN $min_area and $max_area) ";
            }else{
                $max_area =  ceil(number_format((1300 / 10.76391042),2,'.',''));
                $searchQuery .= " and (project_transactions.area >= $max_area) ";
            }
        }
    
        // if ($searchValue != '') {
        //     $searchQuery = " AND (project_transactions.area LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.floorRange LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.contractDate LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.typeOfSale LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.price LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.propertyType LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.district LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.typeOfArea LIKE '%" . $searchValue . "%' OR 
        //         project_transactions.tenure LIKE '%" . $searchValue . "%' OR 
        //         projects.project LIKE '%" . $searchValue . "%' OR 
        //         projects.street LIKE '%" . $searchValue . "%' )";
        
        //     $sel = mysqli_query($db, "SELECT COUNT(*) AS allcount FROM project_transactions JOIN projects ON projects.id = project_transactions.project_id WHERE 1 " . $searchQuery); 
        //     // echo $query . " WHERE 1 " . $searchQuery;
        //     // die();
        // }
        //  else {
            // $searchQuery = ""; // Set an empty string when $searchValue is empty
        
            $sel = mysqli_query($db, "SELECT COUNT(*) AS allcount FROM project_transactions JOIN projects ON projects.id = project_transactions.project_id WHERE 1 " . $searchQuery);
        // }
         
        
        $records = mysqli_fetch_assoc($sel);
        
        
        
        $totalRecordwithFilter = $records['allcount'];
        $totalRecords = $records['allcount'];
        
        // echo $rowperpage;
        // die();
        
        ## Fetch records
        $empQuery = $query." WHERE 1 ".$searchQuery." order by  project_transactions.contractDate asc ";
    
        
        // echo $empQuery;
        // die();
        
        $empRecords = mysqli_query($db, $empQuery);
        
        $data = array();
        
        while ($row = mysqli_fetch_assoc($empRecords)) {
        
            // $month = substr($row['contractDate'], 0, 2);
            // $year = "20" . substr($row['contractDate'], 2, 2);
            // $dateString = $year . "-" . $month . "-01";
            // $formattedDate = date("Y F", strtotime($dateString));
    
            $formattedDate = date("Y F", strtotime($row['contractDate']));
        
            if ($row['typeOfSale'] == '1') {
                $typeOfSale = 'New Sale';
            }
        
            if ($row['typeOfSale'] == '2') {
                $typeOfSale = 'Sub Sale';
            }
        
            if ($row['typeOfSale'] == '3') {
                $typeOfSale = 'Resale';
            }
        
            $data[] = array( 
               // "contractDate"=> date('M Y',strtotime($row['contractDate'])),
                "contractDate"=> $formattedDate,
                "project"=>$row['project'],
                "street"=>$row['street'],
                "district"=>$row['district'],
                "marketSegment"=>$row['marketSegment'],
                "tenure"=>$row['tenure'],
                "typeOfSale"=> $typeOfSale ?? '-',
                "floorRange"=>$row['floorRange'],
                // "area"=>$row['area'],
                "area"=> ceil(number_format(($row['area'] * 10.76391042),2,'.','')),
                "price"=>number_format($row['price']),
            );
        }
        
        ## Response
        // $response = array(
        // //   "draw" => intval($draw),
        //   "iTotalRecords" => $totalRecords,
        //   "iTotalDisplayRecords" => $totalRecordwithFilter,
        //   "aaData" => $data
        // );
        
        // Data processing functions
        function analyzePropertyData($properties) {
            // Initialize the output array
            $output = [
                'highestPrice' => '',
                'lowestPrice' => '',
                'estimatedPrice' => []
            ];
            
            // Data processing functions
            function formatPrice($price) {
                return number_format($price, 0, '.', ',');
            }
            
            // Estimates price based on area and floor range
            function estimatePrice($area, $floorRange, $floorRanges, $avgPricePerSqFt) {
                if (isset($floorRanges[$floorRange])) {
                    return formatPrice($area * $floorRanges[$floorRange]['avgPricePerSqFt']);
                } else {
                    // Fallback to overall average
                    return formatPrice($area * $avgPricePerSqFt);
                }
            }
            
            // Process the property data
            if ($properties) {
                // Convert price strings to numeric values and calculate price per square foot
                foreach ($properties as &$property) {
                    $property['numericPrice'] = (float) str_replace(',', '', $property['price']);
                    $property['pricePerSqFt'] = $property['numericPrice'] / $property['area'];
                }
                
                // Sort by highest price
                $highestPrice = $properties;
                usort($highestPrice, function($a, $b) {
                    return $b['numericPrice'] - $a['numericPrice'];
                });
                
                // Sort by lowest price
                $lowestPrice = $properties;
                usort($lowestPrice, function($a, $b) {
                    return $a['numericPrice'] - $b['numericPrice'];
                });
                
                // Calculate overall average price per square foot
                $totalPricePerSqFt = 0;
                foreach ($properties as $property) {
                    $totalPricePerSqFt += $property['pricePerSqFt'];
                }
                $avgPricePerSqFt = $totalPricePerSqFt / count($properties);
                
                // Group by floor range and calculate average price per sq ft for each range
                $floorRanges = array();
                foreach ($properties as $property) {
                    $floorRange = $property['floorRange'];
                    if (!isset($floorRanges[$floorRange])) {
                        $floorRanges[$floorRange] = array(
                            'totalPricePerSqFt' => 0,
                            'count' => 0
                        );
                    }
                    $floorRanges[$floorRange]['totalPricePerSqFt'] += $property['pricePerSqFt'];
                    $floorRanges[$floorRange]['count']++;
                }
                
                foreach ($floorRanges as $range => $data) {
                    $floorRanges[$range]['avgPricePerSqFt'] = $data['totalPricePerSqFt'] / $data['count'];
                }
                
                // Get unique areas for estimation
                $uniqueAreas = array();
                foreach ($properties as $property) {
                    if (!in_array($property['area'], $uniqueAreas)) {
                        $uniqueAreas[] = $property['area'];
                    }
                }
                sort($uniqueAreas);
                
                // Set the highest and lowest prices
                $output['highestPrice'] = $highestPrice[0]['price'];
                $output['lowestPrice'] = $lowestPrice[0]['price'];
                
                // Generate estimated prices for each floor range and area
                foreach ($floorRanges as $floorRange => $data) {
                    foreach ($uniqueAreas as $area) {
                        $output['estimatedPrice'][] = [
                            'floorRange' => $floorRange,
                            'area' => $area,
                            'price' => estimatePrice($area, $floorRange, $floorRanges, $avgPricePerSqFt)
                        ];
                    }
                }
            } else {
                $output['error'] = "Error: Could not process property data.";
            }
            
            return $output;
        }

        $properties = json_decode(json_encode($data), true);
        $results = analyzePropertyData($properties);
        $highestPrice = "$" . $results['highestPrice'];
        $lowestPrice = "$" . $results['lowestPrice'];
        $estimatedSellingPrice = "$" . $results['estimatedPrice'][0]['price'];
    }    
}

if($response['lead']['form_type'] == 'landed'){
    $project_id = $response['lead_details'][1]['lead_form_value']; 
    $sales_date_q = mysqli_query($db, "SELECT DISTINCT(project_transactions.contractDate) FROM `project_transactions` LEFT JOIN projects ON projects.id = project_transactions.project_id where project_transactions.project_id = '$project_id' ORDER BY contractDate asc");
     
    $sales_date_option = '<option value="">Any Sales Year</option>'; 
     
    
    $sales_data_array = [];
    $j = 0;
    while ($row = mysqli_fetch_assoc($sales_date_q)) {
     
        $sales_data_array[$j] = date("Y", strtotime($row['contractDate']));
        $j++;
    }
    
    $uniqueValues = array_flip(array_flip($sales_data_array));
    $sales_data_array = array_intersect_key($sales_data_array, $uniqueValues);
    
    asort($sales_data_array);
    
    foreach ($sales_data_array as $key => $value) { 
        if (isset($_GET['sales_dates']) && !empty($_GET['sales_dates']) && $_GET['sales_dates'] ==  $key) {
            $sales_date_option .= '<option value="' . $value . '" selected>' . $value . '</option>';
        }
    
        $sales_date_option .= '<option value="' . $value . '" >' . $value . '</option>'; 
    } 

    $query = "SELECT projects.project, projects.street, projects.marketSegment, project_transactions.* FROM project_transactions
    JOIN projects on projects.id = project_transactions.project_id ";
    $searchQuery = " ";
    if (isset($response['lead_details'][1]['lead_form_value']) && !empty($response['lead_details'][1]['lead_form_value'])) {
        $searchQuery .= " and (project_transactions.project_id = '".$response['lead_details'][1]['lead_form_value']."') ";
    } 
    $sel = mysqli_query($db, "SELECT COUNT(*) AS allcount FROM project_transactions JOIN projects ON projects.id = project_transactions.project_id WHERE 1 " . $searchQuery);
    $records = mysqli_fetch_assoc($sel);
    
    
    
    $totalRecordwithFilter = $records['allcount'];
    $totalRecords = $records['allcount'];
    
    $empQuery = $query." WHERE 1 ".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();
    
    while ($row = mysqli_fetch_assoc($empRecords)) {
    
        // $month = substr($row['contractDate'], 0, 2);
        // $year = "20" . substr($row['contractDate'], 2, 2);
        // $dateString = $year . "-" . $month . "-01";
        // $formattedDate = date("Y F", strtotime($dateString));

        $formattedDate = date("Y F", strtotime($row['contractDate']));
    
        if ($row['typeOfSale'] == '1') {
            $typeOfSale = 'New Sale';
        }
    
        if ($row['typeOfSale'] == '2') {
            $typeOfSale = 'Sub Sale';
        }
    
        if ($row['typeOfSale'] == '3') {
            $typeOfSale = 'Resale';
        }
    
        $data[] = array( 
           // "contractDate"=> date('M Y',strtotime($row['contractDate'])),
            "contractDate"=> $formattedDate,
            "project"=>$row['project'],
            "street"=>$row['street'],
            "district"=>$row['district'],
            "marketSegment"=>$row['marketSegment'],
            "tenure"=>$row['tenure'],
            "typeOfSale"=> $typeOfSale ?? '-',
            "floorRange"=>$row['floorRange'],
            // "area"=>$row['area'],
            "area"=> ceil(number_format(($row['area'] * 10.76391042),2,'.','')),
            "price"=>number_format($row['price']),
        );
    }
    function analyzePropertyData($properties) {
        $output = [
            'highestPrice' => '',
            'lowestPrice' => '',
            'estimatedPrice' => ''
        ];
    
        if ($properties) {
            // Convert price strings to numeric values
            $prices = array_map(function($property) {
                return (float) str_replace(',', '', $property['price']);
            }, $properties);
    
            // Calculate highest, lowest, and estimated price
            $output['highestPrice'] = number_format(max($prices), 0, '.', ',');
            $output['lowestPrice'] = number_format(min($prices), 0, '.', ',');
    
            // Estimated price as average of all prices
            $output['estimatedPrice'] = number_format(array_sum($prices) / count($prices), 0, '.', ',');
        } else {
            $output['error'] = "Error: Could not process property data.";
        }
    
        return $output;
    }
    
    
    $properties = json_decode(json_encode($data), true); 
    $results = analyzePropertyData($properties); 
    $highestPrice = "$" . $results['highestPrice'];
    $lowestPrice = "$" . $results['lowestPrice'];
    $estimatedSellingPrice = "$" . $results['estimatedPrice'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" /> 
</head>

<body>
    <!-- banner-section start -->
    <section id="banner">
        <div class="container-large-full">
            <div class="banner-wrapper">
                <div class="banner-inner-wrapper">
                    <div class="row flex-column-reverse flex-sm-row">
                        <div class="col-md-6 banner-content-wrapper">
                            <div class="banner-content-wrapper-inner thank-you-main-box">
                                <div class="thank-you-box">
                                    <h1>Thank You!</h1>
                                    <p>As shown earlier, this is the transaction report for your estate.You’ll receive a
                                        FREE valuation report (worth $88) for your home.</p>
                                    <ul>
                                        <li>Recent rental transactions in your area</li>
                                        <li>Highest transaction records</li>
                                        <li>Potential selling price of your unit</li>
                                        <li>Value market estimate</li>
                                        <li>Local market trends</li>
                                        <li>Past 3 months’ transactions</li>
                                    </ul>
                                </div>
                                <div class="thank-you-box2">
                                    <h3>Results!</h3>
                                    <p>Your Estimated HDB Market Price</p>
                                    <ul>
                                        <?php
                                            if($response['lead']['form_type'] == 'condo' || $response['lead']['form_type'] == 'landed'){
                                        ?>
                                            <li><strong id=""><?=$lowestPrice?></strong> <span>Lowest transacted price based on real-time
                                                    records</span></li>
                                            <li><strong id=""><?=$estimatedSellingPrice?></strong> <span>For A HDB at area, your Estimated selling
                                                    price around </span></li>
                                            <li><strong id=""><?=$highestPrice?></strong> <span>Highest transacted price based on real-time
                                                    records</span></li>
                                        <?php }if($response['lead']['form_type'] == 'hdb'){ ?>
                                            <li><strong id="lowest_price"></strong> <span>Lowest transacted price based on real-time
                                                    records</span></li>
                                            <li><strong id="estimated_price"></strong> <span>For A HDB at area, your Estimated selling
                                                    price around </span></li>
                                            <li><strong id="highest_price"></strong> <span>Highest transacted price based on real-time
                                                    records</span></li>   
                                        <?php } ?>                 
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="thank-you-banner-image-box">
                                <img src="./assets/images/thank-you-bannerwebp.webp" alt="error">
                                <div class="banner-image-box-overlay">
                                </div>
                            </div>
                        </div>
                    </div>

                                           
                </div>
            </div>
        </div>
        </div>
        <?php if($response['lead']['form_type'] == 'hdb'){  ?>
        <div class="container"> 
            <div class="row my-5" style=" border: 1px solid #0058d2; border-radius: 10px; padding-top: 11px; ">
                <div class="col-12" >
                    <div id="table-guider" style="display: none;">
                        <img src="https://cdn-icons-gif.flaticon.com/6844/6844383.gif" alt="">
                    </div>
                    <ul class="nav nav-tabs" id="nav-tab" role="tablist"> 
                        <li class="nav-item" role="presentation" style=" margin-right: 20px; ">
                            <a class="nav-link text-dark" id="nav-block-tab" data-bs-toggle="tab" href="#nav-block" role="tab" aria-controls="nav-block" aria-selected="false">Your block</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link text-dark show active" id="nav-cluster-tab" data-bs-toggle="tab" href="#nav-cluster" role="tab" aria-controls="nav-cluster" aria-selected="true">Your cluster</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="nav-tabContent" style=" background-color: #fff;margin-top: 30px; padding-top: 10px; "> 
                        <div class="tab-pane fade" id="nav-block" role="tabpanel" aria-labelledby="nav-block-tab">
                            <div class="table-responsive mt-2">
                                <table class="table table-striped" id="example2">
                                    <thead>
                                        <tr>
                                            <th>Sold Price</th>
                                            <th>Sold Month</th>
                                            <th>Address</th>
                                            <th>Area</th>
                                            <th>Level</th>
                                            <th>Remaining Lease</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="12" align="center">No Data Found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show active" id="nav-cluster" role="tabpanel" aria-labelledby="nav-cluster-tab"> 
                            <div class="table-responsive mt-2">
                                <table class="table table-striped" id="example">
                                    <thead>
                                        <tr>
                                            <th>Sold Price</th>
                                            <th>Sold Month</th>
                                            <th>Address</th>
                                            <th>Area</th>
                                            <th>Level</th>
                                            <th>Remaining Lease</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="12" align="center">No Data Found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($response['lead']['form_type'] == 'condo'){  ?>    
            <div class="container"> 
                <div class="row" id="main_row" style=" background-color: #fff;margin-top: 30px;padding:20px;border-radius: 20px;">
                    <div class="col-md-12"> 
                        <div class="row">
                            <input type="hidden" name="project" id="project" value="<?= $response['lead_details'][1]['lead_form_value']; ?>">
                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">SALES OF YEAR</label>
                                    <select name="sales_dates" id="sales_dates" class="form-control basic">
                                        <?= @$sales_date_option ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">TYPE OF SALE</label>
                                    <select name="type_of_sales" id="type_of_sales" class="form-control basic">
                                        <option value="">Any type</option>
                                        <option value="1" <?= isset($_GET['type_of_sales']) && !empty($_GET["type_of_sales"]) && $_GET["type_of_sales"] == 1 ? 'selected' : 'null' ?>>New Sale</option>
                                        <option value="2" <?= isset($_GET['type_of_sales']) && !empty($_GET["type_of_sales"]) && $_GET["type_of_sales"] == 2 ? 'selected' : 'null' ?>>Sub Sale</option>
                                        <option value="3" <?= isset($_GET['type_of_sales']) && !empty($_GET["type_of_sales"]) && $_GET["type_of_sales"] == 3 ? 'selected' : 'null' ?>>Resale</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Floor Range</label>
                                    <select name="floor_range" id="floor_range" class="form-control basic"> 
                                        <option value="">Any Floor Range</option>
                                        <option value="1-10">Low floor 1-10</option>
                                        <option value="10-20">Mid floor 10-20</option>
                                        <option value="20 and above">High floor 20 and above</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Area (Sqft)</label>
                                    <select name="area_sqft" id="area_sqft" class="form-control basic"> 
                                        <option value="">Any Area (Sqft)</option>
                                        <option value="400-600">400-600</option>
                                        <option value="600-700">600-700</option>
                                        <option value="700-1300">700-1300</option>
                                        <option value="1300 and above">1300 and above</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-12 mb-5">
                                <button type="button" id="search" class="btn btn-primary">Search</button>
                                <button type="button" id="reset" class="btn btn-secondary">Reset</button> 
                            </div>
                        </div> 
                    </div>
                    <div class="col-12" style=" border: 1px solid #0058d2; border-radius: 10px; ">
                        <div class="table-responsive mt-2">
                            <div id="table-guider" style="display: none;">
                                <img src="assets/img/6844383.gif" alt="">
                            </div>                
                            <table class="table table-striped" id="empTable">
                                <thead>
                                    <tr>
                                        <th>Date of Sales</th>
                                        <th>Project Name</th>
                                        <th>Street Name</th>
                                        <th>District</th>
                                        <th>Market Segment</th>
                                        <th>Tenure</th>
                                        <th>Type of Sale</th>
                                        <th>Floor Level</th>
                                        <th>Area (Sqft)</th>
                                        <th>Sale Price (S$)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="12" align="center">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if($response['lead']['form_type'] == 'landed'){  ?>
            <div class="container"> 
            <div class="row mt-2">
                <div class="row" id="main_row" style=" background-color: #fff;margin-top: 30px;padding:20px;border-radius: 20px;">
                    <div class="col-md-12"> 
                        <div class="row">
                            <input type="hidden" name="project" id="project" value="<?= $project_id; ?>">
                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">SALES OF YEAR</label>
                                    <select name="sales_dates" id="sales_dates" class="form-control basic">
                                        <?= @$sales_date_option ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">TYPE OF SALE</label>
                                    <select name="type_of_sales" id="type_of_sales" class="form-control basic">
                                        <option value="">Any type</option>
                                        <option value="1" <?= isset($_GET['type_of_sales']) && !empty($_GET["type_of_sales"]) && $_GET["type_of_sales"] == 1 ? 'selected' : 'null' ?>>New Sale</option>
                                        <option value="2" <?= isset($_GET['type_of_sales']) && !empty($_GET["type_of_sales"]) && $_GET["type_of_sales"] == 2 ? 'selected' : 'null' ?>>Sub Sale</option>
                                        <option value="3" <?= isset($_GET['type_of_sales']) && !empty($_GET["type_of_sales"]) && $_GET["type_of_sales"] == 3 ? 'selected' : 'null' ?>>Resale</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Floor Range</label>
                                    <select name="floor_range" id="floor_range" class="form-control basic"> 
                                        <option value="">Any Floor Range</option>
                                        <option value="1-10">Low floor 1-10</option>
                                        <option value="10-20">Mid floor 10-20</option>
                                        <option value="20 and above">High floor 20 and above</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Area (Sqft)</label>
                                    <select name="area_sqft" id="area_sqft" class="form-control basic"> 
                                        <option value="">Any Area (Sqft)</option>
                                        <option value="400-600">400-600</option>
                                        <option value="600-700">600-700</option>
                                        <option value="700-1300">700-1300</option>
                                        <option value="1300 and above">1300 and above</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-12 mb-5">
                                <button type="button" id="search" class="btn btn-primary">Search</button>
                                <button type="button" id="reset" class="btn btn-secondary">Reset</button> 
                            </div>
                        </div> 
                    </div>
                    <div class="col-12" style=" border: 1px solid #0058d2; border-radius: 10px; ">
                        <div class="table-responsive mt-2">
                            <div id="table-guider" style="display: none;">
                                <img src="assets/img/6844383.gif" alt="">
                            </div>                
                            <table class="table table-striped" id="empTable">
                                <thead>
                                    <tr>
                                        <th>Date of Sales</th>
                                        <th>Project Name</th>
                                        <th>Street Name</th>
                                        <th>District</th>
                                        <th>Market Segment</th>
                                        <th>Tenure</th>
                                        <th>Type of Sale</th>
                                        <th>Floor Level</th>
                                        <th>Area (Sqft)</th>
                                        <th>Sale Price (S$)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="12" align="center">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        <?php } ?>        
        </div>
    </section>
    <!-- banner-section end -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.3/js/standalone/selectize.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</body>
<script>
    <?php if($response['lead']['form_type'] == 'hdb'){  ?>
    $(document).ready(function() {
            pageLoader('show');
            let first_request_url = '<?=$first_request_url?>';
            let second_request_url = '<?=$second_request_url?>'; 

            sendRequest(first_request_url)
            .then(function (records) {
                // Handle records
                // console.log('first',records) 
                setDataIntoTable(records);
                processRecords(records)
                pageLoader('hide');
            })
            .catch(function (error) {
                // Handle errors
                if (error == 'No records found') {
                    sendRequest(second_request_url).then(function (records) {
                        // Handle records
                        // console.log('second',records)

                        setDataIntoTable(records);
                        processRecords(records)
                        pageLoader('hide');
                    })
                    .catch(function (error) {
                        // Handle errors
                        console.error(error);
                        pageLoader('hide');
                    });
                }
                // console.error(error);
            });

            function sendRequest(url) {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            if (data && data.result && data.result.records && data.result.records.length > 0) {
                                // Resolve the promise with the records
                                resolve(data.result.records);
                            } else {
                                // Reject the promise with a message
                                reject('No records found');
                            }
                        },
                        error: function () {
                            // Reject the promise with an error message
                            reject('Error fetching data');
                        }
                    });
                });
            }

            function setDataIntoTable(records){
                var filteredBlocks = [];
                var selectedBlock = '<?=$response['lead_details'][2]['lead_form_value'] ?? "" ?>';

                var yourBlock = '';
                var yourCluster = '';
                
                if (records.length > 0) {
                    for (let index = 0; index < records.length; index++) {
                        const entry = records[index];
                        if (entry.block === selectedBlock) {
                            yourBlock += `<tr>
                                            <td>${formatCurrency(entry.resale_price)}</td>
                                            <td>${formatDate(entry.month)}</td>
                                            <td>${entry.block + ', ' + entry.street_name}</td>
                                            <td>${entry.floor_area_sqm } sqm</td>
                                            <td>${entry.storey_range}</td>
                                            <td>${entry.remaining_lease}</td>
                                        </tr>`;
                            yourCluster += `<tr>
                                    <td>${formatCurrency(entry.resale_price)}</td>
                                    <td>${formatDate(entry.month)}</td>
                                    <td>${entry.block + ', ' + entry.street_name}</td>
                                    <td>${entry.floor_area_sqm } sqm</td>
                                    <td>${entry.storey_range}</td>
                                    <td>${entry.remaining_lease}</td>
                                </tr>`;
                        }else{
                            yourCluster += `<tr>
                                    <td>${formatCurrency(entry.resale_price)}</td>
                                    <td>${formatDate(entry.month)}</td>
                                    <td>${entry.block + ', ' + entry.street_name}</td>
                                    <td>${entry.floor_area_sqm } sqm</td>
                                    <td>${entry.storey_range}</td>
                                    <td>${entry.remaining_lease}</td>
                                </tr>`;
                        }
                    }

                    let nav1 = $("#nav-block-tab");
                    let nav2 = $("#nav-cluster-tab");

                    let tab1 = $("#nav-block");
                    let tab2 = $("#nav-cluster");
                    
                    if (yourBlock) {
                        tab2.removeClass('show active');
                        nav2.removeClass('active');

                        tab1.addClass('show active');
                        nav1.addClass('active');
                        $("#example2 tbody").html(yourBlock);
                        new DataTable('#example2');
                    }

                    if (yourCluster) { 
                        $("#example tbody").html(yourCluster);
                        new DataTable('#example');
                    }

                    $('#show_total_records').html(`${records.length +' ('+formatDate2()+')'}`)

                    var myDiv = document.getElementById('table-guider');
                    if (window.innerWidth <= 767) {
                        // Show the div
                        myDiv.style.display = 'block';

                        // Hide the div after 5 seconds
                        setTimeout(function () {
                            myDiv.style.display = 'none';
                        }, 5000); // 5000 milliseconds = 5 seconds
                    }
                }
            }

            function processRecords(records) {
                if (!records || records.length === 0) {
                    console.log("No valid data found.");
                    return;
                }

                // Extract resale prices and convert to numbers
                let prices = records.map(item => parseFloat(item.resale_price)).filter(price => !isNaN(price));

                if (prices.length === 0) {
                    console.log("No valid resale prices found.");
                    return;
                }

                let lowestPrice = Math.min(...prices);
                let highestPrice = Math.max(...prices);
                let estimatedSellingPrice = ((lowestPrice + highestPrice) / 2).toFixed(2);
                $("#lowest_price").html(formatCurrency(lowestPrice));
                $("#highest_price").html(formatCurrency(highestPrice));
                $("#estimated_price").html(formatCurrency(estimatedSellingPrice));

                // console.log("Lowest Price:", formatCurrency(lowestPrice));
                // console.log("Highest Price:", formatCurrency(highestPrice));
                // console.log("Estimated Selling Price:", formatCurrency(estimatedSellingPrice));

            
                $("#lowestPrice").text("Lowest Price: " + formatCurrency(lowestPrice));
                $("#highestPrice").text("Highest Price: " + formatCurrency(highestPrice));
                $("#estimatedPrice").text("Estimated Selling Price: " + formatCurrency(estimatedSellingPrice));
            }
            // Function to format currency
            function formatCurrency(amount) {
                return '$' + Number(amount).toFixed(0).replace(/\d(?=(\d{3})+$)/g, '$&,');
            }

            // Function to format date
            function formatDate(dateString) {
                var date = new Date(dateString);
                return date.toLocaleString('en-US', { month: 'short', year: 'numeric' });
            }

            function formatDate2() {
                var currentDate = new Date();
                var day = currentDate.getDate();
                var month = currentDate.toLocaleString('en-US', { month: 'short' });
                var year = currentDate.getFullYear();

                // Add leading zero to day if necessary
                day = (day < 10) ? '0' + day : day;

                return day + ' ' + month + ' ' + year;
            }

            function pageLoader(status){
                if (status=="show") {
                    $('.loader-wrapper').removeClass('d-none');
                }else{
                    $('.loader-wrapper').addClass('d-none');
                }
            }
    }); 
    <?php } ?>
</script>
<!-- condo code start -->
<?php if($response['lead']['form_type'] == 'condo'){  ?>   
<script>
    function openModalDelayed() {
    var myModal = new bootstrap.Modal(document.getElementById('consultationModal'));
    setTimeout(function() {
      myModal.show();
    }, 3000); // 3000 milliseconds = 3 seconds
  }

  // Call the function when the page loads
  window.addEventListener('load', openModalDelayed);

    document.addEventListener('DOMContentLoaded', function () {
        var myDiv = document.getElementById('table-guider');
        if (window.innerWidth <= 767) {
            // Show the div
            myDiv.style.display = 'block';

            // Hide the div after 3 seconds
            setTimeout(function () {
                myDiv.style.display = 'none';
            }, 7000); // 7000 milliseconds = 7 seconds
        }
    });
   $(document).ready(function() { 
        $(document).ready(function() {
            // $('.basic').select2();

            var project = $('#project').val();

            let param = { 
                project
            }

            load_data(param);
        });

        $('#reset').click(function() {
            $('#empTable').DataTable().destroy(); 
            var project = $('#project').val();

            var type_of_sale = $('#type_of_sales').val('');
            var floor_range = $('#floor_range').val('');
            var sales_dates = $('#sales_dates').val('');
            var area_sqft = $('#area_sqft').val('');

            let param = { 
                project
            }

            load_data(param); 
        });

        $('#search').click(function() {
            
            var type_of_sale = $('#type_of_sales').val();
            var floor_range = $('#floor_range').val();
            var sales_dates = $('#sales_dates').val();
            var area_sqft = $('#area_sqft').val();
            var project = $('#project').val();

            let param = {
                type_of_sale,
                floor_range,
                sales_dates,
                area_sqft,
                project
            }


            $('#empTable').DataTable().destroy();
            load_data(param);
        });

        function load_data(param = {}) {
            // console.log(param);
            $('#empTable').DataTable({ 
                'processing': true,
                'serverSide': true,
                'ajax': {
                    'url': 'get_projects_data.php',
                    'data': param
                },
                'columns': [{
                        data: 'contractDate'
                    },
                    {
                        data: 'project'
                    },
                    {
                        data: 'street'
                    },
                    {
                        data: 'district'
                    },
                    {
                        data: 'marketSegment'
                    },
                    {
                        data: 'tenure'
                    },
                    {
                        data: 'typeOfSale'
                    },
                    {
                        data: 'floorRange'
                    },
                    {
                        data: 'area'
                    },
                    {
                        data: 'price'
                    }
                ],
                order: [[ 0, "desc" ]],
            });
        } 

    });
</script>
<?php }?>
<?php if($response['lead']['form_type'] == 'landed'){  ?>  
<script>
    function openModalDelayed() {
    var myModal = new bootstrap.Modal(document.getElementById('consultationModal'));
    setTimeout(function() {
      myModal.show();
    }, 3000); // 3000 milliseconds = 3 seconds
  }

  // Call the function when the page loads
  window.addEventListener('load', openModalDelayed);

    document.addEventListener('DOMContentLoaded', function () {
        var myDiv = document.getElementById('table-guider');
        if (window.innerWidth <= 767) {
            // Show the div
            myDiv.style.display = 'block';

            // Hide the div after 3 seconds
            setTimeout(function () {
                myDiv.style.display = 'none';
            }, 7000); // 7000 milliseconds = 7 seconds
        }
    });
   $(document).ready(function() { 
        $(document).ready(function() {
            // $('.basic').select2();

            var project = $('#project').val();

            let param = { 
                project
            }

            load_data(param);
        });

        $('#reset').click(function() {
            $('#empTable').DataTable().destroy(); 
            var project = $('#project').val();

            var type_of_sale = $('#type_of_sales').val('');
            var floor_range = $('#floor_range').val('');
            var sales_dates = $('#sales_dates').val('');
            var area_sqft = $('#area_sqft').val('');

            let param = { 
                project
            }

            load_data(param); 
        });

        $('#search').click(function() {
            
            var type_of_sale = $('#type_of_sales').val();
            var floor_range = $('#floor_range').val();
            var sales_dates = $('#sales_dates').val();
            var area_sqft = $('#area_sqft').val();
            var project = $('#project').val();

            let param = {
                type_of_sale,
                floor_range,
                sales_dates,
                area_sqft,
                project
            }


            $('#empTable').DataTable().destroy();
            load_data(param);
        });

        function load_data(param = {}) {
            // console.log(param);
            $('#empTable').DataTable({ 
                'processing': true,
                'serverSide': true,
                'ajax': {
                    'url': 'get_projects_data.php',
                    'data': param
                },
                'columns': [{
                        data: 'contractDate'
                    },
                    {
                        data: 'project'
                    },
                    {
                        data: 'street'
                    },
                    {
                        data: 'district'
                    },
                    {
                        data: 'marketSegment'
                    },
                    {
                        data: 'tenure'
                    },
                    {
                        data: 'typeOfSale'
                    },
                    {
                        data: 'floorRange'
                    },
                    {
                        data: 'area'
                    },
                    {
                        data: 'price'
                    }
                ],
                order: [[ 0, "desc" ]],
            });
        } 

    });
</script>
<?php } ?>
<!-- conde code end -->
</html>