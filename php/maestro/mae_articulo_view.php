<?php 

	session_start();
	include_once ("mae_utilidad.php");
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
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>
				
</head>
<body>

<?php 
/*------------------------------------------------------------------------------------------------|
|		Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					  |
|------------------------------------------------------------------------------------------------*/
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
	
/*------------------------------------------------------------------------------------------------|
|					Rutina: Se Utiliza Para Eliminar Un Proveedor.							 	  
|------------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT * FROM t01_detalle WHERE fk_articulo= '$pk_articulo' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM t13_articulo WHERE pk_articulo = '$pk_articulo' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "Articulo Eliminado Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Este Articulo Esta Asociado!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}

	
/*------------------------------------------------------------------------------------------------|
	LEE LOS ARTICULOS DE LA BASE DE DATOS
|------------------------------------------------------------------------------------------------*/

	$ls_sql = "SELECT nb_articulo, nb_clase, nb_categoria, pk_articulo
		FROM t13_articulo 
		INNER JOIN t05_clase ON t13_articulo.fk_clase =  t05_clase.pk_clase
		INNER JOIN t21_categoria ON t05_clase.fk_categoria =  t21_categoria.pk_categoria
		ORDER BY nb_articulo";
	
	//echo $ls_sql ;
				
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado) != 0){
			//Sin Error
		}else{
			
		}	
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}	

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Articulos
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<button class="btn-sm btn-success" onclick="Agregar_articulo()">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Articulo 
							</button>
							<button class="btn-sm btn-info" onclick="Agregar_Clase()">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Clasificacion
							</button>
							<button class="btn-sm btn-info" onclick="Agregar_Categoria()">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Categoria 
							</button>
						</div>
					</div>	
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Catalogo de Articulos
							</div>
							<form class="form-horizontal" name="formulario">
							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Nombre</th>
										<th class="hidden-480">Clasificacion</th>
										<th class="hidden-480">Categoria</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-4;  // Columnas Que Se Muestran En La Tabla.
										$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Referencia Al Índice De La Columna Clave.
										fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTAR_ARTICULO"); // Dibuja La Tabla De Datos.
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado); // Cierra Conexión.
									?>
								</tbody>
							</table>
							<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
							<input type="hidden" name="pk_articulo" value="<?php echo $pk_articulo;?>">   

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
						null,
						{ "orderable": false }
					  ]
				} );
		
				//or change it into a date range picker
				$('.input-daterange').datepicker({
					
					autoclose:true,
					format: "dd/mm/yyyy"
					
				});
				
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

		
	<script src="../../js/funciones.js"></script>
	
	<script type="text/javascript"> 
		function Agregar_articulo(){
			document.formulario.tarea.value = "A";
			document.formulario.action = "mae_articulo_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
		function Agregar_Clase(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "mae_clasificacion.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
		function Agregar_Categoria(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "mae_categoria.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}


		function Editar(pk_articulo){
			document.formulario.tarea.value = "M";
			document.formulario.action = "mae_articulo_add.php";
			document.formulario.pk_articulo.value = pk_articulo;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
		function Eliminar(pk_articulo){
			if (confirm("¿Realmente Desea Eliminar Este Articulo?")){ 			
				document.formulario.tarea.value = "E";
				document.formulario.action = "mae_articulo_view.php";
				document.formulario.pk_articulo.value = pk_articulo;
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}		
	</script>

</body>
</html>

