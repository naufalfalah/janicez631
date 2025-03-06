<?php
// session_start(); 

include('db_config.php');


$condo_projects_name_Stmt = $pdo->query("SELECT DISTINCT(project), id FROM `projects`");
$condo_projects = $condo_projects_name_Stmt->fetchAll(PDO::FETCH_ASSOC);

$landed_projects_name_Stmt = $pdo->query("SELECT id, project,street FROM `projects`");
$landed_projects = $landed_projects_name_Stmt->fetchAll(PDO::FETCH_ASSOC);

$townStmt  = $pdo->query("SELECT town, json_data FROM towns Order By town ");
$towns = $townStmt->fetchAll(PDO::FETCH_ASSOC);

$condo_project_option = '<option value="" selected>Choose</option>';
$landed_project_option = '<option value="" selected>Choose</option>';

foreach ($condo_projects as $key => $value) {
    if ($value['project'] != 'LANDED HOUSING DEVELOPMENT' && $value['project'] != 'RESIDENTIAL APARTMENTS') {
        $condo_project_option .= '<option value="' . $value['id'] . '">' . $value['project'] . '</option>';
    }
}

foreach ($landed_projects as $key => $value) {
    if ($value['project'] == 'LANDED HOUSING DEVELOPMENT' || $value['project'] == 'RESIDENTIAL APARTMENTS') {
        $landed_project_option .= '<option value="' . $value['id'] . '">' . $value['street'] . '</option>';
    }
}

$townStmt  = $pdo->query("SELECT town FROM towns Order By town ");
$towns = $townStmt->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HDB</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error{
            color:red
        }
    </style>
</head>

<body>
    <!-- banner-section start -->
    <section id="form-banner">
        <div class="container-large-full">
            <div class="banner-wrapper">
                <div class="banner-inner-wrapper">
                    <div class="row flex-column-reverse flex-sm-row">
                        <div class="col-md-6 banner-content-wrapper">
                            <div class="form-section">
                                <h1>HDB</h1>
                                <form method="post" class="hdb_form" action="otp.php" id="hdb_form">
                                    <input type="hidden" name="form_type" value="hdb">
                                    <input type="hidden" name="source_url" value="https://launchgovtest.homes/">
                                    <div class="form-group">
                                        <label>Project Name</label>
                                        <select id="select-country" name="town" class="demo-default form-control town-dropdown" required placeholder="Choose Town" data-parsley-errors-container="#errors">
                                            <option Value="">Choose Town</option>
                                            <?php
                                                foreach ($towns as $town) { ?>
                                                    <option value="<?=$town?>"><?=$town?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>What is your Street Name?</label>
                                        <select id="street-name-dropdown" name="street_name" class="demo-default form-control street-name-dropdown" required placeholder="Street Name" data-parsley-errors-container="#errors">
                                            <option value="">Select One</option>
                                                <?php
                                                    foreach ($street_names as $street_name) { ?>
                                                    
                                                    <option value="<?=$street_name?>"><?=$street_name?></option>
                                                <?php }
                                                ?>

                                            </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Block No (BLK)?</label>
                                        <select id="blk-dropdown" name="blk" class="demo-default form-control blk-dropdown" required placeholder="Block Number" data-parsley-errors-container="#errors">
                                            <option value="">Select One</option>
                                            <?php
                                                foreach ($blocks as $block) { ?>
                                                
                                                <option value="<?=$block?>"><?=$block?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>HDB Flat Type?</label>
                                        <label id="flat_type-error" class="error" for="flat_type" style="display:none">This field is required.</label>
                                        <div class="radio-group" id="flat_type">
                                            <label><input type="radio" name="flat_type" value="" required> 2 - Room</label>
                                            <label><input type="radio" name="flat_type" value="" required> 3 - Room</label>
                                            <label><input type="radio" name="flat_type" value="" required> 4 - Room</label>
                                            <label><input type="radio" name="flat_type" value="" required> Executive/Multi-Generation</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>When do you plan to sell your property?</label>
                                        <label id="sellCheck-error" class="error" for="sellCheck" style="display:none">This field is required.</label>
                                        <div class="radio-group">
                                            <label><input type="radio" name="sellCheck" value="0-3 months" required> 0-3 Month</label>
                                            <label><input type="radio" name="sellCheck" value="3-6 months" required> 3-6 Month</label>
                                            <label><input type="radio" name="sellCheck" value="6-12 months" required> 6-12 Month</label>
                                            <label><input type="radio" name="sellCheck" value="Exploring options" required> Exploring options</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>What is your unit number?</label>
                                        <div class="row">
                                            <div class="col-6 pad-right">
                                            <input type="text" class="unit_number" id="floor_number" name="floor_number" placeholder="Floor" required maxlength="3">
                                            </div>
                                            <div class="col-6 pad-left">
                                            <input type="text" class="unit_number" id="unit_number" name="unit_number" placeholder="Unit" required minlength="2" maxlength="4" title="Please enter a number between 2 and 4 digits.">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group text-form">
                                        <p>Please fill up your details and you will be able to retrieve an automated LIVE report extracted base on URA data past transaction reports and a precise property valuation for your unit.</p>
                                    </div>
                                    <div class="form-group">
                                        <label>My Name is</label>
                                        <input id="firstname" name="firstname" class="firstname" type="text" required placeholder="Enter First Name...">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control valid" id="ph_number" name="ph_number" maxlength="8"  placeholder="+65 91111111" required  aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" id="get_code" type="button" style="line-height: 1.2; background-color: #0058d2; color: #fff; ">Get Code</button>
                                            </div>
                                            <!-- <input type="text" id="ph_number" name="ph_number" maxlength="8"  placeholder="+65 91111111" required class="valid"> -->
                                        </div>
                                        <label id="ph_number-error" class="error" for="ph_number" style="display:none">Please enter a phone number.</label>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input id="email" type="email" class="email" name="email" required placeholder="Enter email address...">
                                    </div>
                                    <div class="form-group" id="otp_div" style="display:none">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="otp_input" name="user_otp" maxlength="6" pattern="\d{6}" placeholder="Enter your OTP" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '')">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="otp_timer" style="line-height: 1.2; background-color: #0058d2; color: #fff; "></span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="otp" name="wp_otp">
                                    <div class="form-group check-group">
                                        <label><input type="checkbox" name="a" required> I consent to the collection, use and disclosure of my personal data for purposes as set out in our Privacy Policy. (View Policy)</label>
                                        <label id="a-error" class="error" for="a" style="display:none">This field is required.</label>            
                                    </div>
                                    <p for="" id="hdb_email_error" class="error"></p>
                                    <button type="button" class="button form-btn">Get Immediate Result</button>
                                    <input type="hidden" id="lead_id" name="lead_id">
                                </form>
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="condo-banner-image-box">
                                <img src="./assets/images/cndo-banner.webp" alt="error">
                                <div class="banner-image-box-overlay">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner-section end -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
                                                
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Selectize CSS -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.min.css" /> -->

    <!-- Selectize JS -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script> -->


    <script>
        $(document).on('change','.town-dropdown',function () { 

            $(".loader-wrapper").show();

            let val = $(this).val();
            if (!val) {
            $(".loader-wrapper").hide();

                return false
            }

            let params = {
            'town':val
            };

            $.ajax({
            type: "POST",
            url: "find_data.php",
            complete: function () {
                $(".loader-wrapper").hide();
            }, 
            data: params,
            dataType: "json",
            success: function (res) {
                if (res) { 
                // $('#flat_types_row').html(res.flat_types); 
                
                //   $('#blk-dropdown').selectize()[0].selectize.destroy();
                //   $('#street-name-dropdown').selectize()[0].selectize.destroy();
                //   $('#flat_type').selectize()[0].selectize.destroy();
                
                $('#blk-dropdown').empty();
                $('#street-name-dropdown').empty();
                $('#flat_type').empty();




                $.each(res.flat_types, function (index, flatType) {
                    $('#flat_type').append(
                        '<label><input type="radio" name="flat_type" value="' + flatType.value + '" required> ' + flatType.text + '</label>'
                    );
                });

                $('#street-name-dropdown').append('<option value="">Street Name</option>'); // Placeholder add karo

                $.each(res.street_names, function (index, street) {
                    $('#street-name-dropdown').append('<option value="' + street.value + '">' + street.text + '</option>');
                });

                $("#street-name-dropdown").attr("data-parsley-errors-container", "#errors");

                $('#blk-dropdown').append('<option value="">Block Number</option>'); // Placeholder add karo

                $.each(res.blks, function (index, blk) {
                    $('#blk-dropdown').append('<option value="' + blk.value + '">' + blk.text + '</option>');
                });

                // Ensure Parsley validation container is set correctly for blk-dropdown
                $("#blk-dropdown").attr("data-parsley-errors-container", "#errors"); 
                }
            }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $.validator.addMethod("phoneValidation", function(value, element) {
                return /^[89]\d{7}$/.test(value); // 8 digits, starts with 8 or 9
            }, "Phone number must start with 8 or 9 and be exactly 8 digits.");

            $.validator.addMethod("strictEmail", function(value, element) {
                return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            }, "Please enter a valid email address.");

            $("#get_code").click(function () {
                let phoneNumber = $("#ph_number").val();
                let errorLabel = $("#ph_number-error");
                let getCodeBtn = $("#get_code");
                let otpDiv = $("#otp_div");
                let otpTimer = $("#otp_timer"); 
                $("#hdb_email_error").html('');

                $("#hdb_form").valid(); 
                getCodeBtn.prop("disabled", true).text("Sending...");
                let formData = new FormData($('#hdb_form')[0]);

                if (!$("#hdb_form").valid()) {
                    getCodeBtn.prop("disabled", false).text("Get Code");
                    return false;
                } 
                // runCheckEmail(formData); 
                
                getCodeBtn.prop("disabled", false).text("Get Code");
              
                function startOtpTimer() {
                    let timeLeft = 120; // 2 minutes (120 seconds)

                    // Show OTP div
                    otpDiv.show();

                    let timerInterval = setInterval(function () {
                        let minutes = Math.floor(timeLeft / 60);
                        let seconds = timeLeft % 60;
                        otpTimer.text(`${minutes}:${seconds < 10 ? "0" : ""}${seconds}`);

                        if (timeLeft <= 0) {
                            clearInterval(timerInterval);  // Stop timer
                            getCodeBtn.prop("disabled", false);
                            $('#otp_input').val('');
                            otpDiv.hide();  // Hide OTP div
                        }
                        timeLeft--;
                    }, 1000); // Update every second
                }

                // Validation check
                if (!/^[89]\d{7}$/.test(phoneNumber)) {
                    errorLabel.text("Phone number must start with 8 or 9 and be exactly 8 digits.");
                    errorLabel.show();
                    return false;
                } else {
                    errorLabel.hide();
                }

                getCodeBtn.prop("disabled", true).text("Sending...");

                // If validation passes, send AJAX request
                $.ajax({
                    url: "send_otp.php",  // Replace with your actual URL
                    type: "POST",
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    dataType: 'json',
                    success: function (response) { 
                        if(response.isValid === false){
                            getCodeBtn.prop("disabled", false).text("Get Code");
                            $("#hdb_email_error").html(response.msg);
                            return false
                        } 
                        $('#lead_id').val(response.lead_id);                        
                        $('#otp').val(response.OTP);
                        $('#otp_input').val('');
                        getCodeBtn.text("Resend");
                        startOtpTimer();
                    },
                    error: function () {
                        // alert("Failed to send code.");
                       getCodeBtn.prop("disabled", false).text("Get Code");
                    }
                });
            });

            $("#hdb_form").validate({
                debug: true, 
                ignore: ":hidden", 
                rules: {
                    ph_number: {
                        required: true,
                        digits: true, 
                        minlength: 8,
                        maxlength: 8,
                        phoneValidation: true 
                    },
                    email: {
                        required: true,
                        strictEmail: true  
                    },
                    unit_number: {
                        required: true,
                        number: true,
                        min: 1
                    },
                    floor_number: {
                        required: true,
                        digits: true, 
                        min: 1,
                        max: 50
                    }
                },
                messages: {
                    ph_number: {
                        required: "Please enter a phone number.",
                        number: "Please enter a valid phone number.",
                        minlength: "Phone number must be 8 digits long.",
                        maxlength: "Phone number must be 8 digits long."
                    },
                    email: {
                        required: "Please enter your email.",
                        strictEmail: "Please enter a valid email address (example@domain.com)."
                    },
                    floor_number: {
                        required: "Please enter a floor number.",
                        digits: "Only numeric values are allowed.",
                        min: "Floor number must be at least 1.",
                        max: "Please enter a floor number less than 50."
                    }
                },
                errorPlacement: function(error, element) {
                   // console.log("Validation Error on:", element.attr("name"), "Type:", element.attr("type"));
                    error.insertAfter(element);
                }
            });    
        });
    </script>

    <script>
        $('.form-btn').click(async function(e) {
            e.preventDefault();
            $("#hdb_form").valid(); 
            btn_loader('show','.form-btn')
            let formData = new FormData($('#hdb_form')[0]);

            if (!$("#hdb_form").valid()) {
                btn_loader('hide','.form-btn')
                return false;
            } 
            wp_otp = $('#otp').val();
            user_otp = $('#otp_input').val();
            if(wp_otp == ''){
                $('#hdb_email_error').html('Please verify your number.');
                btn_loader('hide','.form-btn')
                return false;
            }
            if(wp_otp != user_otp){
                $('#hdb_email_error').html('Please enter valid OTP.');
                btn_loader('hide','.form-btn')
                return false;
            }
            runDiscordAjax(formData);
            return;
            // $('#hdb_form').submit();
        });
        async function check_email(formData,formID,formBtnClass,divId){ 
            $(divId).html('');
            const validateUrl = 'https://janicez87.sg-host.com/check_time_email_round.php';
            try {
                const response = await fetch(validateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: formData.get('email'),
                        source_url: 'https://launchgovtest.homes/',
                        ip: "344",
                        ph_number: formData.get('ph_number'),
                    })
                });

                if (response.ok) {
                    const validationData = await response.json();
                    setError(validationData)
                    return validationData;
                } else {
                    console.error('Error fetching validation URL:', response.statusText);
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                return false;
            }

            function setError (res) {
                if (res.isValid === false) {
                     btn_loader('hide',formBtnClass);
                    $(divId).html(res.msg);
                    return;
                }

                res = runDiscordAjax(formData); 
                     
            }
        }

        function btn_loader(status,id='#submit-form-btn') {
            var formBtn = $(id);
            var btnTxt = "Get immediate result";
            var loadText = "Calculating...";


            const loadHtml = `${loadText} <div class="spinner-border spinner-border-sm" role="status">
                    <span class="sr-only">Loading...</span>
                </div>`;

            if (status === 'hide') {
                formBtn.prop('disabled', false);
                formBtn.html(btnTxt);
                return
            }
            formBtn.prop('disabled', true);
            formBtn.html(loadHtml);

        }

        function runDiscordAjax(formData) {  
            $.ajax({ 
                url: "discord_webhook.php",
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                dataType: 'json',
                success: function (res) {  
                     window.location.href = "thank_you.php?lead_id=" + res.lead_id;
                }, 
            });
        }
        $('#ph_number').keyup(function() {
            $("#otp").val('');
            $("#otp_input").val('');
            $('#otp_div').hide();
            $('#get_code').prop('disabled', false).text('Get code');
        })
    </script>
</body>

</html>