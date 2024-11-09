<?php

	include("../../clases/fpdf/fpdf.php");
	include_once ("nom_utilidad.php");	

/* 	ini_set('display_errors', 1);
error_reporting(E_ALL); */
	
	$top=110;// el top se usa para el N� de caracteres que permite pdf
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	if (!$_GET){
		foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}else{
		foreach($_GET as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$ls_sql = "SELECT co_nomina, fk_contrato, UPPER(s01_persona.tx_nombre), 
					 to_char(fe_pago,'dd-TMMon-yyyy'),  to_char(fe_inicial,'dd-TMMon-yyyy') , to_char(fe_final,'dd-TMMon-yyyy'), 
					 s01_persona.tx_cedula, t12_contrato.nu_salario, tx_tipo_nomina, 
					 nu_asig_1,nu_asig_2, nu_asig_3, nu_asig_4, 
					 nu_dedu_1,nu_dedu_2, nu_dedu_3, nu_dedu_4, 
					 nu_sueldo, co_nomina 
			FROM t22_nomina 
			INNER JOIN t12_contrato ON t12_contrato.pk_contrato = t22_nomina.fk_contrato
			INNER JOIN s01_persona  ON t12_contrato.fk_trabajador = 	s01_persona.co_persona 	
			WHERE t12_contrato.in_activo ='S' AND co_nomina = $co_recibo";		
	
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$nomina	    = $row[0];
		$contrato   = $row[1];	
		$nombre     = $row[2];
		$fecha_pago = $row[3];
		$fe_inicial = $row[4];
		$fe_final   = $row[5];
		$cedula     = $row[6];
		$salario    = $row[7];
		$tipo_nomina  = $row[8];
		
		$asig_alimento = $row[9];
		$asig_2        = $row[10];
		$asig_bono     = $row[11];
		$asig_4 	   = $row[12];
		
		$deduc_1        = $row[13];
		$deduc_2        = $row[14];
		$deduc_3        = $row[15];		
		$deduc_4	   = $row[16];
		$sueldo_basico = $row[17];
		
				
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
	/*if($tipo_nomina == "QUINCENAL"){
		$salario   = $salario   * 15;				
	}else if(tipo_nomina == "SEMANAL"){
		$salario   = $salario   * 5;		
		
	}else if(tipo_nomina == "DIARIO"){
			$salario   = $salario  ;		
	}else{
		$salario   = $salario  ;		
	}*/
	
	$total_asignacion = $sueldo_basico + $asig_alimento + $asig_2 + $asig_bono + $asig_4 + $asig_5 + $asig_6 + $asig_7;
	$total_deduccion = $deduc_1 + $deduc_2 + $deduc_3 + $deduc_4  + $deduc_5  + $deduc_6 + $deduc_7;
	$pagar =  $total_asignacion - $total_deduccion ;
/* 	echo "total:";

	echo $total_asignacion;
	exit;
	 */

/*******************************************************************************************************/
//para la cantidad de caracteres dentro de una linea 
if ($cont >= $top){
	
}

$pdf = new FPDF('P','mm','A4');
//$pdf=new FPDF();// orientacion de la hoja, medidas en este caso milimetro, tipo de hoja
$pdf->AddPage();// agregar nueva pagina
//$pdf->SetTextColor(140,140,140);//color del texto(color rojo, color verde, color azul)
$pdf->SetFont('Arial','B', 10);// formato del texto(tipo de fuente, estilo de la fuente, tama�o de la fuente)
//$pdf->Image('../../img/iconos_pagina/logo_pdf.jpg',10,8,320,8);//nombre de imagen, ubicacion horizontal, ubicacion vertical, ancho, grosor
$pdf->Text(10,20,'COMPROBANTE DE PAGO');// ubicacion horizontal, ubicacion vertical, cadena de caracteres

//$pdf->SetDash(1,1);
$pdf->Line(10, 22, 200, 22);

//Fecha
$pdf->SetFont('Times','B', 10);
$col_1 = 18;
$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(60, 6, 'Fecha de Emision: '.$fecha_pago,0 , 'L');
$pdf->Cell(45, 6, 'Periodo: '.$fe_inicial.' hasta '.$fe_final, 0, 'L');

$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(25, 6, 'Empleado: ', 0, 'L');
$pdf->Cell(45, 6, $nombre, 0, 'L');


$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(25, 6, 'Cedula: ', 0, 'L');
$pdf->Cell(45, 6,  number_format($cedula,0,",","."), 0, 'L');

/*$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(25, 6, 'Salario: ', 0, 'L');
$pdf->Cell(45, 6, $sueldo_basico, 0, 'L');*/

$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(25, 6, 'Tipo Nomina: ', 0, 'L');
$pdf->Cell(45, 6, $tipo_nomina, 0, 'L');


//****************************************************

$pdf->Line(10, $col_1+=6, 200, $col_1);

$pdf->SetXY(10,$col_1+=2);
$pdf->Cell(60, 6, 'Descripcion', 0, 'L');
$pdf->Cell(60, 6, 'Asignacion ', 0, 'L');
$pdf->Cell(60, 6, 'Deduccion ', 0, 'L');

$pdf->Line(10, $col_1+=6, 200, $col_1);
//****************************************************

//****************** ASIGNACIONES
if ($sueldo_basico <> 0){
	$pdf->SetXY(10,$col_1+=2);
	$pdf->Cell(60, 6, 'Sueldo Basico: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($sueldo_basico,2,",",".") , 0, 'L');
}

if ($asig_alimento <> 0){
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(60, 6, 'Bono de Alimentacion: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($asig_alimento,2,",","."), 0, 'L');
}

if ($asig_2 <> 0){
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(60, 6, 'Feriados: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($asig_2,2,",","."), 0, 'L');
}

if ($asig_bono <> 0){
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(60, 6, 'Bono de Produccion: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($asig_bono,2,",","."), 0, 'L');
}

if ($asig_4 <> 0){	
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(60, 6, 'Ayuda Unica: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($asig_4,2,",","."), 0, 'L');
}


//****************** DEDUCCIONES
if ($deduc_1 <> 0){	
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(120, 6, 'Seguro Social: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($deduc_1,2,",",".") , 0, 'L');
}

if ($deduc_2 <> 0){	
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(120, 6, 'FAOV: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($deduc_2,2,",","."), 0, 'L');
}

if ($deduc_3 <> 0){	
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(120, 6, 'Prestaciones: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($deduc_3,2,",","."), 0, 'L');
}
	
if ($deduc_4 <> 0){	
	$pdf->SetXY(10,$col_1+=6);
	$pdf->Cell(120, 6, 'Otros: ', 0, 'L');
	$pdf->Cell(30, 6,  number_format($deduc_4,2,",","."), 0, 'L');
}

$pdf->Line(10, $col_1+=6, 200, $col_1);
//****************************************************

$pdf->SetXY(10,$col_1+=2);
$pdf->Cell(60, 6, 'Total: ', 0, 'L');
$pdf->Cell(60, 6,  number_format($total_asignacion,2,",",".") , 0, 'L');
$pdf->Cell(30, 6, number_format($total_deduccion*-1,2,",","."), 0, 'L');


$pdf->Line(10, $col_1+=6, 200, $col_1);
$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(60, 6, 'Total a Pagar: ', 0, 'L');
$pdf->Cell(30, 6, number_format($pagar,2,",",".") , 0, 'L');



$pdf->Line(10, $col_1+=6, 200, $col_1);	
	
$pdf->Output();//cierra el archivo


?>