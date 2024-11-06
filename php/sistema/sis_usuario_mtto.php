<?php 
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("sis_utilidad.php");
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
		
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<script src="../../assets/js/ace-extra.min.js"></script>	
				
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
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
	
	echo 'paso';
/*-------------------------------------------------------------------------------------------
	RUTINA: variables tipo arreglo para rellenar los combos en la interfaz. (VER CONFIG.PHP)
---------------------------------------------------------------------------------------------*/
	$array_profesion  = Combo_profesion();
	$array_sexo       = Combo_Sexo();
	$array_si_no      = Combo_si_no();
	$array_condicion  = Combo_Condicion();
	
	$chk_tipo = isset($chk_tipo)?$chk_tipo:'off'; // Establece el valor del check para la Persona Juridica
	
	
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	LEER DATOS DEL USUARIO DE LA BD
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql = "SELECT tx_cedula, tx_nombre, tx_apellido, 	        
						tx_telefono_hab,   	        
						in_sexo, tx_email, tx_indicador, in_activo,tx_telefono_otro,
						tx_direccion_hab, fe_nacimiento, in_tipo_persona
					FROM s01_persona 
					WHERE co_persona = '$co_usuario'";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_cedula           = $row[0];
			$o_nombre           = $row[1];
			$x_apellido         = $row[2];
			$x_telefono_hab		= $row[3];
			$x_sexo    			= $row[4];
			$x_email       		= $row[5];
			$o_indicador        = $row[6];
			$o_activo        	= $row[7];
			$x_telefono_otro    = $row[8];
			$x_direccion		= $row[9];
			$x_nacimiento    	= $row[10];
			$chk_tipo       	= $row[11];
			

			$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}		
	}
	
/*-----------------------------------------------------------------------------------------------------------------------
	ACTUALIZAR DATOS DEL USUARIO
------------------------------------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$x_nacimiento = $x_nacimiento==''?'null':"'".$x_nacimiento."'";
		$error_sql = false;
		$o_indicador = strtolower($o_indicador);
		
		$ls_sql = "SELECT co_persona FROM s01_persona 
					WHERE (tx_cedula='$o_cedula' OR UPPER(tx_indicador)='$o_indicador') AND co_persona <> '$co_usuario' "; 
		
		$o_indicador =  $o_indicador==''?'NULL':"$o_indicador";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	

       			if( $obj_miconexion->fun_consult(" BEGIN TRANSACTION ") == 0){
					$error_sql = true;
				}
			
				$ls_sql = "UPDATE s01_persona SET 
								tx_cedula	        = '$o_cedula',           
								tx_nombre	        = '$o_nombre',           
								tx_apellido	        = '$x_apellido', 
								in_sexo             = '$x_sexo', 
								tx_telefono_hab     = '$x_telefono_hab',  
								tx_telefono_otro     = '$x_telefono_otro',  
								tx_email            = '$x_email',
								in_activo           = '$o_activo',
								tx_indicador        = '$o_indicador',
								tx_direccion_hab    =  '$x_direccion',
								fe_nacimiento       =  $x_nacimiento,
								in_tipo_persona     = '$chk_tipo'
							WHERE co_persona = '$co_usuario'";
							
							//echo $ls_sql;
				
				if ($obj_miconexion->fun_consult(($ls_sql))== 0)	{
					$error_sql = true;
				}
				
				$ls_sql = preg_replace("/'/", "\"", $ls_sql);
				$fecha = date('Y/m/d H:i');
				$ls_sql = "INSERT INTO t11_bitacora(co_persona,fe_fecha,tx_tabla,tx_accion,tx_sql) 
								VALUES ($ls_usuario,'$fecha','s01_persona','U','$ls_sq_conv')";
				
				if($obj_miconexion->fun_consult($ls_sql) == 0){
					$error_sql = true;
				}
				
				if(!$error_sql){
					$ls_resultado =  $obj_miconexion->fun_consult(" COMMIT ");
					$parametros = "tarea=B&co_usuario=$co_usuario";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('Datos Actualizados Satisfactoriamente');location.href='sis_usuario.php?$parametros';</script>";
				}else{
					$ls_resultado =  $obj_miconexion->fun_consult(" ROLLBACK ");
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
				}
			}else{
				$msg = "La Cedula y/o Indicador ya esta asignada a otro Persona!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}
		$tarea = "M";  //Vuelve a colocar la Tarea 
	}
	
/*-------------------------------------------------------------------------------------------
	LEER DATOS DEL USUARIO DE LA BD
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql = "SELECT tx_cedula, tx_nombre, tx_apellido, 	        
						tx_telefono_hab,   	        
						in_sexo, tx_email, tx_indicador, in_activo,tx_telefono_otro,
						tx_direccion_hab, fe_nacimiento, in_tipo_persona
					FROM s01_persona 
					WHERE co_persona = '$co_usuario'";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_cedula           = $row[0];
			$o_nombre           = $row[1];
			$x_apellido         = $row[2];
			$x_telefono_hab		= $row[3];
			$x_sexo    			= $row[4];
			$x_email       		= $row[5];
			$o_indicador        = $row[6];
			$o_activo        	= $row[7];
			$x_telefono_otro    = $row[8];
			$x_direccion		= $row[9];
			$x_nacimiento    	= $row[10];
			$chk_tipo       	= $row[11];
			

			$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}		
	}
	
	
/*-------------------------------------------------------------------------------------------
	INSERTAR UN USUARIO A LA BD
--------------------------------------------------------------------------------------------*/
	if($tarea == "A"){
		$x_nacimiento = $x_nacimiento==''?'null':"'".$x_nacimiento."'";
		
		
		$ls_sql ="SELECT co_persona FROM s01_persona WHERE tx_cedula='$o_cedula' or UPPER(tx_indicador) = '".strtoupper($o_indicador)."'";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			
			if ($obj_miconexion->fun_numregistros() == 0){//Si la cedula NO EXISTE procede a INSERTAR
       			
				if( $obj_miconexion->fun_consult(" BEGIN TRANSACTION ") == 0){
					$error_sql = true;
				}  

				$ls_sql = "INSERT INTO s01_persona(tx_cedula, tx_nombre, tx_apellido, tx_telefono_hab,tx_telefono_otro, 
							in_sexo, tx_email, tx_indicador, in_activo, tx_direccion_hab, fe_nacimiento, in_tipo_persona) 
					VALUES('$o_cedula', '$o_nombre','$x_apellido','','$x_telefono_otro', 
						'$x_sexo', '$x_email', '$o_indicador','$o_activo', '$x_direccion', $x_nacimiento, '$chk_tipo' )";
					
				//echo $ls_sql;
				
				if($obj_miconexion->fun_consult($ls_sql) == 0){
					$error_sql = true;
				}

				$ls_sql = preg_replace("/'/", "\"", $ls_sql);
				$fecha = date('Y/m/d H:i');
				$ls_sql = "INSERT INTO t11_bitacora(co_persona,fe_fecha,tx_tabla,tx_accion,tx_sql) 
								VALUES ($ls_usuario,'$fecha','s01_persona','I','$ls_sq_conv')";
				
				if($obj_miconexion->fun_consult($ls_sql) == 0){
					$error_sql = true;
				}
									
				if(!$error_sql){
					$ls_resultado =  $obj_miconexion->fun_consult(" COMMIT ");
					$parametros = "tarea=X";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('Usuario Ingresado Satisfactoriamente');location.href='sis_usuario_mtto.php?$parametros';</script>";										
				}else{
					$ls_resultado =  $obj_miconexion->fun_consult(" ROLLBACK ");
					$msg = $ls_sql;
					echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
				}

			}else{
				$msg = "La Cedula y/o Indicador ya esta asignada a otro Usuario!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
				$tarea = "A";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}
	}
	
	$obj_miconexion->fun_closepg($li_id_conex);

	$parametros = !isset($co_usuario)?'tarea=X':'co_usuario='.$co_usuario.'&tarea=B';

	$estado_chk = $chk_tipo=='on'?'checked':'';
	
	
?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Datos Usuario
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<form class="form-horizontal" name="formulario">
							
							<div class="tabbable">
								<ul class="nav nav-tabs padding-18">
								<!-- <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4"> -->
									<li class="active">										
										<a data-toggle="tab" href="#home4">
											<i class="green ace-icon fa fa-user bigger-120"></i>											
											Basicos
										</a>
									</li>

									<li>
										<a data-toggle="tab" href="#profile4">
											<i class="orange ace-icon fa fa-rss bigger-120"></i>
											Adicionales
										</a>
									</li>

									<li>
										<a data-toggle="tab" href="#dropdown14">
											<i class="pink ace-icon fa fa-picture-o bigger-120"></i>
											Foto
										</a>
									</li>
								</ul>

							<div class="tab-content">								
								<div id="home4" class="tab-pane in active">
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >Nombre</label>
										
										<div class="col-sm-9" >
											<input class="col-xs-10 col-sm-5" name="o_nombre" value="<?php echo $o_nombre;?>" id="o_nombre" type="text"  placeholder="Nombre">
											<span class="help-inline col-xs-12 col-sm-7">
												<label class="middle">
													<input name ="chk_tipo" class="ace" type="checkbox" id="id-disable-check" <?php echo $estado_chk;?>  />
													<span class="lbl"> Juridica</span>
												</label>
											</span>
										</div>
										
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >C.I</label>
										<div class="col-sm-9" >
											<input class="col-xs-10 col-sm-5" name="o_cedula" value="<?php echo $o_cedula;?>" id="o_cedula" type="text"  placeholder="Cedula">
										</div>
									</div>
																		
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >Indicador</label>
										<div class="col-sm-9" >
											<input class="col-xs-10 col-sm-5" name="o_indicador" value="<?php echo $o_indicador;?>" id="o_indicador" type="text"  placeholder="Indicador">
										</div>
									</div>
																																				
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Activo</label>
										<div class="col-sm-9" >	
											<select name="o_activo" type="select-one" class="col-xs-10 col-sm-5" id="o_activo">
												<?php 
													if ($o_activo == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_si_no as $k => $v) {
														$ls_cadenasel =($k == $o_activo)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>
									
								</div>

								<div id="profile4" class="tab-pane">
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha de Nacimiento</label>
										<div class="col-sm-4" >	
											<div class="input-group">
												<input name="x_nacimiento" value="<?php echo $x_nacimiento;?>" class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd/mm/yyyy" readonly />
												<span class="input-group-addon">
													<i class="fa fa-calendar bigger-110"></i>
												</span>
											</div>
										</div>
									</div>	
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Sexo</label>
										<div class="col-sm-7" >	
											<select name="x_sexo" class="col-xs-10 col-sm-7" id="x_sexo">
												<?php 
													if ($x_sexo == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_sexo as $k => $v) {
														$ls_cadenasel =($k == $x_sexo)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>										
									</div>								
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-phone">Telefono 1</label>

										<div class="col-sm-9">
											<span class="input-icon input-icon-right">
												<input class="input-medium input-mask-phone" name="x_telefono_hab" value="<?php echo $x_telefono_hab;?>" type="text" id="form-field-phone" />
												<i class="ace-icon fa fa-phone fa-flip-horizontal"></i>
											</span>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-phone">Telefono 2</label>

										<div class="col-sm-9">
											<span class="input-icon input-icon-right">
												<input class="input-medium input-mask-phone" name="x_telefono_otro" value="<?php echo $x_telefono_otro;?>" type="text" id="form-field-phone1" />
												<i class="ace-icon fa fa-phone fa-flip-horizontal"></i>
											</span>
										</div>
									</div>
																			
							
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-email">Email</label>

										<div class="col-sm-9">
											<span class="input-icon input-icon-right">
												<input type="email" id="form-field-email" name="x_email" value="<?php echo $x_email;?>" />
												<i class="ace-icon fa fa-envelope"></i>
											</span>
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Direccion</label>
										<div class="col-sm-9" >
											<textarea name="x_direccion" cols="10" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_direccion;?></textarea>
										</div>
									</div>
								
								</div>

								<div id="dropdown14" class="tab-pane">
									<p>AQUI SE CARGARA LA FOTO</p>
								</div>
							</div>
						</div>
						
								<div class="space-4"></div>	
							
							<div class="form-group center">												
								<button type="button" onClick="Cancelar('<?php echo "B"; ?>')" class="btn btn-sm  btn-danger">
									<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
									Regresar
								</button>
								
								<button type="button" onClick="Guardar();"class="btn btn-sm btn-success">
									<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
									Guardar
								</button>										
							</div>
									
							<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
							<input type="hidden" name="co_usuario" value="<?php echo $co_usuario; ?>"> 
							<input type="hidden" id = "input_filtro" name="input_filtro" value="<?php echo $input_filtro;?>">
							
						</form>
					</div>
				</div>
			</div> <!-- /.row tabla principal -->
		</div> <!-- /.page-content -->

</body>

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/chosen.jquery.min.js"></script>
	<!-- Mascara telefono -->
	<script src="../../assets/js/jquery.maskedinput.min.js"></script> 
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

	<script type="text/javascript">
		 $(document).ready(function() {
		
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			//show datepicker when clicking on the icon
			.next().on(ace.click_event, function(){
				$(this).prev().focus();
			});
			
			$('.input-mask-phone').mask('(9999) 999-9999');
		
		} );
		
	</script>
				
				

<script language="javascript" type="text/javascript">
	
	function Cancelar(parametros){
		document.formulario.tarea.value = "X";
		document.formulario.action = "sis_usuario.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}		
	
	
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				
				if (document.formulario.tarea.value == 'M')	document.formulario.tarea.value = "U";
				else										document.formulario.tarea.value = "A";
					
				document.formulario.method = "POST";
				document.formulario.action = "sis_usuario_mtto.php";
				document.formulario.submit();
			}
		}
	}
</script>

</html>