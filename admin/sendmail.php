<?Php

require_once('class.phpmailer.php');
include('dbconnection.php');

define ("NODE_TYPE_START",0);
define ("NODE_TYPE_ELEMENT",1);
define ("NODE_TYPE_ENDELEMENT",2);
define ("NODE_TYPE_TEXT",3);
define ("NODE_TYPE_COMMENT",4);
define ("NODE_TYPE_DONE",5);


	$mcrid=$_POST['id'];
	$status="voided";
	$location="";
	$txtcustomer="";
$emailid="";
$company="";
      $query = "SELECT * FROM transaction  where trans_sl_no='$mcrid'";
      $result = mysqli_query($con,$query);  
      $search_arr = array(); 
	
      if(mysqli_num_rows($result) > 0)  
      {  
           while($fetch = mysqli_fetch_array($result))  
           {  
              $emailid =$fetch['emailid'];
              $location = $fetch['location'];
              $txtcustomer=$fetch['customername'];
             $company=trim($fetch['company']);

              
           }
             
            
      }






  $mail = new PHPMailer(true);
  $mail->IsSMTP(); 
try {
      $mail->Host       = "mail.enguae.com"; // SMTP server
      $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
      $mail->SMTPAuth   = true;                  // enable SMTP authentication
      $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
      $mail->Username   = "frc-receipts@fastuae.com"; // SMTP account username
      $mail->Password   = "EngR3ce!pt@#$";        // SMTP account password
	
      $mail->AddAddress('frc-receipts@fastuae.com', 'ACCOUNTS');

    //  $mail->AddAddress('jinuelsa88@gmail.com', 'ACCOUNTS');
      $mail->AddCC($emailid);

	if ($location=='AUH')  // AUH Accounts department.
		{ 	
			$mail->AddBCC("naira@fastuae.com",  "ACCOUNTS"); 

			$mail->AddBCC("osama@fastuae.com",  "ACCOUNTS"); 
				$mail->AddBCC("gawdat@fastuae.com", "ACCOUNTS");
				$mail->AddBCC("wael@fastuae.com",   "ACCOUNTS");  
			     $mail->AddBCC("naira@fastuae.com",  "ACCOUNTS");
			$mail->AddBCC("ryan@fastuae.com",   "ACCOUNTS");
		     
		}
	else
		{ 
                        $mail->AddBCC("kennethpinto@fastuae.com",  "ACCOUNTS"); 
			  $mail->AddBCC("itmanager@enguae.com",   "ACCOUNTS");
			$mail->AddBCC("hamad@fastuae.com",   "ACCOUNTS");
			//$mail->AddBCC("shijoy@fastuae.com",   "ACCOUNTS");
			 $mail->AddBCC("evangeline@fastuae.com",   "ACCOUNTS");
		
		}


if($company=="autostrad")
{
 $mail->SetFrom('frc-receipts@fastuae.com', 'AUTOSTRAD RENT A CAR ACCOUNTS DEPARTMENT');
      $mail->Subject = "VOIDED-MCR RECIPT"." OF ". $txtcustomer ." (MCR NUMBER - ".$mcrid.")";
      $mail->AltBody = 'Dear Sir /Madam, Greeting from AUTOSTRAD Rent A Car '; 



       $contactperson="";

      setlocale(LC_MONETARY, 'ar_AE');
      

        $message = "<htlm>Dear Sir /Madam, <br><br>Greetings from AUTOSTRAD Rent A Car.  <br>
        
		This letter is to inform you that the MCR-".$mcrid." is voided due to some typing errors .
		We will send you the new receipt shortly. Sorry for the inconvenience caused.

        <br><br><b>Regards,<br> AUTOSTRAD RENT A CAR</b>
        </html>";
      $mail->msgHTML($message);

      $mail->Send();

}
else
{
  $mail->SetFrom('frc-receipts@fastuae.com', 'FAST RENT A CAR ACCOUNTS DEPARTMENT');
      $mail->Subject = "VOIDED-MCR RECIPT"." OF ". $txtcustomer ." (MCR NUMBER - ".$mcrid.")";
      $mail->AltBody = 'Dear Sir /Madam, Greeting from Fast Rent A Car '; 



       $contactperson="";

      setlocale(LC_MONETARY, 'ar_AE');
      

        $message = "<htlm>Dear Sir /Madam, <br><br>Greetings from Fast Rent A Car.  <br>
        
		This letter is to inform you that the MCR-".$mcrid." is voided due to some typing errors .
		We will send you the new receipt shortly. Sorry for the inconvenience caused.

        <br><br><b>Regards,<br> FAST RENT A CAR</b>
        </html>";
      $mail->msgHTML($message);

      $mail->Send();


}




	}
	catch(phpmailerException $e)
	{

	}
?>