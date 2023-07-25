<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once("./vendor/dompdf/dompdf/autoload.inc.php");
use Dompdf\Dompdf;

class Pdfgenerator {

  public function generate($html, $filename='', $stream=TRUE, $paper = 'A4', $orientation = "portrait")
  {
    
    if(empty($filename))
    {
      $filename = "id_result.pdf";
    }

    $dompdf = new DOMPDF();
    $dompdf->loadHtml($html);
    $dompdf->setPaper($paper, $orientation);
    $dompdf->render();
    if ($stream) 
    {
        $dompdf->stream($filename.".pdf", array("Attachment" => 0));
    } 
    else 
    {
        $output = $dompdf->output();
        
        $filePath = FCPATH."assets/uploads/download_report/";
        
        if (!is_dir($filePath)) 
        {
            mkdir($filePath, 0775, TRUE);
        }
        $ss = file_put_contents($filePath.$filename, $output);
        return $ss;
        //return $dompdf->output();
    }
  }
}