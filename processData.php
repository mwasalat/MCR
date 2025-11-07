<?php
ini_set('display_errors', 1);
session_start();
include('dbconnection.php');
include('dbconnection_fleet.php');
require('fpdf.php');
require_once('class.phpmailer.php');
define ("NODE_TYPE_START",0);
define ("NODE_TYPE_ELEMENT",1);
define ("NODE_TYPE_ENDELEMENT",2);
define ("NODE_TYPE_TEXT",3);
define ("NODE_TYPE_COMMENT",4);
define ("NODE_TYPE_DONE",5);
$sql_cc="";
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
  $userDefaultLocation=$_POST['userlocation'];
   $company=$_POST['company'];
  $phone=$_POST['phone'];
  $salesmannid="";
  $salesmanname="";
  $pscode="";
  $_SESSION['salesman']="";


  if($pri_cust_id!="")
  {
	  $query = "SELECT salmn,pscode FROM pri_cust WHERE pri_cust_id='".$pri_cust_id."%'";  
      $result = mysqli_query($conn,$query);  
      if(mysqli_num_rows($result) > 0)  
      {  
           while($fetch = mysqli_fetch_array($result))  
           {  
	         $salesmannid=  $fetch['salmn'];
             $pscode=$fetch['pscode'];
		   }
	  }
	 
	  
	  if($salesmannid!="")
	  {
		  $query = "SELECT ename FROM emp_file WHERE empno='".$salesmannid."'";  
		  $result = mysqli_query($conn,$query);  
		  if(mysqli_num_rows($result) > 0)  
		  {  
			   while($fetch = mysqli_fetch_array($result))  
			   {  
				 $salesmanname=  $fetch['ename'];
				 $_SESSION['salesman']=$salesmanname;
			   }
		  }
     }
  }
  
  
  if($_POST['userlocation']!="")
  {
	  $_SESSION['location']=$_POST['userlocation'];
  }  
  $afterpoint = substr($amount, strpos($amount, ".") + 1);

 $len_after_point = strlen($afterpoint);
if ($len_after_point > 1 && substr($afterpoint, 0, 1) == '0') {

   $f               = new NumberFormatter("en", NumberFormatter::SPELLOUT);
  
    $amount_in_words = $f->format($amount);
    $amount_in_words = str_replace("point", "and", $amount_in_words);
    $amount_in_words = str_replace("zero", "", $amount_in_words);
    
} else {
  
    $amount_in_words = numberTowords($amount);
    if ($afterpoint == 1) {
        
        $amount_in_words = numberTowords($amount);
       
    }
    
     else {
        $pieces    = explode(' ', $amount_in_words);
        $last_word = array_pop($pieces);
        if ($last_word == 'ZERO') {
            $amount_in_words = preg_replace('/\W\w+\s*(\W*)$/', '$1', $amount_in_words);
        }

        if(strpos($amount_in_words, "and") !== false){
    $amount_in_words=$amount_in_words." FILS";
} else{
   $amount_in_words=$amount_in_words;
}
    }
    
}
    $amount_in_words= "AED " . $amount_in_words . " ONLY";
    

    $sqlrecs = "SELECT MAX(idtransaction) as max_id FROM transaction";  
    $result = mysqli_query($con, $sqlrecs);
    $row = $result->fetch_assoc();
    $mcrno = $row['max_id'];
    $_SESSION['mcrno'] = $mcrno;




  //$customername   =$_POST['customername'];
  $customername   =$_POST['txtCustomer'];
$flagchq = $flagcc = $flagbnk = $flagcash = false;
$paymentmode = $_POST['paymentmode'];
if(isset($_POST['cctypes']))
{
$cctypes    = $_POST['cctypes'];
}
else
{
	
	$cctypes    = "";
}
if($paymentmode == "chq") 
{
    $pm = "CHEQUE";
    $pm =  "CHEQUE-".$_POST['cqmods'];
    $flagchq = true;
}
if($paymentmode == "cc")   
{
    $pm = "CREDIT CARD";
    $flagcc     = true;
    $cctypes    = $_POST['cctypes'];

    if($cctypes=='Amex')
    {
        $cctype = 'Amex';
    }
     if($cctypes=='mastercard')
    {
        $cctype = 'Master Card';
    }
     if($cctypes=='visa')
    {
        $cctype = 'Visa';
    }
     if($cctypes=='discover')
    {
        $cctype = 'Discover';
    }

} 
else
{
    $cctype = "";
}
if($paymentmode == "bnktx")  
{
    $pm = "BANK TRANSFER";
    $flagbnk = true;
} 
if($paymentmode == "cash")  
{
    $pm = "CASH";
    $flagcash = true;

} 

$companyid='001';
if($_POST['company']=="autostrad"){
$companyid='007';
}else if($_POST['company']=="fastline"){
$companyid='600';
}else if($_POST['company']=="fast"){
$companyid='001';
}else if($_POST['company']=="fastinvestment"){
$companyid='601';
}else{
$companyid='001';	
}


        $query = "SELECT * FROM `parameters` where company_id='$companyid'";
    
        $result = mysqli_query($con,$query);  
        $fetch = mysqli_fetch_array($result);

        $slno=1;

        $auh_pdc            =   $fetch['auh_pdc']+1;
        $rest_sl_nos        =   $fetch['auh_rest']+1;

	$dxb_pdc	=	$fetch['dxb_pdc']+1;
	$dxb_rest	=	$fetch['dxb_rest']+1;

          
  if(isset($_POST['cqmods']) && $paymentmode == 'chq' )
    {
        $trans_type = $_POST['cqmods'];
    }
    else
    {
        $trans_type = $pm;
    }

if($txtCustomer!="")
{
if ($userDefaultLocation=='AUH')  
{

    if($trans_type == 'PDC')
    {
        $sql_cc = "update `parameters`  set `auh_pdc`='$auh_pdc' where company_id='$companyid'" ;
        $slno = $auh_pdc;
    }
    else
    {
            $sql_cc = "update `parameters`  set `auh_rest`='$rest_sl_nos' where company_id='$companyid'"; //auh_rest
            $slno = $rest_sl_nos;
    }
}
	
else
	{
        	if($trans_type == 'PDC')
            {
                $sql_cc = "update `parameters`  set `dxb_pdc`='$dxb_pdc' where company_id='$companyid'";
                $slno = $dxb_pdc;
            }
            else
            {
                    $sql_cc = "update `parameters`  set `dxb_rest`='$dxb_rest' where company_id='$companyid'"; //auh_rest
                    $slno = $dxb_rest;
            }
	}

    
}
 

    $_SESSION['mcr'] = $slno;
  //  $result_cc = mysqli_query($con,$sql_cc);


function formatcurrency($floatcurr, $curr = 'USD')
{

$currencies = array(
        'ARS' => array(NULL,2,',','.',0),          //  Argentine Peso
        'AMD' => array(NULL,2,'.',',',0),          //  Armenian Dram
        'AWG' => array(NULL,2,'.',',',0),          //  Aruban Guilder
        'AUD' => array('AU$',2,'.',' ',0),          //  Australian Dollar
        'BSD' => array(NULL,2,'.',',',0),          //  Bahamian Dollar
        'BHD' => array(NULL,3,'.',',',0),          //  Bahraini Dinar
        'BDT' => array(NULL,2,'.',',',0),          //  Bangladesh, Taka
        'BZD' => array(NULL,2,'.',',',0),          //  Belize Dollar
        'BMD' => array(NULL,2,'.',',',0),          //  Bermudian Dollar
        'BOB' => array(NULL,2,'.',',',0),          //  Bolivia, Boliviano
        'BAM' => array(NULL,2,'.',',',0),          //  Bosnia and Herzegovina, Convertible Marks
        'BWP' => array(NULL,2,'.',',',0),          //  Botswana, Pula
        'BRL' => array('R$',2,',','.',0),          //  Brazilian Real
        'BND' => array(NULL,2,'.',',',0),          //  Brunei Dollar
        'CAD' => array('CA$',2,'.',',',0),          //  Canadian Dollar
        'KYD' => array(NULL,2,'.',',',0),          //  Cayman Islands Dollar
        'CLP' => array(NULL,0,'','.',0),           //  Chilean Peso
        'CNY' => array('CN&yen;',2,'.',',',0),          //  China Yuan Renminbi
        'COP' => array(NULL,2,',','.',0),          //  Colombian Peso
        'CRC' => array(NULL,2,',','.',0),          //  Costa Rican Colon
        'HRK' => array(NULL,2,',','.',0),          //  Croatian Kuna
        'CUC' => array(NULL,2,'.',',',0),          //  Cuban Convertible Peso
        'CUP' => array(NULL,2,'.',',',0),          //  Cuban Peso
        'CYP' => array(NULL,2,'.',',',0),          //  Cyprus Pound
        'CZK' => array('Kc',2,'.',',',1),          //  Czech Koruna
        'DKK' => array(NULL,2,',','.',0),          //  Danish Krone
        'DOP' => array(NULL,2,'.',',',0),          //  Dominican Peso
        'XCD' => array('EC$',2,'.',',',0),          //  East Caribbean Dollar
        'EGP' => array(NULL,2,'.',',',0),          //  Egyptian Pound
        'SVC' => array(NULL,2,'.',',',0),          //  El Salvador Colon
        'EUR' => array('&euro;',2,',','.',0),          //  Euro
        'GHC' => array(NULL,2,'.',',',0),          //  Ghana, Cedi
        'GIP' => array(NULL,2,'.',',',0),          //  Gibraltar Pound
        'GTQ' => array(NULL,2,'.',',',0),          //  Guatemala, Quetzal
        'HNL' => array(NULL,2,'.',',',0),          //  Honduras, Lempira
        'HKD' => array('HK$',2,'.',',',0),          //  Hong Kong Dollar
        'HUF' => array('HK$',0,'','.',0),           //  Hungary, Forint
        'ISK' => array('kr',0,'','.',1),           //  Iceland Krona
        'INR' => array('&#2352;',2,'.',',',0),          //  Indian Rupee ₹
        'IDR' => array(NULL,2,',','.',0),          //  Indonesia, Rupiah
        'IRR' => array(NULL,2,'.',',',0),          //  Iranian Rial
        'JMD' => array(NULL,2,'.',',',0),          //  Jamaican Dollar
        'JPY' => array('&yen;',0,'',',',0),           //  Japan, Yen
        'JOD' => array(NULL,3,'.',',',0),          //  Jordanian Dinar
        'KES' => array(NULL,2,'.',',',0),          //  Kenyan Shilling
        'KWD' => array(NULL,3,'.',',',0),          //  Kuwaiti Dinar
        'LVL' => array(NULL,2,'.',',',0),          //  Latvian Lats
        'LBP' => array(NULL,0,'',' ',0),           //  Lebanese Pound
        'LTL' => array('Lt',2,',',' ',1),          //  Lithuanian Litas
        'MKD' => array(NULL,2,'.',',',0),          //  Macedonia, Denar
        'MYR' => array(NULL,2,'.',',',0),          //  Malaysian Ringgit
        'MTL' => array(NULL,2,'.',',',0),          //  Maltese Lira
        'MUR' => array(NULL,0,'',',',0),           //  Mauritius Rupee
        'MXN' => array('MX$',2,'.',',',0),          //  Mexican Peso
        'MZM' => array(NULL,2,',','.',0),          //  Mozambique Metical
        'NPR' => array(NULL,2,'.',',',0),          //  Nepalese Rupee
        'ANG' => array(NULL,2,'.',',',0),          //  Netherlands Antillian Guilder
        'ILS' => array('&#8362;',2,'.',',',0),          //  New Israeli Shekel ₪
        'TRY' => array(NULL,2,'.',',',0),          //  New Turkish Lira
        'NZD' => array('NZ$',2,'.',',',0),          //  New Zealand Dollar
        'NOK' => array('kr',2,',','.',1),          //  Norwegian Krone
        'PKR' => array(NULL,2,'.',',',0),          //  Pakistan Rupee
        'PEN' => array(NULL,2,'.',',',0),          //  Peru, Nuevo Sol
        'UYU' => array(NULL,2,',','.',0),          //  Peso Uruguayo
        'PHP' => array(NULL,2,'.',',',0),          //  Philippine Peso
        'PLN' => array(NULL,2,'.',' ',0),          //  Poland, Zloty
        'GBP' => array('&pound;',2,'.',',',0),          //  Pound Sterling
        'OMR' => array(NULL,3,'.',',',0),          //  Rial Omani
        'RON' => array(NULL,2,',','.',0),          //  Romania, New Leu
        'ROL' => array(NULL,2,',','.',0),          //  Romania, Old Leu
        'RUB' => array(NULL,2,',','.',0),          //  Russian Ruble
        'SAR' => array(NULL,2,'.',',',0),          //  Saudi Riyal
        'SGD' => array(NULL,2,'.',',',0),          //  Singapore Dollar
        'SKK' => array(NULL,2,',',' ',0),          //  Slovak Koruna
        'SIT' => array(NULL,2,',','.',0),          //  Slovenia, Tolar
        'ZAR' => array('R',2,'.',' ',0),          //  South Africa, Rand
        'KRW' => array('&#8361;',0,'',',',0),           //  South Korea, Won ₩
        'SZL' => array(NULL,2,'.',', ',0),         //  Swaziland, Lilangeni
        'SEK' => array('kr',2,',','.',1),          //  Swedish Krona
        'CHF' => array('SFr ',2,'.','\'',0),         //  Swiss Franc 
        'TZS' => array(NULL,2,'.',',',0),          //  Tanzanian Shilling
        'THB' => array('&#3647;',2,'.',',',1),          //  Thailand, Baht ฿
        'TOP' => array(NULL,2,'.',',',0),          //  Tonga, Paanga
        'AED' => array(NULL,2,'.',',',0),          //  UAE Dirham
        'UAH' => array(NULL,2,',',' ',0),          //  Ukraine, Hryvnia
        'USD' => array('$',2,'.',',',0),          //  US Dollar
        'VUV' => array(NULL,0,'',',',0),           //  Vanuatu, Vatu
        'VEF' => array(NULL,2,',','.',0),          //  Venezuela Bolivares Fuertes
        'VEB' => array(NULL,2,',','.',0),          //  Venezuela, Bolivar
        'VND' => array('&#x20ab;',0,'','.',0),           //  Viet Nam, Dong ₫
        'ZWD' => array(NULL,2,'.',' ',0),          //  Zimbabwe Dollar
        );
    
        
        //rupees weird format
        if ($curr == "INR")
            $number = formatinr($floatcurr);
        else 
            $number = number_format($floatcurr,$currencies[$curr][1],$currencies[$curr][2],$currencies[$curr][3]);
        //adding the symbol in the back
        if ($currencies[$curr][0] === NULL)
            $number.= ' '.$curr;
        elseif ($currencies[$curr][4]===1)
            $number.= $currencies[$curr][0];
        //normally in front
        else
            $number = $currencies[$curr][0].$number;
        return $number;
    }
    /**
     * formats to indians rupees
     * from http://www.joelpeterson.com/blog/2011/03/formatting-over-100-currencies-in-php/
     * @param  float $input money
     * @return string        formated currency
     */
    function formatinr($input){
        //CUSTOM FUNCTION TO GENERATE ##,##,###.##
        $dec = "";
        $pos = strpos($input, ".");
        if ($pos === false){
            //no decimals   
        } else {
            //decimals
            $dec = substr(round(substr($input,$pos),2),1);
            $input = substr($input,0,$pos);
        }
        $num = substr($input,-3); //get the last 3 digits
        $input = substr($input,0, -3); //omit the last 3 digits already stored in $num
        while(strlen($input) > 0) //loop the process - further get digits 2 by 2
        {
            $num = substr($input,-2).",".$num;
            $input = substr($input,0,-2);
        }
        return $num . $dec;
    }
    //echo '<br>'.;             //1,000,045.25 (USD)   

function numberTowords($num)
{

$ones = array(
0 =>"ZERO",
1 => "ONE",
2 => "TWO",
3 => "THREE",
4 => "FOUR",
5 => "FIVE",
6 => "SIX",
7 => "SEVEN",
8 => "EIGHT",
9 => "NINE",
10 => "TEN",
11 => "ELEVEN",
12 => "TWELVE",
13 => "THIRTEEN",
14 => "FOURTEEN",
15 => "FIFTEEN",
16 => "SIXTEEN",
17 => "SEVENTEEN",
18 => "EIGHTEEN",
19 => "NINETEEN",
"014" => "FOURTEEN"
);




$tens = array( 
0 => "ZERO",
1 => "TEN",
2 => "TWENTY",
3 => "THIRTY", 
4 => "FORTY", 
5 => "FIFTY", 
6 => "SIXTY", 
7 => "SEVENTY", 
8 => "EIGHTY", 
9 => "NINETY" 
); 


$hundreds = array( 
"HUNDRED", 
"THOUSAND", 
"MILLION", 
"BILLION", 
"TRILLION", 
"QUARDRILLION" 
); /*limit t quadrillion */
$num = number_format($num,2,".",","); 
$num_arr = explode(".",$num); 
$wholenum = $num_arr[0]; 
$decnum = $num_arr[1]; 
$whole_arr = array_reverse(explode(",",$wholenum)); 
krsort($whole_arr,1); 
$rettxt = ""; 
foreach($whole_arr as $key => $i){
    
while(substr($i,0,1)=="0")
        $i=substr($i,1,5);
if($i < 20){ 
/* echo "getting:".$i; */
$rettxt .= $ones[$i]; 
}elseif($i < 100){ 
if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
}else{ 
if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
} 
if($key > 0){ 
$rettxt .= " ".$hundreds[$key]." "; 
}
} 
if($decnum > 0){
$rettxt .= " and ";
if($decnum < 20){
$rettxt .= $ones[$decnum];
}elseif($decnum < 100){
$rettxt .= $tens[substr($decnum,0,1)];
$rettxt .= " ".$ones[substr($decnum,1,1)];
}
}
return $rettxt;
}
extract($_POST);
if(isset($convert))
{
echo "<p align='center' style='color:blue'>".numberTowords("$num")."</p>";
}
class HtmlParser {

 
    public $iNodeType;

    /**
     * Field iNodeName.
     * For elements, it's the name of the element.
     */
    public $iNodeName = "";

    /**
     * Field iNodeValue.
     * For text nodes, it's the text.
     */
    public $iNodeValue = "";

    /**
     * Field iNodeAttributes.
     * A string-indexed array containing attribute values
     * of the current node. Indexes are always lowercase.
     */
    public $iNodeAttributes;

    // The following fields are private:

    private $iHtmlText;
    private $iHtmlTextLength;
    private $iHtmlTextIndex = 0;
    private $iHtmlCurrentChar;
    private $BOE_ARRAY;
    private $B_ARRAY;
    private $BOS_ARRAY;
    
    /**
     * Constructor.
     * Constructs an HtmlParser instance with
     * the HTML text given.
     */
    function __construct ($aHtmlText) {
        $this->iHtmlText = $aHtmlText;
        $this->iHtmlTextLength = strlen($aHtmlText);
        $this->iNodeAttributes = array();
        $this->setTextIndex (0);

        $this->BOE_ARRAY = array (" ", "\t", "\r", "\n", "=" );
        $this->B_ARRAY = array (" ", "\t", "\r", "\n" );
        $this->BOS_ARRAY = array (" ", "\t", "\r", "\n", "/" );
    }

    /**
     * Method parse.
     * Parses the next node. Returns false only if
     * the end of the HTML text has been reached.
     * Updates values of iNode* fields.
     */
    function parse() {
        $text = $this->skipToElement();
        if ($text != "") {
            $this->iNodeType = NODE_TYPE_TEXT;
            $this->iNodeName = "Text";
            $this->iNodeValue = $text;
            return true;
        }
        return $this->readTag();
    }

    function clearAttributes() {
        $this->iNodeAttributes = array();
    }

    function readTag() {
        if ($this->iCurrentChar != "<") {
            $this->iNodeType = NODE_TYPE_DONE;
            return false;
        }
        $this->clearAttributes();
        $this->skipMaxInTag ("<", 1);
        if ($this->iCurrentChar == '/') {
            $this->moveNext();
            $name = $this->skipToBlanksInTag();
            $this->iNodeType = NODE_TYPE_ENDELEMENT;
            $this->iNodeName = $name;
            $this->iNodeValue = "";            
            $this->skipEndOfTag();
            return true;
        }
        $name = $this->skipToBlanksOrSlashInTag();
        if (!$this->isValidTagIdentifier ($name)) {
                $comment = false;
                if (strpos($name, "!--") === 0) {
                    $ppos = strpos($name, "--", 3);
                    if (strpos($name, "--", 3) === (strlen($name) - 2)) {
                        $this->iNodeType = NODE_TYPE_COMMENT;
                        $this->iNodeName = "Comment";
                        $this->iNodeValue = "<" . $name . ">";
                        $comment = true;                        
                    }
                    else {
                        $rest = $this->skipToStringInTag ("-->");    
                        if ($rest != "") {
                            $this->iNodeType = NODE_TYPE_COMMENT;
                            $this->iNodeName = "Comment";
                            $this->iNodeValue = "<" . $name . $rest;
                            $comment = true;
                            // Already skipped end of tag
                            return true;
                        }
                    }
                }
                if (!$comment) {
                    $this->iNodeType = NODE_TYPE_TEXT;
                    $this->iNodeName = "Text";
                    $this->iNodeValue = "<" . $name;
                    return true;
                }
        }
        else {
                $this->iNodeType = NODE_TYPE_ELEMENT;
                $this->iNodeValue = "";
                $this->iNodeName = $name;
                while ($this->skipBlanksInTag()) {
                    $attrName = $this->skipToBlanksOrEqualsInTag();
                    if ($attrName != "" && $attrName != "/") {
                        $this->skipBlanksInTag();
                        if ($this->iCurrentChar == "=") {
                            $this->skipEqualsInTag();
                            $this->skipBlanksInTag();
                            $value = $this->readValueInTag();
                            $this->iNodeAttributes[strtolower($attrName)] = $value;
                        }
                        else {
                            $this->iNodeAttributes[strtolower($attrName)] = "";
                        }
                    }
                }
        }
        $this->skipEndOfTag();
        return true;            
    }

    function isValidTagIdentifier ($name) {
        return preg_match ("/^[A-Za-z0-9_\\-]+$/", $name);
    }
    
    function skipBlanksInTag() {
        return "" != ($this->skipInTag ($this->B_ARRAY));
    }

    function skipToBlanksOrEqualsInTag() {
        return $this->skipToInTag ($this->BOE_ARRAY);
    }

    function skipToBlanksInTag() {
        return $this->skipToInTag ($this->B_ARRAY);
    }

    function skipToBlanksOrSlashInTag() {
        return $this->skipToInTag ($this->BOS_ARRAY);
    }

    function skipEqualsInTag() {
        return $this->skipMaxInTag ("=", 1);
    }

    function readValueInTag() {
        $ch = $this->iCurrentChar;
        $value = "";
        if ($ch == "\"") {
            $this->skipMaxInTag ("\"", 1);
            $value = $this->skipToInTag ("\"");
            $this->skipMaxInTag ("\"", 1);
        }
        else if ($ch == "'") {
            $this->skipMaxInTag ("'", 1);
            $value = $this->skipToInTag ("'");
            $this->skipMaxInTag ("'", 1);
        }                
        else {
            $value = $this->skipToBlanksInTag();
        }
        return $value;
    }

    function setTextIndex ($index) {
        $this->iHtmlTextIndex = $index;
        if ($index >= $this->iHtmlTextLength) {
            $this->iCurrentChar = -1;
        }
        else {
            $this->iCurrentChar = $this->iHtmlText{$index};
        }
    }

    function moveNext() {
        if ($this->iHtmlTextIndex < $this->iHtmlTextLength) {
            $this->setTextIndex ($this->iHtmlTextIndex + 1);
            return true;
        }
        else {
            return false;
        }
    }

    function skipEndOfTag() {
        while (($ch = $this->iCurrentChar) !== -1) {
            if ($ch == ">") {
                $this->moveNext();
                return;
            }
            $this->moveNext();
        }
    }

    function skipInTag ($chars) {
        $sb = "";
        while (($ch = $this->iCurrentChar) !== -1) {
            if ($ch == ">") {
                return $sb;
            } else {
                $match = false;
                for ($idx = 0; $idx < count($chars); $idx++) {
                    if ($ch == $chars[$idx]) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    return $sb;
                }
                $sb .= $ch;
                $this->moveNext();
            }
        }
        return $sb;
    }

    function skipMaxInTag ($chars, $maxChars) {
        $sb = "";
        $count = 0;
        while (($ch = $this->iCurrentChar) !== -1 && $count++ < $maxChars) {
            if ($ch == ">") {
                return $sb;
            } else {
                $match = false;
                for ($idx = 0; $idx < count($chars); $idx++) {
                    if ($ch == $chars[$idx]) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    return $sb;
                }
                $sb .= $ch;
                $this->moveNext();
            }
        }
        return $sb;
    }

    function skipToInTag ($chars) {
        $sb = "";
        while (($ch = $this->iCurrentChar) !== -1) {
            $match = $ch == ">";
            if (!$match) {
                for ($idx = 0; $idx < count($chars); $idx++) {
                    if ($ch == $chars[$idx]) {
                        $match = true;
                        break;
                    }
                }
            }
            if ($match) {
                return $sb;
            }
            $sb .= $ch;
            $this->moveNext();
        }
        return $sb;
    }

    function skipToElement() {
        $sb = "";
        while (($ch = $this->iCurrentChar) !== -1) {
            if ($ch == "<") {
                return $sb;
            }
            $sb .= $ch;
            $this->moveNext();
        }
        return $sb;             
    }

    /**
     * Returns text between current position and $needle,
     * inclusive, or "" if not found. The current index is moved to a point
     * after the location of $needle, or not moved at all
     * if nothing is found.
     */
    function skipToStringInTag ($needle) {
        $pos = strpos ($this->iHtmlText, $needle, $this->iHtmlTextIndex);
        if ($pos === false) {
            return "";
        }
        $top = $pos + strlen($needle);
        $retvalue = substr ($this->iHtmlText, $this->iHtmlTextIndex, $top - $this->iHtmlTextIndex);
        $this->setTextIndex ($top);
        return $retvalue;
    }
}

function HtmlParser_ForFile ($fileName) { 
    return HtmlParser_ForURL($fileName);
}

function HtmlParser_ForURL ($url) {
    $fp = fopen ($url, "r");
    $content = "";
    while (true) {
        $data = fread ($fp, 8192);
        if (strlen($data) == 0) {
            break;
        }
        $content .= $data;
    }
    fclose ($fp);
    return new HtmlParser ($content);
}

//function hex2dec
//returns an associative array (keys: R,G,B) from a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['G']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
////////////////////////////////////

class PDF extends FPDF
{
//variables of html parser
protected $B;
protected $I;
protected $U;
protected $HREF;
protected $fontList;
protected $issetfont;
protected $issetcolor;
protected $txtCustomer;


function Header()
{
    
     $chqdate      =$_POST['chqdate'];
     $invoice_date      = date("d-m-Y");
     $y = date('dhis');
     $rand_number = (int)($y/105);
     $mcno  =$_POST['pri_cust_id'];
if($_POST['company']=="autostrad")
{
$this->Image('img/autostrad.jpg',10,0);

}
 if($_POST['company']=="fastline")
{
$this->Image('img/fastlinelogo.jpg',10,0);

}
if($_POST['company']=="fast")
{
     $this->Image('img/frc-logo.jpg',10,0);
}
if($_POST['company']=="fastinvestment")
{
     $this->Image('img/fastinvestment-logo.jpg',10,0);
}
     $this->SetFont('Arial','B',12);
    // $this->Text( 130, 20, 'MCR#:    MCR'.$_SESSION['mcrno'] );
    $this->Text( 130, 20, 'MCR#:    MCR'.$_SESSION['mcr'] );
    $this->Text( 130, 25, 'DATE:    '.$invoice_date );
    $this->SetFont('Arial','B',22);
    $this->Text( 85, 50, 'RECEIPT' );
    $this->Ln(20);
}


function Footer()
{
    $this->SetFont('Arial','B',10);
    $this->Text( 10, 155, 'RECEIVED BY:');
    $this->Text( 36, 155,  strtoupper($_SESSION['name']));
	if(isset($_SESSION['salesman']))
	{
	$this->Text( 10, 160,  "SALES MAN : ".strtoupper($_SESSION['salesman']));
    $this->SetFont('Arial','B',10);
    }
	else
	{
		$this->Text( 10, 160,  "SALES MAN : ");
        $this->SetFont('Arial','B',10);
	}

    $this->Text( 10, 165,  "LOCATION : ".strtoupper($_SESSION['location']));
    $this->SetFont('Arial','B',10);
if($_POST['company']=="autostrad")
{
    if($_POST['userlocation']=="DXB")
   {
       $this->Text( 10, 170, '(AUTOSTRAD INTERNATIONAL VEHICLE LEASING  LLC)' );

   }
   else
   {
      $this->Text( 10, 170, '(AUTOSTRAD INTERNATIONAL VEHICLE LEASING - SOLE PROPRIETORSHIP L.L.C)' );

   }
}
else if($_POST['company']=="fastline")
{
    $this->Text( 10, 170, '(FAST LINE BUS RENTAL)' );
}
else if($_POST['company']=="fastinvestment")
{
    $this->Text( 10, 170, '(FAST INVESTMENT COMPANY LLC)' );
}
else
{
    $this->Text( 10, 170, '(FAST RENT A CAR LLC)' );
}
    // $this->SetY(-30);
    //$this->Image('img/footer.png');
    $this->SetFont('Arial','B',8);
    $this->Text( 10, 285,'This receipt is temporary and will not be considered a settlement of the respective debt contained unless the actual realization ');
    $this->Text( 10, 288,'of cheque' );
}

function __construct($orientation='P', $unit='mm', $format='A4')
{
    parent::__construct($orientation,$unit,$format);
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
    $this->tableborder=0;
    $this->tdbegin=false;
    $this->tdwidth=0;
    $this->tdheight=0;
    $this->tdalign="L";
    $this->tdbgcolor=false;
    $this->oldx=0;
    $this->oldy=0;
    $this->fontlist=array("arial","times","courier","helvetica","symbol");
    $this->issetfont=false;
    $this->issetcolor=false;
}

function WriteHTML($html)
{
    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
    $html=str_replace("\n",'',$html); //replace carriage returns with spaces
    $html=str_replace("\t",'',$html); //replace carriage returns with spaces
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explode the string
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            elseif($this->tdbegin) {
                if(trim($e)!='' && $e!="&nbsp;") {
                    $this->Cell($this->tdwidth,$this->tdheight,$e,$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
                }
                elseif($e=="&nbsp;") {
                    $this->Cell($this->tdwidth,$this->tdheight,'',$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
                }
            }
            else
                $this->Write(5,stripslashes(txtentities($e)));
        }
        else
        {
            //Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    //Opening tag
    switch($tag){

        case 'SUP':
            if( !empty($attr['SUP']) ) {    
                //Set current font to 6pt     
                $this->SetFont('','',6);
                //Start 125cm plus width of cell to the right of left margin         
                //Superscript "1" 
                $this->Cell(2,2,$attr['SUP'],0,0,'L');
            }
            break;

        case 'TABLE': // TABLE-BEGIN
            if( !empty($attr['BORDER']) ) $this->tableborder=$attr['BORDER'];
            else $this->tableborder=0;
            break;
        case 'TR': //TR-BEGIN
            break;
        case 'TD': // TD-BEGIN
            if( !empty($attr['WIDTH']) ) $this->tdwidth=($attr['WIDTH']/4);
            else $this->tdwidth=40; // Set to your own width if you need bigger fixed cells
            if( !empty($attr['HEIGHT']) ) $this->tdheight=($attr['HEIGHT']/6);
            else $this->tdheight=6; // Set to your own height if you need bigger fixed cells
            if( !empty($attr['ALIGN']) ) {
                $align=$attr['ALIGN'];        
                if($align=='LEFT') $this->tdalign='L';
                if($align=='CENTER') $this->tdalign='C';
                if($align=='RIGHT') $this->tdalign='R';
            }
            else $this->tdalign='L'; // Set to your own
            if( !empty($attr['BGCOLOR']) ) {
                $coul=hex2dec($attr['BGCOLOR']);
                    $this->SetFillColor($coul['R'],$coul['G'],$coul['B']);
                    $this->tdbgcolor=true;
                }
            $this->tdbegin=true;
            break;

        case 'HR':
            if( !empty($attr['WIDTH']) )
                $Width = $attr['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.2);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(1);
            break;
        case 'STRONG':
            $this->SetStyle('B',true);
            break;
        case 'EM':
            $this->SetStyle('I',true);
            break;
        case 'B':
        case 'I':
        case 'U':
            $this->SetStyle($tag,true);
            break;
        case 'A':
            $this->HREF=$attr['HREF'];
            break;
        case 'IMG':
            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
            break;
        case 'BLOCKQUOTE':
        case 'BR':
            $this->Ln(5);
            break;
        case 'P':
            $this->Ln(10);
            break;
        case 'FONT':
            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                $coul=hex2dec($attr['COLOR']);
                $this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
                $this->issetcolor=true;
            }
            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                $this->SetFont(strtolower($attr['FACE']));
                $this->issetfont=true;
            }
            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist) && isset($attr['SIZE']) && $attr['SIZE']!='') {
                $this->SetFont(strtolower($attr['FACE']),'',$attr['SIZE']);
                $this->issetfont=true;
            }
            break;
    }
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='SUP') {
    }

    if($tag=='TD') { // TD-END
        $this->tdbegin=false;
        $this->tdwidth=0;
        $this->tdheight=0;
        $this->tdalign="L";
        $this->tdbgcolor=false;
    }
    if($tag=='TR') { // TR-END
        $this->Ln();
    }
    if($tag=='TABLE') { // TABLE-END
        $this->tableborder=0;
    }

    if($tag=='STRONG')
        $tag='B';
    if($tag=='EM')
        $tag='I';
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
    if($tag=='FONT'){
        if ($this->issetcolor==true) {
            $this->SetTextColor(0);
        }
        if ($this->issetfont) {
            $this->SetFont('arial');
            $this->issetfont=false;
        }
    }
}

function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s) {
        if($this->$s>0)
            $style.=$s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}

}
//check whether its a cq case and location



//end of class
// checking the payment mode
//get the current values of 
//$slno = $slno+1;

$messenger = $_SESSION['login'];
if($txtCustomer!="")
{
$txtCustomer = addslashes($txtCustomer);
  $sql = "INSERT INTO `transaction`( `trans_sl_no`,`location`,`customername`,`customer_phone`, `trans_type`, `emailid`, `chqnumber`,`chqdate`, `bankname`, `invoicenumber`, `amount`,`messenger`,`salesmanname`,salesmancode,pscode,glcode,company)   VALUES ('$slno','$userDefaultLocation','$txtCustomer','$phone','$trans_type','$emailid','$chqnumber','$chqdate','$bankname','$invoicenumber','$amount','$messenger','$salesmanname','$salesmannid','$pscode','$glcod','$company')";
   }
if (mysqli_query($con, $sql)) {

    
   $result_cc = mysqli_query($con,$sql_cc);

if($invoicenumber=="")
{
$invoicenumber="-";
}
$htmlTable='<br><br><br><table border="1" >
<tr>
<td  width="120" height="40" bgcolor="#ffffff">Customer Name</td><td  width="580" height="40" bgcolor="#ffffff">'.$txtCustomer.'</td> </tr>
<tr>
<td  width="120" height="40" bgcolor="#ffffff">Mode of payment</td><td  width="580" height="40" bgcolor="#ffffff">'.$pm.'</td>
</tr>
';

if($flagchq)
{
  $htmlTable.='<tr><td  width="120" height="40" bgcolor="#ffffff">Cheque Number</td><td  width="580" height="40" bgcolor="#ffffff">'.$chqnumber.'</td></tr><tr><td  width="120" height="40" bgcolor="#ffffff">Cheque Date</td><td  width="580" height="40" bgcolor="#ffffff">'.$chqdate.'</td>
</tr><tr><td  width="120" height="40" bgcolor="#ffffff">Cheque Bank</td><td  width="580" height="40" bgcolor="#ffffff">'.$chqbank.'</td></tr>';
}
if($flagbnk)
{

$htmlTable.=
'<tr>
<td  width="120" height="40" bgcolor="#ffffff">Bank Name</td><td  width="580" height="40" bgcolor="#ffffff">'.$bankname.'</td>
</tr><tr>
<td  width="120" height="40" bgcolor="#ffffff">Transferred Date</td><td  width="580" height="40" bgcolor="#ffffff">'.$chqdate.'</td>
</tr>';

}

if($flagcc)
{

$htmlTable.=
'<tr>
<td  width="120" height="40" bgcolor="#ffffff">Credit Card </td><td  width="580" height="40" bgcolor="#ffffff">'.$cctype.'</td>
</tr>';

}

$htmlTable.= 
'
<tr>
<td  width="120" height="40" bgcolor="#ffffff">Invoice Number</td><td  width="580" height="40" bgcolor="#ffffff">'.$invoicenumber.'</td>
</tr>


<tr>
<td  width="120" height="40" bgcolor="#ffffff">Total Amount</td><td  width="580" height="40" bgcolor="#ffffff"><b>'.formatcurrency($amount,"AED").' </b></td>
</tr>

<tr>
<td  width="120" height="40" bgcolor="#ffffff">Amount in Words</td><td  width="580" height="40" bgcolor="#ffffff">'.strtoupper($amount_in_words).'</td>
</tr>

</table>';

  $pdf=new PDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial','',9);
  $pdf->WriteHTML("<br><br><br><br><br><br>                                                       <br>RECEIVED FROM :  ".$glcod." / ".$pscode." / ".$txtCustomer.",<br>".$htmlTable."<br><br><br><br>");
  //txtCustomer."</u>,<br>DATED: ".date("m/d/Y")."<br>".$htmlTable."<br>");
  $pdf->Output( $_SERVER['DOCUMENT_ROOT']."/FRCMCR2020/pdfs/"."MCR".$slno.".pdf",'F');
  
  $mail = new PHPMailer(true);
  $mail->IsSMTP(); 
try
 {


if($_POST['company']!="fast")
  {
                    $mail->Host       = 'mail.autostradtransport.com';  // Specify main and backup SMTP servers
					$mail->SMTPAuth   = true;                               // Enable SMTP authentication
					$mail->Username   = 'notification@autostradtransport.com';         // SMTP username
					$mail->Password   = 'Auto&Strad@2023';                           // SMTP password
					$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					$mail->Port       = 587;     
                    $mail->AddAddress('arc-receipts@autostrad.com', 'ACCOUNTS');
                     //$mail->AddAddress('jinu@enguae.com', 'ACCOUNTS');
                    $mail->AddCC($emailid, $txtCustomer);
  }
else
{
	                $mail->Host       = 'mail.autostradtransport.com';  // Specify main and backup SMTP servers
					$mail->SMTPAuth   = true;                               // Enable SMTP authentication
					$mail->Username   = 'notification@autostradtransport.com';         // SMTP username
					$mail->Password   = 'Auto&Strad@2023';                           // SMTP password
					$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					$mail->Port       = 587;     
                    $mail->AddAddress('frc-receipts@fastuae.com', 'ACCOUNTS');
                    //$mail->AddAddress('jinu@enguae.com', 'ACCOUNTS');
                    $mail->AddCC($emailid, $txtCustomer);
 }
         
   if(isset($_SESSION["login"]))
     {
      $locemail=strtolower($_SESSION["login"]);
      $find1 = strpos($locemail, '@');
       $find2 = strpos($locemail, '.');
      if($find1 !== false && $find2 !== false && $find2 > $find1)
      {
         if($locemail=="sharjah@fastuae.com" || $locemail=="rak@fastuae.com" || $locemail=="fujairah@fastuae.com")
         {
                if($_POST['company']!="fast")
                {
                    $newmail="";
                         if($locemail=="sharjah@fastuae.com")
                         {
                                $newmail="sharjah@autostrad.com";
                         }
                        if($locemail=="rak@fastuae.com")
                        {
                                 $newmail="rak@autostrad.com";
                        }
                        if($locemail=="fujairah@fastuae.com")
                        {
                               $newmail="fujairah@autostrad.com";

                        }
                         $mail->AddAddress($newmail, 'ACCOUNTS');

                }
                else
                {
                 $mail->AddAddress($locemail, 'ACCOUNTS');
                }
            }
         }
           
      }

        

    

if($_POST['company']!="fast")
                {
                        if ($userDefaultLocation=='AUH')  // AUH Accounts department.
                        {   
                         //   $mail->AddBCC("at_ashraf@yahoo.com",   "ACCOUNTS");
                        $mail->AddBCC("osama@autostrad.com",  "ACCOUNTS"); 
                        $mail->AddBCC("wael@autostrad.com",   "ACCOUNTS");  
                        $mail->AddBCC("naira@autostrad.com",  "ACCOUNTS");
                        $mail->AddBCC("ryan@autostrad.com",   "ACCOUNTS");
                        $mail->AddBCC("jeanne@autostrad.com",   "ACCOUNTS");
                        $mail->AddBCC("muhammed.jasil@autostrad.com",   "ACCOUNTS");
                        $mail->AddBCC("ahmed.elsaid@autostrad.com",   "ACCOUNTS");
                        $mail->AddBCC("binoy.devassy@onetechnology.biz",   "ACCOUNTS");
                        //$mail->AddBCC("sanil.sadasivan@enguae.com",   "IT");
                             
                        }
                    else
                        { 
                          //$mail->AddBCC("itmanager@enguae.com",   "ACCOUNTS");
                            $mail->AddBCC("hamad@autostrad.com",   "ACCOUNTS");
                            //$mail->AddBCC("shijoy@autostrad.com",   "ACCOUNTS");
                            $mail->AddBCC("evangeline@autostrad.com",   "ACCOUNTS");
                            $mail->AddBCC("jyothiraj@autostrad.com",   "ACCOUNTS");
                            $mail->AddBCC("hatem@autostrad.com",   "ACCOUNTS");
                            $mail->AddBCC("jeanne@autostrad.com",   "ACCOUNTS");
                            $mail->AddBCC("ajeesh@autostrad.com",   "ACCOUNTS");
							$mail->AddBCC("masih@autostrad.com",   "ACCOUNTS");
							$mail->AddBCC("lukman.hussain@autostrad.com",   "ACCOUNTS");
                            $mail->AddBCC("naira.qutob@autostrad.com",   "ACCOUNTS");
                            //$mail->AddBCC("sanil.sadasivan@enguae.com",   "IT");
                        
                    }
                }
                else
                {

                	if ($userDefaultLocation=='AUH')  // AUH Accounts department.
                		{ 	
                		 
                    		$mail->AddBCC("osama@fastuae.com",  "ACCOUNTS"); 
                    		$mail->AddBCC("gawdat@fastuae.com", "ACCOUNTS");
                    		$mail->AddBCC("wael@fastuae.com",   "ACCOUNTS");  
                    	    $mail->AddBCC("naira@fastuae.com",  "ACCOUNTS");
                    		$mail->AddBCC("ryan@fastuae.com",   "ACCOUNTS");
                           $mail->AddBCC("jeanne@autostrad.com",   "ACCOUNTS");
                           $mail->AddBCC("muhammed.jasil@autostrad.com",   "ACCOUNTS");
                           $mail->AddBCC("ahmed.elsaid@autostrad.com",   "ACCOUNTS");
                           //$mail->AddBCC("sanil.sadasivan@enguae.com",   "IT");
                		     
                		}
                	else
                		{ 
                	      
                            $mail->AddBCC("hamad@fastuae.com",   "ACCOUNTS");
                           // $mail->AddBCC("shijoy@fastuae.com",   "ACCOUNTS");
                            $mail->AddBCC("evangeline@fastuae.com",   "ACCOUNTS");
                            $mail->AddBCC("jyothiraj@fastuae.com",   "ACCOUNTS");
                            $mail->AddBCC("hatem@fastuae.com",   "ACCOUNTS");
                             $mail->AddBCC("jeanne@autostrad.com",   "ACCOUNTS");
							 $mail->AddBCC("ajeesh@autostrad.com",   "ACCOUNTS");
							$mail->AddBCC("masih@autostrad.com",   "ACCOUNTS");
                		    $mail->AddBCC("lukman.hussain@autostrad.com",   "ACCOUNTS");
                            $mail->AddBCC("naira.qutob@autostrad.com",   "ACCOUNTS");
                	}
        }

		if($_POST['company']=="autostrad")
            {

      $mail->SetFrom('notification@autostradtransport.com', 'AUTOSTRAD RENT A CAR ACCOUNTS DEPARTMENT');
      $mail->Subject = "MCR RECIPT"." OF ". $txtCustomer ." (MCR NUMBER - ".$_SESSION['mcr'].")";
      $mail->AltBody = 'Dear Sir /Madam, Greeting from AUTOSTRAD Rent A Car ! Thanks for the payment and please find attached the receipt. '; 
           }
           else if($_POST['company']=="fastline")
            {

      $mail->SetFrom('notification@autostradtransport.com', 'FASTLINE BUS RENTAL');
      $mail->Subject = "MCR RECIPT"." OF ". $txtCustomer ." (MCR NUMBER - ".$_SESSION['mcr'].")";
      $mail->AltBody = 'Dear Sir /Madam, Greeting from AUTOSTRAD Rent A Car ! Thanks for the payment and please find attached the receipt. '; 
           }
		    else if($_POST['company']=="fastinvestment")
            {

      $mail->SetFrom('notification@autostradtransport.com', 'FAST INVESTMENT COMPANY LLC');
      $mail->Subject = "MCR RECIPT"." OF ". $txtCustomer ." (MCR NUMBER - ".$_SESSION['mcr'].")";
      $mail->AltBody = 'Dear Sir /Madam, Greeting from Fast Investment Company ! Thanks for the payment and please find attached the receipt. '; 
           }
         else
       {
             $mail->SetFrom('notification@autostradtransport.com', 'FAST RENT A CAR ACCOUNTS DEPARTMENT');
           $mail->Subject = "MCR RECIPT"." OF ". $txtCustomer ." (MCR NUMBER - ".$_SESSION['mcr'].")";
            $mail->AltBody = 'Dear Sir /Madam, Greeting from Fast Rent A Car ! Thanks for the payment and please find attached the receipt. '; 

         }

      // SMS
      

       if($flagchq)
       {
            $message = file_get_contents(dirname(__FILE__).'/invoice_cheque.html');
            $message = str_replace('%txtCustomer%', $txtCustomer, $message);
            $message = str_replace('%invoicenumber%', $invoicenumber, $message);
            $message = str_replace('%chqnumber%', $chqnumber, $message);
            $message = str_replace('%bankname%', $chqbank, $message);
            $message = str_replace('%chqdate%', $chqdate, $message);
            $message = str_replace('%Cheque_Date%', "NIL", $message);
            $message = str_replace('%Payment_Details%', $paymentmode, $message);
            $message = str_replace('%emailid%', $emailid, $message);
            $message = str_replace('%date_received%', date("d-m-Y"), $message);

      }
      if($flagcc)
      {
            $message = file_get_contents(dirname(__FILE__).'/invoice_cc.html');
            $message = str_replace('%txtCustomer%', $txtCustomer, $message);
            $message = str_replace('%invoicenumber%', $invoicenumber, $message);
            $message = str_replace('%emailid%', $emailid, $message);
            $message = str_replace('%date_received%', date("d-m-Y"), $message);
            $message = str_replace('%cctype%', $cctype, $message);
             $message = str_replace('%chqdate%', $chqdate, $message);
      }
      if($flagcash)
      {

            $message = file_get_contents(dirname(__FILE__).'/invoice_cash.html');
            $message = str_replace('%txtCustomer%', $txtCustomer, $message);
            $message = str_replace('%invoicenumber%', $invoicenumber, $message);
            $message = str_replace('%emailid%', $emailid, $message);
            $message = str_replace('%date_received%', date("d-m-Y"), $message);
      }
      if($flagbnk)
      {
            $message = file_get_contents(dirname(__FILE__).'/invoice_bank.html');
            $message = str_replace('%txtCustomer%', $txtCustomer, $message);
            $message = str_replace('%invoicenumber%', $invoicenumber, $message);
            $message = str_replace('%date_received%', date("d-m-Y"), $message);
            $message = str_replace('%bankname%', $bankname, $message);
      }


       $contactperson="";

      setlocale(LC_MONETARY, 'ar_AE');
      
       $message = str_replace('%amount%', formatcurrency($amount,"AED"), $message);
       $message = str_replace('%amount_in_words%', strtoupper($amount_in_words), $message);

        $message = str_replace('%customername%', $customername, $message);
        $message = str_replace('%contactperson%', $contactperson, $message);
	if($_POST['company']=="autostrad")
            {

        $message = "<htlm>Dear Sir /Madam, <br><br>Greeting from AUTOSTRAD Rent A Car.  <br>
        Thanks for the payment and please find attached the receipt.
        <br><br><b>Regards,<br> AUTOSTRAD RENT A CAR</b>
        </html>";
}
else if($_POST['company']=="fastline")
            {

        $message = "<htlm>Dear Sir /Madam, <br><br>Greeting from Fastline Bus Rental.  <br>
        Thanks for the payment and please find attached the receipt.
        <br><br><b>Regards,<br> FASTLINE BUS RENTAL</b>
        </html>";
}
else if($_POST['company']=="fastinvestment")
            {

        $message = "<htlm>Dear Sir /Madam, <br><br>Greeting from Fast Investment Company.  <br>
        Thanks for the payment and please find attached the receipt.
        <br><br><b>Regards,<br> FAST INVESTMENT COMPANY</b>
        </html>";
}
 else
{
$message = "<htlm>Dear Sir /Madam, <br><br>Greeting from Fast Rent A Car.  <br>
        Thanks for the payment and please find attached the receipt.
        <br><br><b>Regards,<br> FAST RENT A CAR</b>
        </html>";

}
     $mail->msgHTML($message);
      $mail->AddAttachment("pdfs/"."MCR".$_SESSION['mcr'].".pdf");      // attachment
      $mail->Send();
  
}
  catch (phpmailerException $e) 
  {
    echo "Message Sent OK</p>\n";
    echo $e->errorMessage(); //Pretty error messages from PHPMailer
  } 
  catch (Exception $e) {
    echo "Message Sent OK .......</p>\n";
  }
   
} 
else
{
   echo "Error !!";
}
?>
