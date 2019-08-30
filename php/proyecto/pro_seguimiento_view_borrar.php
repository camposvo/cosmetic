<?php 
	session_start();
	include_once ("pro_utilidad.php");
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
<title>La Peperana</title>
	
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
				
</head>
<body>

<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$o_cantidad  = 0;
	$o_cantidad2 = 0;
	$x_rubro     = 0;
	$sw = 0;
	
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nuevo Registro';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
	}
		
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$x_ano_actual = date('Y');
	$x_ano = isset($x_ano)?$x_ano:$x_ano_actual;
	
	$arr_fecha = explode('-',$x_fecha,2);
	$x_fecha_ini = $arr_fecha[0];
	$x_fecha_fin = $arr_fecha[1];


/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA DE VENTAS POR PRODUCTO
-----------------------------------------------------------------------------------------------*/
	
	$i=0;

	if($filtro !='ALL'){ $arr_criterio[$i++]=" (in_proy_activo = 'S') "; }/* solo los ACTIVOS*/
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){ $arr_criterio[$i++]=" fe_fecha_factura >= '".$x_fecha_ini."' and fe_fecha_factura <= '".$x_fecha_fin."' "; $sw =1; }
			
	for($j=0;$j<$i;$j++)$ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];
	$ls_criterio = $ls_criterio==""?"":" WHERE ".$ls_criterio;


	
	$ls_sql = "SELECT nb_tipo_rubro, tx_nombre, sum(venta) as venta, sum(gasto) as gasto, sum((venta - gasto)) as ganancia 
			FROM v06_mov_resumen 
			inner join t02_proyecto ON t02_proyecto.pk_proyecto = v06_mov_resumen.fk_proyecto 
			left join t08_tipo_proyecto ON t08_tipo_proyecto.pk_tipo_rubro = t02_proyecto.fk_tipo_rubro 
			".$ls_criterio." 
			GROUP BY nb_tipo_rubro, tx_nombre 
		";
	
	
	//echo $ls_sql;

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}

	$PorceGanancia = $TotalGasto ==0?0:($TotalGanancia *100)/$TotalGasto;
	
	$collaps = ($sw==1)?'':'collapsed';	
?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Seguimiento Proyectos
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col ">
							<div class="widget-box <?php echo $collaps; ?> ">
								<div class="widget-header  widget-header-small">
									<h5 class="widget-title"> Busqueda Avanzada </h5>
									<div class="widget-toolbar">
																			
										<a href="#" data-action="collapse">
											<i class="ace-icon fa fa-chevron-down"></i>
										</a>

									</div>
								</div>
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
																																				
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right">Fecha</label>
												<div class="col-sm-4">	
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
														<input class="input-sm form-control" id="reportrange" name="x_fecha" value="<?php echo $x_fecha;?>"  type="text"  />
													</div>
												</div>
											</div>
											
																				
											<div class="form-group center ">
												<div class="btn-group">
													<button data-toggle="dropdown" class="btn btn-sm btn-primary  dropdown-toggle">
														<i class="ace-icon fa fa-search align-top bigger-125 "></i>
														Filtro
														<i class="ace-icon fa fa-angle-down icon-on-right"></i>
													</button>

													<ul class="dropdown-menu dropdown-info ">
														
														<li>
															<a href="#" onclick="Buscar('ALL')">Todo</a>
														</li>

														<li>
															<a href="#" onclick="Buscar('ACTIVE')">Activos</a>
														</li>
														
													</ul>
												</div><!-- /.btn-group -->	
												<button type="button" class="btn btn-sm btn-info"  onClick="Limpiar()">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Reset
												</button>				
											</div>											
											
											<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
											<input type="hidden" name="tarea" 		 value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" 		 value="<?php echo $modo;?>">
											<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">		
											<input type="hidden" id = "input_filtro" name="input_filtro" 		 value="<?php echo $input_filtro;?>">		
										</form>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- / ROW BUSQUEDA AVANZADA -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-body">
									<div class="widget-main no-padding">
										<table id="" class="table table-striped table-bordered ">
											<thead>
												<tr class="info">
													<th class="hidden-480">Ventas</th>
													<th class="hidden-480">Gastos</th>
													<th>Ganancia </th>
													<th>Utilidad</th>
													
												</tr>
											</thead>
											<tbody>	
												<tr>
													<td >
														<input id= 'x_venta_total' class="input-sm form-control" name="x_venta_total"  type="text" readonly />
													</td>
													<td >
														<input id= 'x_gasto_total' class="input-sm form-control" name="x_gasto_total"  type="text" readonly />
													</td>
													<td >
														<input id= 'x_ganancia' class="input-sm form-control" name="x_ganancia"  type="text" readonly />
													</td>
													<td >
														<input id= 'x_porc_gan' class="input-sm form-control" name="x_porc_gan"  type="text" readonly />
													</td>
												</tr>																		
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>	
					</div>	<!-- /.row totales -->
					
					
					
					
												
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<div class="table-header">
								Proyectos
							</div>
							
							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="hidden-480">Tipo</th>
										<th class="">Proyecto</th>
										<th class="hidden">Ventas</th>
										<th class="">Ventas</th>
										<th class="hidden">Gastos</th>
										<th class="">Gastos</th>
										<th class="hidden-480">Ganancia</th>
										<th class="">Utilidad(%)</th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										if($tarea == "M"){
											$li_numcampo = $obj_miconexion->fun_numcampos()-9; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos(); // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_SEGUIMIENTO',0); // Dibuja la Tabla de Datos
										}
									?>
									
								</tbody>
							</table>
						</div>
					</div> <!-- /.row tabla principal -->	
			
			
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>	
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/moment.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>
	<script src="../../assets/js/chosen.jquery.min.js"></script>
	
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			$('#dynamic-table').dataTable( {
				"lengthChange": false,
				"pageLength": 50,
				"aaSorting": [ [0,'desc'] ],
				"oLanguage": {
					"sInfo": "De _START_ hasta _END_ de un total de _TOTAL_",
					"sInfoFiltered": " ( filtrado de _MAX_ registros )",
					"sSearch": "Filtro:",						
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
								
				"columns": [
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null
				  ],
				  
				  
				// CALCULA LA SUMA RESUMEN POR FILTRO Y EL TOTAL  
				"fnFooterCallback": function ( row, data, start, end, display ) {					
					var api = this.api(), data;

					FiltroVenta = api.column(2, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );
						
					FiltroGasto = api.column(4, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );	
										
					FiltroGanancia = FiltroVenta - FiltroGasto;	
					FiltroPorcent  = (FiltroGanancia*100)/FiltroGasto;	
					
					FiltroVenta    = formato_numero(FiltroVenta, 2, ',', '.');
					FiltroGasto    = formato_numero(FiltroGasto, 2, ',', '.');
					FiltroGanancia = formato_numero(FiltroGanancia, 2, ',', '.');
					FiltroPorcent  = formato_numero(FiltroPorcent, 2, ',', '.');
					
					$('#x_venta_total').val(FiltroVenta);
					$('#x_gasto_total').val(FiltroGasto);
					$('#x_ganancia').val(FiltroGanancia);
					$('#x_porc_gan').val(FiltroPorcent);
				}	
				  				  
			} );
			
			
			//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
			$('input[name=date-range-picker]').daterangepicker({
				'applyClass' : 'btn-sm btn-success',
				'cancelClass' : 'btn-sm btn-default',
				locale: {
					applyLabel: 'Aplicar',
					cancelLabel: 'Cancelar',
				}
			})
			.prev().on(ace.click_event, function(){
				$(this).next().focus();
			});
			
				
			function cb(start, end) {
				$('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
			}
			
			
			$('#reportrange').daterangepicker({
				startDate:moment().startOf('month'),
				endDate:  moment().endOf('month'),
				format: "DD/MM/YYYY",
				ranges: {
					'Este Mes': [moment().startOf('month'), moment().endOf('month')],
				    'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
					'Este Año': [moment().startOf('year'), moment().endOf('year')],	
					'Ultimo Año': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
				}
			}, cb);
			cb(moment().startOf('year'), moment().endOf('year'));
	
		} );

	</script>

  
	<script type="text/javascript"> 

		function MostrarResumen(){
			document.formulario.action = "repo_resumen.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Buscar(filtro){
			document.formulario.filtro.value = filtro;	
			document.formulario.tarea.value = "B";
			document.formulario.action = "pro_seguimiento_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Limpiar(){	
			location.href='pro_seguimiento_view.php'
		}
		
	
	</script>

</body>
</html>
