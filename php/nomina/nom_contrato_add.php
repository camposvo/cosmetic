<?php 
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("nom_utilidad.php");
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
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
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
	
	$obj_miconexion 	= fun_crear_objeto_conexion();
	$li_id_conex    	= fun_conexion($obj_miconexion);
	$array_asig  		= Combo_Asignacion();
	$array_deduc  		= Combo_Deduccion();
	$array_tipo_nomina	= Combo_TipoNomina();
	$co_usuario     	= $_SESSION["li_cod_usuario"];
	$arr_empleado       = Combo_Empleado();
	
	$resul_asig = "NNNNNNN";	// Inicializa
	$resul_deduc = "NNNNNNN";	// Inicializa
		
	
	$input_asig = $_POST['input_asig'];
	$input_deduc = $_POST['input_deduc'];
	
	//var_dump($_POST['input_asig']);
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: AGREGAR DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){
		
		$ls_sql = "INSERT INTO t12_contrato( 
				fk_trabajador, 
				nu_salario, 
				fe_inicio, 
				fe_fin, 
				tx_descripcion, 
				in_activo,
				tx_tipo_nomina,
				fk_responsable
			) 
					VALUES (
				$o_empleado, 
				$o_salario, 
				'$o_fecha_ini',
				'$o_fecha_ini',
				'$o_descripcion', 
				'S',
				'$o_tiponomina',
				 $co_usuario	
			);";
					
			/************************************************************************************
			CUANDO OCURRE EL INSERT SE ACTIVA UN TRIGGER QUE AGREGAR LA FACTURA ASOCIADA
			************************************************************************************/	
			//echo $ls_sql;
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Operacion realizada Exitosamente');location.href='nom_contrato_view.php?$parametros';</script>";
		}
		
		//$tarea = 'A';
	}	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: ACTUALIZAR DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		// Crea cadena de configuracion de las opciones que se seleccionaron
		if ($_POST['check_asig'])	{ 
			 foreach($_POST['check_asig'] as $valor){
				$resul_asig[$valor]='S';
			}
		}			
		if ($_POST['check_deduc'])	{ 
			 foreach($_POST['check_deduc'] as $valor){
				$resul_deduc[$valor]='S';
			}
		}
	
	
		$ls_sql = "UPDATE t12_contrato  SET
				fk_trabajador	=	$o_empleado, 
				nu_salario 		=	$o_salario, 
				fe_inicio 		=	'$o_fecha_ini',
				fe_fin 			=	'$o_fecha_ini',
				tx_descripcion	=	'$o_descripcion', 
				in_activo		=	'S',
				in_asignacion	=	'$resul_asig',
				in_deduccion	=	'$resul_deduc',
				tx_tipo_nomina  = 	'$o_tiponomina'
				
			WHERE pk_contrato = '$co_contrato' ";
						
		//echo $ls_sql;
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Actualizada Exitosamente');location.href='nom_contrato_view.php';</script>";
		}
		$tarea = 'M';
	}
	
	/*-------------------------------------------------------------------------------------------
	RUTINA: mostrar los datos 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$obj_miconexion = fun_crear_objeto_conexion();
		$li_id_conex = fun_conexion($obj_miconexion);		
	

			$ls_sql="SELECT fk_trabajador, nu_salario, to_char(fe_inicio, 'dd/mm/yyyy'), to_char(fe_fin, 'dd/mm/yyyy'), 
					in_asignacion, in_deduccion, tx_cedula, tx_tipo_nomina
					pk_contrato 
					FROM t12_contrato
					LEFT JOIN s01_persona ON t12_contrato.fk_trabajador = s01_persona.co_persona
					WHERE pk_contrato ='$co_contrato' 
					ORDER BY fe_inicio DESC";
					
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);		
			if ($ls_resultado != 0){
					while($row = pg_fetch_row($ls_resultado)){
						$o_empleado  	= $row[0];
						$o_salario   	= $row[1];
						$o_fecha_ini  	= $row[2];
						$o_fecha_fin  	= $row[3];
						$temp_asig    	= str_split($row[4]);
						$temp_deduc   	= str_split($row[5]);
						$x_cedula 		= $row[6];
						$o_tiponomina 	= $row[7];
												
					}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
			}		
			//echo $ls_sql;
			
			$i = 0;

		foreach($temp_deduc as $valor){
			$activo_deduc[$i++] =	$valor=='S'?'checked':'unchecked';				
		}
		$i = 0;
		foreach($temp_asig as $valor){
			$activo_asig[$i++] =	$valor=='S'?'checked':'unchecked';				
		}
		$tarea = 'U';		
	}
	
	$tarea = ($tarea == 'X')?'I':$tarea ;		
	
?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			Contrato
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
			<div class="row">
			
				<div class="col-xs-12 col-sm-12 widget-container-col">
					<div class="widget-box ">
						<div class="widget-header widget-header-small">
							<h4 class="widget-title">Datos del Contrato </h4>
						</div>
			
						<div class="widget-body">
							<div class="widget-main">
								<form class="form-horizontal" name="formulario">							
								
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Empleado</label>
										<div class="col-sm-7 col-xs-6" >	
											<select name="o_empleado" class="col-xs-6 col-sm-10 chosen-select " id="id_empleado" data-placeholder="Selecciona un Empleado...">
												<?php
													if ($o_empleado == ""){
														echo "<option value='0' selected></option>";
													}else{
														echo "<option value='0'></option>";
													}
													foreach($arr_empleado as $k => $v) {
														$ls_cadenasel =($k == $o_empleado)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>									
											</select>
										</div>													
									</div>
									
									<div id="id_datos">
										<div class="form-group">
											<label class="col-sm-3 control-label no-padding-right">Cedula</label>
											<div class="col-sm-6">
												<input  readonly class="input-sm form-control" name="x_cedula"  value="<?php echo $x_cedula;?>"  type="text"  />
											</div>
										</div>								
									</div>	
								
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Fecha</label>
										<div class="col-sm-6">	
											<div class="input-daterange input-group">
												<input class="input-sm form-control" name="o_fecha_ini" value="<?php echo $o_fecha_ini;?>" placeholder="Desde" type="text" readonly />
												<span class="input-group-addon">
													<i class="fa fa-exchange"></i>
												</span>
												<input class="input-sm form-control" name="o_fecha_fin" value="<?php echo $o_fecha_fin;?>" placeholder="Hasta" type="text" readonly />
											</div>
										</div>
									</div>
								
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Salario</label>
										<div class="col-sm-6">
											<input  class="input-sm form-control" name="o_salario"  value="<?php echo $o_salario; ?>"  type="text" onKeyPress="return validardec(event)" />
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Tipo de Nomina</label>
										<div class="col-sm-7" >	
											<select name="o_tiponomina" class="col-xs-10 col-sm-7" id="o_tiponomina">
												<?php 
													if ($o_tiponomina == ""){
														echo "<option value='' selected>Seleccionar -&gt;</option>";
													}else{
														echo "<option value=''>Seleccionar -&gt;</option>";}
													foreach($array_tipo_nomina as $k => $v) {
														$ls_cadenasel =($k == $o_tiponomina)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" for="o_descripcion" >Descripcion</label>
										<div class="col-sm-9" >
											<textarea name="o_descripcion" cols="10" id="o_descripcion" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_observacion;?></textarea>
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
								
								
								<input name="co_contrato" type="hidden" value="<?php echo $co_contrato;?>">
								<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
								<input type="hidden" name="modo" value="<?php echo $modo;?>">   
								<input name="co_trabajador" type="hidden" value="<?php echo $co_trabajador;?>">
								<input name="x_cedula" value="<?php echo $x_cedula;?>" id="x_cedula" type="hidden" ><!-- /.box-body -->
									
								</form>
							</div>
						</div>
					</div>
				</div>
				
			</div> <!-- /.Row datos -->
				
									
		</div> <!-- /.row Datos  -->
	</div> <!-- /.row tabla principal -->
</div> <!-- /.page-content -->

</body>

		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>
		<script src="../../assets/js/chosen.jquery.min.js"></script>


		<!-- inline scripts related to this page -->
<script type="text/javascript">
	 $(document).ready(function() {
		window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical

		
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
		
		//AJAX cuando se cambia el empleado
		$("#id_empleado").change(function () {
			   $("#id_empleado option:selected").each(function () {
				elegido=$(this).val();
				$.post("ajax_empleado.php", { elegido: elegido}, function(data){
				$("#id_datos").html(data);
				});            
			});
	   })
		
		
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
		if( (IsNumeric(document.formulario.CedulaTemp.value) == true) && (document.formulario.CedulaTemp.value != '')){
			document.formulario.tarea.value = "B";
			document.formulario.action = "nom_contrato_add.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}else{
			alert("Cedula Incorrecta!");
			document.formulario.CedulaTemp.focus();
			document.formulario.CedulaTemp.value = "";			
		}		
	}
	
	function Guardar(tarea){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				document.formulario.tarea.value = tarea;
				document.formulario.action = "nom_contrato_add.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}

	function Atras(parametros){
		location.href = "nom_contrato_view.php?" + parametros;
	}	
	

</script>
</html>