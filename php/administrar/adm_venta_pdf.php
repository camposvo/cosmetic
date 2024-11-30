<?php

include("../../clases/fpdf/fpdf.php");
include_once("adm_utilidad.php");

/* 	ini_set('display_errors', 1);
    error_reporting(E_ALL);  */


	function fix_texto($texto) {
		$texto_normalizado = iconv('UTF-8', 'windows-1252', $texto);	
		return $texto_normalizado;
	}

$max = 110; // el max se usa para el N� de caracteres que permite pdf
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
if (!$_GET) {
	foreach ($_POST as $nombre_campo => $valor) {
		$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
		eval($asignacion);
	}
	$filtro = isset($filtro) ? $filtro : 'NO_ALL';
} else {
	foreach ($_GET as $nombre_campo => $valor) {
		$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
		eval($asignacion);
	}
	$filtro = isset($filtro) ? $filtro : 'NO_ALL';
}

$obj_miconexion = fun_crear_objeto_conexion();
$li_id_conex = fun_conexion($obj_miconexion);

$arr_cliente =  Combo_Cliente();
$arr_vendedor =  Combo_Vendedor();
$arr_rubro   =  Combo_Rubro();
$arr_abono   =  Combo_Abono();
$arr_articulo	=  	Combo_Articulo_Venta();


$ls_sql = "SELECT pk_factura, fk_responsable, fk_cliente, to_char(fe_fecha_factura, 'dd/mm/yyyy'),  tx_nota,
				tx_concepto,  nu_total, nu_abono, to_char(nextval('sec_nota'),'0000000') 
				FROM t20_factura
				WHERE pk_factura = $x_movimiento";

			



$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
if ($ls_resultado != 0) {
	$row = pg_fetch_row($ls_resultado, 0);
	$id_factura      = $row[0];
	$co_usuario	    = $row[1];
	$o_cliente  	= $row[2];
	$o_fecha        = $row[3];
	$x_referencia      = $row[4];
	$x_observacion  = $row[5];
	$x_total        = $row[6];
	$x_abono        = $row[7];
	$x_nota       = $row[8];

	// Extrae el detalle de la factura
	
} else {
	fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
}


/*-------------------------------------------------------------------------------------------
LEE DATOS DEL CLIENTE
-------------------------------------------------------------------------------------------*/
$ls_sql = "SELECT tx_nombre || ' ' || tx_apellido, tx_cedula, tx_direccion_hab, tx_telefono_hab
			FROM s01_persona 
			WHERE s01_persona.co_persona = $o_cliente";



$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
if ($ls_resultado != 0) {
	$row = pg_fetch_row($ls_resultado, 0);
	$o_cliente      = $row[0];	
	$o_cedula      = $row[1];
	$o_direccion     = $row[2];
	$o_telefono    = $row[3];
}

//echo $o_cliente;

$ls_sql = "SELECT nu_cantidad, fk_articulo, nu_precio,  
			  nu_cantidad * nu_precio as total
			  FROM t01_detalle
			  WHERE fk_factura = $id_factura ;";


	$ls_resultado_1 =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado_1) {
		$mostrar_rs = true;
		// Consulta exitosa					
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}


/*******************************************************************************************************/

$top = 10;



$pdf= new FPDF('P', 'mm', 'A4', 'UTF-8');
$pdf->AddPage(); 

//HEADER
$pdf->SetFont('Courier', 'B', 10); 
$pdf->SetXY(150, $top += 5);
/* $pdf->Image('../../img/logo1.png',20,15,45,30);
$pdf->Cell(50, 10, 'BELLINGHIERI COSMETICS, C.A', 0, 0,'R');
$pdf->SetXY(150, $top += 5);
$pdf->Cell(50, 10, 'RIF: J-410596457', 0, 0,'R');
$pdf->SetXY(150, $top += 5);
$pdf->Cell(50, 10, 'CALLE 8 CASA NRO 478 URB DON IGNACIO', 0, 0,'R');
$pdf->SetXY(150, $top += 5);
$pdf->Cell(50, 10, 'EL TIGRE ANZOATEGUI', 0, 0,'R');
$pdf->SetXY(150, $top += 5);
$pdf->Cell(50, 10, 'ZONA POSTAL 6050', 0, 0,'R'); */


$pdf->SetTextColor(255, 0, 0); // Rojo (R, G, B)
$pdf->SetFont('Courier', 'B', 12); 
$pdf->SetXY(150, $top += 10);
$pdf->Cell(50, 10, 'NOTA NRO .'.$x_nota, 0, 0,'R');

$pdf->SetTextColor(0, 0, 0); // Rojo (0, 0, 0)
$pdf->SetFont('Courier', 'B', 10); 
$pdf->SetXY(150, $top += 5);
$pdf->Cell(50, 10, fix_texto('Fecha de Emisión:').$o_fecha, 0, 0,'R');

//CLIENT TITLE
$top += 15;

$pdf->SetFont('Courier', '', 9); 
$pdf->SetXY(20, $top);
$pdf->Cell(120, 5, fix_texto('Nombre o Razón Social:'), 'LTR', 1, 'L');

$pdf->SetXY(20, $top + 10);
$pdf->Cell(120, 5, 'Domicilio Fiscal:', 'LR', 1, 'L');

$pdf->SetXY(140, $top);
$pdf->Cell(60, 5, fix_texto('RIF o CI:'), 'LTR', 1, 'L');

$pdf->SetXY(140, $top + 10);
$pdf->Cell(60, 5, fix_texto('Teléfono:'), 'LR', 1, 'L');

//CLIENT DATA 

$pdf->SetFont('Courier', 'B', 11); 
$pdf->SetXY(20, $top + 5);
$pdf->Cell(120, 5, fix_texto(strtoupper($o_cliente)),'LBR', 1, 'L');

$pdf->SetXY(20, $top + 15);
$pdf->MultiCell(120, 5, fix_texto($o_direccion), 'LBR', 'L');

$pdf->SetXY(140, $top + 5);
$pdf->Cell(60, 5, strtoupper($o_cedula),'LBR', 1, 'L');

$pdf->SetXY(140, $top + 15);
$pdf->Cell(60, 10, strtoupper($o_telefono),'LBR', 1, 'L');

//ITEMS DATA



$top += 30;
$ma = 20;
$h1 = 7;

$w1 = 20;
$w2 = 110;
$w3 = 25;
$w4 = 25;



$pdf->SetFillColor(198,217,241); 

$pdf->SetFont('Courier', 'B', 10); 
//$pdf->SetTextColor(255, 255, 255); // Rojo (0, 0, 0)

$pdf->SetXY($ma, $top);
$pdf->Cell($w1, $h1, 'Cant.', 1, 1, 'C',true);

$pdf->SetXY($ma+20, $top);
$pdf->Cell($w2, $h1, 'Concepto', 1, 1, 'C', true);

$pdf->SetXY($ma+130, $top);
$pdf->Cell($w3, $h1, 'Precio', 1, 1, 'C',true);

$pdf->SetXY($ma+155, $top);
$pdf->Cell($w4, $h1, 'Total', 1, 1, 'C',true);


//$pdf->SetDash(1,1);
$total = 0;
$pdf->SetFont('Courier', '', 10); 
while($fila = pg_fetch_row($ls_resultado_1)){	

	$top += 7;	
	
	$pdf->SetXY($ma, $top);
	$pdf->Cell($w1, $h1, $fila[0], 1, 1, 'C');

	$pdf->SetXY($ma+20, $top);
	$pdf->Cell($w2, $h1,strtoupper($arr_articulo[$fila[1]]), 1, 1, 'L');

	$precio = number_format($fila[2],2,",",".");
	$pdf->SetXY($ma+130, $top);
	$pdf->Cell($w3, $h1, $precio, 1, 1, 'C');

	$subtotal = number_format($fila[3],2,",",".");
	$pdf->SetXY($ma+155, $top);
	$pdf->Cell($w4, $h1, $subtotal, 1, 1, 'C');

	$total += $fila[3];

	
}

	$top += 7;	
	$pdf->SetFont('Courier', 'B', 11); 
	
	$pdf->SetXY($ma, $top);
	$pdf->Cell($w1, $h1, '', 1, 1, 'C');

	$pdf->SetXY($ma+20, $top);
	$pdf->Cell($w2, $h1, '', 1, 1, 'L');

	$pdf->SetXY($ma+130, $top);
	$pdf->Cell($w3, $h1, 'Total:', 1, 1, 'C');

	$temp = number_format($total,2,",",".");

	$pdf->SetXY($ma+155, $top);
	$pdf->Cell($w4, $h1,$temp, 1, 1, 'C');
 


$pdf->Output(); //cierra el archivo
