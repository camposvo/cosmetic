<?php

	include("../../clases/fpdf/fpdf.php");
	include_once ("nom_utilidad.php");	

/* 	ini_set('display_errors', 1);
error_reporting(E_ALL); */
	
	$top=110;// el top se usa para el N� de caracteres que permite pdf

	$o_cantidad  = 0;
	$o_cantidad2 = 0;
	if (!$_GET)	{
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
	
	$obj_miconexion_1 = fun_crear_objeto_conexion();
	$li_id_conex_1 = fun_conexion($obj_miconexion);
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_rubro   =  Combo_Rubro();
	$x_fecha_actual = date('d/m/Y h:i');
	

	/*-------------------------------------------------------------------------------------------
		RUTINAS: MOSTRAR DATOS
	-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT pk_factura, fk_responsable, UPPER(s01_persona.tx_nombre), to_char(fe_fecha_factura,'DD/MM/YYYY') ,  
				tx_nota, tx_concepto,  nu_total, nu_subtotal, f_calcular_abono($x_movimiento),
				(nu_total - f_calcular_abono($x_movimiento)) as Debe
				FROM t20_factura
				INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente
				WHERE pk_factura = $x_movimiento";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$id_factura      = $row[0];
		$co_usuario	    = $row[1];
		$o_cliente  	= $row[2];	
		$o_fecha        = $row[3];
		$o_nota      = $row[4];
		$x_observacion  = $row[5];
		$x_total        = $row[6];
		$x_subtotal     = $row[7];
		$x_abono    	= $row[8];
		$x_debe    	= $row[9];
		
		// Extrae el detalle de la factura
		$ls_sql ="SELECT t02_proyecto.tx_nombre, nb_articulo, t01_detalle.nu_cant_item, t01_detalle.nu_cantidad, t01_detalle.nu_precio, 
						t01_detalle.nu_cantidad * t01_detalle.nu_precio as total, pk_detalle 
						FROM t01_detalle 
						INNER JOIN t02_proyecto ON t02_proyecto.pk_proyecto = t01_detalle.fk_rubro 
						INNER JOIN t13_articulo ON t13_articulo.pk_articulo =t01_detalle.fk_articulo
						WHERE fk_factura = $id_factura ;";
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado){
			$mostrar_rs = true;
			// Consulta exitosa					
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
	/*-------------------------------------------------------------------------------------------
		RUTINAS: Muestra la lista de ABONOS realizados
	-------------------------------------------------------------------------------------------*/
  	$i=0;
	$ls_sql = "SELECT to_char(fe_fecha,'DD/MM/YYYY'), UPPER(s01_persona.tx_nombre||' '||s01_persona.tx_apellido), tx_observacion, nu_monto, pk_abono
					FROM t04_abono
					INNER JOIN s01_persona ON s01_persona.co_persona = t04_abono.fk_indicador
					WHERE fk_factura= $x_movimiento";
					
	//echo $ls_sql;	
	$ls_resultado_1 =  $obj_miconexion_1->fun_consult($ls_sql);			
	if($ls_resultado_1 != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex_1,$ls_sql,$_SERVER['PHP_SELF']);
	}


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
$pdf->Text(10,20,'Nota de Entrega');// ubicacion horizontal, ubicacion vertical, cadena de caracteres

//$pdf->SetDash(1,1);
$pdf->Line(10, 22, 200, 22);

//Fecha
$pdf->SetFont('Times','B', 10);
$col_1 = 18;
$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(60, 6, 'Fecha de Emision: '.$fecha_pago,0 , 'L');
$pdf->Cell(45, 6, 'Periodo: '.$fe_inicial.' hasta '.$fe_final, 0, 'L');

$pdf->SetXY(10,$col_1+=6);
$pdf->Cell(25, 6, 'Cedula: ', 0, 'L');
$pdf->Cell(45, 6,  number_format($o_cliente,0,",","."), 0, 'L');


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