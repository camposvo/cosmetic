<?php

include("../../clases/fpdf/fpdf.php");
include_once("adm_utilidad.php");

/* 	ini_set('display_errors', 1);
    error_reporting(E_ALL);  */

class PDF extends FPDF
{

	 // Page header
    function Header()
    {
        global $x_nota, $o_fecha, $o_cliente, $o_direccion, $o_cedula, $o_telefono; // Declare global variables if they are used outside the class scope and need to be accessed here

        $top = 10; // Initial top position for the header elements
        $x_header = 45; // Initial X position for the text elements

        // Logo
        // Adjust the path to your logo image as needed
        $this->Image('../../img/logo1.png', 20, 14, 23, 20);

        // Company Name
        $this->SetXY($x_header, $top += 1);
        $this->SetFont('Courier', 'B', 12);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(50, 10, 'DISTRIBUIDORA BELLINGHIERI, F.P', 0, 0, 'L');

        // RIF
        $this->SetXY($x_header, $top += 5);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Courier', 'B', 10);
        $this->Cell(50, 10, 'RIF: V-189835180', 0, 0, 'L');

        // Address Line 1
        $this->SetXY($x_header, $top += 5);
        $this->SetFont('Courier', '', 10);
        $this->Cell(50, 10, 'CALLE 8 CASA NRO 478 URB DON IGNACIO', 0, 0, 'L');

        // Address Line 2 / Phone
        $this->SetXY($x_header, $top += 5);
        $this->Cell(50, 10, 'EL TIGRE ANZOATEGUI, TLF: 0424-8891559', 0, 0, 'L');

        // Nota NRO.
        $this->SetTextColor(255, 0, 0); // Red
        $this->SetFont('Courier', 'B', 12);
        $this->SetXY(150, $top += 10);
        // Ensure $x_nota is defined and accessible (e.g., passed to the class or as a global)
        $this->Cell(50, 10, 'NOTA NRO .' . $x_nota, 0, 0, 'R');

        // Fecha de Emisión
        $this->SetTextColor(0, 0, 0); // Black
        $this->SetFont('Courier', 'B', 10);
        $this->SetXY(150, $top += 5);
        // Ensure fix_texto() and $o_fecha are defined and accessible
        $this->Cell(50, 10, fix_texto('Fecha de Emisión:') . $o_fecha, 0, 0, 'R');

		$top += 10;

		$this->SetFont('Courier', '', 9);
		$this->SetXY(20, $top);
		$this->Cell(120, 5, fix_texto('Nombre o Razón Social:'), 'LTR', 1, 'L');

		$this->SetXY(20, $top + 10);
		$this->Cell(120, 5, 'Domicilio Fiscal:', 'LR', 1, 'L');

		$this->SetXY(140, $top);
		$this->Cell(60, 5, fix_texto('RIF o CI:'), 'LTR', 1, 'L');

		$this->SetXY(140, $top + 10);
		$this->Cell(60, 5, fix_texto('Teléfono:'), 'LR', 1, 'L');

		//CLIENT DATA 

		$this->SetFont('Courier', 'B', 11);
		$this->SetXY(20, $top + 5);
		$this->Cell(120, 5, fix_texto(strtoupper($o_cliente)), 'LBR', 1, 'L');

		$this->SetXY(20, $top + 15);
		$this->MultiCell(120, 5, fix_texto($o_direccion), 'LBR', 'L');

		$this->SetXY(140, $top + 5);
		$this->Cell(60, 5, strtoupper($o_cedula), 'LBR', 1, 'L');

		$this->SetXY(140, $top + 15);
		$this->Cell(60, 10, strtoupper($o_telefono), 'LBR', 1, 'L');


        // Line break
        //$this->Ln(20); // Add a line break after the header to give space for content
    }

	// Page footer
	function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(0, 10, 'Pag. ' . $this->PageNo() . ' de {nb}', 0, 0, 'C');
	}
}

function fix_texto($texto)
{
	$texto_normalizado = iconv('UTF-8', 'windows-1252', $texto);

	return $texto_normalizado;
}



function fix_texto_tabla($texto)
{

	$longitud_maxima = 50;

	$puntos_suspensivos = '...';

	if (mb_strlen($texto, 'UTF-8') > $longitud_maxima) {
		$texto_recortado = mb_substr($texto, 0, $longitud_maxima, 'UTF-8');
	} else {
		$texto_recortado = $texto;
	}

	$texto_normalizado = iconv('UTF-8', 'windows-1252', $texto_recortado);

	return $texto_normalizado;
}

$max = 110; // el max se usa para el N� de caracteres que permite pdf

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


/*-------------------------------------------------------------------------------------------
	PDF
-------------------------------------------------------------------------------------------*/


$pdf = new PDF('P', 'mm', 'A4', 'UTF-8');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'I', 8);

$top = 80;
$ma = 20;
$h1 = 7;
$w1 = 20;
$w2 = 110;
$w3 = 25;
$w4 = 25;


$pdf->SetFillColor(198, 217, 241);
$pdf->SetFont('Courier', 'B', 10);
$pdf->SetXY($ma, $top);
$pdf->Cell($w1, $h1, 'Cant.', 1, 1, 'C', true);

$pdf->SetXY($ma + 20, $top);
$pdf->Cell($w2, $h1, 'Concepto', 1, 1, 'C', true);

$pdf->SetXY($ma + 130, $top);
$pdf->Cell($w3, $h1, 'Precio', 1, 1, 'C', true);

$pdf->SetXY($ma + 155, $top);
$pdf->Cell($w4, $h1, 'Total', 1, 1, 'C', true);


//$pdf->SetDash(1,1);
$total = 0;
$pdf->SetFont('Courier', '', 9);
while ($fila = pg_fetch_row($ls_resultado_1)) {

	$pdf->SetFont('Courier', '', 10);
	$top += 7;

	$pdf->SetXY($ma, $top);
	$pdf->Cell($w1, $h1, $fila[0], 1, 1, 'C');

	$pdf->SetXY($ma + 20, $top);
	$pdf->Cell($w2, $h1, strtoupper(fix_texto_tabla($arr_articulo[$fila[1]])), 1, 1, 'L');

	$precio = number_format($fila[2], 2, ",", ".");
	$pdf->SetXY($ma + 130, $top);
	$pdf->Cell($w3, $h1, $precio, 1, 1, 'C');

	$subtotal = number_format($fila[3], 2, ",", ".");
	$pdf->SetXY($ma + 155, $top);
	$pdf->Cell($w4, $h1, $subtotal, 1, 1, 'C');

	$total += $fila[3];

	if ($top >= 200) {
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'I', 8);		
	    $top = 80;    	
		$ma = 20;
		$h1 = 7;
		$w1 = 20;
		$w2 = 110;
		$w3 = 25;
		$w4 = 25;

		$pdf->SetFont('Courier', 'B', 10);
		$pdf->SetXY($ma, $top);
		$pdf->Cell($w1, $h1, 'Cant.', 1, 1, 'C', true);
		$pdf->SetXY($ma + 20, $top);
		$pdf->Cell($w2, $h1, 'Concepto', 1, 1, 'C', true);
		$pdf->SetXY($ma + 130, $top);
		$pdf->Cell($w3, $h1, 'Precio', 1, 1, 'C', true);
		$pdf->SetXY($ma + 155, $top);
		$pdf->Cell($w4, $h1, 'Total', 1, 1, 'C', true);
	}
}

$top += 7;
$pdf->SetFont('Courier', 'B', 11);
$pdf->SetXY($ma, $top);
$pdf->Cell($w1, $h1, '', 1, 1, 'C');
$pdf->SetXY($ma + 20, $top);
$pdf->Cell($w2, $h1, '', 1, 1, 'L');
$pdf->SetXY($ma + 130, $top);
$pdf->Cell($w3, $h1, 'Total:', 1, 1, 'C');
$temp = number_format($total, 2, ",", ".");
$pdf->SetXY($ma + 155, $top);
$pdf->Cell($w4, $h1, $temp, 1, 1, 'C');



$pdf->Output(); //cierra el archivo
