<?php
	session_start();
	include_once ("utilidad.php");
	$usu_autentico= isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI") {
		session_destroy();
    	echo"<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
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
	
						
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$Disponible  = 0;
	$Banco       = 0;
	$sw = 1;
	
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
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_tipo =     Combo_TipoMovimiento();
	
	// Carga variables con los ultimos 30 DIAS POR DEFECTO
	$x_fecha_actual        =  date('d/m/Y');
	$x_fecha_mespasado 	   =  date('d/m/Y', strtotime('-29 day')) ;   

	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE BANCO
----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(nu_capital) FROM t15_banco";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Banco    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	  
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE CUENTAS POR COBRAR
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(f_calcular_abono(fk_factura)) as Abono, 
				sum (t01_detalle.nu_cantidad * nu_precio) as SumaTotal
				FROM t01_detalle 
				INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
		WHERE t20_factura.tx_tipo='CTAXCOBRAR' ";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$AbonoCtxCob    = $row[0];
		$TotalCtxCob    = $row[1];
		$DebeCtxCob     = $row[1] - $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE CUENTAS POR PAGAR
-----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(f_calcular_abono(fk_factura)) as Abono, 
				sum (t01_detalle.nu_cantidad * nu_precio) as SumaTotal
				FROM t01_detalle 
				INNER JOIN t20_factura ON t01_detalle.fk_factura = t20_factura.pk_factura
		WHERE t20_factura.tx_tipo='CTAXPAGAR' ";
     //echo $ls_sql;	
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$AbonoCtxPag    = $row[0];
		$TotalCtxPag    = $row[1];
		$DebeCtxPag     = $row[1] - $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE MONTO DISPONIBLE
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT f_cuenta	FROM vm02_edo_cuenta
			WHERE ref = (SELECT Max(ref) from vm02_edo_cuenta)";
	//echo $ls_sql;		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado)>0){
			$row = pg_fetch_row($ls_resultado,0);
			$Disponible    = $row[0];
		}	
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
	$Caja = $Disponible - $Banco;	
	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA LOS INGRESOS Y EGRESOS 
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT SUM(ingreso) as Ingreso, SUM(egreso) as Egreso
				FROM vm02_edo_cuenta	";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Ingreso   = $row[0];
		$Egreso    = $row[1];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}		


/*-------------------------------------------------------------------------------------------
	 GRAFICA 2 - INGRESOS VS EGRESOS
--------------------------------------------------------------------------------------------*/	
	for ($i = 1; $i <= 12; $i++) {
		$serie1_bar[$i] = "0"; //Crear un array ingreso
		$serie2_bar[$i] = "0"; //egreso
	}

	$total_ingreso=0;
	$total_egreso =0;
	
	$ls_sql = "SELECT SUM(ingreso), SUM(egreso), EXTRACT(month FROM fecha) AS MES 
			FROM vm02_edo_cuenta 
			WHERE EXTRACT(YEAR FROM fecha) = '2016'
			GROUP BY EXTRACT(month FROM fecha)  ORDER BY EXTRACT(month FROM fecha)  asc";
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			//$timestamp = strtotime($row[2])*1000; // Convierte la Fecha en Formato UNIX	en milisegundos
			$serie1_bar[$row[2]] = "$row[0]"; //Crear un array ingreso
			$serie2_bar[$row[2]] = "$row[1]"; //egreso
			$total_ingreso = $total_ingreso + $row[0];
			$total_egreso = $total_egreso + $row[1];
		}
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	

/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 GRAFICA 1 - INGRESOS VS EGRESOS
---------------------------------------------------------------------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT SUM(ingreso), SUM(egreso), to_char(fecha, 'mm/dd/yyyy')
			FROM vm02_edo_cuenta GROUP BY fecha ORDER BY fecha asc";	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);			
	if($ls_resultado != 0){		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$timestamp = strtotime($row[2])*1000; // Convierte la Fecha en Formato UNIX	
			//$timestamp = $timestamp * 1000;  // Convierte el tiempo en milisegundos
			$serie1[] = "[$timestamp, $row[0]]"; //Crear un array ingreso
			$serie2[] = "[$timestamp, $row[1]]"; //egreso
		}		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
		
/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 GRAFICA 3 - GASTOS POR CLASIFICACION
---------------------------------------------------------------------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT nb_clase, SUM(nu_cantidad * nu_precio) AS Precio_Total
		  FROM t01_detalle
			INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
			LEFT JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
			LEFT JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
			LEFT JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria
		WHERE t20_factura.tx_tipo = 'GASTO' OR t20_factura.tx_tipo = 'NOMINA'
		GROUP BY nb_clase";	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);			
	if($ls_resultado != 0){		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$data3[] ="{ name: '$row[0]',y: $row[1]}";
		}		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}	
	
/*-------------------------------------------------------------------------------------------------------------------------------------------------------
	 GRAFICA 4 - GASTOS POR CATEGORIA
---------------------------------------------------------------------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT nb_categoria, SUM(nu_cantidad * nu_precio) AS Precio_Total
		  FROM t01_detalle
			INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
			LEFT JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
			LEFT JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
			LEFT JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria
		WHERE t20_factura.tx_tipo = 'GASTO' OR t20_factura.tx_tipo = 'NOMINA'
		GROUP BY nb_categoria";	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);			
	if($ls_resultado != 0){		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$data4[] ="{ name: '$row[0]',y: $row[1]}";
		}		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}		

	//echo join($data4, ',');
	//print_r ($serie1_bar); // Imprime un arreglo por pantalla		
	$collaps = ($sw==0)?'':'collapsed';	
	
?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
					
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
				
				<!-- CONTAINER PRIMERA GRAFICA FINANZAS -->
				<div class="row">
					<div class="col-xs-12 col-sm-12 widget-container-col">
						<div class="widget-box ">
							<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
									<i class="ace-icon fa fa-signal"></i>
									Finanzas
								</h4>

								<div class="widget-toolbar">
									<a href="#" data-action="collapse">
										<i class="ace-icon fa fa-chevron-up"></i>
									</a>
								</div>
							</div>

							<div class="widget-body">
								<div class="widget-main ">
									<div id="container1" style="height: 400px; min-width: 310px"></div>
								</div><!-- /.widget-main -->
							</div><!-- /.widget-body -->
						</div><!-- /.widget-box -->
					</div><!-- /.col -->
				</div><!-- /.ROW -->
				
				<!-- CONTAINER PRIMERA GRAFICA FINANZAS -->
				<div class="row">
					<div class="col-sm-12">
						<div class="widget-box ">
							<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
									<i class="ace-icon fa fa-signal"></i>
									Finanzas por Mes
								</h4>

								<div class="widget-toolbar">
									<a href="#" data-action="collapse">
										<i class="ace-icon fa fa-chevron-up"></i>
									</a>
								</div>
							</div>

							<div class="widget-body">
								<div class="widget-main padding-4">
									<div id="container2" style="height: 400px; min-width: 310px"></div>
								</div><!-- /.widget-main -->
							</div><!-- /.widget-body -->
						</div><!-- /.widget-box -->
					</div><!-- /.col -->
				</div><!-- /.ROW -->
				
				<!-- CONTAINER PRIMERA GRAFICA GASTOS -->
				<div class="row">
					<div class="col-sm-12">
						<div class="widget-box ">
							<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
									<i class="ace-icon fa fa-signal"></i>
									Gastos por Categoria
								</h4>

								<div class="widget-toolbar">
									<a href="#" data-action="collapse">
										<i class="ace-icon fa fa-chevron-up"></i>
									</a>
								</div>
							</div>

							<div class="widget-body">
								<div class="widget-main padding-4">
									<div id="container4" style="height: 400px; min-width: 310px"></div>
								</div><!-- /.widget-main -->
							</div><!-- /.widget-body -->
						</div><!-- /.widget-box -->
					</div><!-- /.col -->
				</div><!-- /.ROW -->
				
				<!-- CONTAINER PRIMERA GRAFICA GASTOS -->
				<div class="row">
					<div class="col-sm-12">
						<div class="widget-box ">
							<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
									<i class="ace-icon fa fa-signal"></i>
									Gastos por Clasificacion
								</h4>

								<div class="widget-toolbar">
									<a href="#" data-action="collapse">
										<i class="ace-icon fa fa-chevron-up"></i>
									</a>
								</div>
							</div>

							<div class="widget-body">
								<div class="widget-main padding-4">
									<div id="container3" style="height: 400px; min-width: 310px"></div>
								</div><!-- /.widget-main -->
							</div><!-- /.widget-body -->
						</div><!-- /.widget-box -->
					</div><!-- /.col -->
				</div><!-- /.ROW -->
				
				
												
					
				</div> <!-- ROW CONTENT END -->
			</div>  <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- main-content Principal -->

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical				
				var color_bar1=	'#A9D0F5';		
				var color_bar2=	'#FA5858';				
				Highcharts.setOptions({
					lang: {
						months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
						weekdays: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado']
					}
				});
				
				/*FINANZAS FILTRO POR FECHA          ********************************************/	
				
				Highcharts.stockChart('container1', {
					title: {
							text: 'Ingresos vs Gastos'
						},					
					xAxis: {
						minPadding: 0.05,
						maxPadding: 0.05
					},					
					yAxis: {
						 title: {
							text: 'Bs'
						}
					},					
					rangeSelector: {
						selected: 4
					},					
					plotOptions: {
						series: {
							showInNavigator: true
						}
					},
					tooltip: {
						pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Bs</b><br/>',
						valueDecimals: 2
					},					
					 series: [
						{	
							color: '#A9D0F5',
							name: 'Ingreso',
							data: [<?php echo join($serie1, ',') ?>] /* Convierte un array en una cadena*/
						},
						 {	
							color: '#FA5858',
							name: 'Egreso',
							data: [<?php echo join($serie2, ',') ?>] /* Convierte un array en una cadena*/
							
						}
					]				
					
				});
				
				/*****************************************************************************/
				Highcharts.chart('container2', {
					
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
						categories: [
							'Jan',
							'Feb',
							'Mar',
							'Apr',
							'May',
							'Jun',
							'Jul',
							'Aug',
							'Sep',
							'Oct',
							'Nov',
							'Dec'
						],
						crosshair: true
					},
					 /*labels: {
						items: [{
							html: 'Total Finanzas Anuales',
							style: {
								left: '50px',
								top: '18px',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							}
						}]
					},*/
					
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
					
					/*tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y:.2f} Bs</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},*/
								
					
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
					series: [
						{	
							color: color_bar1,
							name: 'Ingreso',
							data: [<?php echo join($serie1_bar, ',') ?>] /* Convierte un array en una cadena*/
							/*data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]*/

						}, 
						{
							color: color_bar2,	
							name: 'Egreso',
							data: [<?php echo join($serie2_bar, ',') ?>] /* Convierte un array en una cadena*/

						},
						/*{
							type: 'pie',
							name: 'Monto:',
							data: [{
								name: 'Ingreso',
								y: <?php echo $total_ingreso ?>,
								color: color_bar1 // Jane's color
							}, {
								name: 'Egreso',
								y: <?php echo $total_egreso ?>,
								color: color_bar2 // Joe's color
							}],
							center: [100, 80],
							size: 100,
							showInLegend: false,
							dataLabels: {
								enabled: false
							}
						}*/
					
					
					
					
					]
				});
				
				/*********************** Gastos por Clasificacion *******************************/
				
				Highcharts.chart('container3', {
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},
					title: {
						text: 'Gastos por Clasificacion'
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
								style: {
									color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
								}
							}
						}
					},
					series: [{
						name: 'Brands',
						colorByPoint: true,
						data: [<?php echo join($data3, ','); ?>]
					}]
				});
				
				/*********************** Gastos por Categoria *******************************/
				
				Highcharts.chart('container4', {
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},
					title: {
						text: 'Gastos por Categoria'
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
								style: {
									color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
								}
							}
						}
					},
					series: [{
						name: 'Brands',
						colorByPoint: true,
						data: [<?php echo join($data4, ','); ?>]
					}]
				});
			
		} );
					
	</script>	
  
	<script type="text/javascript"> 
			
		function Buscar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "pos_estado_cta.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
				
		function Limpiar(parametros){	
			location.href='pos_estado_cta.php'
		}
		
	</script>
</body>
</html>