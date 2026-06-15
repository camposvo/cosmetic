<?php 
/*--------------------------------------------------------------------------------------------------|
|	Nombre: 'pro_catalogo.php'                                            							|
|	Descripción: Esta Interfaz Permite Ver y Agregar Productos Al Catálogo							|
|--------------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------|
|							Verificación Y Autentificación De Usuario.						 |
|-------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("alm_utilidad.php");
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
		
	<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	
	<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/chosen.min.css" /> -->
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/bootstrap-timepicker.min.css" /> -->
		<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.min.css" /> -->
		<!-- <link rel="stylesheet" href="../../assets/css/colorpicker.min.css" /> -->

	<!-- text fonts -->
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="../../assets/js/ace-extra.min.js"></script>
				
</head>
<body>

<script language="javascript" type="text/javascript">
//SCRIPT: Función Que Invoca La Página Adecuada Según La Acción Del Usuario.

/*-------------------------------------------------------------------------------------------|
|	Función: 'Buscar'																		 |
|	Descripción: Busca La Información De Los Productos En El Catalogo De Productos.		 	 |
|-------------------------------------------------------------------------------------------*/	
	function Buscar(){	
		document.formulario.tarea.value = "B";
		document.formulario.action = "alm_catalogo_view.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}

/*-------------------------------------------------------------------------------------------|
|	Función: 'Detalles'																		 |
|	Descripción: Envía A La Página Que Muestra Todos Los Detalles Del Producto.		 		 |
|-------------------------------------------------------------------------------------------*/
	function Detalle(co_tipo_clase, co_marca){	
		document.formulario.tarea.value = "D";
		document.formulario.action = "alm_catalogo_detalle.php";
		document.formulario.co_marca.value = co_marca;
		document.formulario.co_tipo_clase.value = co_tipo_clase;
		document.formulario.method = "POST";
		document.formulario.submit();
	}
</script>

<?php
/*-------------------------------------------------------------------------------------------|
|				Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					 |
|-------------------------------------------------------------------------------------------*/


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
	
	$arr_almacen   =  Combo_Almacen();
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	

	
/*-------------------------------------------------------------------------------------------|
|						Rutina: Se Modifica O Agrega Una Ubicación							 |
|-------------------------------------------------------------------------------------------*/
	
	$i=0;
	$li_tampag = 50;
	
	if($x_almacen!=0)$arr_criterio[$i++]=" t09_almacen.pk_almacen = ".$x_almacen;
	if($o_ubicacion!=0)$arr_criterio[$i++]=" t10_ubicacion.pk_ubicacion = ".$o_ubicacion;
	
	for($j=0;$j<$i;$j++)$ls_criterio = $ls_criterio.($ls_criterio==""?"":" and ").$arr_criterio[$j];

	$ls_criterio = $ls_criterio==""?"":" and ".$ls_criterio;
	
	$ls_sql = " SELECT t05_clase.nb_clase, t13_articulo.nb_articulo, UPPER(tx_observacion), t09_almacen.nb_almacen, t10_ubicacion.nb_ubicacion,   
				to_char(fe_fecha_factura, 'dd/mm/yyyy'), t01_movimiento.nu_cantidad, nu_precio,  
				t01_movimiento.nu_cantidad * nu_precio AS total, pk_movimiento
		FROM t01_movimiento 
			LEFT JOIN t10_ubicacion ON t10_ubicacion.pk_ubicacion = t01_movimiento.fk_ubicacion
			LEFT JOIN t09_almacen ON t09_almacen.pk_almacen = t10_ubicacion.fk_almacen
			INNER JOIN t02_proyecto ON t02_proyecto.pk_proyecto = t01_movimiento.fk_rubro
			INNER JOIN t13_articulo ON t13_articulo.pk_articulo = t01_movimiento.fk_tipo_clase
			INNER JOIN t05_clase ON t13_articulo.fk_clase = t05_clase.pk_clase
		WHERE t01_movimiento.tx_tipo='GASTO' AND t05_clase.pk_clase IN (9)
		ORDER BY t01_movimiento.tx_factura "; 
		
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
	if($ls_resultado != 0){
		
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}

?>


<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Catalogo de Clasificaciones
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
										
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Clasificacion y tipos
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>Clase</th>
											<th>Tipo</th>
											<th>Descripcion</th>
											<th>Almacen</th>
											<th>Ubicacion</th>
											<th>Fecha</th>
											<th>Cantidad</th>
											<th>Precio</th>
											<th>Total</th>
											<th>Colocar</th>
										</tr>
									</thead>
									<tbody>	
										<?php   
											$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Columnas Que Se Muestran En La Tabla.
											$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Referencia Al Índice De La Columna Clave.
											fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTAR_INVENTARIO"); // Dibuja La Tabla De Datos.
											$obj_miconexion->fun_closepg($li_id_conex); 
										?>
									</tbody>
								</table>
								<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
								<input type="hidden" name="co_marca" value="<?php echo $co_marca;?>">
								<input type="hidden" name="co_tipo_clase" value="<?php echo $co_tipo_clase;?>">
						</form>
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->






		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		
		<script src="../../assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<!-- <script src="../../assets/js/dataTables.tableTools.min.js"></script> -->
		<!-- <script src="../../assets/js/dataTables.colVis.min.js"></script> -->
		<script src="../../assets/js/daterangepicker.min.js"></script>

		
	<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<!-- <script src="../../assets/js/jquery.ui.touch-punch.min.js"></script> -->
		<!-- <script src="../../assets/js/chosen.jquery.min.js"></script> -->
		<!--<script src="../../assets/js/fuelux.spinner.min.js"></script>  -->
		<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
		<!-- <script src="../../assets/js/bootstrap-timepicker.min.js"></script> -->
		<!-- <script src="../../assets/js/moment.min.js"></script> -->
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<!-- <script src="../../assets/js/bootstrap-datetimepicker.min.js"></script> -->
		<!-- <script src="../../assets/js/bootstrap-colorpicker.min.js"></script> -->
		<!-- <script src="../../assets/js/jquery.knob.min.js"></script> -->
		<script src="../../assets/js/jquery.autosize.min.js"></script>
		<!-- <script src="../../assets/js/jquery.inputlimiter.1.3.1.min.js"></script> -->
		<!-- <script src="../../assets/js/jquery.maskedinput.min.js"></script> -->
		<!-- <script src="../../assets/js/bootstrap-tag.min.js"></script> -->

		
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
					"oLanguage": {
						"sInfo": "Mostrando (_START_ hasta _END_) de un total _TOTAL_",
						"sSearch": "Buscar:",
						"spaginate": {
						  "next": "Próximo",
						  "previous": "Previo"
						}
					},
					
					"columns": [
						null,
						null,
						{ "orderable": false }
					  ]
				} );
		
				//or change it into a date range picker
				$('.input-daterange').datepicker({
					
					autoclose:true,
					format: "dd/mm/yyyy"
					
				});
			
			
			} );

		</script>


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript"> 

		
	
	</script>

</body>
</html>




