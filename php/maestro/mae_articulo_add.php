<?php 
/*---	DESCRIPCION : Agregar un Proyecto nuevo a la Base de Datos       --*/
/*---	RUTINA:  Verificacion y Autenticacion de Usuario  ---*/
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
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RECIBE VARIABLES POR URL
-------------------------------------------------------------------------------------------*/
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
	$arr_clase_articulo =	Combo_Clasificacion();
	$arr_categoria      =   Combo_Categoria_Articulo();
	

/*-------------------------------------------------------------------------------------------|
	EDITAR  UN REGISTRO
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT nb_articulo, t05_clase.fk_categoria, fk_clase, tx_descripcion, in_venta, in_gasto, nu_precio_item
				  FROM t13_articulo
				  INNER JOIN t05_clase ON t05_clase.pk_clase = t13_articulo.fk_clase
				  WHERE pk_articulo = $pk_articulo";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado != 0){
			$row  = pg_fetch_row($ls_resultado,0);
			$o_nombre      		= $row[0];
			$o_categoria        = $row[1];
			$o_clase_articulo   = $row[2];
			$x_descripcion 		= $row[3];
			$x_venta     		= $row[4];
			$x_gasto     		= $row[5];
			$x_precio    		= $row[6];
			
		$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
		}		
	}
	
/*-------------------------------------------------------------------------------------------|
	ACTUALIZA UN REGISTRO
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "B"){
		$error_sql = false;
	
		
		$ls_sql = "SELECT pk_articulo FROM t13_articulo 
					WHERE (UPPER(nb_articulo)='".strtoupper($o_nombre)."' ) AND pk_articulo <> '$pk_articulo' "; 
		//echo $ls_sql;
		$o_nombre =  $o_nombre==''?'NULL':"$o_nombre";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if ($obj_miconexion->fun_numregistros() == 0){	
       			$o_nombre = mb_convert_case($o_nombre, MB_CASE_TITLE, "UTF-8"); 				
				$ls_sql = "UPDATE t13_articulo SET 
								nb_articulo    = '".ucwords($o_nombre)."',           
								fk_clase       = $o_clase_articulo  ,
								tx_descripcion = '".ucwords($x_descripcion)."',
								in_venta       = '$x_venta',
								in_gasto       = '$x_gasto',
								nu_precio_item      = $x_precio													
								
				 		   WHERE pk_articulo = $pk_articulo";
				
				if ($obj_miconexion->fun_consult($ls_sql)== 0)	{
					$error_sql = true;
				}

				$parametros = "tarea=B&pk_articulo=$pk_articulo";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('¡Datos Actualizados Satisfactoriamente!');location.href='mae_articulo_view.php?$parametros';</script>";

			}else{
				$msg = "¡El Nombre ya Estan Registrados!";
				echo "<script language='JavaScript' type='text/JavaScript'>alert('$msg')</script>";			
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$tarea = "M";  // Vuelve A Colocar La Tarea.
	}
	
/*-------------------------------------------------------------------------------------------
  INSERTAR UN REGISTRO
-------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){

		// Verfica la duplicidad del nombre del rubro
		$ls_sql ="SELECT nb_articulo	FROM t13_articulo WHERE nb_articulo = '$o_nombre'" ;
		//echo $ls_sql;
		$o_nombre = mb_convert_case($o_nombre, MB_CASE_TITLE, "UTF-8"); 
		$x_descripcion = mb_convert_case($x_descripcion, MB_CASE_TITLE, "UTF-8"); 
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros() == 0){
				$ls_sql = "INSERT INTO t13_articulo(nb_articulo, tx_descripcion, fk_clase , in_venta, in_gasto, nu_precio_item	)
							VALUES ('".$o_nombre."', '".$x_descripcion."',  $o_clase_articulo, '$x_venta','$x_gasto', $x_precio		
							);";
						
				//echo $ls_sql;			
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if($ls_resultado == 0){
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
				}else{
					$parametros = "tarea=A";
					echo "<script language='javascript' type='text/javascript'>alert('Agregado Exitosamente');location.href='mae_articulo_add.php?$parametros';</script>";
					/*echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='adm_venta_view.php'</script>";	*/
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

	$chek_venta = $x_venta=='on'?'checked':'unchecked';
	$chek_gasto = $x_gasto=='on'?'checked':'unchecked';
	
?>

<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Catalogo de Articulos
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
												<label  class="col-sm-3 control-label no-padding-right"  >Nombre</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="o_nombre" value="<?php echo $o_nombre;?>" id="o_nombre" type="text"  placeholder="Nombre del Articulo">
												</div>
											</div>

											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  >Precio</label>
												<div class="col-sm-7" >
													<input class="col-xs-10 col-sm-7" name="x_precio" value="<?php echo $x_precio;?>" id="x_precio" type="text"  placeholder="Precio" onkeypress = "return validardec(event)">
												</div>
											</div>
											
											
											
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Categoria</label>
												<div class="col-sm-7" >	
													<select name="o_categoria" id = "id_categoria" class="form-control  ">
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
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Clasificacion</label>
												<div class="col-sm-7" >	
													<select name="o_clase_articulo" id="id_clase" class="form-control" data-placeholder="Selecciona la Clase...">
																			
													</select>
												</div>													
											</div>
											
																						
																																												
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right" for="x_descripcion" >Descripcion</label>
												<div class="col-sm-9" >
													<textarea name="x_descripcion" cols="10" id="x_descripcion" class="form-control" rows="5" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_descripcion;?></textarea>
												</div>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-4 control-label no-padding-right">
													<input type="checkbox" name="x_venta"  <?php echo $chek_venta; ?> />
													<span class="lbl"> Venta </span>
												</label>
											</div>
											
											<div class="form-group">
												<label  class="col-sm-4 control-label no-padding-right">
													<input type="checkbox" name="x_gasto" <?php echo $chek_gasto; ?> />
													<span class="lbl"> Gasto </span>
												</label>
											</div>
											
											
											<div class="form-group center">	
												<button type="button" onClick="location.href = 'mae_articulo_view.php'" class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
													Regresar
												</button>
											
												<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
											
											
											
										</div>	
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" value="<?php echo $modo;?>">
											<input type="hidden" name="pk_articulo" value="<?php echo $pk_articulo;?>">   
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
	<script type="text/javascript">
	
			
		 $(document).ready(function() {	
			
			$("#id_categoria").change(function () {
				   $("#id_categoria option:selected").each(function () {
					elegido=$(this).val();
					$.post("ajax_clasificacion.php", { elegido: elegido }, function(data){
					$("#id_clase").html(data);
					});            
				});
			})
			
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
		} );
		
	</script>
				
	<script type="text/javascript">
		function actualizar_combo(clase, categoria){

			$.post("ajax_clasificacion.php", { elegido: categoria, clasificacion : clase }, function(data){
			$("#id_clase").html(data);
			});            
			
		}		  	
				
		function Guardar(){
			if(campos_blancos(document.formulario) == false){
				if (document.formulario.tarea.value == "M"){
					document.formulario.tarea.value =  "B";
				}else{
					
				}	
				document.formulario.method = "POST";
				document.formulario.action = "mae_articulo_add.php";
				document.formulario.submit();
			}
		}	
		
		function Cancelar(parametros){
			location.href = "pro_proyecto_view.php?" + parametros;
		}
			
	</script>
	
</html>
<?php 
// SI VA ACTUALIZAR
	if ($tarea == "M"){
		echo "<script language='JavaScript' type='text/JavaScript'>actualizar_combo(".$o_clase_articulo.",".$o_categoria.");</script>";
	}
?>

