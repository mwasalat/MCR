<?php 
session_start();
include('dbconnection.php');
require('fpdf.php');

define ("NODE_TYPE_START",0);
define ("NODE_TYPE_ELEMENT",1);
define ("NODE_TYPE_ENDELEMENT",2);
define ("NODE_TYPE_TEXT",3);
define ("NODE_TYPE_COMMENT",4);
define ("NODE_TYPE_DONE",5);

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



class PDF extends FPDF
{
protected $B;
protected $I;
protected $U;
protected $HREF;

function __construct($orientation='P', $unit='mm', $format='A4')
{
    //Call parent constructor
    parent::__construct($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
}


function Header()
{
 $this->Image('https://fastuae.com/ui/media/dist/logo/frc-logo.jpg',0,0);
}


function WriteHTML2($html)
{
    //HTML parser
    $html=str_replace("\n",' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
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
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF=$attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
    if($tag=='P')
        $this->Ln(10);
 if($tag=='IMG')
     {
      if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
     }

     if($tag=='img')
     {
      if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
     }



}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
    if($tag=='P')
        $this->Ln(10);



}

function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
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

function WriteTable($data, $w)
{
    $this->SetLineWidth(.3);
    $this->SetFillColor(255,255,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    foreach($data as $row)
    {
        $nb=0;
        for($i=0;$i<count($row);$i++)
            $nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));
        $h=5*$nb;
        $this->CheckPageBreak($h);
        for($i=0;$i<count($row);$i++)
        {
            $x=$this->GetX();
            $y=$this->GetY();
            $this->Rect($x,$y,$w[$i],$h);
            $this->MultiCell($w[$i],5,trim($row[$i]),0,'C');
            //Put the position to the right of the cell
            $this->SetXY($x+$w[$i],$y);                    
        }
        $this->Ln($h);

    }
}

function NbLines($w, $txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function ReplaceHTML($html)
{
    $html = str_replace( '<li>', "\n<br> - " , $html );
    $html = str_replace( '<LI>', "\n - " , $html );
    $html = str_replace( '</ul>', "\n\n" , $html );
    $html = str_replace( '<strong>', "<b>" , $html );
    $html = str_replace( '</strong>', "</b>" , $html );
    $html = str_replace( '&#160;', "\n" , $html );
    $html = str_replace( '&nbsp;', " " , $html );
    $html = str_replace( '&quot;', "\"" , $html ); 
    $html = str_replace( '&#39;', "'" , $html );
    return $html;
}

function ParseTable($Table)
{
    $_var='';
    $htmlText = $Table;
    $parser = new HtmlParser ($htmlText);
    while ($parser->parse())
    {
        if(strtolower($parser->iNodeName)=='table')
        {
            if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
                $_var .='/::';
            else
                $_var .='::';
        }

        if(strtolower($parser->iNodeName)=='tr')
        {
            if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
                $_var .='!-:'; //opening row
            else
                $_var .=':-!'; //closing row
        }
        if(strtolower($parser->iNodeName)=='td' && $parser->iNodeType == NODE_TYPE_ENDELEMENT)
        {
            $_var .='#,#';
        }
        if ($parser->iNodeName=='Text' && isset($parser->iNodeValue))
        {
            $_var .= $parser->iNodeValue;
        }
    }
    $elems = explode(':-!',str_replace('/','',str_replace('::','',str_replace('!-:','',$_var)))); //opening row
    foreach($elems as $key=>$value)
    {
        if(trim($value)!='')
        {
            $elems2 = explode('#,#',$value);
            array_pop($elems2);
            $data[] = $elems2;
        }
    }
    return $data;
}

/* Load data */
  function LoadData($file)
  {
    /* Read file lines */
    $lines = file($file);
    $data = array();
    foreach($lines as $line)
      $data[] = explode(';',trim($line));
    return $data;
  }
  /* Colored table */
  function FancyTable($header, $data)
  {
    /* Colors, line width and bold font */
    $this->SetFillColor(69, 171, 82);
    $this->SetTextColor(255);
    $this->SetDrawColor(209, 212, 207);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    /* Header */
    $w = array(20,40, 40, 40, 50);
    for($i=0;$i<count($header);$i++)
      $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    /* Color and font restoration */
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    /* Data */
    $fill = false;
    foreach($data as $row)
    {
      $this->Cell($w[0],6,number_format($row[0]),'LR',0,'L',$fill);
      $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
      $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
      $this->Cell($w[3],6,$row[3],'LR',0,'L',$fill);
      $this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
      $this->Ln();
      $fill = !$fill;
    }
    /* Closing line */
    $this->Cell(array_sum($w),0,'','T');
  }



function WriteHTML($html)
{
    $html = $this->ReplaceHTML($html);
    //Search for a table
    $start = strpos(strtolower($html),'<table');
    $end = strpos(strtolower($html),'</table');
    if($start!==false && $end!==false)
    {
        $this->WriteHTML2(substr($html,0,$start).'<BR>');

        $tableVar = substr($html,$start,$end-$start);
        $tableData = $this->ParseTable($tableVar);
        for($i=1;$i<=count($tableData[0]);$i++)
        {
            if($this->CurOrientation=='L')
                $w[] = abs(120/(count($tableData[0])-1))+24;
            else
                $w[] = abs(120/(count($tableData[0])-1))+5;
        }
        $this->WriteTable($tableData,$w);

        $this->WriteHTML2(substr($html,$end+8,strlen($html)-1).'<BR>');
    }
    else
    {
        $this->WriteHTML2($html);
    }
}

}


$txtCustomer      =$_POST['txtCustomer'];
  $emailid        =$_POST['emailid'];
  $chqnumber      =$_POST['chqnumber'];
  $contactperson  =$_POST['contactperson'];
  $chqdate        =$_POST['chqdate'];
  $bankname       =$_POST['bankname'];
  $invoicenumber  =$_POST['invoicenumber'];
  $amount         =$_POST['amount'];



  $f              = new NumberFormatter("en", NumberFormatter::SPELLOUT);
  $amount_in_words=  $f->format($amount);

  $customername   =$_POST['customername'];


  $dataload = array();

   $dataload['txtCustomer'] = $txtCustomer;
    $dataload['emailid'] = $emailid;
     $dataload['chqnumber'] = $chqnumber;
      $dataload['contactperson'] = $contactperson);
       $dataload['chqdate'] = $chqdate;
        $dataload['bankname'] = $bankname;
         $dataload['invoicenumber'] = $invoicenumber;
         $dataload['amount'] = $amount;

   

  $sql = "INSERT INTO `transaction`( `customer_phone`, `customername`, `contactperson`, `emailid`, `chqnumber`,`chqdate`, `bankname`, `invoicenumber`, `amount`)   VALUES ('$txtCustomer','$customername','$contactperson','$emailid','$chqnumber','$chqdate','$bankname','$invoicenumber','$amount')";

if (mysqli_query($con, $sql)) {


  $htmlTable='<table>

   <tr>
<td>Dear Valued Customer,</td>
<td>We confirm the receipt of Cheque/Cash with receipt# MCR-'.$invoicenumber.' dated: 22/07/2019 11:55 AM</td>
</tr>


<tr>
<td>CUSTOMER NAME</td>
<td>'.$customername.'</td>
</tr>
<tr>
<td>Cash /Chq Amount : </td>
<td>'.$amount.'</td>
</tr>


<tr>
<td> Cheque Number : </td>
<td>'.$chqnumber.'</td>
</tr>

<tr>
<td> Cheque Date : </td>
<td>'.$chqdate.'</td>
</tr>


<tr>
<td>BANK NAME</td>
<td>'.$bankname.'</td>
</tr>

<tr>
<td>INVOICE NUMBER</td>
<td>'.$invoicenumber.'</td>
</tr>


<tr>
<td>INVOICE DATE</td>
<td>'.$chqdate.' </td>
</tr>




<tr>
<td>CUSTOMER PHONE No.</td>
<td>'.$txtCustomer.'</td>
</tr>


<tr>
<td>EMAIL ADDRESS</td>
<td>'.$emailid.'</td>
</tr>


</table>';


  $pdf=new PDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial','',14);
  //$pdf->WriteHTML("<br><br><br><br><br>$htmlTable<br>");
  //$pdf->Output("pdfs/".$invoicenumber.".pdf",'F');


  $header = array('Sl No','Name', 'Roll No', 'Class', 'City');
/* Data loading */
//$data = $pdf->LoadData('data.txt');
$pdf->SetFont('Arial','',12);
$pdf->AddPage();
$pdf->FancyTable($header,$dataload);
$pdf->Output("pdfs/".$invoicenumber.".pdf",'F');



  require_once('class.phpmailer.php');
  $mail = new PHPMailer(true);
  $mail->IsSMTP(); 
try {
      $mail->Host       = "mail.enguae.com"; // SMTP server
      $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
      $mail->SMTPAuth   = true;                  // enable SMTP authentication
      $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
      $mail->Username   = "billing@fastuae.com"; // SMTP account username
      $mail->Password   = "B372245!";        // SMTP account password
      $mail->AddAddress('arun@enguae.com', 'Arun Chithran');
     // $mail->AddAddress('itmanager@enguae.com', 'Itmanager');
      $mail->AddAddress($emailid, 'Arun Chithran');
      $mail->SetFrom('admin@enguae.com', 'FASTRENTACAR BILLING');
      $mail->Subject = "MCR RECIPT"." OF ". $customername ." (INVOICE NUMBER - ".$invoicenumber.")";
      $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
      $message = file_get_contents(dirname(__FILE__).'/invoice.html');
      $message = str_replace('%txtCustomer%', $txtCustomer, $message);
      $message = str_replace('%invoicenumber%', $invoicenumber, $message);

       $message = str_replace('%emailid%', $emailid, $message);
      $message = str_replace('%chqnumber%', $chqnumber, $message);

       $message = str_replace('%chqdate%', $chqdate, $message);
      $message = str_replace('%bankname%', $bankname, $message);

       $message = str_replace('%amount%', $amount, $message);
       $message = str_replace('%amount_in_words%', $amount_in_words, $message);

      $message = str_replace('%customername%', $customername, $message);
      $message = str_replace('%contactperson%', $contactperson, $message);


      $mail->msgHTML($message);
      $mail->AddAttachment("pdfs/".$invoicenumber.".pdf");      // attachment
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