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

	// Asigna criterio dependiendo del valor de la fecha 
	if (!empty($x_fecha)){
		 $sw =1;
		$arr_fecha = explode('-',$x_fecha,2);
		$x_fecha_ini = $arr_fecha[0];
		$x_fecha_fin = $arr_fecha[1];


	}else{
		$x_fecha_ini = $x_fecha_mespasado;
		$x_fecha_fin = $x_fecha_actual;
	}

/*-------------------------------------------------------------------------------------------
	INVOCA FUNCION DE B.D. PARA REFRESCAR VISTA MATERIALIZADA
----------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT f_refresh();";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	
	if($ls_resultado != 0){
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}

/*-------------------------------------------------------------------------------------------
	CONSULTA DE BANCO DINERO EN EL BANCO
----------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT sum(nu_capital) FROM t15_banco";
			
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Banco    = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
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
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
/*-------------------------------------------------------------------------------------------
	CONSULTA DE CUENTAS POR PAGAR
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
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}
	
/*-------------------------------------------------------------------------------------------
	LEE EL SALDO DISPONIBLE DESDE UNA FUNCION DE BASE DE DATOS
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT f_saldo_disponible() ";
	//echo $ls_sql;		
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado)>0){
			$row = pg_fetch_row($ls_resultado,0);
			$Disponible    = $row[0];
		}	
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}	
	$Caja = $Disponible - $Banco;	
	
/*-------------------------------------------------------------------------------------------
	DEFINE EL CRITERIO DE LA BUSQUEDA AVANZADA
--------------------------------------------------------------------------------------------*/
	$i=0;
	$ls_criterio = "";

	if($x_tipo != ''){ $arr_criterio[$i++]=" operacion = '".$x_tipo."'"; $sw = 1;}
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){ $arr_criterio[$i++]=" fecha >= '".strtoupper($x_fecha_ini)."' and fecha <= '".strtoupper($x_fecha_fin)."' ";  }		
		
	for($j=0;$j<$i;$j++) $ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];		
	$ls_criterio = $ls_criterio==""?"":" WHERE ".$ls_criterio;	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: CONSULTA LOS INGRESOS Y EGRESOS 
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT SUM(ingreso) as Ingreso, SUM(egreso) as Egreso
				FROM vm02_edo_cuenta	".$ls_criterio;
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$Ingreso   = $row[0];
		$Egreso    = $row[1];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);// enviar mensaje de error de consulta
	}	

/*-------------------------------------------------------------------------------------------
	CONSULT DE MOVIMIENTOS
--------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT to_char(pk_factura,'0000000'), to_char(fecha, 'dd-Mon-yyyy'), operacion,				
			UPPER(vm02_edo_cuenta.cliente), ingreso, egreso, f_cuenta, pk_factura
			FROM vm02_edo_cuenta "
			.$ls_criterio.
           "ORDER BY  fecha desc , ref desc; ";
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	
	$collaps = ($sw==1)?'':'collapsed';	

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Posicion Global
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
												<label class="col-sm-3 control-label no-padding-right"> Tipo </label>
												<div class="col-sm-7" >	
													<select id="x_tipo" name="x_tipo" class="chosen-select form-control " data-placeholder="Seleccione ...">
														<?php
															if ($x_tipo == ""){
																echo "<option value='' selected></option>";
															}else{
																echo "<option value=''></option>";
															}
															foreach($arr_tipo as $k => $v) {
																$ls_cadenasel =($k == $x_tipo)?'selected':'';
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
												<button type="button" class="btn btn-sm btn-info"  onClick="Buscar()">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Buscar
												</button>		
												<button type="button" class="btn btn-sm btn-info"  onClick="Limpiar('<?php echo "tarea=X"; ?>')">
													<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
													Reset
												</button>				
											</div>											
											
											<input type="hidden" name="ingreso1" value="<?php echo $ingreso1;?>">
											<input type="hidden" name="egreso1" value="<?php echo $egreso1;?>">
											<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
										</form>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- ROW BUSQUEDA AVANZADA -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-body">
									<div class="widget-main no-padding">
										<table id="simple-table" class="table table-striped table-bordered">
											<thead>
												<tr  class="success">
													<th class="hidden-480">Efectivo</th>
													<th class="hidden-480">Banco</th>
													<th>Ingreso</th>
													<th>Egreso</th>
													<th>Disponible</th>
												</tr>
											</thead>
											<tbody>	
												<tr>
													<td class="hidden-480"><?php echo number_format($Caja,2,",","."); ?> </td>
													<td class="hidden-480"><?php echo number_format($Banco,2,",","."); ?></td>
													<td><?php echo number_format($Ingreso,2,",","."); ?></td>
													<td><?php echo number_format($Egreso*(-1),2,",","."); ?></td>
													<td><?php echo number_format($Disponible,2,",","."); ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>	
					</div>	<!-- ROW RESUMEN  -->
		
				
			
					<div class="row">
						<div class="col-xs-12">
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Movimientos
							</div>

							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th class="">id</th>
										<th class='hidden-480'>Fecha Reg.</th>
										<th class='hidden-480'>Tipo</th>
										<th class='hidden-480'>Descripcion</th>
										<th>Ingreso</th>
										<th>Egreso</th>
										<th>Monto</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										if($tarea == "M"){
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos() - 1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_CUENTA'); // Dibuja la Tabla de Datos
										}
									?>
								</tbody>
							</table>						
						</div>
					</div> <!-- ROW TABLA PRINCIPAL -->				
				</div> <!-- ROW CONTENT END -->
			</div>  <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- main-content Principal -->

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
				   'Ultimos 7 Dias': [moment().subtract(6, 'days'), moment()],
				   'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
				   'Este Mes': [moment().startOf('month'), moment().endOf('month')],
				   'Este Año': [moment().startOf('year'), moment().endOf('year')]
				  // 'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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

		function Mostrar_Info(identificador,ingreso1,egreso1){
			document.formulario.tarea.value = "X";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.ingreso1.value = ingreso1;
			document.formulario.egreso1.value = egreso1;
			document.formulario.action = "pos_mov_inf.php";
			document.formulario.method = "POST";
			document.formulario.submit();

		}

		
		function Buscar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "pos_estado_cta_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
	
		function Refrescar(){
			document.formulario.tarea.value = "U";
			document.formulario.action = "pos_estado_cta_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
		function Limpiar(parametros){	
			location.href='pos_estado_cta_view.php'
		}
		
	</script>
</body>
</html>