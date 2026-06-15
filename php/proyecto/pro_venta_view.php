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
		
/*-------------------------------------------------------------------------------------------
	RUTINAS: permite MOSTRAR LOS DATOS
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT tx_nombre, to_char(fe_inicial, 'dd/mm/yyyy') , nu_cantidad, fk_responsable, 
				t02_proyecto.tx_descripcion, fk_tipo_rubro, nu_muerte, t08_tipo_proyecto.nb_tipo_rubro
		FROM t02_proyecto
		LEFT JOIN t08_tipo_proyecto ON t02_proyecto.fk_tipo_rubro = t08_tipo_proyecto.pk_tipo_rubro
		WHERE pk_proyecto = $pk_proyecto";
		
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$o_nombre        = $row[0];
		$x_fecha	     = $row[1];
		$o_cantidad  	 = $row[2];	
		$x_responsable   = $row[3];
		$o_descripcion   = $row[4];
		$o_tipo_rubro    = $row[5];
		$x_muerte        = $row[6];
		$x_tipo_rubro  = $row[7];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}

/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR una actividad Factura
-------------------------------------------------------------------------------------------*/
	$i=0; /*Banderar para cantidad de reglas */
	$sw = 0; // Bandera para indicar si hay filtros

	if($filtro =='NO_ALL')$arr_criterio[$i++]=" (f_calcular_abono(pk_factura) < nu_total) "; /* solo los que deben*/
	if($x_cliente!=0){ $arr_criterio[$i++]=" t20_factura.fk_cliente = ".$x_cliente; $sw = 1;}
	if($x_factura!=0){ $arr_criterio[$i++]=" UPPER(t20_factura.tx_factura) = '".strtoupper($x_factura)."' "; $sw = 1; }
	if($x_fecha_ini !=0 and $x_fecha_fin !=0){ $arr_criterio[$i++]=" t20_factura.fe_fecha_factura >= '".strtoupper($x_fecha_ini)."' and t20_factura.fe_fecha_factura <= '".strtoupper($x_fecha_fin)."' "; $sw =1; }
	if($x_vendedor !=0){ $arr_criterio[$i++]=" t20_factura.fk_responsable = ".$x_vendedor; $sw =1; }
		
	for($j=0;$j<$i;$j++)$ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];	
	$ls_criterio = $ls_criterio==""?"":" and ".$ls_criterio;	

/*-------------------------------------------------------------------------------------------
RUTINAS: Consulta  de registros de la busqueda
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT to_char(pk_factura,'0000000'), to_char(t20_factura.fe_fecha_factura, 'dd-TMMon-yyyy'),  UPPER(CLIENTE.tx_nombre) AS client,
			UPPER(VENDEDOR.tx_nombre) AS vend, nb_articulo, t01_detalle.nu_cant_item, t01_detalle.nu_cantidad, 
			nu_precio,  t01_detalle.nu_cantidad * nu_precio as total 
  FROM t01_detalle
   INNER JOIN t20_factura ON t20_factura.pk_factura    = t01_detalle.fk_factura
   INNER JOIN t02_proyecto ON t02_proyecto.pk_proyecto = t01_detalle.fk_rubro 
   INNER JOIN t13_articulo ON t13_articulo.pk_articulo = t01_detalle.fk_articulo
   INNER JOIN s01_persona AS VENDEDOR ON VENDEDOR.co_persona = t20_factura.fk_responsable
   INNER JOIN s01_persona AS CLIENTE ON CLIENTE.co_persona = t20_factura.fk_cliente 
   WHERE t01_detalle.fk_rubro = ".$pk_proyecto;
	

	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
							<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
							Regresar
						</button>			
					</div>
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12">
				
					
				<div class="row">
					<div class="col-xs-12 ">
							<div class="alert alert-block alert-success">							
								<i class="ace-icon fa fa-check green"></i>							
								<strong class="green">
									<?php echo 'Proyecto: '.strtoupper($o_nombre);?> ( <?php echo $x_fecha;?> )
								</strong>,
								<?php echo $o_descripcion;?>.
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
												<tr class="success">
													<th>Item</th>
													<th>Cantidad</th>
													<th>Total</th>
												</tr>
											</thead>
											<tbody>	
												<tr>
													<td >
														<input id= 'x_sum_item' class="input-sm form-control" name="x_sum_item"  type="text" readonly />
													</td>
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
									
									<form class="form-horizontal" name="formulario">											
											
											<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
											<input type="hidden" name="tarea" 		 value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" 		 value="<?php echo $modo;?>">
											<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">		
											<input type="hidden" id = "input_filtro" name="input_filtro" 		 value="<?php echo $input_filtro;?>">		
										</form>
									
								</div>
							</div>
						</div>	
					</div>	<!-- /.row totales -->
					
					
							
					<div class="row">
						<div class="col-xs-12">	
							<div class="table-header">
								Resumen de Ventas
							</div>
							
							<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr>
										<th>Factura</th>
										<th>Fecha</th>
										<th>Cliente</th>
										<th>Vendedor</th>
										<th>Articulo</th>
										<th>Item</th>
										<th class="hidden">Cantidad</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->
										<th>Cantidad</th>
										<th>Precio</th>
										<th class="hidden">Total</th> <!-- Se utiliza una columna oculta para visualizar el formato numerico correcto  -->
										<th>Total</th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										if($tarea == "M"){
											$li_hidden = 0; /* Cantidad de columnas que se van A OCULTAR cuando la pantalla es pequeña*/
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_VENTA_PROYECTO',0); // Dibuja la Tabla de Datos
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
					null,
					null,
					null,
					null					
				  ],
				  
				  
				// CALCULA LA SUMA RESUMEN POR FILTRO Y EL TOTAL  
				"fnFooterCallback": function ( row, data, start, end, display ) {					
					var api = this.api(), data;

					Filtroitem = api.column(5, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );
						
					FiltroCantidad = api.column(6, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );	
					
					FiltroTotal = api.column(9, { search: 'applied'} ).data()	.reduce( function (a, b) {
							 return parseFloat(a) + parseFloat(b); 
						}, 0 );			
							 								
					Filtroitem = formato_numero(Filtroitem, 0, ',', '.');
					FiltroCantidad = formato_numero(FiltroCantidad, 2, ',', '.');
					FiltroTotal  = formato_numero(FiltroTotal, 2, ',', '.');
					
					$('#x_sum_item').val(Filtroitem);
					$('#x_sum_cantidad').val(FiltroCantidad);
					$('#x_sum_total').val(FiltroTotal);				
				}
				  				  
			} );
			
			// Toma el valor del Filtro 
			var table = $('#dynamic-table').DataTable(); 
			table.on( 'search.dt', function () {
				$('#input_filtro').val(table.search());
			} );
				
			
						
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

		function Mostrar_Info(identificador){
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "adm_venta_info.php";
			//document.formulario.action = "content-slider.html";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		
		function Buscar(filtro){
			document.formulario.filtro.value = filtro;	
			document.formulario.tarea.value = "B";
			document.formulario.action = "adm_venta_view.php"
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Atras(parametros){
			location.href = "pro_proyecto_view.php?" + parametros;
		}	
		
		
		function Limpiar(parametros){	
			location.href='adm_venta_view.php'
		}
		
	</script>
</body>
</html>