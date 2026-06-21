<?php

include("../../clases/fpdf/fpdf.php");
include_once("adm_utilidad.php");

/*ini_set('display_errors', 1);
    error_reporting(E_ALL);  */



class PDF extends FPDF
{

	function SetDash($black = null, $white = null)
	{
		if ($black !== null) {
			$s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
		} else {
			$s = '[] 0 d';
		}
		$this->_out($s);
	}

	// Page header
	function Header()
	{
		global $x_nota, $o_fecha, $o_cliente, $o_direccion, $o_cedula, $o_telefono; // Declare global variables if they are used outside the class scope and need to be accessed here

		$top = 5; // Initial top position for the header elements
		$x_header = 45; // Initial X position for the text elements
		$font = 'Helvetica';

		// DATOS DE LA EMPRESA
		$this->Image('../../img/logo1.png', 19, 8, 23, 20);

		$this->SetXY($x_header, $top += 1);
		$this->SetFont($font, '', 12);
		$this->SetTextColor(0, 51, 102);
		$this->Cell(50, 10, 'DISTRIBUIDORA BELLINGHIERI, F.P', 0, 0, 'L');

		$this->SetTextColor(0, 51, 102);
		$this->SetFont($font, '', 12);
		$this->SetXY(150, $top);
		$this->Cell(50, 10, 'NOTA NRO. ' . $x_nota, 0, 0, 'R');

		$this->SetXY($x_header, $top += 5);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont($font, 'B', 10);
		$this->Cell(50, 10, 'RIF: V-189835180', 0, 0, 'L');

		$this->SetTextColor(0, 0, 0); // Black
		$this->SetFont($font, '', 10);
		$this->SetXY(165, $top);
		$this->Cell(15, 10, 'Fecha: ', 0, 0, 'R');

		$this->SetFont($font, 'B', 10);
		$this->SetXY(180, $top);
		$this->Cell(20, 10, $o_fecha, 0, 0, 'R');


		$this->SetFont($font, '', 10);
		$this->SetXY($x_header, $top += 5);
		$this->SetFont($font, '', 10);
		$this->Cell(50, 10, 'CALLE 8 CASA NRO 478 URB DON IGNACIO', 0, 0, 'L');

		$this->SetXY($x_header, $top += 5);
		$this->Cell(50, 10, 'EL TIGRE ANZOATEGUI, TLF: 0424-8891559', 0, 0, 'L');


		// DATOS DEL CLIENTE
		$top += 20;
		//$this->SetDash(1, 1);
		$this->SetFont($font, '', 9);

		$this->SetXY(20, $top);
		$this->Cell(100, 5, fix_texto('Razón Social:'), 'LTR', 1, 'L');

		$this->SetXY(120, $top);
		$this->Cell(40, 5, fix_texto('RIF/CI:'), 'TR', 1, 'L');

		$this->SetXY(160, $top);
		$this->Cell(40, 5, fix_texto('Teléfono:'), 'RT', 1, 'L');

		$this->SetXY(20, $top + 10);
		$this->Cell(180, 5, 'Domicilio:', 'LR', 1, 'L');

		//CLIENT DATA 		

		$this->SetFont($font, 'B', 10);
		$this->SetXY(20, $top + 5);
		$this->Cell(100, 5, fix_texto(strtoupper($o_cliente)), 'LBR', 1, 'L');

		
		$this->SetXY(120, $top + 5);
		$this->Cell(40, 5, strtoupper($o_cedula), 'RB', 1, 'L');

		$this->SetXY(160, $top + 5);
		$this->Cell(40, 5, strtoupper($o_telefono), 'RB', 1, 'L');

		$this->SetFont($font, '', 10);
		$this->SetXY(20, $top + 15);
		$this->MultiCell(180, 5, fix_texto($o_direccion), 'LBR', 'L');

		//$this->SetDash();
	}

	// Page footer
	function Footer()
	{
		$font = 'Helvetica';
		$ma = 20;
		$ancho_cuadro = 180;

		$this->SetXY($ma, -40);
		$this->SetFont($font, 'B', 9);
		// 1. Línea superior con el texto (el texto queda arriba de forma natural)
		$this->Cell($ancho_cuadro, 5, ' NOTA:', 'LTR', 1, 'L');
		$this->SetX($ma);
		$this->Cell($ancho_cuadro, 15, '', 'LBR', 1, 'L');


		$this->SetY(-15);
		$this->SetFont($font, '', 8);
		$this->Cell(0, 10, 'Pag. ' . $this->PageNo() . ' de {nb}', 0, 0, 'C');
	}
}

function fix_texto($texto)
{
	$texto_limpio = trim($texto);
	$texto_normalizado = iconv('UTF-8', 'windows-1252', $texto);

	return $texto_normalizado;
}



function fix_texto_tabla($texto)
{

	$longitud_maxima = 55;

	$puntos_suspensivos = '...';

	if (mb_strlen($texto, 'UTF-8') > $longitud_maxima) {
		$texto_recortado = mb_substr($texto, 0, $longitud_maxima, 'UTF-8');
	} else {
		$texto_recortado = $texto;
	}

	$texto_normalizado = iconv('UTF-8', 'windows-1252', $texto_recortado);

	return $texto_normalizado;
}

$max = 110; 
$x_movimiento = 0;

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
$arr_articulo	=  	Combo_Articulo();


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


$ls_sql = "SELECT nu_cantidad, fk_articulo, nu_precio,  
			  nu_cantidad * nu_precio as total
			  FROM t01_detalle
			  WHERE fk_factura = $id_factura ;";


$ls_resultado_1 =  $obj_miconexion->fun_consult($ls_sql);
if ($ls_resultado_1) {
	$mostrar_rs = true;
} else {
	fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
}


// GENERA BODY DE MI REPORTE PDF
$font = 'Helvetica';
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont($font, 'I', 8);

$top = 75;
$ma = 20;
$h1 = 5;
$maxHight = 230;

// Anchos de columnas en un array para facilitar el manejo de posiciones
$widths = [15, 115, 25, 25]; 

$pdf->SetFillColor(230, 230, 230);

// Función anónima para renderizar la cabecera de la tabla sin duplicar código
$printHeader = function($pdf, $font, $ma, $top, $h1, $widths) {
    $pdf->SetFont($font, 'B', 10);
    $pdf->SetXY($ma, $top);
    
    $pdf->Cell($widths[0], $h1, 'Cant.', 1, 0, 'C', true);
    $pdf->Cell($widths[1], $h1, 'Concepto', 1, 0, 'L', true);
    $pdf->Cell($widths[2], $h1, 'Precio', 1, 0, 'R', true);
    $pdf->Cell($widths[3], $h1, 'Total', 1, 1, 'R', true); // ln=1 para bajar de línea
};

// Primera cabecera
$printHeader($pdf, $font, $ma, $top, $h1, $widths);

$total = 0;
$pdf->SetFont($font, '', 9);

while ($fila = pg_fetch_row($ls_resultado_1)) {
    $top += $h1;
    $B = ($top >= $maxHight) ? 'B' : '';

    $pdf->SetXY($ma, $top);
    
    // Renderizado de la fila actual
    $pdf->Cell($widths[0], $h1, $fila[0], $B . 'LR', 0, 'C');
    
    $concepto = strtoupper(fix_texto_tabla($arr_articulo[$fila[1]]));
    $pdf->Cell($widths[1], $h1, $concepto, $B . 'R', 0, 'L');
    
    $precio = number_format($fila[2], 2, ",", ".");
    $pdf->Cell($widths[2], $h1, $precio, $B . 'R', 0, 'R');
    
    $subtotal = number_format($fila[3], 2, ",", ".");
    $pdf->Cell($widths[3], $h1, $subtotal, $B . 'R', 1, 'R');

    $total += $fila[3];

    // Control de salto de página 
    if ($top >= $maxHight) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'I', 8); // Mantiene el comportamiento original
        
        $top = 80;
        $printHeader($pdf, $font, $ma, $top, $h1, $widths);
        $pdf->SetFont($font, '', 10); // Restaura la fuente para el contenido
    }
}

// Fila de Totales

$top += $h1;
$pdf->SetFont($font, 'B', 10);
$pdf->SetXY($ma, $top);

$pdf->Cell($widths[0], $h1, '', 'T', 0, 'C');
$pdf->Cell($widths[1], $h1, '', 'T', 0, 'L');
$pdf->Cell($widths[2], $h1, 'Total', 1, 0, 'R');

$total_formateado = number_format($total, 2, ",", ".");
$pdf->Cell($widths[3], $h1, $total_formateado, 1, 1, 'R');

$pdf->Output();