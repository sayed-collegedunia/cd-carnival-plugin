<?php

use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Writer\PngWriter;

class Cd_Carnival_Pdf_Generator extends Cd_Carnival{
    protected $public_dir;
    protected $public_url;

    protected $qrPublicPath;
    public function __construct()
    {
        $upload_dir = wp_upload_dir();
        $subdir = 'cd-carnival/';
        $this->public_dir = $upload_dir['basedir'] . '/' . $subdir;
        $this->public_url = $upload_dir['baseurl'] . '/' . $subdir;

        if ( ! file_exists( $this->public_dir ) ) {
            wp_mkdir_p( $this->public_dir );
        }
    }

    public function generateQR($reg_number){
        $hash="https://www.collegeduniacarnival.com/mark-visitors?reg=".$reg_number;
        $qrCode = new QrCode($hash);
        $qrCode->setSize(300);
    
        $writer = new PngWriter();
        $filename = $reg_number.'.png';
        $qrDirectory = $this->public_dir.'generated_qr/';
        if ( ! file_exists( $qrDirectory ) ) {
            wp_mkdir_p( $qrDirectory );
        }
        $qrCodePath = $qrDirectory.$filename;
    
        $writer->write($qrCode)->saveToFile($qrCodePath);
        return $this->public_url.'generated_qr/'.$filename;
    }

    public function generatePdf($name, $reg_number, $qrcode, $link){
        $link = $this->shortenLinks($link);
        $basePath = plugins_url( '/', dirname(__FILE__) );
        $htmlFile = plugin_dir_path(dirname(__FILE__)).'template/index.php';
        $htmlContent = file_get_contents($htmlFile);
        
        $htmlContent = str_replace('{{base_path}}', $basePath, $htmlContent);
        $htmlContent = str_replace('{{qr_code}}', $qrcode, $htmlContent);
        $htmlContent = str_replace('{{student_name}}', $name, $htmlContent);
        $htmlContent = str_replace('{{reg_no}}', $reg_number, $htmlContent);
        $htmlContent = str_replace('{{pdf_link}}', $link, $htmlContent);
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
    
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'potrait');
    
        // Render the HTML as PDF
        $dompdf->render();
    
        // Output the generated PDF to Browser
        // $dompdf->stream();
        $output = $dompdf->output();
        $pdfDirectory = $this->public_dir.'generated_pdf/';
        if ( ! file_exists( $pdfDirectory ) ) {
            wp_mkdir_p( $pdfDirectory );
        }
        $filename = $pdfDirectory.$reg_number.'.pdf';
        file_put_contents($filename, $output);
        return $this->public_url.'generated_pdf/'.$filename;
    }

    public function shortenLinks($link){
        // OAUWbvlaqqUoLSmBitNEkY5MwYckf9gnEbIhycXQJp02T
    
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://shrtlnk.dev/api/v2/link',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"url": "'.$link.'"}',
            CURLOPT_HTTPHEADER => array(
                'api-key: OAUWbvlaqqUoLSmBitNEkY5MwYckf9gnEbIhycXQJp02T',
                'Content-Type: application/json'
            ),
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        $obj = json_decode($response, true);
        return $obj['shrtlnk'];
    }

}