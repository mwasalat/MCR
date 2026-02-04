<?php
ob_start();   
session_start();
require('fpdf.php');
include('pdf.class.php');
if (strlen($_SESSION['id']==0)) {
  header('location:logout.php');
  } else{
	
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>MCR System - Mwasalat</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="css/heroic-features.css" rel="stylesheet">
    
    <!-- Bootstrap Date-Picker Plugin -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
    
<style type="text/css">
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: url("images/bg.jpg") no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    padding-top: 0 !important;
}

/* Header Styles */
.main-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    padding: 15px 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-section img {
    height: 50px;
    filter: brightness(0) invert(1);
}

.logo-section h1 {
    color: #fff;
    font-size: 1.5em;
    font-weight: 600;
    margin: 0;
}

.nav-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.nav-section .user-info {
    color: rgba(255, 255, 255, 0.9);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-section .user-info i {
    font-size: 16px;
}

.nav-section .logout-btn {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    padding: 8px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-section .logout-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

/* Main Content */
.main-content {
    max-width: 800px;
    margin: 30px auto;
    padding: 0 20px;
}

.form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-header {
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
    padding: 25px 30px;
    color: #fff;
}

.form-header h2 {
    font-size: 1.4em;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-body {
    padding: 30px;
}

/* Form Styling */
.form-group {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    gap: 15px;
}

.form-group label {
    min-width: 140px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group select {
    flex: 1;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    background: #f8f9fa;
    max-width: 300px;
}

.form-group input[type="text"]:focus,
.form-group input[type="number"]:focus,
.form-group select:focus {
    outline: none;
    border-color: #e74c3c;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1);
}

.form-group select {
    cursor: pointer;
}

/* Radio Button Styling */
.radio-group {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.radio-group label {
    min-width: auto;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-weight: 400;
    padding: 8px 15px;
    border-radius: 25px;
    background: #f0f0f0;
    transition: all 0.3s ease;
}

.radio-group label:hover {
    background: #e0e0e0;
}

.radio-group input[type="radio"] {
    accent-color: #e74c3c;
    width: 16px;
    height: 16px;
}

.radio-group input[type="radio"]:checked + span,
.radio-group input[type="radio"]:checked ~ span {
    color: #e74c3c;
    font-weight: 500;
}

/* Submit Button */
.submit-btn {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: #fff;
    padding: 14px 40px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Reset Icon */
.reset-icon {
    color: #e74c3c;
    cursor: pointer;
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.reset-icon:hover {
    transform: rotate(90deg);
}

/* Search Results */
#searchResult {
    list-style: none;
    padding: 0;
    position: absolute;
    margin: 0;
    max-height: 200px;
    overflow: auto;
    z-index: 999;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    width: 300px;
}

#searchResult li {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.2s ease;
}

#searchResult li:hover {
    background: #f8f9fa;
}

#searchResult li:nth-child(even) {
    background: #f0f4f8;
}

#searchResult li:nth-child(even):hover {
    background: #e8ecf0;
}

/* Conditional Fields */
.chqmodediv, .ccmodediv, .bnkmodediv {
    display: none;
}

/* Alert */
.alert {
    display: none;
    margin-top: 20px;
}

/* Hide spinner for number input */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance: textfield;
}

/* Responsive */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .form-group {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-group label {
        min-width: auto;
    }
    
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select {
        max-width: 100%;
        width: 100%;
    }
    
    .radio-group {
        flex-direction: column;
        gap: 10px;
    }
}

/* Footer */
.page-footer {
    text-align: center;
    padding: 20px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 13px;
}

.page-footer a {
    color: #fff;
    text-decoration: none;
}
</style>
</head>
<body>

<!-- Header -->
<header class="main-header">
    <div class="header-container">
        <div class="logo-section">
            <img src="images/mwasalat_holding_logo.png" alt="Mwasalat Logo">
            <h1>MCR System</h1>
        </div>
        <nav class="nav-section">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo $_SESSION['name'];?></span>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>
    </div>
</header>

<!-- Main Content -->
<div class="main-content">
    <div class="form-card">
        <div class="form-header">
            <h2><i class="fas fa-file-invoice-dollar"></i> Payment Collection</h2>
        </div>
        <div class="form-body">
            <form id="mcrform" name="mcrform" method="POST">
		  
		  
		 
                <div id="HiddenInvalidInputContainer"></div>
                
                <!-- Region -->
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Region</label>
                    <select name="location">
                        <option value="AUH" <?php if($_SESSION['location']=='AUH') { echo 'selected';}?>>AUH</option>
                        <option value="DXB" <?php if($_SESSION['location']=='DXB') { echo 'selected';}?>>DXB</option>
                    </select>
                </div>

                <!-- Company -->
                <div class="form-group">
                    <label><i class="fas fa-building"></i> Company</label>
                    <select name="company" id="company">
                        <option value="autostrad">Autostrad</option>
                        <option value="fast">Fast Rent A Car</option>
                        <option value="fastline">Fast Line Bus Rental</option>
                        <option value="fastinvestment">Fast Investment</option>
                    </select>
                </div>

                <!-- Customer Type -->
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> Customer Type</label>
                    <div class="radio-group">
                        <label><input type="radio" id="chkoldcustomer" checked="checked" name="chk"> <span>Existing</span></label>
                        <label><input type="radio" id="chknewcustomer" name="chk"> <span>Other Payments</span></label>
                    </div>
                </div>

                <!-- Customer -->
                <div class="form-group" style="position: relative;">
                    <label><i class="fas fa-user"></i> Customer <span class="reset-icon" id="configreset"><i class="fas fa-times-circle"></i></span></label>
                    <input type="text" name="txtCustomer" id="txtCustomer" required placeholder="Search customer...">
                    <ul id="searchResult"></ul>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone</label>
                    <input type="text" name="phone1" id="phone1" readonly="true" placeholder="Phone number">
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="text" name="emailid" id="emailid" placeholder="Email address">
                </div>

                <!-- Invoice# -->
                <div class="form-group">
                    <label><i class="fas fa-file-invoice"></i> Invoice #</label>
                    <input type="text" name="invoicenumber" id="invoicenumber" placeholder="Invoice number">
                </div>

                <!-- Payment Mode -->
                <div class="form-group">
                    <label><i class="fas fa-credit-card"></i> Payment Mode</label>
                    <div class="radio-group">
                        <label><input type="radio" name="paymentmode" value="cash" checked="true"> <span>Cash</span></label>
                        <label><input type="radio" name="paymentmode" value="chq"> <span>CQ</span></label>
                        <label><input type="radio" name="paymentmode" value="cc"> <span>CC</span></label>
                        <label><input type="radio" name="paymentmode" value="bnktx"> <span>BANK</span></label>
                    </div>
                </div>

                <!-- Cheque Mode (conditional) -->
                <div class="form-group chqmodediv">
                    <label></label>
                    <div class="radio-group">
                        <label><input type="radio" name="cqmods" id="cqmodscdc" value="CDC" checked> <span>CDC</span></label>
                        <label><input type="radio" name="cqmods" id="cqmodspdc" value="PDC"> <span>PDC</span></label>
                    </div>
                </div>

                <!-- Cheque Number (conditional) -->
                <div class="form-group chqmodediv">
                    <label><i class="fas fa-money-check"></i> Cheque Number</label>
                    <input type="text" name="chqnumber" id="chqnumber" placeholder="Cheque number">
                </div>

                <!-- Cheque Bank (conditional) -->
                <div class="form-group chqmodediv">
                    <label><i class="fas fa-university"></i> Bank</label>
                    <input type="text" name="chqbank" id="chqbank" placeholder="Bank name">
                </div>

                <!-- CC Types (conditional) -->
                <div class="form-group ccmodediv">
                    <label><i class="fas fa-credit-card"></i> CC Types</label>
                    <div class="radio-group">
                        <label><input type="radio" name="cctypes" id="cctypesAmex" value="Amex"> <span>Amex</span></label>
                        <label><input type="radio" name="cctypes" id="cctypesmastercard" value="mastercard"> <span>Master Card</span></label>
                        <label><input type="radio" name="cctypes" id="cctypesvisa" value="visa"> <span>VISA</span></label>
                        <label><input type="radio" name="cctypes" id="cctypesdiscover" value="discover"> <span>Discover</span></label>
                    </div>
                </div>

                <!-- Bank Name (conditional) -->
                <div class="form-group bnkmodediv">
                    <label><i class="fas fa-university"></i> Bank Name</label>
                    <input type="text" name="bankname" id="bankname" placeholder="Bank name">
                </div>

                <!-- Date -->
                <div class="form-group" id="chequdateidtr">
                    <label id="chequdateid"><i class="fas fa-calendar-alt"></i> Date</label>
                    <input type="text" name="chqdate" id="chqdate" placeholder="DD-MM-YYYY" required>
                </div>

                <!-- Amount -->
                <div class="form-group">
                    <label><i class="fas fa-coins"></i> Amount</label>
                    <input type="number" step="any" name="amount" id="amount" required placeholder="Enter amount">
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <label></label>
                    <button class="submit-btn" type="submit" id="butsave">
                        <i class="fas fa-save"></i> SAVE
                    </button>
                </div>

                <!-- Hidden Fields -->
                <input type="hidden" name="pri_cust_id" id="pri_cust_id">
                <input type="hidden" name="pscode" id="pscode">
                <input type="hidden" name="glcod" id="glcod">

                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Success! Message sent successfully.
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="page-footer">
    <p>Mwasalat &copy; <?php echo date('Y'); ?> - All Rights Reserved</p>
</div>

    <script>

      $('#configreset').click(function(){
            $("#mcrform").trigger("reset");
 });




$('input[type=radio]').change(function () {
    
    
    if ($(this).prop("checked")) {
       var chkval = $('input[name=paymentmode]:checked', '#mcrform').val(); 

       if(chkval=='chq')
       {
           $(".chqmodediv").show();
           $("#chequdateid").html('<i class="fas fa-calendar-alt"></i> Cheque Date');
           $("#chequdateidtr").show();
		   $("#chqdate").prop('required',true);
       }
       else
       {
        $(".chqmodediv").hide();
        $("#chequdateid").html('<i class="fas fa-calendar-alt"></i> Date');
        
       }


        if(chkval=='cc')
       {
           $(".ccmodediv").show();
        $("#chequdateidtr").hide();
		$("#chqdate").prop('required',false);

       }
       else
       {
        $(".ccmodediv").hide();
       }


        if(chkval=='bnktx')
       {
           $("#chequdateidtr").show();
         $("#chequdateid").html('<i class="fas fa-calendar-alt"></i> Transferred Date');
        //$("#chequdateidtr").hide();
		$("#chqdate").prop('required',false);
       
           $(".bnkmodediv").show();
       }
       else
       {
        $(".bnkmodediv").hide();
       }



    }
    //Here do the stuff you want to do when 'unchecked'
});


      
    $(document).ready(function(){

setInterval(function(){ 
   $.get("checksession.php", function(data){
                
          if(data==0)
           {
          
           window.location.href="logout.php";
           }
          });
  }, 600000);
     
    $("#chequdateidtr").hide();
	$("#chqdate").prop('required',false);
       document.getElementById("HiddenInvalidInputContainer").style.display = "none"; 
      // document.getElementById("chqmodediv").style.display = "none"; 
      $('.chqmodediv').hide();
      $('.ccmodediv').hide();
      $('.bnkmodediv').hide();
      var date_input=$('input[name="chqdate"]'); //our date input has the name "date"
      var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options={
        format: 'dd-mm-yyyy',
        container: container,
        todayHighlight: true,
        autoclose: true,
      };
      date_input.datepicker(options);
      date_input.datepicker('update', new Date());
       date_input.datepicker('minDate', 0);

    })



    $(document).ready(function(){ 

 $('input[name="chk"]').click(function() {
        if($('#chknewcustomer').is(':checked'))
           {
             $("#txtCustomer").val("");
             $("#phone1").val("");
           }
           else
           {
             
           }
    });








      $('#txtCustomer').keyup(function(){  
           var query = $(this).val();  
           //alert(query);
           //$("#id").val().length
           if($('#chknewcustomer').is(':checked'))
           {
              $("#phone1").attr("readonly", false); 


           }
           else
           {
                $.ajax({  
                     url:"getbyId.php",  
                     method:"POST",  
                     dataType: "json",
                     data:{query:query},  
                     success:function(response)  
                     {  
                            // alert(data);
                            //  $('#searchResult').text(data);
                            // $('#emailid').val(data.details.email);
                            // $('#phone1').val(data.details.phone1);

                           var len = response.length;
                    $("#searchResult").empty();
                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var fname = response[i]['fname'];
                      var glcod = response[i]['glcod'];
                        $("#searchResult").append("<li value='"+id+"'>"+fname+" - "+glcod+"</li>");

                    }

                     // binding click event to li
                    $("#searchResult li").bind("click",function(){
                        setText(this);
                    });



                     }  
                });  
              }
             
      });  

      // Set Text to search box and get details
function setText(element){

    var value = $(element).text();
    var userid = $(element).val();


    $("#txt_search").val(value);
    $("#searchResult").empty();
    
    // Request User Details
    $.ajax({
        url: 'getSearch.php',
        type: 'post',
        data: {userid:userid},
        dataType: 'json',
        success: function(response){
            // alert("ss");
            var len = response.length;
            // alert(len);
            $("#userDetail").empty();
            if(len > 0){
                var username = response[0]['id'];
                // alert(username);
              //  var email = response[0]['fname'];
               var email = response[0]['email'];
                 var id = response[0]['id'];
                 var phone1 = response[0]['phone1'];
                 var pscode = response[0]['pscode'];
                 var glcod = response[0]['glcod'];

                $('#emailid').val(email);
                 $('#phone1').val(phone1);
                  $('#pri_cust_id').val(id);
                   $('#pscode').val(pscode);
                    $('#glcod').val(glcod);
                //$("#userDetail").append("Username : " + username + "<br/>");
                //$("#userDetail").append("Email : " + email);
            }
        }

    });
}



     $("#reset").click(function() {
    $(this).closest('form').find("input[type=text], textarea").val("");
});
       $(document).on('click', 'li', function(){  
           $('#txtCustomer').val($(this).text());  
           $('#CustomerList').fadeOut();  
      }); 

        $("#butsave").click(function(){
          debugger
        var emailid     = $('#emailid').val();
		var phone=$('#phone1').val();
        var pri_cust_id = $('#pri_cust_id').val();
        var pscode      = $('#pscode').val();
        var glcod       = $('#glcod').val();
        var txtCustomer = $('#txtCustomer').val();
        var chqnumber   = $('#chqnumber').val();
        var chqdate     = $('#chqdate').val();
		var userlocation = $('#userlocation').val();
        var bankname    = $('#bankname').val();
        var invoicenumber = $('#invoicenumber').val();
        var amount      =   $('#amount').val();
        var chqbank     = $('#chqbank').val();
        var paymentmode = $("input[name='paymentmode']:checked").val();
        var cctypes     = $("input[name='cctypes']:checked").val();
        var cqmods     = $("input[name='cqmods']:checked").val();
	var userlocation = $("select[name='location']").val();
         var company = $("select[name='company']").val();


 if($('#chknewcustomer').is(':checked'))
 {

  var pscode  ="OTHER PAYMENTS";
 }
  
        if(cctypes =="")
        {
          cctypes=false;
        }
        
                $.ajax({
                    url:'processData.php',
                    method:'POST',
                    data: {
                     pri_cust_id: pri_cust_id,
					 phone:phone,
                     pscode: pscode,
                     glcod: glcod,
                    chqnumber: chqnumber,
                    chqdate: chqdate,
                    bankname: bankname,
                    invoicenumber: invoicenumber,
                    amount: amount,
                    emailid: emailid,
                    txtCustomer: txtCustomer,
                    paymentmode:paymentmode,
                    chqbank:chqbank,
                    cctypes:cctypes,
                    cqmods:cqmods,
		userlocation:userlocation,
                  company :company 

                },
                beforeSend: function() {
                  if(txtCustomer=="" || amount=="")
                  {
                    return false;
                  }
                  if(chqdate=="")
                  {
                    return false;
                  }
                  $("#butsave").prop("disabled", true);
                  $("#butsave").text("Processing....");
                },
                 error: function(error) {
        console.log(error);
    },
                success:function(data){
                      //  $('.alert').show();
                     //    setTimeout(function() { $(".alert").hide(); }, 3000);
                        $("#butsave").text("Save");
                       $("#butsave").prop("disabled", false);
                    //    $("#mcrform").trigger("reset");
                      alert("success");
		 window.location.reload();
						//location.reload();
                       
                   }
                });
              
            });
        }); 



</script>
    


</body>

</html>
<?php } ?>