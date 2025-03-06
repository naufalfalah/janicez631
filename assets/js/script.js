$(document).ready(function () {
  
  $('<input>').attr({
    type: 'hidden',
    id: 'source_url',
    name: 'source_url',
    value: window.location
  }).appendTo('form');

  $(function () {

    if($('select').length > 0){
      $('#street-name-dropdown,#blk-dropdown,#flat_type,#sellCheck,#hdb_sellCheck,#select-project1,#select-project2').selectize({

      onInitialize: function () {

        $("#select-country-selectized").attr("data-parsley-errors-container", "#errors");
        $("#street-name-dropdown-selectized").attr("data-parsley-errors-container", "#errors");
        $("#blk-dropdown-selectized").attr("data-parsley-errors-container", "#errors");
        $("#flat_type-selectized").attr("data-parsley-errors-container", "#errors");
        $("#hdb_sellCheck-selectized").attr("data-parsley-errors-container", "#errors");

      }

    });
    }





    var onClass = "on";

    var showClass = "show";



    $("input, .selectize-control ").bind("checkval", function () {

      var label = $(this).parent().find('label');

      if (this.value !== "") {

        label.addClass(showClass);

      } else {

        label.removeClass(showClass);

      }

    }).on("keyup", function () {

      $(this).trigger("checkval");

    }).on("focus", function () {

      $(this).prev("label").addClass(onClass);

    }).on("blur", function () {

      $(this).prev("label").removeClass(onClass);

    }).trigger("checkval");

  });

  setTimeout(function () {

    $(".VistorView.pop-1").addClass('active');

  }, 1500);

  setTimeout(function () {

    $(".VistorView.pop-1").css("opacity", "0");

  }, 5000);



  setTimeout(function () {

    $(".VistorView.pop-2").addClass('active');

  }, 5500);

  setTimeout(function () {

    $(".VistorView.pop-2").css("opacity", "0");

  }, 9000);



  setTimeout(function () {

    $(".VistorView.pop-3").addClass('active');

  }, 9500);

  setTimeout(function () {

    $(".VistorView.pop-3").css("opacity", "0");

  }, 13000);



  setTimeout(function () {

    $(".VistorView.pop-4").addClass('active');

  }, 13500);

  setTimeout(function () {

    $(".VistorView.pop-4").css("opacity", "0");

  }, 17000);



  setTimeout(function () {

    $(".VistorView.pop-5").addClass('active');

  }, 17500);

  setTimeout(function () {

    $(".VistorView.pop-5").css("opacity", "0");

  }, 21000);



  setTimeout(function () {

    $(".VistorView.pop-6").addClass('active');

  }, 21500);

  setTimeout(function () {

    $(".VistorView.pop-6").css("opacity", "0");

  }, 25000);



  setTimeout(function () {

    $(".VistorView.pop-7").addClass('active');

  }, 25500);

  setTimeout(function () {

    $(".VistorView.pop-7").css("opacity", "0");

  }, 29000);



  setTimeout(function () {

    $(".VistorView.pop-8").addClass('active');

  }, 29500);

  setTimeout(function () {

    $(".VistorView.pop-8").css("opacity", "0");

  }, 33000);



  setTimeout(function () {

    $(".VistorView.pop-9").addClass('active');

  }, 33500);

  setTimeout(function () {

    $(".VistorView.pop-9").css("opacity", "0");

  }, 37000);



  setTimeout(function () {

    $(".VistorView.pop-10").addClass('active');

  }, 37500);

  setTimeout(function () {

    $(".VistorView.pop-10").css("opacity", "0");

  }, 41000);



  setTimeout(function () {

    $(".VistorView.pop-11").addClass('active');

  }, 41500);

  setTimeout(function () {

    $(".VistorView.pop-11").css("opacity", "0");

  }, 45000);



  setTimeout(function () {

    $(".VistorView.index").addClass('active');

  }, 8500);

  setTimeout(function () {

    $(".VistorView.index").css("opacity", "0");

   }, 18000);
 

 

  $("#hdb_form").submit(function (e) {

    // e.preventDefault();

    if (!$("#hdb_form").valid()) {
      return false;
    }
    
    btn_loader('show','.hbd_form_submit_btn');

    // var TownName = $('#select-country option:selected').html();

    // var FlatType = $(".gridCheck:checked").val();

    // $(".room_val").html(FlatType);

    // $(".town_val").html(TownName);

    // var sellCheck = $(".sellCheck:checked").val();

    // var unit_number = $(".unit_number").val();

    // var postalcode = $(".postalcode").val();

    // var firstname = $(".firstname").val();

    // var email = $(".email").val();

    // var number = $(".number").val();

    // if (sellCheck.length != "" && unit_number.length != "" && firstname.length != "" && email.length != "" && number.length != "" && postalcode.length != "") {

    //   getData(TownName,FlatType);

    //   //   $(".VistorView").addClass("active");

    //   //dniyal discord webhook code start 

      //  var form = document.getElementById('hdb_form');

      // var formData = new FormData(form);

        // $.ajax({

        //     url: "discord_webhook.php",

        //     method: "POST",

        //     data: formData,

        //     dataType: 'json',

        //     contentType: false,

        //     processData: false,

        //     success: function(data) {

        //         console.log(data);

        //     }

        // });

    //   //dniyal discord webhook code end 

    //   setTimeout(function () {

    //     $(".VistorView.cal").addClass('active');

    //   }, 1000);

    //   setTimeout(function () {

    //       $(".VistorView.cal").css("opacity", "0");

    //   }, 10000);



    // }else{
    //   btn_loader('hide');
    //   console.log('data not found');
    // }

  });

})


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

function getData(town, rooms) {
  var escapedTown = encodeURIComponent(town);
  var escapedRooms = encodeURIComponent(rooms);

  var url = 'https://data.gov.sg/api/action/datastore_search?resource_id=f1765b54-a209-4718-8d38-a39237f502b3&limit=14000&q={"town":"' + escapedTown + '","flat_type":"' + escapedRooms + '"}';

  $.ajax({
    url: url,
    method: 'GET',
    dataType: 'json',
    complete: function () {
      btn_loader('hide');
      $(".loader-wrapper").hide();
      $("#show-data-results").show();
    }, 
    success: function (data) {
      // Process the retrieved data here
      // console.log(data);

      const totalRows = data.result.total;
      const totalSum = sumResaleValues(data.result.records);
      const estimatePrice = (totalSum / totalRows);

      const min_max = findMinMaxResaleValues(data.result.records);

      btn_loader('hide');
      $(".price_val").text(' $' + Math.floor(estimatePrice).toLocaleString());
      $(".min_val").text(' $' + min_max.min.toLocaleString());
      $(".max_val").text(' $' + min_max.max.toLocaleString());

      $(".box").addClass('d-block');
      $(".box").addClass('col-md-3');
      $("body.main_scroll").addClass('overflow-hidden');
      $(".box").animate({ width: "100%" });
      $(".result_main").animate({ top: "0%" });
      $("#overlay").animate({ opacity: "1" });
      $("#overlayWrapper").css("position", "fixed");

    },
    error: function (xhr, status, error) {
      // Handle errors here
      console.error('Error:', error);
      btn_loader('hide');
    }
  });
}

function findMinMaxResaleValues(dataArray) {
  var minValue = Number.MAX_SAFE_INTEGER; // Set initial minimum to a large value
  var maxValue = Number.MIN_SAFE_INTEGER; // Set initial maximum to a small value

  $.each(dataArray, function(index, data) {
      if (data.hasOwnProperty('resale_price')) {
          var resalePrice = parseInt(data.resale_price);

          if (resalePrice < minValue) {
              minValue = resalePrice;
          }

          if (resalePrice > maxValue) {
              maxValue = resalePrice;
          }
      }
  });

  return { min: minValue, max: maxValue };
}

function sumResaleValues(dataArray) {
  var sum = 0;
  $.each(dataArray, function(index, data) {
      if (data.hasOwnProperty('resale_price')) {
          sum += parseInt(data.resale_price);
      }
  });
  return sum;
}


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
        
        $('#blk-dropdown').selectize()[0].selectize.destroy();
        $('#street-name-dropdown').selectize()[0].selectize.destroy();
        $('#flat_type').selectize()[0].selectize.destroy();
        
        $('#blk-dropdown').empty();
        $('#street-name-dropdown').empty();
        $('#flat_type').empty();

        $('#flat_type').selectize({
            options: res.flat_types,
            allowEmptyOption: true,
            placeholder: 'Choose Flat Type',
            items: [], // Ensure no default item is selected
            onInitialize: function() {

                // $("#street-name-dropdown-selectized").attr("data-parsley-errors-container", "#errors");

            },
        });

        $('#street-name-dropdown').selectize({
          options: res.street_names,
          allowEmptyOption: true,
          placeholder: 'Street Name', 
          items: [], // Ensure no default item is selected
          onInitialize: function () {
    
            $("#street-name-dropdown-selectized").attr("data-parsley-errors-container", "#errors");
    
          },
        });

        $('#blk-dropdown').selectize({
          options: res.blks,
          allowEmptyOption: true,
          placeholder: 'Block Number', 
          items: [], // Ensure no default item is selected
          onInitialize: function () {
    
            $("#blk-dropdown-selectized").attr("data-parsley-errors-container", "#errors");
    
          },
        }); 
      }
    }
  });

  

});