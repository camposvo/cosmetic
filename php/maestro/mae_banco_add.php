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
	
	$arr_tipo_cuenta  =  Combo_TipoCuenta();

/*-------------------------------------------------------------------------------------------|
|	Rutina: AGREGA UN NUEVO REGISTRO		           |
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){
		if($pk_banco==''){
			$ls_sql = "SELECT pk_banco FROM t15_banco	WHERE (tx_nro_cuenta) = '".strtoupper($tx_nro_cuenta)."'";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
			if($ls_resultado != 0){
				if($obj_miconexion->fun_numregistros() == 0){
					$ls_sql = " INSERT INTO t15_banco (nb_banco, tx_nro_cuenta, tx_tipo, in_activo, fe_apertura) 
								VALUES ('".strtoupper($o_nombre)."','".$o_cuenta."','".$o_tipo."','S','".$o_fecha."')";
					
					//echo $ls_sql;
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado == 0){
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}else{
						echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Registro Agregado Exitosamente!');location.href='mae_banco_view.php';</script>";
					}
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('¡Nombre Duplicado!');</script>";
				}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}
		$pk_banco = '';
		$o_nombre = '';
	}

/*-------------------------------------------------------------------------------------------|
|	Rutina:  ACTUALIZA LOS DATOS
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$ls_sql = " UPDATE t15_banco SET 
						nb_banco       = '".strtoupper($o_nombre)."', 
						tx_nro_cuenta  = '".$o_cuenta."',
						tx_tipo        = '".$o_tipo."',
						fe_apertura    = '".$o_fecha."'
					WHERE pk_banco = $pk_banco ";
					
		//echo $ls_sql;			
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Exitosamente!');location.href='mae_banco_view.php';</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$tarea = 'M';
	}
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Permite Colocar Los Datos En Modo Edición En La Misma Página			 |
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT nb_banco, tx_nro_cuenta, tx_tipo, to_char(fe_apertura, 'dd/mm/yyyy') FROM t15_banco WHERE pk_banco = $pk_banco";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_nombre = $row[0];
			$o_cuenta = $row[1];
			$o_tipo   = $row[2];
			$o_fecha  = $row[3];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$tarea = 'U';
	}

	
	if ($tarea == "A"){
		$tarea = 'I';
		$modo= 'Ingresar Nuevo Gasto';
	}
	
?>
<div class="container-fluid">
	<div class="page-header">
		<h1>
			Agregar Nuevo Banco
		</h1>
	</div><!-- /.page-header -->
	
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
			<div class="row">
				<div class="col-xs-12 col-sm-12 widget-container-col">
					<div class="widget-box ">
						<div class="widget-header widget-header-small">
							<h4 class="widget-title">Crear Banco</h4>
						</div>
			
						<div class="widget-body">
							<div class="widget-main">
								<form class="form-horizontal" name="formulario">
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  for="o_nombre">Nombre</label>
										<div class="col-sm-7" >
											<input class="col-xs-10 col-sm-7" name="o_nombre" value="<?php echo $o_nombre;?>" id="o_nombre" type="text"  placeholder="Ingrese Nombre">
										</div>
									</div>  
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right"  for="x_correo">Cuenta</label>
										<div class="col-sm-7" >
											<input class="col-xs-10 col-sm-7" name="o_cuenta" value="<?php echo $o_cuenta;?>" id="x_correo" type="text"  placeholder="Nro. de Cuenta">
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" >Tipo de Cuenta:</label>
										<div class="col-sm-7" >	
											<select name="o_tipo" class="col-xs-10 col-sm-7">
												<?php
													if ($o_tipo == ""){
														echo "<option value='0' selected>Seleccionar -&gt;</option>";
													}else{
														echo "<option value='0'>Seleccionar -&gt;</option>";
													}
													foreach($arr_tipo_cuenta as $k => $v) {
														$ls_cadenasel =($k == $o_tipo)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
													?>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha</label>
										<div class="col-sm-4" >	
											<div class="input-group">
												<input name="o_fecha" value="<?php echo $o_fecha;?>" class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd/mm/yyyy" readonly />
												<span class="input-group-addon">
													<i class="fa fa-calendar bigger-110"></i>
												</span>
											</div>
										</div>
									</div>									
										
								</div>	
								
								<div class="form-group center">												
									<button type="button" onClick="location.href = 'mae_banco_view.php'" class="btn btn-sm  btn-danger">
										<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
										Regresar
									</button>
									
									<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
										<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
										Guardar
									</button>																								
								</div>
								
									<input type="hidden" name="tarea" value="<?php echo $tarea;?>"> 
									<input type="hidden" name="pk_banco" value="<?php echo $pk_banco;?>">  
								</form>
							</div>
						</div>
				</div>
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
			
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
		} );		

	</script>
	
	<script type="text/javascript"> 

		function Guardar(identificador){
			if(campos_blancos(document.formulario) == false){
				if (confirm('¿Está Conforme Con Los Datos Ingresados?') == true){	
					document.formulario.tarea.value = identificador;
					document.formulario.action = "mae_banco_add.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		}
		
	</script>
</html>