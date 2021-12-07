<?php

use Fpdf\Fpdf;

class ReportePDF
{


    public static function generarLogo($request, $response, $args)
    {

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont("Arial", "B", 12);

        //////////////////////////////  LOGO  //////////////////////////////   
        $pdf->Image('..\PDFs\Logo\logo.jpg', 10, 6, 30);
        // Arial bold 15
        $pdf->SetFont('Arial', 'B', 15);
        // Move to the right
        $pdf->Cell(80);
        // Title
        $pdf->Cell(80, 10, 'La Comanda - Restaurante', 1, 0, 'C');
        // Line break
        $pdf->Ln(20);

        //////////////////////////////  FOOTER  //////////////////////////////   
        // Position at 1.5 cm from bottom
        $pdf->SetY(-15);
        // Arial italic 8
        $pdf->SetFont('Arial', 'I', 8);
        // Page number
        $pdf->Cell(0, 10, 'Page 1'  . '/{nb}', 0, 0, 'C');

        $fecha = new DateTime(date('d-m-Y'));
        $destination = ".\PDFs\Logo\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output($destination . "logo" . $fecha->format('d-m-Y') . ".pdf", "F");
        $payload = json_encode(array("Mensaje" => "Reporte generado correctamente en: " . $destination . "logo" . $fecha->format('d-m-Y') . ".pdf"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
