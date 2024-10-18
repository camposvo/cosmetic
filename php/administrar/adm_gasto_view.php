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
		
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	$o_cantidad  = 0;
	$o_cantidad2 = 0;
	$x_proyecto     = 0;
	
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nuevo Registro';
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$x_fecha_actual        =  date('d/m/Y');
	$x_fecha_mespasado 	   =  date('d/m/Y', strtotime('-29 day')) ;   
	
	$co_usuario        =  $_SESSION["li_cod_usuario"];
	$arr_proveedor     =  Combo_Proveedor();
	$arr_rubro         =  Combo_Rubro();
	$arr_clasificacion =  Combo_Clasificacion();
	$arr_responsable   =  Combo_Cliente();
	$arr_abono         =  Combo_Abono();
	
	$x_mes_actual = date('m');
	$x_ano_actual = date('Y');
	
	// Coloca por defecto los ultimos 30 dias como fecha inicial
	if (!empty($x_fecha)){ // Seleccion de una Fecha
		$arr_fecha = explode('-',$x_fecha,2);
		$x_fecha_ini = $arr_fecha[0];
		$x_fecha_fin = $arr_fecha[1];	
		
	}else{ // No hay seleccion de Fecha  y Carga variables con los ultimos 30 DIAS POR DEFECTO
		$x_fecha_ini  = isset($x_fecha_ini)?$x_fecha_ini:$x_fecha_mespasado;
		$x_fecha_fin  = isset($x_fecha_fin)?$x_fecha_fin:$x_fecha_actual;
	}

/*-------------------------------------------------------------------------------------------
	LEE DATOS DEL PROVEEDOR
-------------------------------------------------------------------------------------------*/	
	$x_proveedor = isset($x_proveedor)?$x_proveedor:0;
	$ls_sql = "SELECT nb_proveedor
				FROM t03_proveedor 
				WHERE t03_proveedor.pk_proveedor = $x_proveedor";
	

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		if ($obj_miconexion->fun_numregistros() != 0){
			$i=0;
			while($fila = pg_fetch_row($ls_resultado)){				
				$filtro_proveedor=ucwords(strtolower($fila[0]));
				$filtro_proveedor= '<span class="badge badge-pill badge-default">Provee: '.$filtro_proveedor.'</span>';
			}
		}else{
			$filtro_proveedor= '';			
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
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}
		
		if ($Result==0){
			$msg = "¡La factura existe pero no puede ser borrada !";    			
		}elseif($Result==1){
			$msg = "¡La factura fue borrada Satisfactoriamente !";			
		}elseif($Result==2){
			$msg = "¡La factura No existe !";
		}		
		echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_gasto_view.php'</script>";
		
	}

/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una actividad Factura
-------------------------------------------------------------------------------------------*/
	$i=0; /*Banderar para cantidad de reglas */
	$sw = 0; // Bandera para indicar si hay filtros
	if($x_proveedor!=0){ $arr_criterio[$i++]=" t20_factura.fk_proveedor = ".$x_proveedor; $sw=1; }
	if($x_factura!=0){ $arr_criterio[$i++]=" UPPER(t20_factura.tx_factura) = '".strtoupper($x_factura)."' "; $sw=1; }
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){  $arr_criterio[$i++]=" t20_factura.fe_fecha_factura >= '".strtoupper($x_fecha_ini)."' and t20_factura.fe_fecha_factura <= '".strtoupper($x_fecha_fin)."' "; $sw=1; }
	if($x_vendedor !=0){ $arr_criterio[$i++]=" t20_factura.fk_responsable = ".$x_vendedor; $sw=1; }
	//if($filtro =='NO_ALL'){  $arr_criterio[$i++]=" t20_factura.fe_fecha_factura >= '".$x_fecha_ini."' and t20_factura.fe_fecha_factura <= '".$x_fecha_fin."' ";  }
		
	for($j=0;$j<$i;$j++)$ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];	
	$ls_criterio = $ls_criterio==""?"":" and ".$ls_criterio;
			
/*-------------------------------------------------------------------------------------------
RUTINAS: Consulta  de registros de la busqueda
-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT to_char(t20_factura.pk_factura,'0000000'), to_char(t20_factura.fe_fecha_factura, 'dd-TMMon-yyyy'), 
			nb_articulo, 
			nb_categoria,
			t02_proyecto.tx_nombre, 
			t01_detalle.nu_cantidad,
			nu_precio,			
			t01_detalle.nu_cantidad * nu_precio AS MONTO, t20_factura.pk_factura 
		    FROM t01_detalle
		    INNER JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
		    INNER JOIN t13_articulo ON t01_detalle.fk_articulo = t13_articulo.pk_articulo
		    INNER JOIN t05_clase ON t13_articulo.fk_clase = t05_clase.pk_clase  
		    INNER JOIN t21_categoria ON t21_categoria.pk_categoria = t05_clase.fk_categoria 
			INNER JOIN t02_proyecto ON t01_detalle.fk_rubro = t02_proyecto.pk_proyecto
			WHERE t20_factura.tx_tipo='GASTO' ".$ls_criterio;
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
	//$collaps = ($sw==1)?'':'collapsed';	
	$collaps = 'collapsed';	
	
//	if($filtro =='NO_ALL'){
		$filtro_fecha =  $x_fecha_ini.' hasta '.$x_fecha_fin;
		$filtro_fecha =  '<span class=" badge badge-pill badge-default">Fecha: '.$filtro_fecha.' </span>';
	//}else{
		//$filtro_fecha = '';
	//}
	
?>
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button class="hidden-480  btn-danger btn-sm pull-left " onclick="Agregar()">
							<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
							Gasto
						</button>
					</div>
				</div>
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
												<label class="col-sm-3 control-label no-padding-right" >Proveedor</label>
												<div class="col-xs-12 col-sm-7" >	
													<select name="x_proveedor" class="chosen-select col-xs-12 col-sm-7 " id="" data-placeholder="Selecciona un Proveedor...">
														<?php
															if ($x_proveedor == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_proveedor as $k => $v) {
																$ls_cadenasel =($k == $x_proveedor)?'selected':'';
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
														<input class="input-sm form-control" id="reportrange" name="x_fecha"  value="<?php echo $x_fecha;?>"  type="text"/>
													</div>
												</div>
											</div>
											
											
																						
											<div class="form-group center ">												
												<button type="button" class="btn btn-sm btn-primary" onclick="Buscar('ALL')">
													<i class="ace-icon fa fa-search align-top bigger-125 "></i>
													Buscar
												</button>
												
												<button type="button" class="btn btn-sm "  onClick="Limpiar('<?php echo "tarea=X"; ?>')">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Reset
												</button>				
											</div>												
											<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" value="<?php echo $modo;?>">
											<input type="hidden" name="filtro" value="<?php echo $filtro;?>">	
											<input type="hidden" id = "input_filtro" name="input_filtro" 		 value="<?php echo $input_filtro;?>">													
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-body">
									<div class="widget-main no-padding">
										<table id="" class="table table-striped table-bordered ">
											<thead>
												<tr class="info">
													<th>Cantidad</th>
													<th>Total</th>
																											
												</tr>
												
											</thead>
											<tbody>	
												<tr>
													<td >
														<input id= 'x_sum_cantidad' class="input-sm form-control" name="x_sum_cantidad"  type="text" readonly />
													</td>
													<td >
														<input id= 'x_sum_total' class="input-sm form-control" name="x_sum_total"  type="text" readonly />
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
								<?php echo $filtro_fecha;?>
								<?php echo $filtro_proveedor;?>
							</div>

							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="hidden-480" >id</th>
										<th class="">Fecha</th>
										<th class="">Descripcion</th>
										<th class="">Categoria</th>										
										<th class="hidden-480">Proyecto</th>			
										<th class="">Cant.</th>
										<th class="">Precio</th>
										<th class="hidden">Total</th>
										<th class="">Total</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										$li_hidden = 5; /* Cantidad de columnas que se van A OCULTAR cuando la pantalla es pequeña*/
										$li_numcampo = 0; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_GASTO',0); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
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


	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
		
			$('#dynamic-table').dataTable( {
				"search": {
					"search": $('#input_filtro').val()
				  },
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
					{ "orderable": false },
					null,
					null,
					null,
					null,
					null,
					null,
					{ "orderable": false },
					{ "orderable": false }
				  ],
				  
				// CALCULA LA SUMA RESUMEN POR FILTRO Y EL TOTAL  
				"fnFooterCallback": function ( row, data, start, end, display ) {					
					var api = this.api(), data;

					FiltroTotal = api.column(7, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );
					
					FiltroCantidad = api.column(5, { search: 'applied'} ).data()	.reduce( function (a, b) {
						 return parseFloat(a) + parseFloat(b); 
					}, 0 );
						
					FiltroTotal = formato_numero(FiltroTotal, 2, ',', '.');
					FiltroCantidad = formato_numero(FiltroCantidad, 2, ',', '.');
								
								
					$('#x_sum_cantidad').val(FiltroCantidad);
					$('#x_sum_total').val(FiltroTotal);
	
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
			
			cb(moment().subtract(29, 'days'), moment());

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
  
	<script type="text/javascript"> 

	function calcular_total(){
		document.formulario.o_total.value = document.formulario.o_cantidad.value * document.formulario.o_precio.value;
	}
	
	function Agregar(){
		document.formulario.tarea.value = "A";
		document.formulario.action = "adm_gasto_add.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
		function Buscar(filtro){
			document.formulario.filtro.value = filtro;	
			document.formulario.action = "adm_gasto_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function Eliminar_Gasto(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_gasto_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	function Editar_Gasto(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_gasto_add.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function Limpiar(){
			location.href='adm_gasto_view.php'
		}	
	

	
	</script>
</body>
</html>