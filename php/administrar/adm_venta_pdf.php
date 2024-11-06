<?php

include("../../clases/fpdf/fpdf.php");
include_once("adm_utilidad.php");

/* 	ini_set('display_errors', 1);
error_reporting(E_ALL); */

$top = 110; // el top se usa para el N� de caracteres que permite pdf
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

$co_usuario  =  $_SESSION["li_cod_usuario"];
$arr_cliente =  Combo_Cliente();
$arr_vendedor =  Combo_Vendedor();
$arr_rubro   =  Combo_Rubro();
$arr_abono   =  Combo_Abono();

$arr_fecha = explode('-', $x_fecha, 2);
$x_fecha_ini = $arr_fecha[0];
$x_fecha_fin = $arr_fecha[1];

$x_vendedor = isset($x_vendedor) ? $x_vendedor : $co_usuario;
$x_cliente   = isset($x_cliente) ? $x_cliente : 0;


$ls_sql = "SELECT pk_factura, fk_responsable, fk_cliente, to_char(fe_fecha_factura, 'dd/mm/yyyy'),  tx_nota,
				tx_concepto,  nu_total, nu_abono
				FROM t20_factura
				WHERE pk_factura = $x_movimiento";

echo $ls_sql;



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

	// Extrae el detalle de la factura
	
} else {
	fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
}


var_dump($ls_resultado_1);

/*-------------------------------------------------------------------------------------------
LEE DATOS DEL CLIENTE
-------------------------------------------------------------------------------------------*/
$ls_sql = "SELECT tx_nombre || ' ' || tx_apellido, tx_cedula, tx_direccion_hab
			FROM s01_persona 
			WHERE s01_persona.co_persona = $o_cliente";

			echo $ls_sql;


$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
if ($ls_resultado != 0) {
	$row = pg_fetch_row($ls_resultado, 0);
	$o_cliente      = $row[0];	
	$o_cedula      = $row[1];
	$o_direccion     = $row[2];
}

//echo $o_cliente;

$ls_sql = "SELECT fk_rubro, fk_articulo, nu_cantidad, nu_precio,  
			  nu_cantidad * nu_precio as total
			  FROM t01_detalle
			  WHERE fk_factura = $id_factura ;";

echo $ls_sql;

	$ls_resultado_1 =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado_1) {
		$mostrar_rs = true;
		// Consulta exitosa					
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}


/*******************************************************************************************************/
//para la cantidad de caracteres dentro de una linea 
if ($cont >= $top) {
}

$pdf = new FPDF('P', 'mm', 'A4');
//$pdf=new FPDF();// orientacion de la hoja, medidas en este caso milimetro, tipo de hoja
$pdf->AddPage(); // agregar nueva pagina
//$pdf->SetTextColor(140,140,140);//color del texto(color rojo, color verde, color azul)
$pdf->SetFont('Arial', 'B', 10); // formato del texto(tipo de fuente, estilo de la fuente, tama�o de la fuente)
//$pdf->Image('../../img/iconos_pagina/logo_pdf.jpg',10,8,320,8);//nombre de imagen, ubicacion horizontal, ubicacion vertical, ancho, grosor
$pdf->Text(10, 20, 'COMPROBANTE DE PAGO'); // ubicacion horizontal, ubicacion vertical, cadena de caracteres

//$pdf->SetDash(1,1);
$pdf->Line(10, 22, 200, 22);

while($fila = pg_fetch_row($ls_resultado_1)){				
	echo ucwords(strtolower($fila[0]));	
}

//Fecha
$pdf->SetFont('Times', 'B', 10);
$col_1 = 18;
$pdf->SetXY(10, $col_1 += 6);
$pdf->Cell(60, 6, 'Fecha de Emision: ' . $o_fecha, 0, 'L');

$pdf->SetXY(10, $col_1 += 6);
$pdf->Cell(25, 6, 'Cedula: ', 0, 'L');
$pdf->Cell(45, 6,  number_format($o_cedula, 0, ",", "."), 0, 'L');


$pdf->Line(10, $col_1 += 6, 200, $col_1);

$pdf->Output(); //cierra el archivo
