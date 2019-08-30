<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: sis_usuario_rol.php                                                    
	Descripcion: contiene una interfaz donde se visualiza los roles creados previamente
		de un usuario determinado
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("sis_utilidad.php");
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
	}else{
		foreach($_GET as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}

	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	RUTINA: Guardar los roles al usuario.
--------------------------------------------------------------------------------------------*/
	if ($tarea == "G"){
		$ls_sql = "DELETE FROM s02_persona_rol WHERE co_persona = '$co_usuario'";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		for ($i = 0; $i < $n_rol; $i++){
			$asignacion = "\$rol". $i;
			eval("\$dato = \"$asignacion\";");
			if ($dato != ""){
				$ls_sql = "INSERT INTO s02_persona_rol(co_persona   ,co_rol)
						   VALUES('$co_usuario','$dato')";
				
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			}
		}
		
		
		$parametros = "tarea=B&co_usuario=$co_usuario";
		$msg = "Guardado Exitosamente!.";
		echo "<script language='javascript' type='text/javascript'>alert('$msg');location.href='sis_usuario.php?$parametros';</script>";
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINA: llenar el array de roles para luego mostrar en la interfaz.
--------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT co_rol,tx_rol 
				FROM s04_rol ORDER BY tx_rol ";/*consulto todos los roles*/
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		$i=0;
		while($fila = pg_fetch_row($ls_resultado)){
			$roles[$i][0] = $fila[0];
			$roles[$i][1] = $fila[1];
			$i++;
		}
		$n_rol = $i;
		
		$ls_sql = "SELECT s02_persona_rol.co_rol, s04_rol.tx_rol 
					FROM s02_persona_rol,s04_rol 
					WHERE s04_rol.co_rol=s02_persona_rol.co_rol and co_persona='$co_usuario'";
					
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			while($fila = pg_fetch_row($ls_resultado)){
				for ($x = 0; $x < $i; $x++){
					if ($roles[$x][0] == $fila[0]){
						$roles[$x][2] = "checked";
					}
				}
			}
		}else{
		}
	}
	

/*-------------------------------------------------------------------------------------------
	RUTINA: cargar Nombre/Apellido del Usuario
--------------------------------------------------------------------------------------------*/
	$ls_sql =  "SELECT tx_nombre, tx_apellido
				FROM   s01_persona
				WHERE  co_persona = $co_usuario";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		if ($obj_miconexion->fun_numregistros() > 0){
			$row = pg_fetch_row($ls_resultado,0);
			$ls_nombre_apellido_usu      = $row[0]." ".$row[1];
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}

	$obj_miconexion->fun_closepg($li_id_conex); 
/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>


<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Roles de Usuario
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box widget-color-blue">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> Usuario: <?php echo $ls_nombre_apellido_usu; ?> </h4>
								</div>
								
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
										
													
													
										<div class="row">
											<div class="col-xs-12">
												
												<div class="clearfix">
													<div class="pull-right tableTools-container"></div>
												</div>
												
												
												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<th>Rol</th>
															<th></th>
														</tr>
													</thead>
													<tbody>	
														<?php 
														for ($x = 0; $x < $n_rol; $x++){
														$ls_status = "";
														if (($rol_defaut == $roles[$x][1]) || ($rol_status == 1)){
															$ls_status = "disabled";
														}
														echo "<tr>";
															echo "<td >".$roles[$x][1]."</td>";
															echo "<td><input type='checkbox' ".$roles[$x][2]." name='rol".$x."' value='".$roles[$x][0]."' ".$ls_status."></td>";
														echo "</tr>";
														
														}
														?>
													</tbody>
												</table>
												
												
												
											</div>
										</div> <!-- /.row tabla principal -->
										<div class="form-group center">												
											
											
											
											
											<button type="button" onClick="Regresar('<?php echo "co_usuario=$co_usuario&tarea=B"; ?>')" class="btn btn-sm  btn-danger">
												<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
												Regresar
											</button>	
											
											<button type="button" onClick="Guardar_rol();" class="btn btn-sm btn-success">
												<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
												Guardar
											</button>
											<button type="button" onClick="Cancelar('<?php echo "co_usuario=$co_usuario&tarea=B"; ?>')" class="btn btn-sm  btn-default">
												<i class="ace-icon fa fa-undo bigger-110 icon-on-right"></i>
												Cancelar
											</button>
											
											
										</div>										
			

										<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
										<input name="co_usuario" type="hidden" value="<?php echo $co_usuario;?>">
										<input name="cedula" type="hidden" value="<?php echo $cedula;?>">
										<input name="n_rol" type="hidden" value="<?php echo $n_rol;?>">
										<input name="n_plantel" type="hidden" value="<?php echo $n_plantel;?>">						
										
										
										
									</form>
									</div>
								</div>
							</div>
						</div>					
					</div>	
						
					
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->






		<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
	
				
				//  Tooltip
				$( ".open-event" ).tooltip({
					show: null,
					position: {
						my: "left top",
						at: "left bottom"
					},
					open: function( event, ui ) {
						ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
					}
				});
				
				
			} );
		</script>


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript">
	function Guardar_rol(){
		if (confirm('Esta conforme con los Datos Ingresados?') == true){	
			document.formulario.action = "sis_usuario_rol.php";
			document.formulario.tarea.value = "G";
			document.formulario.method = "post";
			document.formulario.submit();
		}
	}
	
	function Regresar(parametros){
		location.href = "sis_usuario.php?" + parametros;
	}
	
	function Cancelar(parametros){
		location.href = "sis_usuario_rol.php?" + parametros;
	}
</script>

</body>
</html>