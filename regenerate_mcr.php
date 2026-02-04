<?php
/**
 * MCR PDF Regeneration Script
 * Upload to /var/www/html/FRCMCR2020/ and run from browser or CLI
 * 
 * Usage: 
 *   Browser: http://mcr.enguae.com/FRCMCR2020/regenerate_mcr.php
 *   CLI: php regenerate_mcr.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require('fpdf.php');
require('dbconnection.php');

// Transactions to regenerate - UPDATE THESE VALUES
$transactions = [
    [
        'idtransaction' => 16047,
        'trans_sl_no' => '400000024',  // New MCR number
        'chqnumber' => '50062',         // Correct cheque number
        'chqdate' => '01-01-2026',
    ],
    [
        'idtransaction' => 16048,
        'trans_sl_no' => '400000025',
        'chqnumber' => '50063',
        'chqdate' => '01-05-2026',
    ],
    [
        'idtransaction' => 16049,
        'trans_sl_no' => '400000026',
        'chqnumber' => '50064',
        'chqdate' => '01-09-2026',
    ],
];

// Number to words function
function convertNumberToWords($number) {
    $ones = array(
        0 => '', 1 => 'ONE', 2 => 'TWO', 3 => 'THREE', 4 => 'FOUR', 5 => 'FIVE',
        6 => 'SIX', 7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE', 10 => 'TEN',
        11 => 'ELEVEN', 12 => 'TWELVE', 13 => 'THIRTEEN', 14 => 'FOURTEEN', 15 => 'FIFTEEN',
        16 => 'SIXTEEN', 17 => 'SEVENTEEN', 18 => 'EIGHTEEN', 19 => 'NINETEEN'
    );
    $tens = array(
        2 => 'TWENTY', 3 => 'THIRTY', 4 => 'FORTY', 5 => 'FIFTY',
        6 => 'SIXTY', 7 => 'SEVENTY', 8 => 'EIGHTY', 9 => 'NINETY'
    );
    
    $number = (int)$number;
    if ($number == 0) return 'ZERO';
    
    $words = '';
    if ($number >= 1000) {
        $words .= convertNumberToWords((int)($number / 1000)) . ' THOUSAND ';
        $number = $number % 1000;
    }
    if ($number >= 100) {
        $words .= $ones[(int)($number / 100)] . ' HUNDRED ';
        $number = $number % 100;
    }
    if ($number >= 20) {
        $words .= $tens[(int)($number / 10)] . ' ';
        $number = $number % 10;
    }
    if ($number > 0) {
        $words .= $ones[$number] . ' ';
    }
    
    return trim($words);
}

function formatcurrency($amount, $currency = "AED") {
    return number_format($amount, 2) . " " . $currency;
}

// PDF Class (same as original)
class PDF extends FPDF
{
    public $mcr_number;
    public $invoice_date;
    public $company;
    public $received_by;
    public $salesman;
    public $location;
    
    function Header()
    {
        // Logo based on company
        if($this->company == "fastinvestment") {
            $this->Image('img/fastinvestment-logo.jpg', 10, 0);
        } elseif($this->company == "fast") {
            $this->Image('img/frc-logo.jpg', 10, 0);
        } elseif($this->company == "autostrad") {
            $this->Image('img/autostrad.jpg', 10, 0);
        } elseif($this->company == "fastline") {
            $this->Image('img/fastlinelogo.jpg', 10, 0);
        }
        
        $this->SetFont('Arial', 'B', 12);
        $this->Text(130, 20, 'MCR#:    MCR' . $this->mcr_number);
        $this->Text(130, 25, 'DATE:    ' . $this->invoice_date);
        $this->SetFont('Arial', 'B', 22);
        $this->Text(85, 50, 'RECEIPT');
        $this->Ln(20);
    }
    
    function Footer()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Text(10, 155, 'RECEIVED BY:');
        $this->Text(36, 155, strtoupper($this->received_by));
        $this->Text(10, 160, "SALES MAN : " . strtoupper($this->salesman));
        $this->Text(10, 165, "LOCATION : " . strtoupper($this->location));
        
        if($this->company == "fastinvestment") {
            $this->Text(10, 170, '(FAST INVESTMENT COMPANY LLC)');
        } elseif($this->company == "fast") {
            $this->Text(10, 170, '(FAST RENT A CAR LLC)');
        } elseif($this->company == "autostrad") {
            $this->Text(10, 170, '(AUTOSTRAD INTERNATIONAL VEHICLE LEASING - SOLE PROPRIETORSHIP L.L.C)');
        }
    }
    
    function WriteHTML($html)
    {
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i => $e) {
            if($i % 2 == 0) {
                $this->Write(5, $e);
            } else {
                if($e[0] == '/') {
                    $this->CloseTag(strtoupper(substr($e, 1)));
                } else {
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $this->OpenTag($tag);
                }
            }
        }
    }
    
    function OpenTag($tag)
    {
        if($tag == 'B') $this->SetFont('', 'B');
        if($tag == 'BR') $this->Ln(5);
    }
    
    function CloseTag($tag)
    {
        if($tag == 'B') $this->SetFont('', '');
    }
}

echo "<h1>MCR PDF Regeneration</h1>";
echo "<pre>";

foreach($transactions as $trans) {
    // Fetch transaction data from database
    $sql = "SELECT * FROM transaction WHERE idtransaction = " . $trans['idtransaction'];
    $result = mysqli_query($con, $sql);
    
    if(!$result || mysqli_num_rows($result) == 0) {
        echo "ERROR: Transaction ID " . $trans['idtransaction'] . " not found!\n";
        continue;
    }
    
    $row = mysqli_fetch_assoc($result);
    
    // Use corrected values
    $slno = $trans['trans_sl_no'];
    $chqnumber = $trans['chqnumber'];
    $chqdate = $trans['chqdate'];
    
    // Get other values from database
    $txtCustomer = $row['customername'];
    $amount = $row['amount'];
    $bankname = $row['bankname'];
    $invoicenumber = $row['invoicenumber'];
    $glcod = $row['glcode'];
    $pscode = $row['pscode'];
    $company = $row['company'];
    $location = $row['location'];
    $messenger = $row['messenger'];
    
    // Determine payment mode
    $pm = 'CHEQUE-CDC';
    $chqbank = $bankname ?: 'ADIB';
    
    // Amount in words
    $amount_in_words = 'AED ' . convertNumberToWords((int)$amount) . ' ONLY';
    
    // Build HTML table
    $htmlTable = '<br><br><br><table border="1">
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Customer Name</td>
    <td width="580" height="40" bgcolor="#ffffff">' . $txtCustomer . '</td>
    </tr>
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Mode of payment</td>
    <td width="580" height="40" bgcolor="#ffffff">' . $pm . '</td>
    </tr>
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Cheque Number</td>
    <td width="580" height="40" bgcolor="#ffffff">' . $chqnumber . '</td>
    </tr>
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Cheque Date</td>
    <td width="580" height="40" bgcolor="#ffffff">' . $chqdate . '</td>
    </tr>
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Cheque Bank</td>
    <td width="580" height="40" bgcolor="#ffffff">' . $chqbank . '</td>
    </tr>
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Invoice Number</td>
    <td width="580" height="40" bgcolor="#ffffff">' . $invoicenumber . '</td>
    </tr>
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Total Amount</td>
    <td width="580" height="40" bgcolor="#ffffff"><b>' . formatcurrency($amount, "AED") . '</b></td>
    </tr>
    <tr>
    <td width="120" height="40" bgcolor="#ffffff">Amount in Words</td>
    <td width="580" height="40" bgcolor="#ffffff">' . $amount_in_words . '</td>
    </tr>
    </table>';
    
    // Create PDF
    $pdf = new PDF();
    $pdf->mcr_number = $slno;
    $pdf->invoice_date = date("d-m-Y");
    $pdf->company = $company ?: 'fastinvestment';
    $pdf->received_by = 'BINOY';
    $pdf->salesman = '';
    $pdf->location = $location ?: 'AUH';
    
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 9);
    $pdf->WriteHTML("<br><br><br><br><br><br>                                                       <br>RECEIVED FROM :  " . $glcod . " / " . $pscode . " / " . $txtCustomer . ",<br>" . $htmlTable . "<br><br><br><br>");
    
    // Save PDF
    $pdfPath = $_SERVER['DOCUMENT_ROOT'] . "/FRCMCR2020/pdfs/MCR" . $slno . ".pdf";
    $pdf->Output($pdfPath, 'F');
    
    echo "SUCCESS: Generated MCR" . $slno . ".pdf\n";
    echo "  - Transaction ID: " . $trans['idtransaction'] . "\n";
    echo "  - Customer: " . $txtCustomer . "\n";
    echo "  - Cheque: " . $chqnumber . " dated " . $chqdate . "\n";
    echo "  - Amount: " . formatcurrency($amount, "AED") . "\n";
    echo "  - Path: " . $pdfPath . "\n\n";
}

echo "</pre>";
echo "<h2>Done! PDFs regenerated.</h2>";
echo "<p>Download links:</p>";
echo "<ul>";
foreach($transactions as $trans) {
    echo '<li><a href="pdfs/MCR' . $trans['trans_sl_no'] . '.pdf" target="_blank">MCR' . $trans['trans_sl_no'] . '.pdf</a> (Cheque ' . $trans['chqnumber'] . ')</li>';
}
echo "</ul>";
?>

