<?php 
/*--------------------------------------------------------------------------------------------------|
|  	Nombre: 'ma_ubicaciones.php'                                            						|
|	Descripción: Esta Interfaz Permite Ver, Editar y Agregar Ubicacion Asociandolo a Un Almacen	.	|
|--------------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------|
|  						Verificación Y Autentificación De Usuario.                           |
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
<title>La Peperana</title>
	
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
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	$parametros = "tarea=B";
/*-------------------------------------------------------------------------------------------|
|						Rutina: Se Modifica O Agrega Una Ubicación							 |
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		
		if($co_ubicacion==''){
			$ls_sql = " SELECT pk_ubicacion FROM t10_ubicacion 
						WHERE (nb_ubicacion) = '".strtoupper($o_ubicacion)."' AND fk_almacen=$co_almacen";
						
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
			if($ls_resultado != 0){
				if($obj_miconexion->fun_numregistros() == 0){
		
					$ls_sql = " INSERT INTO t10_ubicacion (fk_almacen, nb_ubicacion)	
								VALUES ('$co_almacen', '".strtoupper($o_ubicacion)."')";
				
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado == 0){
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}else{
						// ¡Nueva Ubicación Ingresada Satisfactoriamente!
					}
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('¡Nombre Duplicado!');</script>";
				}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}else{
			$ls_sql = " UPDATE t10_ubicacion SET nb_ubicacion = '".strtoupper($o_ubicacion)."'
						WHERE pk_ubicacion = $co_ubicacion";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				echo "<script language='javascript' type='text/javascript'>alert('¡Ubicación Actualizada Satisfactoriamente!');</script>";
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}
		$co_ubicacion = '';
		$o_ubicacion = '';
	}
/*-------------------------------------------------------------------------------------------|
|								Rutina: Para Eliminar Una Ubicación							 |
|-------------------------------------------------------------------------------------------*/	
	if ($tarea == "E"){
		$ls_sql = "SELECT * FROM  t01_movimiento WHERE fk_ubicacion= '$co_ubicacion' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM t10_ubicacion WHERE pk_ubicacion = '$co_ubicacion' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Ubicación Eliminada Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Esta Ubicacón Esta Asociada A Diferentes Productos!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}
/*-------------------------------------------------------------------------------------------|
|				 			Rutina: Carga Datos Del Almacén Asociado.						 |
|-------------------------------------------------------------------------------------------*/
	$ls_sql = " SELECT nb_almacen FROM t09_almacen WHERE pk_almacen = $co_almacen";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$o_almacen = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}
/*-------------------------------------------------------------------------------------------|
|				 		Rutina: Coloca Los Datos En Modo Edición.							 |
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql = " SELECT nb_ubicacion FROM t10_ubicacion WHERE pk_ubicacion = $co_ubicacion";	
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_ubicacion = $row[0];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}
/*-------------------------------------------------------------------------------------------|
|		Rutina: Permite Cargar En La Interfaz Los Registros De La Tabla 'c010t_ubicaciones'  |
|-------------------------------------------------------------------------------------------*/	
	$ls_sql = " SELECT t10_ubicacion.pk_ubicacion, t10_ubicacion.nb_ubicacion, t10_ubicacion.pk_ubicacion
				FROM t10_ubicacion
				INNER JOIN t09_almacen ON t10_ubicacion.fk_almacen= t09_almacen.pk_almacen
				WHERE t10_ubicacion.fk_almacen = $co_almacen
				ORDER BY pk_ubicacion";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}
/*---------------------------------------------------------------------------------------------|
|          						Fin De Rutinas Para El Mantenimiento.                          |
|---------------------------------------------------------------------------------------------*/
?>



<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Ubicaciones
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
					<div class="space-6"></div>	
								
					<div class="row">
						<div class="col-xs-12 col-sm-6 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> Crear Ubicacion </h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
												
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Almacen</label>
												<div class="col-sm-7" >
													<input readonly name="o_almacen" value="<?php echo $o_almacen;?>" id="factura" type="text" class="form-control">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_factura">Ubicacion</label>
												<div class="col-sm-7" >
													<input name="o_ubicacion" value="<?php echo $o_ubicacion;?>" id="factura" type="text" class="form-control" placeholder="Nombre de la Ubicacion">
												</div>
											</div>
											
											<div class="form-group center">												
												<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
													Regresar
												</button>
												
												<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
									
										
										</div>
										
										<input type="hidden" name="tarea" value="<?php echo $tarea;?>"> 
										<input type="hidden" name="co_almacen" value="<?php echo $co_almacen;?>">
										<input type="hidden" name="co_ubicacion" value="<?php echo $co_ubicacion;?>">   
											
										</form>
									</div>
								</div>
							</div>
						</div>	
							
							<div class="space-4"></div>
							
							
					
						
					<div class="row">
						<div class="col-xs-12">
							<table id="simple-table" class="table table-striped table-bordered table-hover">
								<thead>
								<tr class="bg-primary" >
									<th>Código</th>
									<th>Descripción</th>
									<th>Editar</th>
									<th>Eliminar</th>
								</tr>
							</thead>
							<tbody>	
								<?php   
									$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Columnas Que Se Muestran En La Tabla.
									$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Referencia Al Índice De La Columna Clave.
									fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTAR_UBICACIONES"); // Dibuja La Tabla De Datos.
									$obj_miconexion->fun_closepg($li_id_conex); 
								?>
							</tbody>
						</table>
					</div> 
					</div>

				
				</div>
			
		</div> <!-- /.page-content -->


</body>

<!--  SISTEMA   -->	
<script src="../../js/funciones.js"></script>  
<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		
	<script src="../../assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/dataTables.tableTools.min.js"></script>
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




				
				



<script type="text/javascript">
	    
   // When the document is ready
        
	$(document).ready(function () {
		$('.date').datepicker({
		});
		
		window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical

	
	});
       
	    
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (confirm('¿Está Conforme Con Los Datos Ingresados?') == true){	
				document.formulario.tarea.value = "A";
				document.formulario.action = "alm_ubicacion.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Eliminar'																		 |
|	Descripción: Permite Eliminar Una Ubicación De La Base De Datos.					 	 |
|-------------------------------------------------------------------------------------------*/		
	function Eliminar(co_ubicacion){
		if (confirm('¿Realmente Desea Eliminar Esta Ubicación?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.action = "alm_ubicacion.php";
			document.formulario.co_ubicacion.value = co_ubicacion;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Editar'																		 |
|	Descripción: Permite Editar Una Ubicación.	 		 									 |
|-------------------------------------------------------------------------------------------*/		
	function Editar(co_ubicacion){
	  document.formulario.tarea.value = "M";
	  document.formulario.action = "alm_ubicacion.php";			
	  document.formulario.co_ubicacion.value = co_ubicacion;
	  document.formulario.method = "POST";
	  document.formulario.submit();
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Atras'															 	  		 |
|	Descripción: Permite Regresar A La Página De Maestro De Almacenes.						 |
|-------------------------------------------------------------------------------------------*/
	function Atras(parametros){
		location.href = "alm_almacen_view.php?" + parametros;
	}	
	
</script>
 

</html>