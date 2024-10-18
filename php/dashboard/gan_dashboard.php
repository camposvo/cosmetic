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
	$sw = 0;
	
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
	CONSULTA LISTA DE ANIMALES
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT count(id_numero), in_sexo
			FROM gan_ganado
			GROUP BY in_sexo ";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$serie1[] = "['$row[1]', $row[0]]"; 
			
		}
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}

	
	/*-------------------------------------------------------------------------------------------
	CONSULTA LISTA DE ANIMALES
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT count(id_numero), f_grupo_etareo(fe_nacimiento,in_sexo)
			FROM gan_ganado
			GROUP BY f_grupo_etareo(fe_nacimiento,in_sexo) ";
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		
		while ($row = pg_fetch_row($obj_miconexion->li_idconsult)){
			$serie2[] = "['$row[1]', $row[0]]"; 
			
		}
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
	
		
	//print_r ($serie1); // Imprime un arreglo por pantalla		
	$collaps = ($sw==1)?'':'collapsed';	
	
?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
					
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
				
				<div class="col-sm-6">
					<div class="widget-box ">
						<div class="widget-header widget-header-flat">
							<h4 class="widget-title lighter">
								<i class="ace-icon fa fa-signal"></i>
								Examinar Sexo
							</h4>

							<div class="widget-toolbar">
								<a href="#" data-action="collapse">
									<i class="ace-icon fa fa-chevron-up"></i>
								</a>
							</div>
						</div>

						<div class="widget-body">
							<div class="widget-main padding-4">
								<div id="container" style="height: 400px; min-width: 310px"></div>
							</div><!-- /.widget-main -->
						</div><!-- /.widget-body -->
					</div><!-- /.widget-box -->
				</div><!-- /.col -->
				
				<div class="col-sm-6">
					<div class="widget-box ">
						<div class="widget-header widget-header-flat">
							<h4 class="widget-title lighter">
								<i class="ace-icon fa fa-signal"></i>
								Examinar Grupos Etareos
							</h4>

							<div class="widget-toolbar">
								<a href="#" data-action="collapse">
									<i class="ace-icon fa fa-chevron-up"></i>
								</a>
							</div>
						</div>

						<div class="widget-body">
							<div class="widget-main padding-4">
								<div id="container1" style="height: 400px; min-width: 310px"></div>
							</div><!-- /.widget-main -->
						</div><!-- /.widget-body -->
					</div><!-- /.widget-box -->
				</div><!-- /.col -->
												
					
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
				
				/*var color_bar1=	'#A9D0F5';		
				var color_bar2=	'#FA5858';*/
				
				    Highcharts.chart('container', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: 0,
							plotShadow: false
						},
						title: {
							text: 'Examinar por<br>Sexo<br>',
							align: 'center',
							verticalAlign: 'middle',
							y: 40
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
						},
						plotOptions: {
							pie: {
								dataLabels: {
									enabled: true,
									distance: -50,
									style: {
										fontWeight: 'bold',
										color: 'white'
									}
								},
								startAngle: -90,
								endAngle: 90,
								center: ['50%', '75%']
							}
						},
						series: [{
							type: 'pie',
							name: 'Ganado por Sexo',
							innerSize: '50%',
							data: [<?php echo join($serie1, ',') ?>] /* Convierte un array en una cadena*/
						}]
					});
								
				
				
				
				/*Highcharts.chart('container', {
					chart: {
						type: 'pie',
						options3d: {
							enabled: true,
							alpha: 45
						}
					},
					title: {
						text: 'Ganado Por sexo'
					},
					subtitle: {
						text: 'Subtitulo'
					},
					plotOptions: {
						pie: {
							innerSize: 100,
							depth: 45
						}
					},
					series: [{
						name: 'Delivered amount',
						data: [<?php echo join($serie1, ',') ?>] // Convierte un array en una cadena
					}]
				});*/
				
				Highcharts.chart('container1', {
					chart: {
						type: 'pie',
						options3d: {
							enabled: true,
							alpha: 45
						}
					},
					title: {
						text: 'Examinar por Grupo Etareo'
					},
					subtitle: {
						text: 'Subtitulo'
					},
					plotOptions: {
						pie: {
							innerSize: 100,
							depth: 45
						}
					},
					series: [{
						name: 'Cantidad',
						data: [<?php echo join($serie2, ',') ?>] /* Convierte un array en una cadena*/
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