<?php
namespace App\Response;
use App\Controller\generate_qr_code;
use Symfony\Component\HttpFoundation\Response;
use Endroid\QrCode\QrCode;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use TCPDF;


class PdfResponse extends Response
{
    public function __construct($pdfContent, $filename)
    {
        parent::__construct();
        
        $this->headers->set('Content-Type', 'application/pdf');
        $this->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"');
        $this->setContent($pdfContent);
       


    }
    

    public function generatePdfAction($reservation)
    {
        // Create new PDF document
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Your Name');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Reservation Details');
        $pdf->SetSubject('Reservation Details');
        $pdf->SetKeywords('Reservation, Details');
        
        // Add a new page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 12);
        
        // Write reservation details
        $pdf->Cell(0, 10, 'Date de réservation: ' . $reservation->getDateRes()->format('m/d/Y'), 0, 1);
        $pdf->Cell(0, 10, 'Heure de réservation: ' . $reservation->getHeureRes(), 0, 1);
        $pdf->Cell(0, 10, 'Nombre de places réservées: ' . $reservation->getNbPlace(), 0, 1);
        $pdf->Cell(0, 10, 'Type de ticket: ' . $reservation->getTypeTicket(), 0, 1);
        
        // Generate QR code
        $qrCode = new \Endroid\QrCode\QrCode('Reservation details: ' . $reservation->getId_res());
        $qrCodeImageData = $qrCode->writeString();
        
        // Add QR code image to PDF
        
        // Output PDF to browser
        $pdf->Output('Reservation Details.pdf', 'I');
        $reservation = new Reservation(); // Replace with your Reservation object
generatePdfAction($reservation);

    }
    

}
