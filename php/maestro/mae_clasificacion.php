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
|				Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					 |
|-------------------------------------------------------------------------------------------*/
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nueva Clasificacion';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nueva Clasificacion';
	}
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$arr_categoria      =   Combo_Categoria_Articulo();
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Para Agregar Una Nueva Marca						 	 |
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		$o_nombre = mb_convert_case($o_nombre, MB_CASE_TITLE, "UTF-8"); 
		if($pk_clase==''){
			$ls_sql = "SELECT pk_clase FROM t05_clase	WHERE UPPER(nb_clase) = '".strtoupper($o_nombre)."'	AND fk_categoria = ".$o_categoria.";";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
			if($ls_resultado != 0){
				if($obj_miconexion->fun_numregistros() == 0){
					
					
					$ls_sql = " INSERT INTO t05_clase (nb_clase, fk_categoria) 
								VALUES ('".$o_nombre."',".$o_categoria.")";
					
					//echo $ls_sql ;
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado == 0){
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
					}else{echo "<script language='javascript' type='text/javascript'>alert('¡Nueva Clase Ingresada Satisfactoriamente!');</script>";}
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('¡Nombre Duplicado!');</script>";
				}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}else{
			$ls_sql = " UPDATE t05_clase SET fk_categoria = $o_categoria, nb_clase = '$o_nombre' WHERE pk_clase = $pk_clase ";

			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				echo "<script language='javascript' type='text/javascript'>alert('Clase Actualizada Satisfactoriamente!');</script>";
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}
		$pk_clase = '';
		$o_nombre = '';
		$modo = 'Insertar Nueva Clasificacion';
	}
	
/*-------------------------------------------------------------------------------------------|
|								Rutina: Para Eliminar Una Marca		
|-------------------------------------------------------------------------------------------*/	
	if ($tarea == "E"){
		
		$ls_sql = "SELECT * FROM t13_articulo WHERE fk_clase= '$pk_clase' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM t05_clase WHERE pk_clase = '$pk_clase'";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Proveedor Eliminado Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Este Clasificacion esta Asociado a un Producto!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		
		$modo = 'Insertar Nueva Clasificacion';
		
	}
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Permite Colocar Los Datos En Modo Edición En La Misma Página			 |
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT nb_clase, fk_categoria FROM t05_clase WHERE pk_clase = $pk_clase";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_nombre = $row[0];
			$o_categoria = $row[1];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$modo = "Editar Clasificacion";
	}
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Permite Cargar En La Interfaz Los Registros De La Tabla 't05_clase' 		 |
|-------------------------------------------------------------------------------------------*/	
	$ls_sql = " SELECT nb_clase, nb_categoria, pk_clase 
	FROM t05_clase 
	INNER JOIN t21_categoria ON t05_clase.fk_categoria =  t21_categoria.pk_categoria
	ORDER BY pk_clase";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
	}	
	
	$color_modo = $modo == "Editar Clasificacion" ?"widget-color-red":"widget-color-green";
	
?>
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					 <?php echo $modo?>
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
				
					<div class="row">
						<div class="col-xs-12 ">
								<div class="alert alert-block alert-success">							
									<i class="ace-icon fa fa-check green"></i>							
									<strong class="green">
										La Clasificacion
									</strong>,
									 Representa un nivel medio de jerarquia, que permite agrupar los articulos dependiendo de una tipificacion
								</div>
						</div>
					</div>
				
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								
								
																
								
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Clasificacion</label>
												<div class="col-sm-6">
													<input  class="input-sm form-control" name="o_nombre"  value="<?php echo $o_nombre;?>" id="factura" placeholder="Clasificacion" type="text"  />
												</div>
											</div>
										
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Categoria</label>
												<div class="col-sm-7" >	
													<select name="o_categoria" class="col-xs-10 col-sm-7 " data-placeholder="Selecciona un Cliente...">
														<?php
															if ($o_categoria == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_categoria as $k => $v) {
																$ls_cadenasel =($k == $o_categoria)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>							
													</select>
												</div>													
											</div>

											
											<div class="form-group center">
												<button type="button" onClick="Atras();" class="btn btn-sm btn-danger">
													<i class="ace-icon fa fa-arrow-left icon-on-right bigger-110"></i>
													Atras
												</button>		
												<button type="button" onClick="Limpiar();" class="btn btn-sm btn-info">
													<i class="ace-icon fa fa-undo   bigger-110 icon-on-right"></i>
													Limpiar
												</button>
												
												<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
											
											<div class="space-4"></div>
											
											<input type="hidden" name="pk_clase" value="<?php echo $pk_clase;?>">
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" value="<?php echo $modo;?>">   				
										
									</form>
									</div>
								</div>
							</div>
						</div>
					
					</div>	
						
								
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Clasificacion
							</div>

							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>ID</th>
										<th>Clasificacion</th>
										<th>Categoria</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php    
										$li_numcampo = 0; // Columnas Que Se Muestran En La Tabla.
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia Al Índice De La Columna Clave.
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_CLASE'); // Dibuja La Tabla De Datos.
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
</div> <!-- /.main-content-inner -->

	<script src="../../js/funciones.js"></script>
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<!-- page specific plugin scripts -->
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>	
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>	
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
		function Guardar(){
			if(campos_blancos(document.formulario) == false){
				if (confirm('¿Está Conforme Con Los Datos Ingresados?') == true){	
					document.formulario.tarea.value = "A";
					document.formulario.action = "mae_clasificacion.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		}
	
		function Eliminar(pk_clase){
			if (confirm('¿Realmente Desea Eliminar Esta Marca?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.action = "mae_clasificacion.php";
				document.formulario.pk_clase.value = pk_clase;
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	/*-------------------------------------------------------------------------------------------|
	|	Función: 'Editar'																	 	 |
	|	Descripción: Permite En La Misma Página Editar Los Datos De Una Marca. 					 |
	|-------------------------------------------------------------------------------------------*/	
		function Editar(pk_clase){
			document.formulario.tarea.value = "M";
			document.formulario.action = "mae_clasificacion.php"
			document.formulario.pk_clase.value = pk_clase;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	/*-------------------------------------------------------------------------------------------|
	|	Función: 'Limpiar'																 		 |
	|	Descripción: Limpia La Información Introducida. 										 |
	|-------------------------------------------------------------------------------------------*/			
		function Limpiar(){
			document.formulario.modo.value = "Insertar Nuevo Registro";
			document.formulario.tarea.value = "X";
			document.formulario.action = "mae_clasificacion.php"
			document.formulario.pk_clase.value = '';
			document.formulario.o_nombre.value = '';
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Atras(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "mae_articulo_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
	</script>

</body>
</html>