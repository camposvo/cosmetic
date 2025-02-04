<?php 
	session_start();
	include_once ("adm_utilidad.php");
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
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>
	
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	$arr_vendedor=  Combo_Vendedor();
	$arr_rubro   =  Combo_Rubro();
	$arr_abono   =  Combo_Abono();
	
	$arr_fecha = explode('-',$x_fecha,2);
	$x_fecha_ini = $arr_fecha[0];
	$x_fecha_fin = $arr_fecha[1];
	
	$x_vendedor = isset($x_vendedor)?$x_vendedor:$co_usuario;
	$x_cliente   = isset($x_cliente)?$x_cliente:0;
	
/*-------------------------------------------------------------------------------------------
	LEE DATOS DEL VENDEDOR
-------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT tx_nombre || ' ' || tx_apellido
				FROM s01_persona 
				WHERE s01_persona.co_persona = $x_vendedor";
	

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		if ($obj_miconexion->fun_numregistros() != 0){
			$i=0;
			while($fila = pg_fetch_row($ls_resultado)){				
				$filtro_vend=ucwords(strtolower($fila[0]));
				$filtro_vend= '<span class="badge badge-pill badge-default">Vend: '.$filtro_vend.'</span>';
			}
		}else{
			$filtro_vend= '';			
		}
	}
	
/*-------------------------------------------------------------------------------------------
	LEE DATOS DEL CLIENTE
-------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT tx_nombre || ' ' || tx_apellido
				FROM s01_persona 
				WHERE s01_persona.co_persona = $x_cliente";
	

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		if ($obj_miconexion->fun_numregistros() != 0){
			$i=0;
			while($fila = pg_fetch_row($ls_resultado)){
				$filtro_clien = ucwords(strtolower($fila[0]));
				$filtro_clien = '<span class="badge badge-pill badge-default">Clte: '.$filtro_clien.'</span>';
			}
		}else{
			$filtro_clien = '';			
		}
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para ELIMINAR una actividad 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT f_borrar_factura($x_movimiento )";		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);		
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$Result    = $row[0];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
		}
		
		if ($Result==0){
			$msg = "¡La factura existe pero no puede ser borrada !";    			
		}elseif($Result==1){
			$msg = "¡La factura fue borrada Satisfactoriamente !";			
		}elseif($Result==2){
			$msg = "¡La factura No existe !";
		}		
		echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_venta_view.php'</script>";		
	}

/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una actividad Factura
-------------------------------------------------------------------------------------------*/
	$i=0; /*Banderar para cantidad de reglas */
	$sw = 0; // Bandera para indicar si hay filtros

	if($filtro =='NO_ALL')$arr_criterio[$i++]=" (f_calcular_abono(pk_factura) < nu_total) "; /* solo los que deben*/
	if($x_cliente!=0){ $arr_criterio[$i++]=" t20_factura.fk_cliente = ".$x_cliente; $sw = 1;}
//	if($x_factura!=0){ $arr_criterio[$i++]=" UPPER(t20_factura.tx_factura) = '".strtoupper($x_factura)."' "; $sw = 1; }
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){ $arr_criterio[$i++]=" t20_factura.fe_fecha_factura >= '".strtoupper($x_fecha_ini)."' and t20_factura.fe_fecha_factura <= '".strtoupper($x_fecha_fin)."' "; $sw =1; }
	if($x_vendedor !=0){ $arr_criterio[$i++]=" t20_factura.fk_responsable = ".$x_vendedor; $sw =1; }
		
	for($j=0;$j<$i;$j++)$ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];	
	$ls_criterio = $ls_criterio==""?"":" and ".$ls_criterio;	
	
/*-------------------------------------------------------------------------------------------
RUTINAS: Consulta  datos resumen
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT sum (f_calcular_factura(pk_factura)) as SumaTotal, 
				sum(f_calcular_abono(pk_factura)) as SumaAbono
				FROM t20_factura 
				INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente 
			WHERE t20_factura.tx_tipo='VENTA'  ".$ls_criterio;			

	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$SumaTotal    = $row[0];
		$SumaAbono    = $row[1];
		$SumaDebe     = $SumaTotal - $SumaAbono ;
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}

/*-------------------------------------------------------------------------------------------
	LEE LOS DATOS DE LAS VENTAS PARA MOSTRAR EN LA TABLA
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT to_char(pk_factura,'0000000'), to_char(fe_fecha_factura, 'dd-TMMon-yyyy'), tx_telefono_hab, 
				UPPER(s01_persona.tx_nombre), 
				CASE 
				WHEN in_pedido = 'S' THEN 'NUEVO'
				WHEN (f_calcular_factura(pk_factura) - f_calcular_abono(pk_factura)) = 0 THEN 'COMPLETADO'
				ELSE 'EN PROCESO'
			END AS nombre_status,
				f_calcular_factura(pk_factura), f_calcular_abono(pk_factura) AS abono, (f_calcular_factura(pk_factura) - f_calcular_abono(pk_factura)) as debe,
				CASE WHEN fe_fecha_factura <= CURRENT_DATE - INTERVAL '10 days' THEN 'VENCIDA'
         ELSE 'EN CURSO'
    		END AS antiguedad,
				pk_factura
			FROM t20_factura
			INNER JOIN s01_persona ON s01_persona.co_persona = t20_factura.fk_cliente
			WHERE t20_factura.tx_tipo='VENTA'  ".$ls_criterio." order by  fe_fecha_factura asc ;";	

	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){
		$filtro_fecha =  $x_fecha_ini.' hasta '.$x_fecha_fin;
		$filtro_fecha =  '<span class=" badge badge-pill badge-default">Fecha: '.$filtro_fecha.' </span>';
	}else{
		
		$filtro_fecha = '';
	}	
	
	//$collaps = ($sw==1)?'':'collapsed';	
	$collaps = 'collapsed';		
	if($filtro =='NO_ALL'){
		$filtro_deudas = '<span class="badge badge-pill badge-success">Solo Deudas </span>';
		//$filtro_deudas = '<span class="label label-sm label-success arrowed-right">Solo Deudas</span>';
	}else{
		$filtro_deudas = '';
	}

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button class="hidden-480  btn-success btn-sm pull-left " onclick="Agregar()">
							<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
							Venta
						</button>
					</div>
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12">

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
												<label class="col-sm-3 control-label no-padding-right"> Cliente </label>
												<div class="col-sm-7" >	
													<select id="x_cliente" name="x_cliente" class="chosen-select form-control " data-placeholder="Seleccione un Cliente...">
														<?php
															if ($x_cliente == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_cliente as $k => $v) {
																$ls_cadenasel =($k == $x_cliente)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>							
													</select>
												</div>													
											</div>
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Vendedor</label>
												<div class="col-sm-7" >	
													<select name="x_vendedor" class=" chosen-select form-control" id="form-field-select-3" data-placeholder="Seleccione un Vendedor...">
														<?php
															if ($x_vendedor == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_vendedor as $k => $v) {
																$ls_cadenasel =($k == $x_vendedor)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>							
													</select>
												</div>													
											</div>
										
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
												
												<button type="button" class="btn btn-sm btn-primary"  onclick="Buscar('ALL')">
													<i class="ace-icon fa fa-search align-top bigger-125 "></i>
													Todos
												</button>
												
												<button type="button" class="btn btn-sm btn-info"  onclick="Buscar('NO_ALL')">
													<i class="ace-icon fa fa-search align-top bigger-125 "></i>
													Deben
												</button>			
												
												<button type="button" class="btn btn-sm btn-info"  onClick="Limpiar('<?php echo "tarea=X"; ?>')">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Reset
												</button>				
											</div>											
											
											<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
											<input type="hidden" name="tarea" 		 value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" 		 value="<?php echo $modo;?>">
											<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">		
											<input type="hidden" id = "input_filtro" name="input_filtro" value="<?php echo $input_filtro;?>">		
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
													<th>Total</th>
													<th>Abono</th>
													<th>Debe</th>													
												</tr>
												
											</thead>
											<tbody>	
												<tr>
													<td >
														<input id= 'x_sum_total' class="input-sm form-control" name="x_sum_total"  type="text" readonly />
													</td>
													<td >
														<input id= 'x_sum_abono' class="input-sm form-control" name="x_sum_abono"  type="text" readonly />
													</td>
													<td >
														<input id= 'x_sum_debe' class="input-sm form-control" name="x_sum_debe"  type="text" readonly />
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
						<div class="col-xs-12">	
							<div class="table-header align-right">
									
									<?php echo $filtro_deudas;?>
									<?php echo $filtro_vend;?>
									<?php echo $filtro_clien;?>
									<?php echo $filtro_fecha;?>
							</div>
							
							<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr>
										<th class="hidden-480">id</th>
										<th class="">Fecha</th>
										<th class="">Cliente</th>
										<th class="">Estatus</th>
										
										<th class="hidden">Total</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->	
										<th class="">Total</th>
										
										<th class="hidden">Abono</th>
										<th class="hidden-480">Abono</th>
										
										<th class="hidden">Debe</th>
										<th class="">Debe</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										if($tarea == "M"){
											//$li_hidden = 4; /* Cantidad de columnas que se van A OCULTAR cuando la pantalla es pequeña*/
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_VENTA',0); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
										}
									?>
								</tbody>
							</table>
						</div>
					</div> <!-- /.row tabla resumen -->
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->

	<div class="modal fullscreen-modal fade" id="modal-1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<div class="d-flex justify-content-end"> 
						<button type="button" class="btn btn-default" id="btn-1">Close</button>
					</div>
				</div>

				<div class="modal-body">
					<iframe id="iframeModalWindow-1" width="100%" height="600px" src="blank.html" name="iframe_modal_1" style="border:none;"></iframe>
				</div>

			</div>
		</div>
	</div>

	
	
	<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='../../assets/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='../../assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
	
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

	<script type="text/javascript">
				


		$(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical	

			$('#dynamic-table').dataTable( {
				"search": {
					"search": $('#input_filtro').val()
				  },
				"lengthChange": false,
				"pageLength": 50,
				"ordering": false,
				"oLanguage": {
					"sInfo": "De _START_ hasta _END_ de un total de _TOTAL_",
					"sInfoFiltered": " ( filtrado de _MAX_ registros )",
					"sSearch": "Filtro:",						
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
								
				
				  
				  
				// CALCULA LA SUMA RESUMEN POR FILTRO Y EL TOTAL  
				"fnFooterCallback": function ( row, data, start, end, display ) {					
					var api = this.api(), data;

					FiltroTotal = api.column(5, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );
						
					FiltroAbono = api.column(7, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );	
					
					FiltroDebe = api.column(9, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );			
							 								
					FiltroTotal = formato_numero(FiltroTotal, 2, ',', '.');
					FiltroAbono = formato_numero(FiltroAbono, 2, ',', '.');
					FiltroDebe  = formato_numero(FiltroDebe, 2, ',', '.');
					
					$('#x_sum_total').val(FiltroTotal);
					$('#x_sum_abono').val(FiltroAbono);
					$('#x_sum_debe').val(FiltroDebe);				
				}
				  				  
			} );
			
			// Toma el valor del Filtro 
			var table = $('#dynamic-table').DataTable(); 
			table.on( 'search.dt', function () {
				$('#input_filtro').val(table.search());
			} );
				
			if(!ace.vars['touch']) {
				$('.chosen-select').chosen({allow_single_deselect:true}); 
				//resize the chosen on window resize
				$(window)
				.off('resize.chosen')
				.on('resize.chosen', function() {
					$('.chosen-select').each(function() {
						 var $this = $(this);	
						 ancho = $(window).width() < 580 ? 180:350; // Establece el ancho dependiendo de la ventana
						 $this.next().css({'width': ancho});
					})
				}).trigger('resize.chosen');
				//resize chosen on sidebar collapse/expand
				$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
					if(event_name != 'sidebar_collapsed') return;
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});
					})
				});			
			}
					
	
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
				format: "DD/MM/YYYY",
				ranges: {
				     'Hoy': [moment(), moment()],
				   'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				   //'Ultimos 7 Dias': [moment().subtract(6, 'days'), moment()],
				   //'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
				   'Este Mes': [moment().startOf('month'), moment().endOf('month')],
				   'Este Año': [moment().startOf('year'), moment().endOf('year')]
				   //'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}
			}, cb);
			
			cb(moment().subtract(29, 'days'), moment());
			
			$('[data-rel=tooltip]').tooltip({container:'body'});
				$('[data-rel=popover]').popover({container:'body'});
			
			//  Tooltip
			$( ".open-event" ).tooltip({
				show: null,
				position: {
					my: "left top",
					at: "left bottom"
				},
				open: function( event, ui ) {
					ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
				}
			});
			
		} );
	</script>

	<script>
	
		$("#btn-1").click(function() {
			$('#modal-1').find('iframe').attr('src', 'blank.html')
			$('#modal-1').modal('hide');
		});
	</script>

	<script type="text/javascript"> 


		function Mostrar_Info(identificador){
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_venta_info.php";
			$('#modal-1').find('iframe').attr('src', 'adm_venta_info.php?x_movimiento=' + identificador)
			$('#modal-1').modal('show');
			window.parent.ScrollToTop();
		}
		
			
		function Agregar(){
			document.formulario.tarea.value = "A";
			document.formulario.action = "adm_venta_add.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}		
		
		function Pagar_Venta(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_venta_cobrar.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}

		function Reporte_Venta(identificador){
			pagina = "adm_venta_pdf.php?x_movimiento="+identificador;
			window.open(pagina,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');

		}


	
		function Eliminar_Venta(identificador){
			if (confirm('Desea Eliminar este Registro?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.x_movimiento.value = identificador;
				document.formulario.action = "adm_venta_view.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
		
		function Editar_Venta(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_venta_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Buscar(filtro){
			document.formulario.filtro.value = filtro;	
			document.formulario.tarea.value = "B";
			document.formulario.action = "adm_venta_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Limpiar(parametros){	
			location.href='adm_venta_view.php'
		}
		
	</script>
</body>
</html>