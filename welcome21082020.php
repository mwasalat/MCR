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
  <style type="text/css">
  body {
    padding-top: 38px !important; 
}
    #HiddenInvalidInputContainer{
      align-content: center;
      color: red;
    }

    .alert{
    display: none;
}

.btn-primary
{
   padding: 10px 20px;
    font-size: 20px;
    border-radius: 10px;
    width:50%;    //Specify your width here

}

    .clear{
 clear:both;
 margin-top: 20px;
}

#searchResult{
 list-style: none;
 padding-top: 40px;
 /*width: 250px;*/
 position: absolute;
 margin: 0;
 height:200px;
 overflow: auto;
 z-index: 999;
}

#searchResult li{
 background: lavender;
 padding: 4px;
 margin-bottom: 1px;
}


.table-borderless td,
.table-borderless th {
    border: 0;
}


#searchResult li:nth-child(even){
 background: cadetblue;
 color: white;
}

#searchResult li:hover{
 cursor: pointer;
}

input[type=text]{
 padding: 5px;
 width: 200px;
 letter-spacing: 1px;
}

input[type=number]{
 padding: 5px;
 width: 200px;
 letter-spacing: 1px;
}
.borderless td, .borderless th {
    border: none;
}

.navbar-nav > li > a {padding-top:3px !important; padding-bottom:3px !important;}
.navbar {min-height:22px !important}

  </style>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
    <title>Welcome </title>
    <link href="css/heroic-features.css" rel="stylesheet">
    <!-- Bootstrap Date-Picker Plugin -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
  border-top:0px;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance:textfield;
}
</style>
</head>
<body>


    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container hidden-xs
        ">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
               
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">

                <li>
                        <a href="#">MCR</a>
                    </li>
                    <li>
                        <a href="#"><?php echo $_SESSION['name'];?></a> 
                    </li>
                    <li>
                        <a href="logout.php">Logout</a> 
                    </li>
                  <li>
                        <a href="logout.php">Logout123</a> 
                    </li>
                </ul>
            </div>
        </div>
    </nav>
   <div class="container borderless"  class="border-0" >
         <div class="col-md-6" id="div1">
          <form id="mcrform" name="mcrform" method="POST">
          <table class="table table-borderless">
            <div id="HiddenInvalidInputContainer"></div>
            <tbody>
              <tr>
                <td>Customer<span style="color:red" id="configreset" class="glyphicon glyphicon-remove"></span></td>
          <td><input type="text" name="txtCustomer" id="txtCustomer"  required></td>
          <ul id="searchResult"></ul>
              </tr>
            
              <tr>
                <td>Phone</td>
                <td><input type="text" name="phone1" id="phone1" readonly="true"></td>
              </tr>
               <tr>
                <td>Email</td>
                <td><input type="text" name="emailid" id="emailid" >

                </td>
              </tr>
              <tr>
                <td>Invoice#</td>
                <td><input type="text" name="invoicenumber" id="invoicenumber"></td>
              </tr>
              <tr>
                <td>Payment Mode</td>
                <td>
                  <input type="radio" name="paymentmode" value="cash" checked="true"> Cash 
                  <input type="radio"  class="form-check-input" name="paymentmode" value="chq"> CQ  
                   <input type="radio" name="paymentmode" value="cc"> CC 
                   <input type="radio" name="paymentmode" value="bnktx">BANK
                </td>
              </tr>


              <tr class="chqmodediv">
                        <td></td>
                        <td><input type="radio" name="cqmods" id="cqmodscdc" value="CDC" checked>CDC &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="cqmods" id="cqmodspdc" value="PDC">PDC</td>
                </tr>


                <tr class="chqmodediv">
                        <td>Cheque number</td>
                        <td><input type="text" name="chqnumber" id="chqnumber"></td>
                </tr>
                <tr class="chqmodediv">
                        <td>Bank</td>
                        <td><input type="text" name="chqbank" id="chqbank"></td>
                </tr>
                <tr class="ccmodediv">
                        <td>CC Types</td>
                        <td><input type="radio" name="cctypes" id="cctypesAmex" value="Amex"> &nbsp;&nbsp;Amex &nbsp;&nbsp;<input type="radio" name="cctypes" id="cctypesmastercard" value="mastercard"> &nbsp;&nbsp;Master Card &nbsp;&nbsp;<input type="radio" name="cctypes" id="cctypesvisa" value="visa">&nbsp;&nbsp;VISA&nbsp;&nbsp;<input type="radio" name="cctypes" id="cctypesdiscover" value="discover">&nbsp;&nbsp;Discover</td>
                </tr>
                <tr class="bnkmodediv">
                        <td>Bank Name</td>
                        <td><input type="text" name="bankname" id="bankname"></td>
                </tr>
                  </div>
              <tr id="chequdateidtr">
                <td id="chequdateid">Date</td>
                <td><input type="text" name="chqdate" id="chqdate" placeholder="DD-MM-YYYY" required></td>
              </tr>

              <tr>
                <td>Amount</td>
                <td><input type="number" name="amount" id="amount" required></td>
              </tr>
 
              <tr>
                
                <td>    </td>
                <td> &nbsp;&nbsp;&nbsp; <button class="btn btn-primary type="submit" id="butsave">
                SAVE </button></td>
              </tr>
 
              <div class="alert alert-success alert-dismissable">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			Success! This Test message sent successfully.
        </div>

            </tbody>
          </table>
          <div><input type="text" name="pri_cust_id"  id="pri_cust_id" style="visibility: hidden;" >
<input type="text" name="pscode"  id="pscode" style="visibility: hidden;" >
<input type="text" name="glcod"  id="glcod" style="visibility: hidden;" ></div>
        </form>
        </div>

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
           $("#chequdateid").html("Cheque Date");
           $("#chequdateidtr").show();
       }
       else
       {
        $(".chqmodediv").hide();
        $("#chequdateid").html("Date");
        
       }


        if(chkval=='cc')
       {
           $(".ccmodediv").show();
        $("#chequdateidtr").hide();

       }
       else
       {
        $(".ccmodediv").hide();
       }


        if(chkval=='bnktx')
       {
        $("#chequdateidtr").hide();
       
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
    $("#chequdateidtr").hide();
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
      $('#txtCustomer').keyup(function(){  
           var query = $(this).val();  
           //alert(query);
           //$("#id").val().length
           
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

                        $("#searchResult").append("<li value='"+id+"'>"+fname+"</li>");

                    }

                     // binding click event to li
                    $("#searchResult li").bind("click",function(){
                        setText(this);
                    });



                     }  
                });  
         
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

        var emailid     = $('#emailid').val();
        var pri_cust_id = $('#pri_cust_id').val();
        var pscode      = $('#pscode').val();
        var glcod       = $('#glcod').val();
        var txtCustomer = $('#txtCustomer').val();
        var chqnumber   = $('#chqnumber').val();
        var chqdate     = $('#chqdate').val();
        var bankname    = $('#bankname').val();
        var invoicenumber = $('#invoicenumber').val();
        var amount      =   $('#amount').val();
        var chqbank     = $('#chqbank').val();
        var paymentmode = $("input[name='paymentmode']:checked").val();
        var cctypes     = $("input[name='cctypes']:checked").val();
        var cqmods     = $("input[name='cqmods']:checked").val();
        if(cctypes =="")
        {
          cctypes=false;
        }
        
                $.ajax({
                    url:'processData.php',
                    method:'POST',
                    data: {
                     pri_cust_id: pri_cust_id,
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
                    cqmods:cqmods
                },
                beforeSend: function() {
                  if(txtCustomer=="")
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
                         $('.alert').show();
                         setTimeout(function() { $(".alert").hide(); }, 3000);
                        $("#butsave").text("Save");
                        $("#butsave").prop("disabled", false);
                        $("#mcrform").trigger("reset");
                       
                   }
                });
              
            });
        }); 



</script>
    


</body>

</html>
<?php } ?>