<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
include('dbconnection.php');
//echo $_SESSION['location'];

  $query = "SELECT * FROM `parameters`";
        $result = mysqli_query($con,$query);  
        $fetch = mysqli_fetch_array($result);
        $slno=1;
        $auh_pdc    =   $fetch['auh_pdc']+1;
        $auh_cdc    =   $fetch['auh_cdc']+1;
        $dxb_pdc    =   $fetch['dxb_pdc']+1;
        $dxb_cdc    =   $fetch['dxb_cdc']+1;
       echo $default    =   $fetch['default']+1;

echo $sql_cc = "update `parameters`  set 'default'=$default";
    $slno = $default;

        exit;
$txtCustomer      = $_POST['txtCustomer'];
  $emailid        = $_POST['emailid'];
  $chqnumber      = $_POST['chqnumber'];
  $chqdate        = $_POST['chqdate'];
  $bankname       = $_POST['bankname'];
  $invoicenumber  = $_POST['invoicenumber'];
  $amount         = $_POST['amount'];
  $chqbank        = $_POST['chqbank'];
  $pri_cust_id    = $_POST['pri_cust_id'];
  $cqmods         = $_POST['cqmods'];


  		  $query = "SELECT * FROM `parameters`";
        $result = mysqli_query($con,$query);  
        $fetch = mysqli_fetch_array($result);
        
        $_SESSION['auh_pdc'] =$fetch['auh_pdc'];
        $_SESSION['auh_cdc'] =$fetch['auh_cdc'];
        $_SESSION['dxb_pdc'] =$fetch['dxb_pdc'];
        $_SESSION['dxb_cdc'] =$fetch['dxb_cdc'];




if($_POST['paymentmode'] == 'chq')
{
    if($_POST['cqmods'] == 'PDC')
    {
        if($_SESSION[location] == 'AUH')
        {
           $slno =  $_SESSION['auh_pdc'];
           $sql_cc = "update `parameters`  set auh_pdc=auh_pdc+1";
           
        }
        else
        {
            $slno =  $_SESSION['dxb_pdc'];
            $sql_cc = "update `parameters`  set dxb_pdc=dxb_pdc+1";
        }
      

    }
    elseif($_POST['cqmods'] == 'CDC')
    {
        if($_SESSION[location] == 'AUH')
        {
           $slno =  $_SESSION['auh_cdc'];
           $sql_cc = "update `parameters`  set auh_cdc=auh_cdc+1";
        }
        else
        {
            $slno =  $_SESSION['dxb_cdc'];
            $sql_cc = "update `parameters`  set dxb_cdc=dxb_cdc+1";
        }
       
    }
echo $sql_cc;
    $result = mysqli_query($con,$sql_cc);

    
}
  


?>

<div class="container borderless"  class="border-0" >
         <div class="col-md-6" id="div1">
          <form id="mcrform" name="mcrform" method="POST">
          <table class="table table-borderless">
            <div id="HiddenInvalidInputContainer"></div>
            <tbody>
              <tr>
                <td>Customer</td>
          <td><input type="text" name="txtCustomer" value="111" id="txtCustomer"  required><span style="color:red" id="configreset" class="glyphicon glyphicon-remove"></span></td>
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
              <tr>
                <td id="chequdateid">Date</td>
                <td><input type="text" name="chqdate" id="chqdate" placeholder="DD-MM-YYYY" value="222"  required></td>
              </tr>

              <tr>
                <td>Amount</td>
                <td><input type="number" value="333" name="amount" id="amount" required></td>
              </tr>
 
              <tr>
                
                <td>    </td>
                <td> &nbsp;&nbsp;&nbsp; <button class="btn btn-primary type="submit" id="butsave">
                SAVE </button></td>
              </tr>
 
              <div class="alert alert-success alert-dismissable">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Success! message sent successfully.
        </div>
  

            </tbody>
          </table>
          <div><input type="text" name="pri_cust_id"  id="pri_cust_id" style="visibility: hidden;" >
<input type="text" name="pscode"  id="pscode" style="visibility: hidden;" >
<input type="text" name="glcod"  id="glcod" style="visibility: hidden;" ></div>
        </form>
        </div>

</div>


