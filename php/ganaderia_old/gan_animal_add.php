<?php 
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
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
<title>La Peperana</title>
		
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
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
	
/*-------------------------------------------------------------------------------------------
	RUTINA: variables tipo arreglo para rellenar los combos en la interfaz. (VER CONFIG.PHP)
---------------------------------------------------------------------------------------------*/
	$array_profesion = Combo_profesion();
	$array_sexo      = Combo_Sexo_Animal();
	$array_raza      = Combo_Raza();
	$array_madre      = Combo_Gan_Madre();
	$array_padre      = Combo_Gan_Padre();
	$array_tipo_gan   = Combo_Tipo_Ganado();
	
	
	//echo $tarea;
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	RUTINA: mostrar los datos del representante legal a editar en la interfaz.
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql = "SELECT id_numero, id_lomo, id_arete, 
						   to_char(fe_nacimiento, 'dd/mm/yyyy'),  in_sexo, 
						   nb_nombre_animal, fk_madre, fk_padre, 
						   fk_raza, in_tipo 
					FROM gan_ganado 									
					WHERE pk_ganado = '$pk_animal'";
					
		//echo $ls_sql;

		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
									
			$row = pg_fetch_row($ls_resultado,0);
			$o_numero           = $row[0];
			$x_lomo           	= $row[1];
			$x_oreja         	= $row[2];
			$x_nacimiento		= $row[3];
			$o_sexo    			= $row[4];
			$x_nombre       	= $row[5];
			$x_madre       		= $row[6];
			$x_padre        	= $row[7];
			$x_raza    			= $row[8];
			$x_tipo				= $row[9];
			
			$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}		
	}
	
/*-----------------------------------------------------------------------------------------------------------------------
	RUTINA: actualizo los datos del representante legal una vez editados.
------------------------------------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$x_nacimiento = $x_nacimiento==''?'null':"'".$x_nacimiento."'";
		$error_sql = false;
		
		$x_nacimiento = $x_nacimiento==''?'null':"'".$x_nacimiento."'";
		$x_madre = $x_madre==''?'null':$x_madre;
		$x_padre = $x_padre==''?'null':$x_padre;
		$x_raza = $x_raza==0?'null':$x_raza;		
		
		$o_indicador = strtoupper($o_indicador);
		
		$ls_sql = "SELECT pk_ganado FROM gan_ganado 
					WHERE (nb_nombre_animal='$x_nombre' OR UPPER(id_numero)='$o_numero') AND pk_ganado <> '$pk_animal' "; 
		
		$o_indicador =  $o_indicador==''?'NULL':"$o_indicador";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	

       			if( $obj_miconexion->fun_consult(" BEGIN TRANSACTION ") == 0){
					$error_sql = true;
				}
			
				$ls_sql = "UPDATE gan_ganado SET
								id_numero	        = '$o_numero',           
								id_lomo	        = '$x_lomo',           
								id_arete	        = '$x_oreja', 
								fe_nacimiento             = '$x_nacimiento', 
								in_sexo     = '$o_sexo',  
								nb_nombre_animal     = '$x_nombre',  
								fk_madre            = $x_madre,
								fk_padre           = $x_padre,
								fk_raza        = $x_raza,
								in_tipo    =  '$x_tipo'
							WHERE pk_ganado = '$pk_animal'";
							
							//echo $ls_sql;
				
				if ($obj_miconexion->fun_consult(strtoupper($ls_sql))== 0)	{
					$error_sql = true;
				}
				
				if(!$error_sql){
					$ls_resultado =  $obj_miconexion->fun_consult(" COMMIT ");
					$parametros = "tarea=B";
					echo "<script language='JavaScript' type='text/JavaScript'>alert('Datos Actualizados Satisfactoriamente');location.href='gan_animal_view.php?$parametros';</script>";
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
	
/*-------------------------------------------------------------------------------------------
	RUTINA: insertar un nuevo USUARIO
--------------------------------------------------------------------------------------------*/
	if($tarea == "A"){
		// Valida campos NO obligatorios
		$x_nacimiento = $x_nacimiento==''?'null':"'".$x_nacimiento."'";
		$x_madre = $x_madre==''?'null':$x_madre;
		$x_padre = $x_padre==''?'null':$x_padre;
		$x_raza = $x_raza==0?'null':$x_raza;		
		
		$ls_sql = "INSERT INTO gan_ganado( id_numero, id_lomo, id_arete, 
											fe_nacimiento, in_sexo, nb_nombre_animal, 
											fk_madre, fk_padre, fk_raza, 
											in_tipo
											) 
					VALUES('$o_numero', '$x_lomo','$x_oreja',
							$x_nacimiento,  '$o_sexo', '$x_nombre',
							 $x_madre, $x_padre, $x_raza,
							 '$x_tipo')";
					
		//echo $ls_sql;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
		if ($ls_resultado != 0){
			$parametros = "tarea=B";
			echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Ingresados Satisfactoriamente!');location.href='gan_animal_add.php?$parametros';</script>";

		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		
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
					Datos del Animal
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<form class="form-horizontal" name="formulario">
							
							<div class="tabbable">
								<ul class="nav nav-tabs padding-18">
									<li class="active">
										<a data-toggle="tab" href="#profile1">
										<i class="blue ace-icon fa fa-paw bigger-120"></i>
										Datos Generales</a>
									</li>
									
									<li>										
										<a data-toggle="tab" href="#profile2">
										<i class="green ace-icon fa fa fa-sitemap bigger-120"></i>
										Genealogia</a>
									</li>

								
									<li>
										<a data-toggle="tab" href="#profile3">
										<i class="blue ace-icon fa fa-eye bigger-120"></i>
										Fenotipo</a>
									</li>
									
								</ul> <!-- Menu de Datos -->

							<div class="tab-content">								
								
								<div id="profile1" class="tab-pane in active">
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >Numero</label>
										<div class="col-sm-7" >
											<input class="col-xs-10 col-sm-7" name="o_numero" value="<?php echo $o_numero;?>" type="text"  placeholder="Numero">
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Sexo</label>
										<div class="col-sm-7" >	
											<select name="o_sexo" class="col-xs-10 col-sm-7">
												<?php 
													if ($o_sexo == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_sexo as $k => $v) {
														$ls_cadenasel =($k == $o_sexo)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>										
									</div>

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
										<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha Destete</label>
										<div class="col-sm-4" >	
											<div class="input-group">
												<input name="x_fe_destete" value="<?php echo $x_fe_destete;?>" class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd/mm/yyyy" readonly />
												<span class="input-group-addon">
													<i class="fa fa-calendar bigger-110"></i>
												</span>
											</div>
										</div>
									</div>	
									
																		
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >Nombre</label>
										<div class="col-sm-7" >
											<input class="col-xs-10 col-sm-7" name="x_nombre" value="<?php echo $x_nombre;?>"  type="text"  placeholder="Nombre">
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >Marca de Oreja</label>
										<div class="col-sm-7" >
											<input class="col-xs-10 col-sm-7" name="x_oreja" value="<?php echo $x_oreja;?>" type="text"  placeholder="Arete">
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  >Marca de Lomo</label>
										<div class="col-sm-7" >
											<input class="col-xs-10 col-sm-7" name="x_lomo" value="<?php echo $x_lomo;?>" type="text"  placeholder="Marca de Lomo">
										</div>
									</div>
									
									
																																				
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" >Raza</label>
										<div class="col-sm-7" >	
											<select name="x_raza" class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Selecciona la raza...">
												<?php
													if ($x_raza == ""){
														echo "<option value='0' selected></option>";
													}else{
														echo "<option value='0'></option>";
													}
													foreach($array_raza as $k => $v) {
														$ls_cadenasel =($k == $x_raza)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>							
											</select>
										</div>													
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" >Tipo </label>
										<div class="col-sm-7" >	
											<select name="x_tipo" class="col-xs-10 col-sm-7">
												<?php
													if ($x_tipo == ""){
														echo "<option value='0' selected></option>";
													}else{
														echo "<option value='0'></option>";
													}
													foreach($array_tipo_gan as $k => $v) {
														$ls_cadenasel =($k == $x_tipo)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>							
											</select>
										</div>													
									</div>
									
								</div> <!-- fin datos Generales -->

								<div id="profile2" class="tab-pane">  <!-- datos Genealogia -->
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Concepcion</label>
										<div class="col-sm-7" >	
											<select name="x_concepcion" type="select-one" class="col-xs-10 col-sm-7" id="x_concepcion">
												<?php 
													if ($x_concepcion == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_raza as $k => $v) {
														$ls_cadenasel =($k == $x_concepcion)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>					
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Padre</label>
										<div class="col-sm-7" >	
											<select name="x_padre" type="select-one" class="col-xs-10 col-sm-7" >
												<?php 
													if ($x_padre == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_padre as $k => $v) {
														$ls_cadenasel =($k == $x_padre)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Madre</label>
										<div class="col-sm-7" >	
											<select name="x_madre" type="select-one" class="col-xs-10 col-sm-7" ">
												<?php 
													if ($x_madre == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_madre as $k => $v) {
														$ls_cadenasel =($k == $x_madre)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>								
												
																	
								</div> <!-- fin datos Genealogia  -->

								<div id="profile3" class="tab-pane">  <!-- datos Fenotipo  -->
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Madre</label>
										<div class="col-sm-7" >	
											<select name="x_madre" type="select-one" class="col-xs-10 col-sm-7" ">
												<?php 
													if ($x_madre == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_madre as $k => $v) {
														$ls_cadenasel =($k == $x_madre)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>				

									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Madre</label>
										<div class="col-sm-7" >	
											<select name="x_madre" type="select-one" class="col-xs-10 col-sm-7" ">
												<?php 
													if ($x_madre == ""){
														echo "<option value='' selected>Seleccionar</option>";
													}else{
														echo "<option value=''>Seleccionar</option>";}
													foreach($array_madre as $k => $v) {
														$ls_cadenasel =($k == $x_madre)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>							
									
								</div> <!-- datos Fenotipo  -->
								
							
								
								
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
							<input type="hidden" name="pk_animal" value="<?php echo $pk_animal;?>">
							
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
			
			if(!ace.vars['touch']) {
				$('.chosen-select').chosen({allow_single_deselect:true}); 
			
				//resize chosen on sidebar collapse/expand
				
				$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
					if(event_name != 'sidebar_collapsed') return;
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});
					})
				});	

				$('#chosen-multiple-style .btn').on('click', function(e){
					var target = $(this).find('input[type=radio]');
					var which = parseInt(target.val());
					if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
					 else $('#form-field-select-4').removeClass('tag-input-style');
				});
			}
			
			
				$('#id-input-file-3').ace_file_input({
					style:'well',
					btn_choose:'Drop files here or click to choose',
					btn_change:null,
					no_icon:'ace-icon fa fa-cloud-upload',
					droppable:true,
					thumbnail:'fit'//'small'//large | fit
					//,icon_remove:null//set null, to hide remove/reset button
					/**,before_change:function(files, dropped) {
						//Check an example below
						//or examples/file-upload.html
						return true;
					}*/
					/**,before_remove : function() {
						return true;
					}*/
					,
					preview_error : function(filename, error_code) {
						//name of the file that failed
						//error_code values
						//1 = 'FILE_LOAD_FAILED',
						//2 = 'IMAGE_LOAD_FAILED',
						//3 = 'THUMBNAIL_FAILED'
						//alert(error_code);
					}
			
				}).on('change', function(){
					//console.log($(this).data('ace_input_files'));
					//console.log($(this).data('ace_input_method'));
				});
				
				
								///////////////////////////////////////////
				
			
			$('.input-mask-phone').mask('(9999) 999-9999');			
		
		} );
		
	</script>
				
				

<script language="javascript" type="text/javascript">
	function Cancelar(parametros){
		location.href = "gan_animal_view.php?" + parametros;
	}
	
	function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				
				if (document.formulario.tarea.value == 'M')	document.formulario.tarea.value = "U";
				else										document.formulario.tarea.value = "A";
					
				document.formulario.method = "POST";
				document.formulario.action = "gan_animal_add.php";
				document.formulario.submit();
			}
		}
	}
</script>

</html>