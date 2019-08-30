<?php 

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
<title>La Peperana</title>
		
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>
				
</head>
<body>

<?php
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
|						Rutina: Se Modifica O Agrega Una Ubicación							
|-------------------------------------------------------------------------------------------*/
	
	$i=0;
	$li_tampag = 50;
	
	
	$ls_sql = " SELECT nb_articulo, nu_cantidad, UPPER(nb_almacen), UPPER(nb_ubicacion), pk_detalle       
        FROM t01_detalle
	   LEFT JOIN  t13_articulo ON  t13_articulo.pk_articulo =  t01_detalle.fk_articulo
	   LEFT JOIN t10_ubicacion ON t10_ubicacion.pk_ubicacion = t01_detalle.fk_ubicacion
	   LEFT JOIN t09_almacen  ON t10_ubicacion.fk_almacen = t09_almacen.pk_almacen
	   INNER JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
	   WHERE t01_detalle.in_inventario = 'on'
	   ;"; 
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
	if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) != 0){
				$tarea      = "M";
			}else{
				
			}	
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}			
?>


<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Inventario
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
											<th class=''>Articulo</th>
											<th class='hidden-480'>Cantidad</th>
											<th class=''>Almacen</th>
											<th class='hidden-480'>Ubicacion</th>
											<th>Colocar</th>
										</tr>
									</thead>
									<tbody>	
										<?php   
											$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-10; // Columnas Que Se Muestran En La Tabla.
											$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Referencia Al Índice De La Columna Clave.
											fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTAR_INVENTARIO"); // Dibuja La Tabla De Datos.
											$obj_miconexion->fun_closepg($li_id_conex); 
										?>
									</tbody>
								</table>
								<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
								<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
						</form>
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->


	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>				
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
	
		function Buscar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "alm_inventario_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
			
			
			
		function Inventario(identificador){
			document.formulario.tarea.value = "X";
			document.formulario.x_movimiento.value = identificador;
			document.formulario.action = "alm_inventario_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
			
			
		function Buscar(){	
			document.formulario.tarea.value = "B";
			document.formulario.action = "alm_catalogo_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}


		function Detalle(co_tipo_clase, co_marca){	
			document.formulario.tarea.value = "D";
			document.formulario.action = "alm_catalogo_detalle.php";
			document.formulario.co_marca.value = co_marca;
			document.formulario.co_tipo_clase.value = co_tipo_clase;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
	
	</script>

</body>
</html>




