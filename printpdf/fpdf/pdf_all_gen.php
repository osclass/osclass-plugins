<?php
require('fpdf.php');

class PDF extends FPDF
{
function Header()
{
    global $title;

    $this->Image('logo.jpg',10,10,12);
    $this->Cell(25);
    $this->SetFont('Arial','BI',18);
    $w=$this->GetStringWidth($title);//+6;
    //$this->SetX((210-$w)/2);
    $this->SetTextColor(50,40,150);
    //$this->Cell($w,9,$title,0,1,'C', false);
    $this->Cell($w,9,$title,'B',0,'L', false);
    $this->SetTextColor(0,0,0);
    $this->SetFont('Arial','',8);
    $this->Cell(0,9,"Fecha Listado ".date('d/m/Y'),'',1,'R', false);
    $this->Ln(10);
    $this->cabeceraDatos();
}

function Footer()
{
    //Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Text color in gray
    $this->SetTextColor(128);
    //Page number
    $this->Cell(0,10, utf8_decode('Página '.$this->PageNo()),0,0,'C');
}

function cabeceraDatos() {
    //Arial 12
    $this->SetFont('Arial','',10);
    //Background color
    $this->SetFillColor(200,220,255);
    //Title
    $this->Cell(20,6,"SOCIO",0,0,'L',true);
    $this->Cell(60,6,"APELLIDOS",0,0,'L',true);
    $this->Cell(35,6,"NOMBRE",0,0,'L',true);
    $this->Cell(25,6,"DNI",0,0,'L',true);
    $this->Cell(105,6,"DOMICILIO",0,0,'L',true);
    $this->Cell(15,6,"BANCO",0,0,'C',true);
    $this->Cell(15,6,"CUOTA",0,0,'C',true);
    //Line break
    $this->Ln();
    $this->Ln();
    global $calle;
    $calle = '';
}
function usuarioDatos($user, $even = 0) {

    // CAMBIO DE PAGINA PARA QUE NO SE CORTE ENTRE EL TITULO DE LA CALLE Y EL ABONADO
    if($this->GetY()>260) {
        $this->addPage('L','A4');
    }
    
    
    $this->SetFont('Arial','',8);
    //Background color
    if($even==0) {
        $this->SetFillColor(255,255,255);
    } else {
        $this->SetFillColor(235,235,235);
    }
    
    $this->SetFont('Arial', 'B');
    $this->Cell(20,6,$user["CODIGO"],0,0,'L',true);
    $this->Cell(60,6,$user["APELLIDOS"],0,0,'L',true);
    $this->Cell(35,6,$user["NOMBRE"],0,0,'L',true);
    $this->SetFont('');
    $this->Cell(25,6,$user["DNI"],0,0,'L',true);
    $this->Cell(105,6,$user["PREFIJO"]." ".$user["DOMICILIO"].", ".$user['NUMERO'],0,0,'L',true);
    $this->Cell(15,6,$user["BANCO"],0,0,'C',true);
    $this->Cell(15,6,number_format($user["CUOTA"],2,',',''),0,0,'C',true);

    $this->Ln();
    
    // SEGUNDA FILA DE DATOS
    $falta_ar = explode("-", $user['FALTA']);
    $falta = $falta_ar[2]."/".$falta_ar[1]."/".$falta_ar[0];
    $this->Cell(20,6,'',0,0,'L',true);
    $this->Cell(30,6,'Fecha Alta',0,0,'L',true);
    $this->Cell(20,6,$falta,0,0,'R',true);
    $this->Cell(45,6,'',0,0,'L',true);
    $this->Cell(25,6,'',0,0,'L',true);
    $this->Cell(105,6,$user["CPOSTAL"]."    ".$user['POBLACION'],0,0,'L',true);
    $this->Cell(15,6,'',0,0,'C',true);
    $this->Cell(15,6,'',0,0,'C',true);
    
    
    $this->Ln();
}

function usuarios() {

    global $title;
    require_once('config.php');
    $conex= mysql_connect($server_db, $user_db, $pass_db) or die("no se puede 
conectar porque ".mysql_error());
    mysql_select_db($db_db);
    
    $sql = '';
    if(isset($_REQUEST['sql']) && $_REQUEST['sql']!='') {
        $sql_ar = explode("ORDER BY", $_REQUEST['sql']);
        $sql = "SELECT * FROM RECIBOS WHERE ( CAUSABAJA = '' OR CAUSABAJA = NULL ) AND ".$sql_ar[0]." ORDER BY POBLACION ASC, DOMICILIO ASC, NUMERO ASC";
    } else {
        $sql = "SELECT * FROM RECIBOS WHERE ( CAUSABAJA = '' OR CAUSABAJA = NULL ) ORDER BY POBLACION ASC, DOMICILIO ASC, NUMERO ASC";
    }
    
            
    $result = mysql_query($sql, $conex);
    $this->addPage('L','A4');
    $even = 0;

    while ($row=mysql_fetch_array($result)) {
        $this->usuarioDatos($row, $even);
        $even++;
        if($even>1) { $even = 0; }
    };

    mysql_close($conex);

}

function outputDatos()
{
    $this->usuarios();

}}


if(!isset($_REQUEST['code']) || $_REQUEST['code']!='aSFS45FaefsfrSErva4') {
    die;
}


$pdf=new PDF();

$pdf->SetTitle(utf8_decode('Listado General de Socios'));
$title = utf8_decode('Listado General de Socios');

$pdf->SetAuthor('A.VV. Barrio Peral');
$pdf->outputDatos();
$pdf->Output();
?>
