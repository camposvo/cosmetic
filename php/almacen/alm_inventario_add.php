<?php 
/*--------------------------------------------------------------------------------------------------|
|  	Nombre: 'ma_ubicaciones.php'                                            						|
|	Descripción: Esta Interfaz Permite Ver, Editar y Agregar Ubicacion Asociandolo a Un Almacen	.	|
|--------------------------------------------------------------------------------------------------*/
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
<title>BellinghieriCosmetic</title>
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
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
	
	$arr_almacen   =  Combo_Almacen();
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: ACTUALIZAR DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$x_ubicacion = $x_ubicacion==''?'null':$x_ubicacion;
		
		$ls_sql ="UPDATE t01_detalle SET 
				fk_ubicacion      =  $x_ubicacion			
		WHERE pk_detalle   =  $x_movimiento";
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Registro Actualizado Exitosamente');</script>";
		}
	}
	
	$ls_sql ="SELECT  UPPER(s01_persona.tx_indicador), nb_articulo, nu_cantidad, nu_precio,  
       nb_ubicacion, to_char(fe_fecha_registro, 'yyyy/mm/dd'), fk_almacen, fk_ubicacion
		  FROM t01_detalle
			LEFT JOIN t13_articulo ON t01_detalle.fk_articulo = t13_articulo.pk_articulo
			LEFT JOIN s01_persona ON s01_persona.co_persona = t01_detalle.fk_responsable
			LEFT JOIN t10_ubicacion ON t01_detalle.fk_ubicacion = t10_ubicacion.pk_ubicacion
			LEFT JOIN t09_almacen ON t09_almacen.pk_almacen = t10_ubicacion.fk_almacen
			WHERE pk_detalle = $x_movimiento";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			
			$o_indicador	    = $row[0];
			$x_descripcion		= $row[1];	
			$o_cantidad  		= $row[2];
			$o_precio        	= $row[3];
			$x_ubicacion     	= $row[4]==null?0:$row[10]; // Para leer el null
			$o_fecha            = $row[5];
			$x_almacen          = $row[6];
			$co_ubicacion       = $row[7];
		
			
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}

?>



<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			<?php echo  "Inventariar";?>
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
			<div class="row">
				<div class="col-xs-12 col-sm-6 widget-container-col">
					<div class="widget-box ">
						<div class="widget-header widget-header-small">
							<h4 class="widget-title"> <?php echo "Ubicar Herramienta"?> </h4>
						</div>
			
						<div class="widget-body">
							<div class="widget-main">
								<form class="form-horizontal" name="formulario">
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" for="x_almacen">Almacen</label>
										<div class="col-sm-7" >
											<select  name="x_almacen" class="col-xs-10 col-sm-7" id="id_almacen">
												<?php
													if ($x_almacen == ""){
														echo "<option value=0 selected>Seleccionar -&gt;</option>";
													}else{
														echo "<option value=0>Seleccionar -&gt;</option>";
													}
													foreach($arr_almacen as $k => $v) {
														$ls_cadenasel =($k == $x_almacen)?'selected':'';
														echo "<option value='$k' $ls_cadenasel>$v</option>";                
													}
												?>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" > Ubicacion </label>
										<div class="col-sm-7" >
										<select  name="x_ubicacion" class="col-xs-10 col-sm-7" id="id_ubicacion">
											
										</select>
										</div>
									</div>
									
									<input type="hidden" name="tarea" value="<?php echo $tarea;?>"> 
									<input type="hidden" name="x_movimiento" value="<?php echo $x_movimiento;?>">
									
								</form>
							</div>
						</div>
					</div>
				</div>
			
				<div class="col-xs-12 col-sm-6">
					<div class="profile-user-info profile-user-info-striped">
						
						<div class="profile-info-row">
								<div class="profile-info-name"> Articulo </div>
							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $x_descripcion;?></span>
							</div>							
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name"> Fecha </div>
							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $o_fecha;?></span>
							</div>
						</div>

						<div class="profile-info-row">
							<div class="profile-info-name"> Responsable </div>
							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $o_indicador;?></span>
							</div>
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name">Clasificacion</div>
							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $o_clasificacion;?></span>
							</div>
						</div>
												
						<div class="profile-info-row">
							<div class="profile-info-name"> Precio </div>
							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $o_precio;?></span> 
							</div>
						</div>
						
					
					</div>
				</div>
			
			</div> <!-- /.Row datos -->
			
	
			<div class="space-4"></div>
			
			<div class="row">
				<div class="col-xs-12 ">
					<div class="form-group center">												
						<button type="button"  onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
							<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
							Regresar
						</button>
						
						<button type="button" onClick="Guardar('U');" class="btn btn-sm btn-success">
							<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
							Guardar
						</button>																								
					</div>
				</div>
			</div>
			
			
		</div> <!-- /.row Datos  -->
	</div> <!-- /.row tabla principal -->
</div> <!-- /.page-content -->

</body>

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>
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

			$("#id_almacen").change(function () {
				   $("#id_almacen option:selected").each(function () {
					elegido=$(this).val();
					$.post("ajax_inventario.php", { elegido: elegido, ubicacion: "2" }, function(data){
					$("#id_ubicacion").html(data);
					});            
				});
			})

			//show datepicker when clicking on the icon
			.next().on(ace.click_event, function(){
				$(this).prev().focus();
			});			

		} );
	</script>


	<script type="text/javascript">
	/*----------- LOGICA DE NEGOCIO    ----------------------*/	
			function Buscar(){
			document.formulario.tarea.value = "B";
			document.formulario.action = "alm_inventario_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
		function Guardar(tarea){
			document.formulario.tarea.value = tarea;
			document.formulario.action = "alm_inventario_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
		
		function Inventario(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "alm_inventario_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
		
		
		function Atras(parametros){
			location.href = "alm_inventario_view.php?" + parametros;
		}	
		
	</script>

</html>

