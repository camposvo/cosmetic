<?php 
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("gan_utilidad.php");
	$usu_autentico = isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI"){
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
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />	
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>	
				
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------|
|	Rutina: Se Utiliza Para Recibir Las Variables Por La URL.                 
|-------------------------------------------------------------------------------------------*/
	if (!$_GET){
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
	
	
	$arr_tipo   =  Combo_Tipo_Diagnostico();
	
/*-------------------------------------------------------------------------------------------|
|	LEER UN REGISTRO DE LA TABLA DIAGNOSTICO 		 
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT tx_descripcion, tx_comentario, in_tipo_diagnostico
				   FROM   gan_diagnostico
				   WHERE pk_diagnostico = '$pk_diagnostico'";
			
		//echo $ls_sql;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado != 0){
			$row           = pg_fetch_row($ls_resultado,0);
			$o_descripcion      = $row[0];
			$x_comentario  = $row[1];
			$o_tipo       = $row[2];
			
		$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}		
	}
	
/*-------------------------------------------------------------------------------------------|
|	ACTUALIZA UNA VACUNA	
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "B"){
		$error_sql = false;
		$o_descripcion = strtoupper($o_descripcion);
		$ls_sql = "SELECT pk_diagnostico FROM gan_diagnostico 
					WHERE (tx_descripcion='$o_descripcion') AND pk_diagnostico <> '$pk_diagnostico' "; 
		
		//echo $ls_sql;
		$o_descripcion =  $o_descripcion==''?'NULL':"$o_descripcion";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	
       							
				$ls_sql = "UPDATE gan_diagnostico SET 
								tx_descripcion = '".strtoupper($o_descripcion)."',           
								tx_comentario  = '".strtoupper($x_comentario)."',     
								in_tipo_diagnostico = '".ucwords($o_tipo)."'
				 		   WHERE pk_diagnostico = '$pk_diagnostico'";
				
				if ($obj_miconexion->fun_consult($ls_sql)== 0)	{
					$error_sql = true;
				}

				$parametros = "tarea=B&pk_diagnostico=$pk_diagnostico";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Satisfactoriamente!');location.href='gan_vacuna_view.php?$parametros';</script>";

			}else{
				$msg = "¡Nombre ya Esta Registrado!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$tarea = "M";  // Vuelve A Colocar La Tarea.
	}
/*-------------------------------------------------------------------------------------------|
|	INSERTAR UN REGISTRO EN LA TABLA VACUNA
|-------------------------------------------------------------------------------------------*/		
	if($tarea == "A"){
		
		$ls_sql ="SELECT pk_diagnostico FROM gan_diagnostico WHERE tx_descripcion='$o_descripcion' or UPPER(tx_descripcion) = '".strtoupper($o_descripcion)."'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){ // Si La Cedula No Existe Procede A Insertar.
				
				
				$ls_sql = "INSERT INTO gan_diagnostico(tx_descripcion, tx_comentario, in_tipo_diagnostico) 
							VALUES('".strtoupper($o_descripcion)."','".strtoupper($x_comentario)."','".strtoupper($o_tipo)."')";			
				
				//echo $ls_sql; 
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);	
				if ($ls_resultado != 0){
					$parametros = "tarea=B";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Ingresados Satisfactoriamente!');location.href='gan_diagnostico_view.php?$parametros';</script>";

				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
				}
								
			}else{
				$msg = "¡El Nombre, Ya Estan Registrados!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
				$tarea = "A";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}
	
	$obj_miconexion->fun_closepg($li_id_conex);
	$parametros = !isset($pk_proveedor)?'tarea=X':'pk_proveedor='.$pk_proveedor.'&tarea=B';

?>


<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Diagnosticos
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title">Crear Diagnostico</h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Descripcion </label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_descripcion" value="<?php echo $o_descripcion;?>"  type="text"  placeholder="Ingrese Nombre">
												</div>
											</div>  
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Tipo</label>
												<div class="col-sm-7" >	
													<select name="o_tipo" class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Selecciona un Cliente...">
														<?php
															if ($o_tipo == ""){
																echo "<option value='0' selected></option>";
															}else{
																echo "<option value='0'></option>";
															}
															foreach($arr_tipo as $k => $v) {
																$ls_cadenasel =($k == $o_tipo)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>							
													</select>
												</div>													
											</div>
											
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Comentario</label>
												<div class="col-sm-9" >
													<textarea name="x_comentario" cols="10" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_comentario;?></textarea>
												</div>
											</div>
											
											
										</div>	
										
										<div class="form-group center">												
											<button type="button" onClick="location.href = 'gan_diagnostico_view.php'" class="btn btn-sm  btn-danger">
												<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
												Regresar
											</button>
											
											<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
												<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
												Guardar
											</button>																								
										</div>
											
										<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
										<input type="hidden" name="pk_diagnostico" value="<?php echo $pk_diagnostico; ?>">
										
										</form>
									</div>
								</div>
						</div>
					</div>
					
				</div>
			</div> <!-- /.row tabla principal -->
		</div> <!-- /.page-content -->
</body>

<!--  SISTEMA   -->	
	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>



<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
		} );
		
	</script>
				
				

<script type="text/javascript">
	
/*------------------------------------------------------------------------------------------------|
|	Función 1: 'Guardar'															 	  		  |
|	Descripción: Permite Guardar Los Cambios Echos En El Formulario y/o Guardar Nuevos Proveedores|
|------------------------------------------------------------------------------------------------*/			
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (document.formulario.tarea.value == "M"){
				document.formulario.tarea.value =  "B";
			}else{
				document.formulario.tarea.value =  "A";
			}	
			document.formulario.method = "POST";
			document.formulario.action = "gan_diagnostico_add.php";
			document.formulario.submit();
		}
	}
	
</script>
</html>