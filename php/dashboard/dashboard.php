<?php

/* ini_set('display_errors', 1);
error_reporting(E_ALL);   */

session_start();
include_once("utilidad.php");
$usu_autentico = isset($_SESSION['autentificado']) ? $_SESSION['autentificado'] : '';
if ($usu_autentico != "SI") {
	session_destroy();
	echo "<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
	exit();
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<title>BellinghieriCosmetic</title>

	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  -->
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../clases/Highstock-5.0.2/code/highstock.js"></script>
	<script src="../../clases/Highstock-5.0.2/code/modules/exporting.js"></script>
	<script src="../../clases/Highstock-5.0.2/code/highcharts-3d.js"></script>

	<style type="text/css">
		td.details-control {
			background: url('../../img/details_open.png') no-repeat center center;
			cursor: pointer;
		}

		tr.shown td.details-control {
			background: url('../../img/details_close.png') no-repeat center center;
		}
	</style>

</head>

<body>
	<?php
	/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
	-------------------------------------------------------------------------------------------*/
	$Disponible  = 0;
	$Banco       = 0;
	$sw = 1;
	$CURRENT_YEAR = '2024';

	if (!$_GET) {
		foreach ($_POST as $nombre_campo => $valor) {
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	} else {
		foreach ($_GET as $nombre_campo => $valor) {
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}


	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

	$obj_miconexion_1 = fun_crear_objeto_conexion();
	$li_id_conex_1 = fun_conexion($obj_miconexion_1);

	$obj_miconexion_2 = fun_crear_objeto_conexion();
	$li_id_conex_2 = fun_conexion($obj_miconexion_2);

	$obj_miconexion_3 = fun_crear_objeto_conexion();
	$li_id_conex_3 = fun_conexion($obj_miconexion_3);

	$obj_miconexion_4 = fun_crear_objeto_conexion();
	$li_id_conex_4 = fun_conexion($obj_miconexion_4);

	$obj_miconexion_5 = fun_crear_objeto_conexion();
	$li_id_conex_5 = fun_conexion($obj_miconexion_5);

	$obj_miconexion_6 = fun_crear_objeto_conexion();
	$li_id_conex_6 = fun_conexion($obj_miconexion_6);

	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_tipo =     Combo_TipoMovimiento();

	// Carga variables con los ultimos 30 DIAS POR DEFECTO
	$x_fecha_actual        =  date('d/m/Y');
	$x_fecha_mespasado 	   =  date('d/m/Y', strtotime('-29 day'));

	/*-------------------------------------------------------------------------------------------
	INVOCA FUNCION DE B.D. PARA REFRESCAR VISTA MATERIALIZADA
----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT f_refresh();";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);

	if ($ls_resultado != 0) {
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
	}

	/*-------------------------------------------------------------------------------------------
	CONSULTA DE CUENTAS POR COBRAR
	-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(abono) as Abono, sum (detalle) as Deuda
				FROM v01_pago 
				WHERE v01_pago.tx_tipo='CTAXCOBRAR' ";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		$row = pg_fetch_row($ls_resultado, 0);
		$AbonoCtxCob    = $row[0];
		$TotalCtxCob    = $row[1];
		$CtxCob     = $row[1] - $row[0];
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
	}


	/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE BANCO
	----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(nu_capital) FROM t15_banco";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		$row = pg_fetch_row($ls_resultado, 0);
		$Banco    = $row[0];
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
	}


	/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE CUENTAS POR PAGAR
	-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(f_calcular_factura(pk_factura)) - sum(f_calcular_abono_capital(pk_factura)) as Debe 
				FROM t20_factura 
			WHERE t20_factura.tx_tipo='CTAXPAGAR' or t20_factura.tx_tipo='GASTO' ";

	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		$row = pg_fetch_row($ls_resultado, 0);
		$DebeCtxPag    = $row[0];
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
	}


	/*-------------------------------------------------------------------------------------------
	CONSULTA DE CUENTAS POR COBRAR VENTAS
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(detalle) - sum(abono) as Debe 
		FROM v01_pago 
		WHERE v01_pago.tx_tipo='VENTA'";


	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		$row = pg_fetch_row($ls_resultado, 0);
		$VentaXcobrar    = $row[0];
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
	}

	/*-------------------------------------------------------------------------------------------
	CONSULTA DE MONTO DISPONIBLE
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT f_saldo_disponible() ";
	//echo $ls_sql;		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		if ($obj_miconexion->fun_numregistros($ls_resultado) > 0) {
			$row = pg_fetch_row($ls_resultado, 0);
			$Caja    = $row[0];
		}
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
	}

	$Disponible  = $Caja + $Banco;



	/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA LOS INGRESOS Y EGRESOS 
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT SUM(ingreso) as Ingreso, SUM(egreso) as Egreso FROM vm02_edo_cuenta	";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		$row = pg_fetch_row($ls_resultado, 0);
		$ingreso   = $row[0];
		$egreso    = $row[1];
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__); // enviar mensaje de error de consulta
	}


	/*-------------------------------------------------------------------------------------------
	 GRAFICA 2 - INGRESOS VS EGRESOS
	--------------------------------------------------------------------------------------------*/
	for ($i = 1; $i <= 12; $i++) {
		$serie1_bar[$i] = "0"; //Crear un array ingreso
		$serie2_bar[$i] = "0"; //egreso
	}

	$total_ingreso = 0;
	$total_egreso = 0;

	$ls_sql = "SELECT SUM(ingreso), SUM(egreso), EXTRACT(month FROM fecha) AS MES 
			FROM vm02_edo_cuenta 
			WHERE EXTRACT(YEAR FROM fecha) = $CURRENT_YEAR
			GROUP BY EXTRACT(month FROM fecha)  ORDER BY EXTRACT(month FROM fecha)  asc";

	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);

	if ($ls_resultado != 0) {

		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)) {
			//$timestamp = strtotime($row[2])*1000; // Convierte la Fecha en Formato UNIX	en milisegundos
			$serie1_bar[$row[2]] = "$row[0]"; //Crear un array ingreso
			$serie2_bar[$row[2]] = "$row[1]"; //egreso
			$total_ingreso = $total_ingreso + $row[0];
			$total_egreso = $total_egreso + $row[1];
		}
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}

	/*-------------------------------------------------------------------------------------------
	RUTINAS: DETALLE DE CUENTAS POR COBRAR VENTAS
	---------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT  v01_pago.fk_responsable, UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		sum(detalle) - sum(abono) as Debe 
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_responsable
		WHERE v01_pago.tx_tipo='VENTA' and (detalle - abono) > 0 
		GROUP BY v01_pago.fk_responsable,s01_persona.tx_nombre, s01_persona.tx_apellido 
		ORDER BY Debe DESC	";

	//echo $ls_sql;
	$ls_resultado_1 =  $obj_miconexion_1->fun_consult($ls_sql);


	if ($ls_resultado_1 != 0) {
		$tarea = "N";
	} else {
		fun_error(1, $li_id_conex_1, $ls_sql, $_SERVER['PHP_SELF']);
	}

	/*-------------------------------------------------------------------------------------------
	RUTINAS: DETALLE DE CUENTAS POR COBRAR PRESTAMOS
    ---------------------------------------------------------------------------------------------*/

	$ls_sql = "SELECT UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		sum(detalle) - sum(abono) as Debe 
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_cliente 		
		WHERE (v01_pago.tx_tipo='CTAXCOBRAR') and (detalle - abono) > 0 
		GROUP BY s01_persona.tx_nombre, s01_persona.tx_apellido 	
		ORDER BY Debe DESC ";

	//echo $ls_sql;
	$ls_resultado_2 =  $obj_miconexion_2->fun_consult($ls_sql);


	if ($ls_resultado_2 != 0) {
		$tarea = "N";
	} else {
		fun_error(1, $li_id_conex_2, $ls_sql, $_SERVER['PHP_SELF']);
	}

	/*-------------------------------------------------------------------------------------------
	RUTINAS: DETALLE DE CUENTAS POR PAGAR
	--------------------------------------------------------------------------------------------*/
	$i = 0;

	$ls_sql = "SELECT UPPER(s01_persona.tx_nombre ||' '|| s01_persona.tx_apellido),
		sum(detalle) - sum(abono) as Debe 
		FROM v01_pago
			INNER JOIN s01_persona ON s01_persona.co_persona = v01_pago.fk_cliente 		
		WHERE (v01_pago.tx_tipo='CTAXPAGAR' or v01_pago.tx_tipo='GASTO') and (detalle - abono) > 0 
		GROUP BY s01_persona.tx_nombre, s01_persona.tx_apellido 	
		ORDER BY Debe DESC ";

	//echo $ls_sql;
	$ls_resultado_3 =  $obj_miconexion_3->fun_consult($ls_sql);

	if ($ls_resultado_3 != 0) {
		$tarea = "M";
	} else {
		fun_error(1, $li_id_conex_3, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}


	/*-----------------------------------------------------------------------------------------
	 GRAFICA 4 - GASTOS POR CATEGORIA
	------------------------------------------------------------------------------------------*/
	$total_gasto_periodo = 0;
	$ls_sql = "SELECT nb_categoria, SUM(nu_cantidad * nu_precio) AS Precio_Total
		  FROM t01_detalle
			INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
			LEFT JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
			LEFT JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
			LEFT JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria
		WHERE EXTRACT(YEAR FROM t20_factura.fe_fecha_factura) = $CURRENT_YEAR AND t20_factura.tx_tipo = 'GASTO' OR t20_factura.tx_tipo = 'NOMINA'
		GROUP BY nb_categoria";
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)) {
			$data4[] = "{ name: '$row[0]',y: $row[1]}";
			$total_gasto_periodo = $total_gasto_periodo + $row[1];
		}
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}

	/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 TABLA - GASTOS POR CATEGORIA
	---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT nb_categoria, SUM(nu_cantidad * nu_precio) AS Precio_Total
	FROM t01_detalle
	  INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
	  LEFT JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
	  LEFT JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
	  LEFT JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria
	WHERE EXTRACT(YEAR FROM t20_factura.fe_fecha_factura) = $CURRENT_YEAR AND  t20_factura.tx_tipo = 'GASTO' OR t20_factura.tx_tipo = 'NOMINA'
	GROUP BY nb_categoria ORDER BY Precio_Total DESC";

	$ls_resultado_4 =  $obj_miconexion_4->fun_consult($ls_sql);
	if ($ls_resultado_4 != 0) {
	} else {
		fun_error(1, $li_id_conex_4, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}

	/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 RESUMEN DE PROYECTOS DE INVERSION
---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT tx_nombre,  
			sum(venta) as venta, 
			sum(gasto) as gasto, 
			(sum(venta) - $VentaXcobrar)  as ingreso, 
			(sum(gasto) -  $DebeCtxPag) as egreso, 		
			in_proy_activo, 
			pk_proyecto
			FROM v06_mov_resumen 
			inner join t02_proyecto ON t02_proyecto.pk_proyecto = v06_mov_resumen.fk_proyecto 
			left join t08_tipo_proyecto ON t08_tipo_proyecto.pk_tipo_rubro = t02_proyecto.fk_tipo_rubro 
			WHERE tx_categoria_proyecto = 'INVERSION'
			GROUP BY tx_nombre, in_proy_activo, pk_proyecto 			
		";

	$data_5 = array();
	$ls_resultado_5 =  $obj_miconexion_5->fun_consult($ls_sql);
	if ($ls_resultado_5 != 0) {
		while ($row = pg_fetch_row($ls_resultado_5)) {
			$data_5[] = $row;
		}
	} else {
		fun_error(1, $li_id_conex_5, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}


	/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 TABLA 4 - VENTAS POR CATEGORIA
	---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	$total_gasto_periodo = 0;
	$ls_sql = "SELECT nb_categoria, nb_clase, SUM(nu_cantidad * nu_precio) AS Precio_Total FROM t01_detalle 
	INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura 
	LEFT JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo 
	LEFT JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase 
	LEFT JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria 
	WHERE EXTRACT(YEAR FROM t20_factura.fe_fecha_factura) = $CURRENT_YEAR AND t20_factura.tx_tipo = 'VENTA'  
	GROUP BY nb_categoria, nb_clase ORDER BY Precio_Total DESC";

	$ls_resultado_6 =  $obj_miconexion_6->fun_consult($ls_sql);

	if ($ls_resultado_6 != 0) {
	} else {
		fun_error(1, $li_id_conex_6, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}


	$total_venta_periodo = 0;
	$ls_sql = "SELECT nb_categoria, SUM(nu_cantidad * nu_precio) AS Precio_Total
		  FROM t01_detalle
			INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
			LEFT JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
			LEFT JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
			LEFT JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria
		WHERE EXTRACT(YEAR FROM t20_factura.fe_fecha_factura) = $CURRENT_YEAR AND t20_factura.tx_tipo = 'VENTA' 
		GROUP BY nb_categoria";
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0) {
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)) {
			//$data4[] ="{ name: '$row[0]',y: $row[1]}";
			$total_venta_periodo = $total_venta_periodo + $row[1];
		}
	} else {
		fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF'], __LINE__);
	}


	$activos_fijo = $Caja + $Banco;
	$pasivos = $DebeCtxPag;
	$total_activos = $activos_fijo + $VentaXcobrar + $CtxCob;
	$balance = ($activos_fijo + $VentaXcobrar + $CtxCob) - $pasivos;


	if (!(isset($data3))) {
		$data3[] = 0;
	}

	if (!(isset($data4))) {
		$data4[] = 0;
	}


	$collaps = ($sw == 0) ? '' : 'collapsed';
	//print_r ($data4); 	
	?>

	<div class="main-content">
		<div class="main-content-inner">
			<div class="page-content">

				<div class="page-header">
					<h1>
						Tablero de Comandos
					</h1>
				</div><!-- /.page-header -->

				<div class="row">
					<div class="col-xs-12">

						<div class="row">
							<div class="col-sm-8 col-md-6 infobox-container">

								<div class="infobox infobox-blue">
									<div class="infobox-icon">
										<i class="ace-icon fa fa-dollar"></i>
									</div>

									<div class="infobox-data">
										<span class="infobox-data-number"><?php echo number_format($Disponible, 2, ",", ".");  ?></span>
										<div class="infobox-content">Disponible</div>
									</div>

								</div>

								<div class="infobox infobox-red">
									<div class="infobox-icon">
										<i class="ace-icon fa fa-shopping-cart "></i>
									</div>

									<div class="infobox-data">
										<span class="infobox-data-number"><?php echo number_format($pasivos, 2, ",", "."); ?></span>
										<div class="infobox-content">Cuentas x Pagar</div>
									</div>
								</div>

								<div class="infobox infobox-green">
									<div class="infobox-icon">
										<i class="ace-icon fa fa-dollar"></i>
									</div>

									<div class="infobox-data">
										<span class="infobox-data-number"><?php echo number_format($VentaXcobrar, 2, ",", "."); ?></span>
										<div class="infobox-content">Ventas x Cobrar</div>
									</div>

								</div>

								<div class="infobox infobox-orange">
									<div class="infobox-icon">
										<i class="ace-icon fa fa-bolt "></i>
									</div>

									<div class="infobox-data">
										<span class="infobox-data-number"><?php echo number_format($CtxCob, 2, ",", "."); ?></span>
										<div class="infobox-content">Dinero Prestado</div>
									</div>
								</div>

								<div class="space-6"></div>

								<div class="infobox infobox-green infobox-small infobox-dark">
									<div class="infobox-icon">
										<i class="ace-icon fa fa-download"></i>
									</div>

									<div class="infobox-data">
										<div class="infobox-content">Activos</div>
										<div class="infobox-content"><?php echo number_format($total_activos, 2, ",", "."); ?> </div>
									</div>
								</div>

								<div class="infobox infobox-blue infobox-small infobox-dark">
									<div class="infobox-icon">
										<i class="ace-icon fa fa-download"></i>
									</div>

									<div class="infobox-data">
										<div class="infobox-content">Pasivos</div>
										<div class="infobox-content"><?php echo number_format($pasivos, 2, ",", "."); ?> </div>
									</div>
								</div>

								<div class="infobox infobox-grey infobox-small infobox-dark">
									<div class="infobox-icon">
										<i class="ace-icon fa fa-download"></i>
									</div>

									<div class="infobox-data">
										<div class="infobox-content">Capital</div>
										<div class="infobox-content"><?php echo number_format($balance, 2, ",", "."); ?> </div>
									</div>
								</div>

							</div>
						</div>

						<div class="space-8"></div>

						<div class="row">
							<div class="widget-box transparent">
								<div class="widget-header widget-header-flat">
									<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Detalle de Finanzas
									</h4>

									<div class="widget-toolbar">
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-up"></i>
										</a>
									</div>
								</div>

								<div class="widget-body">
									<div class="widget-main padding">

										<div class="col-sm-4">
											<div class="table-header">
												Ventas x Cobrar
											</div>
											<table id="example" class="table table-bordered table-striped">
												<thead class="thin-border-bottom">
													<tr>
														<th width="40px"></th>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Vendedor</th>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Deuda</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$li_numcampo = 0; // Columnas que se muestran en la Tabla
													$li_indicecampo = $obj_miconexion_1->fun_numcampos() - 1; // Referencia al indice de la columna clave
													fun_dibujar_tabla($obj_miconexion_1, $li_numcampo, $li_indicecampo, 'LISTAR_DEUDAS_VENDEDOR'); // Dibuja la Tabla de Datos
													$obj_miconexion_1->fun_closepg($li_id_conex_1, $ls_resultado_1);
													?>
												</tbody>
												<tfoot>
													<tr>
														<td colspan="2" align="right"><strong>Total </strong></td>
														<td>
															<span class="label  label-info ">
																<?php echo number_format($VentaXcobrar, 2, ",", ".") . ' Bs'; ?>
															</span>
														</td>
													</tr>
												</tfoot>
											</table>
										</div>

										<div class="col-sm-4">
											<div class="table-header">
												Cuentas x Pagar
											</div>
											<table id="tabla_dinamica_2" class="table table-bordered table-striped">
												<thead class="thin-border-bottom">
													<tr>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Acreedor</th>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Credito</th>
													</tr>
												</thead>

												<tbody>
													<?php
													if ($tarea == "M") {
														$li_numcampo = 0; // Columnas que se muestran en la Tabla
														$li_indicecampo = $obj_miconexion_3->fun_numcampos(); // Referencia al indice de la columna clave
														fun_dibujar_tabla($obj_miconexion_3, $li_numcampo, $li_indicecampo, 'LISTAR_CREDITOS'); // Dibuja la Tabla de Datos
														$obj_miconexion_3->fun_closepg($li_id_conex_3, $ls_resultado_3);
													}
													?>
												</tbody>
												<tfoot>
													<tr>
														<th>Total</th>
														<th>
															<span class="label label-warning ">
																<?php echo number_format($pasivos, 2, ",", ".") . ' Bs'; ?>
															</span>
														</th>

													</tr>
												</tfoot>
											</table>
										</div><!-- /.col -->

										<div class="col-sm-4">
											<div class="table-header">
												Dinero Prestado
											</div>
											<table id="tabla_dinamica_1" class="table table-bordered table-striped">
												<thead class="thin-border-bottom">
													<tr>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Deudor</th>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Monto</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$li_numcampo = 0; // Columnas que se muestran en la Tabla
													$li_indicecampo = $obj_miconexion_2->fun_numcampos(); // Referencia al indice de la columna clave
													fun_dibujar_tabla($obj_miconexion_2, $li_numcampo, $li_indicecampo, 'LISTAR_PRESTADO'); // Dibuja la Tabla de Datos
													$obj_miconexion_2->fun_closepg($li_id_conex_2, $ls_resultado_2);
													?>
												</tbody>
												<tfoot>
													<tr>
														<th>Total</th>
														<th>
															<span class="label  label-info ">
																<?php echo number_format($CtxCob, 2, ",", ".") . ' Bs'; ?>
															</span>
														</th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div><!-- /.widget-main -->
								</div><!-- /.widget-body -->
							</div><!-- /.widget-box -->
						</div><!-- /.row -->

						<div class="space-6"></div>


						<div class="row">
							<div class="widget-box transparent ">
								<div class="widget-header widget-header-flat">
									<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Finanzas (Real)
									</h4>

									<div class="widget-toolbar">
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-up"></i>
										</a>
									</div>
								</div>
								<div class="widget-body no-padding">
									<div class="widget-main ">
										<div class="col-xs-12">
											<div class="table-header">
												Lista de Proyectos
											</div>
											<table id="dynamic-table" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="">Proyecto</th>
														<th class="">Ingresos</th>
														<th class="">Egresos</th>
														<th class="hidden-480">Ganancia</th>
														<th class="">Utilidad(%)</th>
													</tr>
												</thead>
												<tbody>
													<?php

													foreach ($data_5 as $row) {
														// Accediendo al valor de la columna "nombre" en cada fila
														$proyecto   = $row[0];
														$ventaNeta    = floatval($row[3]);
														$gastoNeto    = floatval($row[4]);
														$gananciaNeta    = $ventaNeta  - $gastoNeto;
														$porc_gan = ($gastoNeto == 0) ? 0 : (($ventaNeta - $gastoNeto) * 100) / $gastoNeto;


														echo "<td class='blue'>" . $proyecto . "</td>";
														echo "<td>" . number_format($ventaNeta, 2, ",", ".") . "</td>";  // venta
														echo "<td>" . number_format($gastoNeto, 2, ",", ".") . "</td>";  // gasto
														echo "<td>" . number_format(floatval($gananciaNeta), 2, ",", ".") . "</td>";  // Ganancia	
														echo "<td class=''>" . number_format($porc_gan, 2, ",", ".") . "%</td>";
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>

							</div>
						</div>


						<div class="space-6"></div>

						<div class="row">
							<div class="widget-box transparent ">
								<div class="widget-header widget-header-flat">
									<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Finanzas (Estimada)
									</h4>

									<div class="widget-toolbar">
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-up"></i>
										</a>
									</div>
								</div>
								<div class="widget-body no-padding">
									<div class="widget-main ">
										<div class="col-xs-12">
											<div class="table-header">
												Lista de Proyectos
											</div>
											<table id="dynamic-table" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="">Proyecto</th>
														<th class="">Venta Neta</th>
														<th class="">Gastos Neto</th>
														<th class="hidden-480">Ganancia Neta</th>
														<th class="">Utilidad(%) Neta</th>
													</tr>
												</thead>
												<tbody>
													<?php

													foreach ($data_5 as $row) {
														// Accediendo al valor de la columna "nombre" en cada fila
														$proyecto   = $row[0];
														$ventaNeta    = floatval($row[1]);
														$gastoNeto    = floatval($row[2]);
														$gananciaNeta    = $ventaNeta  - $gastoNeto;
														$porc_gan = ($gastoNeto == 0) ? 0 : (($ventaNeta - $gastoNeto) * 100) / $gastoNeto;


														echo "<td class='blue'>" . $proyecto . "</td>";
														echo "<td>" . number_format($ventaNeta, 2, ",", ".") . "</td>";  // venta
														echo "<td>" . number_format($gastoNeto, 2, ",", ".") . "</td>";  // gasto
														echo "<td>" . number_format(floatval($gananciaNeta), 2, ",", ".") . "</td>";  // Ganancia	
														echo "<td class=''>" . number_format($porc_gan, 2, ",", ".") . "%</td>";
													}
													?>
												</tbody>
											</table>
										</div><!-- /.widget-main -->
									</div><!-- /.widget-body -->
								</div><!-- /.widget-box -->

							</div>
						</div>


						<div class="space-6"></div>

						<!-- CONTAINER FINANZAS -->
						<div class="row">
							<div class="widget-box transparent ">
								<div class="widget-header widget-header-flat">
									<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Ventas por Ingresos Mensual
									</h4>

									<div class="widget-toolbar">
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-up"></i>
										</a>
									</div>
								</div>
								<div class="widget-body no-padding">
									<div class="widget-main ">
										<div class="col-sm-12">
											<div id="container_1" style="height: 400px; min-width: 310px"></div>
										</div>
									</div><!-- /.widget-main -->
								</div><!-- /.widget-body -->
							</div><!-- /.widget-box -->
						</div><!-- /.ROW -->

						<div class="space-6"></div>

						<!-- GASTOS DETALLE -->
						<div class="row">
							<div class="widget-box transparent ">
								<div class="widget-header widget-header-flat">
									<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Egresos
									</h4>

									<div class="widget-toolbar">
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-up"></i>
										</a>
									</div>
								</div>

								<div class="widget-body">
									<div class="widget-main ">
										<div class="col-sm-6">
											<table class="table table-bordered table-striped">
												<thead class="thin-border-bottom">
													<tr>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Descripcion</th>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Monto</th>
													</tr>
												</thead>

												<tbody>
													<?php

													$li_numcampo = 0; // Columnas que se muestran en la Tabla
													$li_indicecampo = $obj_miconexion_4->fun_numcampos(); // Referencia al indice de la columna clave
													fun_dibujar_tabla($obj_miconexion_4, $li_numcampo, $li_indicecampo, 'LISTAR_GASTO_PERIODO'); // Dibuja la Tabla de Datos
													$obj_miconexion_4->fun_closepg($li_id_conex_4, $ls_resultado_4);

													?>
												</tbody>
												<tfoot>
													<tr>
														<th>Total</th>
														<th>
															<span class="label label-warning ">
																<?php echo number_format($total_gasto_periodo, 2, ",", ".") . ' Bs'; ?>
															</span>
														</th>

													</tr>
												</tfoot>
											</table>
										</div>

										<div class="col-sm-6">
											<div id="container_2" style="height: 400px; min-width: 310px"></div>
										</div>
									</div><!-- /.widget-main -->
								</div><!-- /.widget-body -->
							</div><!-- /.widget-box -->

						</div><!-- /.ROW -->

						<div class="space-6"></div>

						<!-- VENTAS  DETALLE -->
						<div class="row">
							<div class="widget-box transparent ">
								<div class="widget-header widget-header-flat">
									<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Ventas
									</h4>

									<div class="widget-toolbar">
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-up"></i>
										</a>
									</div>
								</div>

								<div class="widget-body">
									<div class="widget-main ">
										<div class="col-sm-6">
											<table class="table table-bordered table-striped">
												<thead class="thin-border-bottom">
													<tr>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Categoria</th>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Clase</th>
														<th><i class="ace-icon fa fa-caret-right blue"></i>Monto</th>
													</tr>
												</thead>

												<tbody>
													<?php

													$li_numcampo = 0; // Columnas que se muestran en la Tabla
													$li_indicecampo = $obj_miconexion_6->fun_numcampos(); // Referencia al indice de la columna clave
													fun_dibujar_tabla($obj_miconexion_6, $li_numcampo, $li_indicecampo, 'LISTAR_VENTAS_PERIODO'); // Dibuja la Tabla de Datos
													$obj_miconexion_4->fun_closepg($li_id_conex_6, $ls_resultado_6);

													?>
												</tbody>
												<tfoot>
													<tr>
														<th colspan="2">Total</th>
														<th>
															<span class="label label-warning ">
																<?php echo number_format($total_venta_periodo, 2, ",", ".") . ' Bs'; ?>
															</span>
														</th>

													</tr>
												</tfoot>
											</table>
										</div>

										<div class="col-sm-6">
											<div id="container_3" style="height: 400px; min-width: 310px"></div>
										</div>
									</div><!-- /.widget-main -->
								</div><!-- /.widget-body -->
							</div><!-- /.widget-box -->

						</div><!-- /.ROW -->


					</div> <!-- ROW CONTENT END -->
				</div> <!-- /.main-content-inner -->

			</div> <!-- page-content-end -->
		</div> <!-- main-content-inner-end -->
	</div> <!-- main-content-end -->

	<script src="../../js/funciones.js"></script>
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>



	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		$(document).ready(function() {

			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical	

			var color_bar1 = '#A9D0F5';
			var color_bar2 = '#FA5858';
			Highcharts.setOptions({
				lang: {
					months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
					weekdays: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado']
				}
			});

			// DATATABLE  ASOCIADA A VENTAS DEL VENDEDOR
			var table = $('#example').DataTable({
				"paging": false,
				"ordering": false,
				"info": false,
				"searching": false,
				"columns": [{
						//"className":      'details-control',
						"orderable": false,
						"data": null,
						"defaultContent": ''
					},

					{
						"orderable": false
					},
					{
						"orderable": false
					}

				],
				"order": [
					[1, 'asc']
				]
			});

			// Add event listener for opening and closing details
			$('#example tbody').on('click', 'td.details-control', function() {
				var tr = $(this).closest('tr');
				var row = table.row(tr);

				var id_vendedor = $(this).attr("id");

				$.post("ajax_deudas_ventas.php", {
					id_vendedor: id_vendedor
				}, function(data) {
					if (row.child.isShown()) {
						row.child.hide();
						tr.removeClass('shown');
					} else {
						row.child(data).show();
						tr.addClass('shown');
					}
				});

			});

			// DATATABLE  ASOCIADA A DEUDAS POR COBRAR			
			$('#tabla_dinamica_1').DataTable({
				"paging": false,
				"ordering": false,
				"info": false,
				"searching": false,
				"columns": [{
						"orderable": false
					},
					{
						"orderable": false
					}

				],
				"order": [
					[1, 'asc']
				]
			});

			// DATATABLE  ASOCIADA A CREDITO
			$('#tabla_dinamica_2').DataTable({
				"paging": false,
				"ordering": false,
				"info": false,
				"searching": false,
				"columns": [{
						"orderable": false
					},
					{
						"orderable": false
					}

				],
				"order": [
					[1, 'asc']
				]
			});


			// GRAFICA DE INGRESOS POR EGRESOS POR MES
			Highcharts.chart('container_1', {

				chart: {
					type: 'column',
					options3d: {
						enabled: true,
						alpha: 15,
						beta: 15,
						viewDistance: 25,
						depth: 60
					}
				},

				title: {
					text: 'Ingreso vs Egresos por Mes'
				},

				subtitle: {
					text: ''
				},

				xAxis: {
					categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					crosshair: true
				},

				yAxis: {
					min: 0,
					title: {
						text: 'Monto (Bs)'
					}
				},

				tooltip: {
					headerFormat: '<b>{point.key}</b><br>',
					pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y}  {point.stackTotal}'
				},

				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [{
						color: color_bar1,
						name: 'Ingreso',
						data: [<?php echo join(',', $serie1_bar) ?>] /* Convierte un array en una cadena*/
						//data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

					},
					{
						color: color_bar2,
						name: 'Egreso',
						data: [<?php echo join(',', $serie2_bar) ?>] /* Convierte un array en una cadena*/
						//data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

					}

				]

			});


			/*********************** Gastos por Categoria *******************************/

			Highcharts.chart('container_2', {
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				title: {
					text: 'Egresos por Categoria'
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false
						},
						showInLegend: true
					}
				},
				series: [{
					name: 'Brands',
					colorByPoint: true,
					data: [<?php echo join(',', $data4); ?>]
				}]
			});


			/*********************** Gastos por Clasificacion *******************************/


		});
	</script>

	<script type="text/javascript">
		function Buscar() {
			document.formulario.tarea.value = "B";
			document.formulario.action = "pos_estado_cta.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}

		function Limpiar(parametros) {
			location.href = 'pos_estado_cta.php'
		}
	</script>
</body>

</html>