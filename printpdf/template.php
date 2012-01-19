<?php

    require_once('tcpdf/config/lang/eng.php');
    require_once('tcpdf/tcpdf.php');

    class TCPDF_Rotate extends TCPDF {
        function RotatedText($x,$y,$txt,$angle) {
            //Text rotated around its origin
            //$this->SetXY($x,$y);
            $this->StartTransform();
            $this->Rotate($angle,$x,$y);
            $this->Text($x,$y,$txt);
            $this->StopTransform();
            //$this->Rotate(-$angle);
        }

        function RotatedMultiText($x,$y,$txt,$angle) {
            //Text rotated around its origin
            $this->SetXY($x,$y);
            $this->StartTransform();
            $this->Rotate($angle,$x,$y);
            $this->MultiCell((205-$y), 4, $txt);
            $this->StopTransform();
            //$this->Rotate(-$angle);
        }

        function RotatedImage($file,$x,$y,$w,$h,$angle) {
            //Image rotated around its upper-left corner
            $this->Rotate($angle,$x,$y);
            $this->Image($file,$x,$y,$w,$h);
            $this->Rotate(-$angle);
        }
        
    }

    $pdf=new TCPDF_Rotate('L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle((osc_item_title()));
    $pdf->SetAuthor(osc_page_title());
    
    $bottom_y = 135;

    
    // CREATE PAGE
    $pdf->addPage('L','A4');

    // HEADER
    
    $pdf->Image('logo.jpg', 10, 10, 30, 20);
    
    $x = 50;
    $pdf->SetXY($x,10);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(255,0,50); // TITLE COLOR
    $pdf->SetFont('helvetica','B',24);
    $pdf->Cell(0,9,  osc_item_title(),'',1,'L', false);
    $pdf->SetTextColor(0,0,0); // BLACK COLOR
        // Contact information
        $pdf->SetFont('helvetica','B',10);
        $pdf->setX($x);
        $pdf->Cell(0,4,  sprintf(__('Contact: %s', 'printpdf'), osc_item_contact_name()),'',1,'L', false);

        // QR-CODE
        $y = $pdf->GetY();
        $pdf->write2DBarcode(osc_item_url(), 'QRCODE,L', 295 - 10 -20, $y, 20, 20);
        
        $pdf->setX($x);
        $pdf->Cell(0,4,  sprintf(__('Email: %s', 'printpdf'), osc_item_contact_email()),'',1,'L', false);
        $pdf->setX($x);
        $pdf->Cell(0,4,  (osc_item_url()),'',1,'L', false, (osc_item_url()));

    
    // CONTENT - DESCRIPTION
    if(osc_has_item_resources()) {
        $y = $pdf->GetY();
        $x = 20;
        $width = 100;
        $height = $bottom_y-$y-20;
        
        $data = getimagesize(osc_resource_url());
        $w = $data[0];
        $h = $data[1];

        if(($w/$h)>=($width/$height)) {
            $newW = ($w > $width)? $width : $w;
            $newH = $h * ($newW / $w);
        } else {
            $newH = ($h > $height)? $height : $h;
            $newW = $w * ($newH / $h);
        }
        
        $pdf->Image(osc_resource_url(), 20 + (($width-$newW)/2), $y+10 + (($height-$newH)/2), $newW, $newH);
        $x = 140;
    } else {
        $x = 20;
    }

    $pdf->Ln();
        // ADDRES AND PRICE
        $address_array = array();
        if ( osc_item_address() != "" ) { $address_array[] = osc_item_address() ; }
        if ( osc_item_city_area() != "" ) { $address_array[] = osc_item_city_area() ; }
        if ( osc_item_city() != "" ) { $address_array[] = osc_item_city() ; }
        
        $address = implode(", ", $address_array);
        
        $pdf->SetFont('helvetica','',10);
        $pdf->setX($x);
        $pdf->Cell(0,4,  __('Address:', 'printpdf'),'',0,'L', false);
        $pdf->SetFont('helvetica','B',10);
        $pdf->setX($x+50);
        $pdf->Cell(0,4,  $address,'',0,'L', false);
        $pdf->Ln();
        $pdf->SetFont('helvetica','',10);
        $pdf->setX($x);
        $pdf->Cell(0,4,  __('Price:', 'printpdf'),'',0,'L', false);
        $pdf->SetFont('helvetica','B',10);
        $pdf->setX($x+50);
        $pdf->Cell(0,4,  osc_item_formated_price(),'',0,'L', false);
        $pdf->Ln();
    // DESCRIPTION
    $pdf->Ln();
    $pdf->SetFont('helvetica','',10);
    $pdf->setX($x);
    $y = $pdf->GetY();
    
    $pdf->MultiCell(0,4, strip_tags(osc_item_description()),0,'J', false, 0, $x, $y, true, 0, false, true, 80);
    //$pdf->writeHTMLCell(0,4, $x, $y,"<div style=\"height:100px\">".osc_item_description()."</div>",0,'J', false, 100);
    
    
    
    
    
    // DE-ATTACHABLE TAGS
    $x = 0;
    $y = $bottom_y;
    $pdf->Line($x, $y, 300, $y);
    for($i = 0;$i<10;$i++) {

        // QR-CODE
        $pdf->write2DBarcode(osc_item_url(), 'QRCODE,L', $x + (29.5*$i) + 2 + (3*(10-$i)/10) , $bottom_y + 2, 20, 20);
        $y = $bottom_y + 19;
        
        // Title
        $pdf->SetFont('helvetica','B',10);
        $pdf->SetTextColor(255,0,50); // TITLE COLOR
        $pdf->RotatedMultiText($x + (29.5*$i) + 24 , $y+5, osc_item_title(), 270);
        // Contact
        $pdf->SetFont('helvetica','',8);
        $pdf->SetTextColor(0,0,0); // BLACK COLOR
        $pdf->RotatedText($x + (29.5*$i) + 14 , $y+5, osc_item_contact_name(), 270);
        // Email
        $pdf->RotatedText($x + (29.5*$i) + 10 , $y+5, osc_item_contact_email(), 270);
        // URL
        $pdf->SetTextColor(0,0,155); // BLUE COLOR
        $short_url = printpdf_shorturl(osc_item_url());
        $pdf->RotatedMultiText($x + (29.5*$i) + 8 , $y+5, ($short_url), 270);
        if($i!=9) {
            $pdf->Line((29.5*($i+1)), $bottom_y, (29.5*($i+1)), 210);
        }
        $pdf->SetTextColor(0,0,0); // BLACK COLOR
 
    }
    
    


    $pdf->Output($path, 'F');

?>
