<?php
include('db.php');

$draw = $_GET['draw'];
$row = $_GET['start'];
$rowperpage = $_GET['length']; // Rows display per page
$columnIndex = $_GET['order'][0]['column']; // Column index
$columnName = $_GET['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_GET['search']['value']); // Search value

if (isset($_GET{'project'})) {
    $query = "SELECT projects.project, projects.street, projects.marketSegment, project_transactions.* FROM project_transactions
    JOIN projects on projects.id = project_transactions.project_id ";
    $searchQuery = " ";
    if (isset($_GET['project']) && !empty($_GET['project'])) {
        $searchQuery .= " and (project_transactions.project_id = '".$_GET['project']."') ";
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

    if ($searchValue != '') {
        $searchQuery = " AND (project_transactions.area LIKE '%" . $searchValue . "%' OR 
            project_transactions.floorRange LIKE '%" . $searchValue . "%' OR 
            project_transactions.contractDate LIKE '%" . $searchValue . "%' OR 
            project_transactions.typeOfSale LIKE '%" . $searchValue . "%' OR 
            project_transactions.price LIKE '%" . $searchValue . "%' OR 
            project_transactions.propertyType LIKE '%" . $searchValue . "%' OR 
            project_transactions.district LIKE '%" . $searchValue . "%' OR 
            project_transactions.typeOfArea LIKE '%" . $searchValue . "%' OR 
            project_transactions.tenure LIKE '%" . $searchValue . "%' OR 
            projects.project LIKE '%" . $searchValue . "%' OR 
            projects.street LIKE '%" . $searchValue . "%' )";
    
        $sel = mysqli_query($db, "SELECT COUNT(*) AS allcount FROM project_transactions JOIN projects ON projects.id = project_transactions.project_id WHERE 1 " . $searchQuery); 
        // echo $query . " WHERE 1 " . $searchQuery;
        // die();
    } else {
        // $searchQuery = ""; // Set an empty string when $searchValue is empty
    
        $sel = mysqli_query($db, "SELECT COUNT(*) AS allcount FROM project_transactions JOIN projects ON projects.id = project_transactions.project_id WHERE 1 " . $searchQuery);
    }
     
    
    $records = mysqli_fetch_assoc($sel);
    
    
    
    $totalRecordwithFilter = $records['allcount'];
    $totalRecords = $records['allcount'];
    
    // echo $rowperpage;
    // die();
    
    ## Fetch records
    $empQuery = $query." WHERE 1 ".$searchQuery." order by  project_transactions.contractDate ".$columnSortOrder." limit ".$row.",".$rowperpage;

    
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
    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordwithFilter,
      "aaData" => $data
    );
    
    echo json_encode($response);
}