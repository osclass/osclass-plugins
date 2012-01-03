<?php

    require_once('fpdf/pdf_rotation.php');

    class PDF extends PDF_Rotate {
        function RotatedText($x,$y,$txt,$angle) {
            //Text rotated around its origin
            $this->Rotate($angle,$x,$y);
            $this->Text($x,$y,$txt);
            $this->Rotate(0);
        }

        function RotatedMultiText($x,$y,$txt,$angle) {
            //Text rotated around its origin
            $this->SetXY($x,$y);
            $this->Rotate($angle,$x,$y);
            $this->MultiCell((205-$y), 4, $txt);
            $this->Rotate(0);
        }

        function RotatedImage($file,$x,$y,$w,$h,$angle) {
            //Image rotated around its upper-left corner
            $this->Rotate($angle,$x,$y);
            $this->Image($file,$x,$y,$w,$h);
            $this->Rotate(0);
        }
    }

    $pdf=new PDF('L', 'mm');
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
    $pdf->SetFont('Arial','B',24);
    $pdf->Cell(0,9,  utf8_decode(html_entity_decode(osc_item_title())),'',1,'L', false);
    $pdf->SetTextColor(0,0,0); // BLACK COLOR
        // Contact information
        $pdf->SetFont('Arial','B',10);
        $pdf->setX($x);
        $pdf->Cell(0,4,  sprintf(__('Contact: %s', 'printpdf'), utf8_decode(html_entity_decode(osc_item_contact_name()))),'',1,'L', false);
        $pdf->setX($x);
        $pdf->Cell(0,4,  sprintf(__('Email: %s', 'printpdf'), utf8_decode(html_entity_decode(osc_item_contact_email()))),'',1,'L', false);
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
        
        $address = utf8_decode(html_entity_decode(implode(", ", $address_array)));
        
        $pdf->SetFont('Arial','',10);
        $pdf->setX($x);
        $pdf->Cell(0,4,  __('Address:', 'printpdf'),'',0,'L', false);
        $pdf->SetFont('Arial','B',10);
        $pdf->setX($x+50);
        $pdf->Cell(0,4,  $address,'',0,'L', false);
        $pdf->Ln();
        $pdf->SetFont('Arial','',10);
        $pdf->setX($x);
        $pdf->Cell(0,4,  __('Price:', 'printpdf'),'',0,'L', false);
        $pdf->SetFont('Arial','B',10);
        $pdf->setX($x+50);
        $pdf->Cell(0,4,  osc_item_formated_price(),'',0,'L', false);
        $pdf->Ln();
    // DESCRIPTION
    $pdf->Ln();
    $pdf->SetFont('Arial','',10);
    $pdf->setX($x);
    $pdf->MultiCell(0,4,  strip_tags(preg_replace("|<br([^>]*)>|i", "\n\n", (html_entity_decode(osc_item_description())))),0,'J', false);
    
    
    // QR-CODE
    if(function_exists('qrcode_generateqr')) {
        $img_filename = osc_item_id()."_".md5(osc_item_url())."_2.png";
        if(!file_exists(osc_get_preference('upload_path', 'qrcode').$img_filename)) {
            qrcode_generateqr(osc_item_url(), osc_item_id(), 2);
        }
        $pdf->Image(osc_get_preference('upload_url', 'qrcode').$img_filename, 295 - 10 -20, $bottom_y - 10 -20, 20);
    }
    
    
    
    // DE-ATTACHABLE TAGS
    $x = 0;
    $y = $bottom_y;
    $pdf->Line($x, $y, 300, $y);
    for($i = 0;$i<10;$i++) {

        if(function_exists('qrcode_generateqr')) {
            $img_filename = osc_item_id()."_".md5(osc_item_url())."_2.png";
            $pdf->Image(osc_get_preference('upload_url', 'qrcode').$img_filename, $x + (29.5*$i) + 5 , $bottom_y + 2, 20);
            $y = $bottom_y + 19;
        }
        
        // Title
        $pdf->SetFont('Arial','B',10);
        $pdf->SetTextColor(255,0,50); // TITLE COLOR
        $pdf->RotatedMultiText($x + (29.5*$i) + 24 , $y+5, utf8_decode(html_entity_decode(osc_item_title())), 270);
        // Contact
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0); // BLACK COLOR
        $pdf->RotatedText($x + (29.5*$i) + 14 , $y+5, utf8_decode(html_entity_decode(osc_item_contact_name())), 270);
        // Email
        $pdf->RotatedText($x + (29.5*$i) + 10 , $y+5, utf8_decode(html_entity_decode(osc_item_contact_email())), 270);
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
