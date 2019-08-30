<?php 
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
	//$o_cantidad  = 0;
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nuevo Registro';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	
	$x_responsable  	=  	$_SESSION["li_cod_usuario"];
	$arr_categoria_proy =	Combo_Categoria_Proyecto();
	$arr_responsable 	=  	Combo_Cliente();
	$arr_tipo_rubro   	= 	Combo_Tipo_Rubro();

/*-------------------------------------------------------------------------------------------
	RUTINAS: para ACTUALIZAR un RUBRO
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$o_total = number_format($o_total,2,".",",");
		$o_precio = number_format($o_precio,2,".",",");
		
		$ls_sql ="SELECT pk_proyecto FROM t02_proyecto WHERE tx_nombre = '$o_nombre' AND pk_proyecto <> $pk_proyecto" ;
		//echo $ls_sql ;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($obj_miconexion->fun_numregistros() == 0){
			$ls_sql = "UPDATE t02_proyecto SET 
				tx_nombre             = '$o_nombre', 
				fe_inicial            = '$o_fecha', 
				tx_descripcion        = '$o_descripcion', 			
				tx_categoria_proyecto = '$o_categoria_proy',
				fk_tipo_rubro         =  $o_tipo_rubro
			WHERE pk_proyecto         ='$pk_proyecto';";
	

			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado == 0){
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
			}else{
				echo "<script language='javascript' type='text/javascript'>alert('Registro Actualizado Exitosamente');location.href='pro_proyecto_view.php';</script>";
			}
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Nombre Duplicado');</script>";
		}
		$tarea= "M";
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: permite colocar los datos en modo EDICION
-------------------------------------------------------------------------------------------*/
	if ($tarea == "M"){
		$ls_sql ="SELECT tx_nombre, to_char(fe_inicial, 'dd/mm/yyyy') , fk_responsable, 
					tx_descripcion, fk_tipo_rubro, tx_categoria_proyecto
			FROM t02_proyecto
			WHERE pk_proyecto = $pk_proyecto";
			
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_nombre          = $row[0];
			$o_fecha	       = $row[1];
			$x_responsable     = $row[2];
			$o_descripcion     = $row[3];
			$o_tipo_rubro      = $row[4];
			$o_categoria_proy  = $row[5];
			
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}
		$modo= 'Actualizar Datos';
		$tarea= "U";
	}
	

	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para AGREGAR 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){		
		$o_total = number_format($o_total,2,".",",");
		$o_precio = number_format($o_precio,2,".",",");		
		
		// Verfica la duplicidad del nombre del rubro
		$ls_sql ="SELECT tx_nombre	FROM t02_proyecto WHERE tx_nombre = '$o_nombre'" ;
		//echo $ls_sql;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros() == 0){
				$ls_sql = "INSERT INTO t02_proyecto(
					tx_nombre, 	fe_inicial,							
						fk_responsable,	tx_descripcion, in_proy_activo, fk_tipo_rubro,tx_categoria_proyecto 
					)
					VALUES ('$o_nombre', '$o_fecha',  
						'$x_responsable','$o_descripcion', 'S', $o_tipo_rubro,'$o_categoria_proy'
					);";
						
							
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if($ls_resultado == 0){
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}else{
					//$parametros = "tarea=A";		
					echo "<script language='javascript' type='text/javascript'>alert('Agregado Exitosamente');location.href='pro_proyecto_view.php';</script>";
				}
							
			}else{
				echo "<script language='javascript' type='text/javascript'>alert('Nombre Duplicado');</script>";
			}
		}
				
		$tarea= "A";
	}	
	
	if ($tarea == "A"){
		$tarea = "I";
	}
?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Proyectos
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
											
											<div class="space-6"></div>	 
											
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
												
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Nombre del Proyecto</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_nombre" value="<?php echo $o_nombre;?>" id="o_nombre" type="text"  placeholder="Nombre del Proyecto">
												</div>
											</div>
																																
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Tipo</label>
												<div class="col-sm-7" >	
													<select name="o_tipo_rubro" type="select-one" class="col-xs-10 col-sm-7">
														<?php
															if ($o_tipo_rubro == ""){
																echo "<option value='0' selected>Seleccionar</option>";
															}else{
																echo "<option value='0'>Seleccionar</option>";
															}
															foreach($arr_tipo_rubro as $k => $v) {
																$ls_cadenasel =($k == $o_tipo_rubro)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>
													</select>
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" >Clase</label>
												<div class="col-sm-7" >	
													<select name="o_categoria_proy" type="select-one" class="col-xs-10 col-sm-7">
														<?php
															if ($o_categoria_proy == ""){
																echo "<option value='0' selected>Seleccionar</option>";
															}else{
																echo "<option value='0'>Seleccionar</option>";
															}
															foreach($arr_categoria_proy as $k => $v) {
																$ls_cadenasel =($k == $o_categoria_proy)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>
													</select>
												</div>
											</div>
																						
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" for="o_descripcion" >Descripcion</label>
												<div class="col-sm-9" >
													<textarea name="o_descripcion" cols="10" id="o_descripcion" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $o_descripcion;?></textarea>
												</div>
											</div>

											<div class="form-group center">
												
												<button type="button" onClick="Cancelar('<?php echo "B"; ?>')" class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
													Regresar
												</button>
												
												<button type="button" onClick="Guardar_Rubro('<?php echo $tarea; ?>');"class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>
												
																								
											</div>
											
											
											
										</div>	
										<input type="hidden" name="pk_proyecto" value="<?php echo $pk_proyecto;?>">
										<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
										<input type="hidden" name="modo" value="<?php echo $modo;?>">    
											
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
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
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
				
			
			} );
			
		</script>
				
				

<script type="text/javascript">
	    
   // When the document is ready
        
	$(document).ready(function () {
		$('.date').datepicker({
		});
		
		window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
	});
       
	function Cancelar(parametros){
		location.href = "pro_proyecto_view.php?" + parametros;
	}
	
	function Guardar_Rubro(Identificador){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				document.formulario.tarea.value = Identificador;
				document.formulario.action = "pro_proyecto_add.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}   
	   
	
</script>
</html>