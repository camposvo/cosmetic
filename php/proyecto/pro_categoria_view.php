<?php 
/*-------------------------------------------------------------------------------------------|
|  						Verificación Y Autentificación De Usuario.                           |
|-------------------------------------------------------------------------------------------*/ 
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
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>
				
</head>
<body>


<?php 
/*-------------------------------------------------------------------------------------------|
|	Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					 |
|-------------------------------------------------------------------------------------------*/
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Nuevo Tipo';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Nuevo Tipo';
	}
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	RUTINAS PARA ELIMINAR UNA CATEGORIA DE PROYECTO
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		
		$ls_sql = "SELECT fk_tipo_rubro FROM t02_proyecto where fk_tipo_rubro = '$pk_tipo_rubro'";	
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM t08_tipo_proyecto WHERE pk_tipo_rubro ='$pk_tipo_rubro'";	
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Categoria Eliminada Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar este Registro, ya que esta Asociado a un Proyecto!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		
		$modo = 'Insertar Nuevo Registro';
		
	}	

/*-------------------------------------------------------------------------------------------|
|		Rutina: Permite Cargar En La Interfaz Los Registros De La Tabla 't07_marca' 	
|-------------------------------------------------------------------------------------------*/	
	$ls_sql = " SELECT nb_tipo_rubro, tx_descripcion, pk_tipo_rubro 
					FROM t08_tipo_proyecto ORDER BY nb_tipo_rubro";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}
	
	$color_modo = $modo == "Editar Registro" ?"widget-color-orange":"widget-color-green";
	

?>
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button class="hidden-480  btn-success btn-sm pull-left " onclick="Agregar()">
							<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
							Tipo de Proyecto
						</button>
					</div>
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->				
													
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Tipos
							</div>
							<form class="form-horizontal" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>Nombre</th>
											<th>Descripcion</th>
											<th></th>										
										</tr>
									</thead>
									<tbody>	
										<?php    
											$li_numcampo = 0; // Columnas Que Se Muestran En La Tabla.
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia Al Índice De La Columna Clave.
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_TIPO_PROYECTO'); // Dibuja La Tabla De Datos.
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado); 
										?>
									</tbody>
								</table>

								<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
								<input type="hidden" name="pk_tipo_rubro" value="<?php echo $pk_tipo_rubro;?>">       			
							</form>
														
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- /.main-content-inner -->
		
	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>	
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
						{ "orderable": false }
					  ]
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


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript"> 
		function Agregar(){
			document.formulario.tarea.value = "A";
			document.formulario.action = "pro_categoria_mtto.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}		
/*-------------------------------------------------------------------------------------------|
|	Función: 'Eliminar'																	
|	Descripción: Permite Eliminar Una Marca De La Base De Datos.	 					 	
|-------------------------------------------------------------------------------------------*/	
	function Eliminar(pk_tipo_rubro){
		if (confirm('¿Realmente Desea Eliminar Este Tipo?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.action = "pro_categoria_view.php";
			document.formulario.pk_tipo_rubro.value = pk_tipo_rubro;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Editar'																	 	
|	Descripción: Permite En La Misma Página Editar Los Datos De Una Marca. 					 
|-------------------------------------------------------------------------------------------*/	
	function Editar(pk_tipo_rubro){
		document.formulario.tarea.value = "M";
		document.formulario.action = "pro_categoria_mtto.php"
		document.formulario.pk_tipo_rubro.value = pk_tipo_rubro;
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Limpiar'																 		 
|	Descripción: Limpia La Información Introducida. 										
|-------------------------------------------------------------------------------------------*/			
	function Limpiar(){
		document.formulario.tarea.value = "X";
		document.formulario.action = "pro_categoria_mtto.php"
		document.formulario.pk_tipo_rubro.value = '';
		document.formulario.o_nombre.value = '';
		document.formulario.method = "POST";
		document.formulario.submit();
	}	
	
	</script>

</body>
</html>