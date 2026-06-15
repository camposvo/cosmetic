<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: sis_usuario_mtto.php                                            
	Descripcion: Esta interfaz permite Agregar un NUEVO usuario y EDITAR su Datos
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
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
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	if (!$_GET){
		foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);			
		}
		$tarea = isset($_POST['tarea'])?$_POST['tarea']:'M';
	}else{
		foreach($_GET as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$tarea = isset($_POST['tarea'])?$_POST['tarea']:'M';
	}
	
	$co_usuario = $_SESSION["li_cod_usuario"];
/*-------------------------------------------------------------------------------------------
	RUTINA: variables tipo arreglo para rellenar los combos en la interfaz. (VER CONFIG.PHP)
---------------------------------------------------------------------------------------------*/
	

	$array_profesion = Combo_profesion();
	$array_sexo      = Combo_Sexo();
	$array_si_no     = Combo_si_no();
	$array_condicion     = Combo_Condicion();
	
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	RUTINA: mostrar los datos del representante legal a editar en la interfaz.
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		
	}
	
/*-----------------------------------------------------------------------------------------------------------------------
	RUTINA: actualizo los datos del representante legal una vez editados.
------------------------------------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$error_sql = false;
		$o_indicador = strtoupper($o_indicador);
		
		$ls_sql = "SELECT co_persona FROM s01_persona 
					WHERE (tx_cedula='$o_cedula' OR UPPER(tx_indicador)='$o_indicador') AND co_persona <> '$co_usuario' "; 
		
		$o_indicador =  $o_indicador==''?'NULL':"$o_indicador";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	

       			if( $obj_miconexion->fun_consult(" BEGIN TRANSACTION ") == 0){
					$error_sql = true;
				}
			
				$ls_sql = "UPDATE s01_persona SET tx_cedula	        = '$o_cedula',           
								tx_nombre	        = '$o_nombre',           
								tx_apellido	        = '$o_apellido', 
								in_sexo             = '$o_sexo', 
								tx_telefono_hab     = '$x_telefono_hab',  
								tx_email            = '$x_email',
								in_activo           = '$o_activo',
								tx_indicador        = '$o_indicador'
							WHERE co_persona = '$co_usuario'";
							
							//echo $ls_sql;
				
				if ($obj_miconexion->fun_consult(strtoupper($ls_sql))== 0)	{
					$error_sql = true;
				}
				
				$ls_sq_conv = ereg_replace("'","\"",$ls_sql);
				$fecha = date('Y/m/d H:i');
				$ls_sql = "INSERT INTO t11_bitacora(co_persona,fe_fecha,tx_tabla,tx_accion,tx_sql) 
								VALUES ($ls_usuario,'$fecha','s01_persona','U','$ls_sq_conv')";
				
				if($obj_miconexion->fun_consult($ls_sql) == 0){
					$error_sql = true;
				}
				
				if(!$error_sql){
					$ls_resultado =  $obj_miconexion->fun_consult(" COMMIT ");
					$parametros = "tarea=B&co_usuario=$co_usuario";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('Datos Actualizados Satisfactoriamente');</script>";
				}else{
					$ls_resultado =  $obj_miconexion->fun_consult(" ROLLBACK ");
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}
			}else{
				$msg = "La Cedula y/o Indicador ya esta asignada a otro Persona!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
		$tarea == "M";  //Vuelve a colocar la Tarea 
	}


	$ls_sql = "SELECT tx_cedula, tx_nombre, tx_apellido, 	        
						tx_telefono_hab, tx_condicion, 	        
						in_sexo, tx_email, tx_indicador, in_activo
					FROM s01_persona 
					WHERE co_persona = '$co_usuario'";

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_cedula           = $row[0];
			$o_nombre           = $row[1];
			$o_apellido         = $row[2];
			$x_telefono_hab		= $row[3];
			$o_condicion        = $row[4];
			$o_sexo             = $row[5];
			$x_email         	= $row[6];
			$o_indicador        = $row[7];
			$o_activo           = $row[8];		   

			$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}		



	
	$obj_miconexion->fun_closepg($li_id_conex);

	$parametros = !isset($co_usuario)?'tarea=X':'co_usuario='.$co_usuario.'&tarea=B';
/*-------------------------------------------------------------------------------------------
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.           
|------------------------------------------------------------------------------------------*/
?>



<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Usuario
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> <?php echo $modo;?> </h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main ">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Cedula</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_cedula" value="<?php echo $o_cedula;?>" id="o_cedula" type="text"  placeholder="Cedula">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Nombre</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_nombre" value="<?php echo $o_nombre;?>" id="o_nombre" type="text"  placeholder="Nombre">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Apellido</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_apellido" value="<?php echo $o_apellido;?>" id="o_apellido" type="text"  placeholder="Apellido">
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Condicion</label>
												<div class="col-sm-7" >	
													<select disabled name="o_condicion" class="col-xs-10 col-sm-7" id="o_condicion">
														 <?php 
															if ($o_condicion == ""){
																echo "<option value='' selected>Seleccionar --&gt;</option>";
															}else{
																echo "<option value=''>Seleccionar --&gt;</option>";
															}
															foreach($array_condicion as $k => $v) {
																$ls_cadenasel =(strtoupper($v) == strtoupper($o_condicion))?'selected':'';
																echo "<option value='$v' $ls_cadenasel>$k</option>";                
															}
														?>
													</select>
												</div>
											</div>
											
											 
			
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Sexo</label>
												<div class="col-sm-7" >	
													<select name="o_sexo" class="col-xs-10 col-sm-7" id="o_sexo">
														<?php 
															if ($o_sexo == ""){
																echo "<option value='' selected>Seleccionar -&gt;</option>";
															}else{
																echo "<option value=''>Seleccionar -&gt;</option>";}
															foreach($array_sexo as $k => $v) {
																$ls_cadenasel =($k == $o_sexo)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>
													</select>
												</div>
											</div>
			
																						
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Activo</label>
												<div class="col-sm-7" >	
													<select name="o_activo" class="col-xs-10 col-sm-7" id="o_activo">
														<?php 
															if ($o_activo == ""){
																echo "<option value='' selected>Seleccionar -&gt;</option>";
															}else{
																echo "<option value=''>Seleccionar -&gt;</option>";}
															foreach($array_si_no as $k => $v) {
																$ls_cadenasel =($k == $o_activo)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>
													</select>
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Indicador</label>
												<div class="col-sm-7" >
													<input readonly class="col-xs-10 col-sm-7" name="o_indicador" value="<?php echo $o_indicador;?>" id="o_indicador" type="text"  placeholder="Indicdor">
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
												<label class="col-sm-3 control-label no-padding-right" for="form-field-phone">Telefono</label>

												<div class="col-sm-9">
													<span class="input-icon input-icon-right">
														<input class="input-medium input-mask-phone" name="x_telefono_hab" value="<?php echo $x_telefono_hab;?>" type="text" id="form-field-phone" />
														<i class="ace-icon fa fa-phone fa-flip-horizontal"></i>
													</span>
												</div>
											</div>
											
											<div class="form-group center">												
												<button type="button" onClick="Guardar();"class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>										
											</div>
											
											
											
										</div>	
										<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
										<input type="hidden" name="co_usuario" value="<?php echo $co_usuario; ?>"> 
											
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
	
	<!-- Mascara telefono -->
	<script src="../../assets/js/jquery.maskedinput.min.js"></script> 

	
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>



<!-- inline scripts related to this page -->
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
		
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			document.formulario.tarea.value = "U";
			document.formulario.method = "POST";
			document.formulario.action = "sis_profile.php";
			document.formulario.submit();
		}
	}	
	
</script>

</html>