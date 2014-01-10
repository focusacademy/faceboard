<?php

//require_once('../../common/libs/tcpdf/examples/tcpdf_include.php');

require_once('../../common/libs/tcpdf/tcpdf.php');

//include_once('../../config.php');
//
////error_reporting(E_ALL) ; ini_set('display_errors', '1');  
//$db = testDB(2);
// extend TCPF with custom functions
class MYPDF extends TCPDF {

    // Users table
    public function usersTable($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(188, 32, 36);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
//        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        //$w = array(7,20,80,5,80,40,7,20,20,0,20);
        $w = array(10, 20, 80, 0, 0, 0, 0, 20, 20, 0, 20);
//        $num_headers = count($header);
//        for ($i = 0; $i < $num_headers; ++$i) {
//            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
//        }
        $this->Cell($w[0], 6, 'S.No', 1, 0, 'C', 1, '', 1, 1, 'T', 'T');
        $this->Cell($w[1], 6, 'Uid', 1, 0, 'C', 1, '', 1, 1, 'T', 'T');
        $this->Cell($w[2], 6, 'Name', 1, 0, 'C', 1, '', 1, 1, 'T', 'T');
        //$this->Cell($w[3], 6, $row[3], 'LR', 0, 'R', $fill);
        //$this->Cell($w[4], 6, $row[4], 'LR', 0, 'L', $fill);
        //$this->Cell($w[5], 6, $row[5], 'LR', 0, 'L', $fill);
        //$this->Cell($w[6], 6, $row[6], 'LR', 0, 'R', $fill);
        $this->Cell($w[7], 6, 'Degree', 1, 0, 'C', 1, '', 1, 1, 'T', 'T');
        $this->Cell($w[8], 6, 'Dept', 1, 0, 'C', 1, '', 1, 1, 'T', 'T');
        //$this->Cell($w[9], 6, $row[9], 'LR', 0, 'L', $fill);
        $this->Cell($w[10], 6, 'Pswd', 1, 0, 'C', 1, '', 1, 1, 'T', 'T');
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(51);
        $this->SetFont('times', '', 12);
        // Data
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'C', $fill, '', 1, 1, 'T', 'T');
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill, '', 1, 1, 'T', 'T');
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'L', $fill, '', 1, 1, 'T', 'T');
            //$this->Cell($w[3], 6, $row[3], 'LR', 0, 'R', $fill);
            //$this->Cell($w[4], 6, $row[4], 'LR', 0, 'L', $fill);
            //$this->Cell($w[5], 6, $row[5], 'LR', 0, 'L', $fill);
            //$this->Cell($w[6], 6, $row[6], 'LR', 0, 'R', $fill);
            $this->Cell($w[7], 6, $row[7], 'LR', 0, 'C', $fill, '', 1, 1, 'T', 'T');
            $this->Cell($w[8], 6, $row[8], 'LR', 0, 'C', $fill, '', 1, 1, 'T', 'T');
            //$this->Cell($w[9], 6, $row[9], 'LR', 0, 'L', $fill);
            $this->Cell($w[10], 6, $row[10], 'LR', 0, 'C', $fill, '', 1, 1, 'T', 'T');
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }

    // Attendance table
    public function attendance($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(188, 32, 36);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetFont('', 'B');
        // Header
        $num_headers = count($header);
        $w = array(10, 40, 10);
        for ($i = 1; $i < $num_headers - 2; $i++)
            array_push($w, 8);

        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1, '', 1, 1, 'T', 'T');
        }
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(51);
        $this->SetFont('times', '', 12);
        // Data
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 1, 0, 'C', $fill, '', 1, 1, 'T', 'T');
            $this->Cell($w[1], 6, $row[1], 1, 0, 'L', $fill, '', 1, 1, 'T', 'T');
            $this->Cell($w[2], 6, $row[2], 1, 0, 'C', $fill, '', 1, 1, 'T', 'T');
            for ($i = 3; $i < $num_headers; $i++) {
                if ($row[$i] == "present")
                    $val = "P";
                else if ($row[$i] == "absent")
                    $val = "-";
                else
                    $val = $row[$i];
                // set color for background
                $this->Cell($w[$i], 6, $val, 'LRB', 0, 'C', $fill, '', 1, 1, 'T', 'T');
                //$this->Cell($w[$i], 6, $val, 'LR', $i, $align, $fill, $w, $stretch, $ignore_min_height, $calign, $valign);
            }
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }

    // Exam Analysis table
    public function exam_analysis($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(188, 32, 36);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetFont('', 'B');
        // Header
        $num_headers = count($header);
        $w = array(10, 40, 20, 20, 20, 20, 20, 20, 20, 20);
        $num_headers = count($header);
        $width = (200 - $w[1]) / ($num_headers - 1);
        for ($i = 0; $i < $num_headers; ++$i) {
            if ($i == $num_headers - 1)
                continue;
            $ind_width = (($i == 1) ? $w[1] . '%' : $width . '%');
            //echo $ind_width ."<br />";
            $this->Cell($ind_width, 7, $header[$i], 'TB', 0, 'C', 1, '', 1, 1, 'T', 'T');
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(51);
        $this->SetFont('times', '', 12);
        // Data
        $fill = 0;
        //print_r($data);
        for ($j = 0; $j < count($data); $j++) {
            for ($i = 0; $i < count($data[$j]); $i++) {
                if ($i == count($data[$j]) - 1)
                    continue;
                $this->Cell((($i == 1) ? $w[1] . '%' : $width . '%'), 6, $data[$j][$i], 0, 0, 'C', 0, '', 1, 1, 'T', 'T');
            }
            $this->Ln();
            $fill = !$fill;
        }
        //echo $num_headers;
        $this->Cell(($w[1] + (($num_headers - 2) * $width)) . '%', 0, '', 'T');
    }

    // Exam Analysis table
    public function exam_analysis_can($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(188, 32, 36);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetFont('', 'B');
        // Header
        $num_headers = count($header);
        //$w = array(7,20,80,5,80,40,7,20,20,0,20);
        $w = array(10, 30, 10, 15, 15, 15, 15, 15, 15, 15, 15, 15);
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 'TB', 0, 'C', 1, '', 1, 1, 'T', 'T');
        }
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(51);
        $this->SetFont('times', '', 12);
        // Data
        $fill = 0;
        for ($j = 0; $j < count($data); $j++) {
            for ($i = 0; $i < count($data[$j]); $i++) {
                $this->Cell($w[$i], 6, $data[$j][$i], 1, 0, 'C', 0, '', 1, 1, 'T', 'T');
            }
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }

}

//$upit = "SELECT  `uid`,`email`,`usr`,`usr_type` FROM `users` limit 100";
//$result = mysql_query($upit,$db);
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
//set some language-dependent strings
//$pdf->setLanguageArray($l);
// ---------------------------------------------------------
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(190, 0, 0), array(0, 0, 0)); //
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$default = ini_get('max_execution_time');
set_time_limit(1000);

// add a page
$pdf->AddPage();
// thead
//$mode=$_REQUEST['mode'];
$header1 = explode('FACE:1', $_REQUEST['header']);
$header2 = explode('FACE:2', $header1[0]);


$header = array();
foreach ($header2 as $head) {
    $header[] = preg_replace('/[^[:alpha:]]/', '', preg_replace("/&.{0,}?;/", '', strip_tags($head)));
}

// Content start here
$content = explode('FACE:1', $_REQUEST['content']);
$i = 0;
foreach ($content as $k1 => $v1) {
    if ($k1 < (($_REQUEST['mode'] == "userlist" || $_REQUEST['mode'] == "exam_analysis" || $_REQUEST['mode'] == "exam_analysis_can") ? 1 : 0))
        continue;
    $data[$i] = explode('FACE:2', $v1);
    $i++;
}


// set some text to print
$txt = $_REQUEST['title'] . "\n\n";

// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);


switch ($_REQUEST['mode']) {
    case "attendance":
        $pdf->attendance($header2, $data);
        break;
    case "userlist":        
//        $txt = $_REQUEST['college_name'] . "\n\n";
//        $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
//        $txt = $_REQUEST['batch'] . "\n\n";
//        $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
        $pdf->usersTable($header, $data);
        break;
    case "exam_analysis":
        $pdf->exam_analysis($header, $data);
        break;
    case 'exam_analysis_can':
        $pdf->exam_analysis_can($header, $data);
        break;
}


// ---------------------------------------------------------
// close and output PDF document
$pdf->Output('example_011.pdf', 'I');
?>
