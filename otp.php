<?php
include('db_config.php');

$lead_id = $_GET['lead_id'];

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
// header('Content-Type: application/json');
// $response = json_encode($response, JSON_PRETTY_PRINT);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP Verification</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        
    </style>
  </head>
  <body>
    <div class="container">
      <h2 class="title">OTP Verification</h2>
      <p class="subtitle">
        Enter the code from the SMS we sent to
        <span class="phone-number">+62<?=$response['lead']['ph_number'];?></span>
      </p>

      <!-- <p class="timer" id="timer">02:32</p> -->
      <form id="check_otp">
      <div class="otp-box">
        <input type="hidden" name="lead_id" value="<?=$_GET['lead_id']?>">
        <div class="t-otp">
          <input id="d1" type="number" pattern="[0-9]*" name="otp1" inputtype="numeric" autocomplete="one-time-code" oninput="otpValidate(this)" maxlength="6" required />
          <input id="d2" type="number" pattern="[0-9]*" name="otp2" inputtype="numeric" oninput="digitValidate(this)" onkeyup="tabChange(2)" maxlength="1" />
          <input id="d3" type="number" pattern="[0-9]*" name="otp3" inputtype="numeric" oninput="digitValidate(this)" onkeyup="tabChange(3)" maxlength="1" />
          <input id="d4" type="number" pattern="[0-9]*" name="otp4" inputtype="numeric" oninput="digitValidate(this)" onkeyup="tabChange(4)" maxlength="1" />
          <input id="d5" type="number" pattern="[0-9]*" name="otp5" inputtype="numeric" oninput="digitValidate(this)" onkeyup="tabChange(5)" maxlength="1" />
          <input id="d6" type="number" pattern="[0-9]*" name="otp6" inputtype="numeric" oninput="digitValidate(this)" onkeyup="tabChange(6)" maxlength="1" />
        </div>
      
      </div>

      <!-- <p class="resend-text">
        Don't receive the OTP? <span class="resend-link">RESEND</span>
      </p> -->
      <br>
      <span id="error" style="color:red"></span>
      <button class="submit-btn" id="otp_btn" type="submit">Submit</button>
      </form>
    </div>
  </body>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let otpValidate = function (ele) {
      ele.value = ele.value.replace(/[^0-9]/g, "");
      if (ele.value == "") return;
      let d_value = ele.value.toString();
      if (d_value.length == 1) {
        document.getElementById("d2").focus();
      } else {
        d_input = document.querySelectorAll(".t-otp input");
        for (let i = 0; i < d_value.length; i++) {
          d_input[i].value = d_value.substring(i, i + 1);
        }
        d_input[d_value.length - 1].focus();
      }
    };
    let digitValidate = function (ele) {
      ele.value = ele.value.replace(/[^0-9]/g, "");
      let d_value = ele.value.toString();
      if (d_value.length > 1) {
        ele.value = d_value.substring(0, 1);
      }
    };
    let tabChange = function (val) {
      let ele = document.querySelectorAll(".t-otp > input");
      if (ele[val - 1].value != "") {
        if (val != 6) ele[val].focus();
      } else if (ele[val - 1].value == "") {
        var key = event.keyCode || event.charCode;
        if (key == 8) {
          //backspace pressed
          ele[val - 2].focus();
        }
      }
    };

  </script>
  <script>
    $('#check_otp').submit(function(e) {
      e.preventDefault(); 
      btn_loader('show','#otp_btn')
      let formData = new FormData(this); 
      $.ajax({ 
          url: "check_otp.php",
          type: 'POST',
          data: formData,
          processData: false, // Prevent jQuery from processing the data
          contentType: false, // Prevent jQuery from setting the content type
          dataType: 'json',
          success: function (res) {  
              console.log(res);
              if(res.status == false){
                btn_loader('hide','#otp_btn');
                $('#error').html(res.msg);
              }else{
                window.location.href = "thank_you.php?lead_id=<?=$_GET['lead_id']?>";
              }
          }, 
          error: function(xhr, status, error) {
              console.error("Error:", error);
          }
      });
    });

    function btn_loader(status,id='#submit-form-btn') {
      var formBtn = $(id);
      var btnTxt = "Submit";
      var loadText = "Loading...";


      const loadHtml = `${loadText} `;

      if (status === 'hide') {
          formBtn.prop('disabled', false);
          formBtn.html(btnTxt);
          return
      }
      formBtn.prop('disabled', true);
      formBtn.html(loadHtml);
    }        
  </script>
</html>