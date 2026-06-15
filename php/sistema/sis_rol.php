<?php

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
/*----------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
----------------------------------------------------------------------------*/
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
			
/*----------------------------------------------------------------------------
	RUTINA: Eliminar un rol.
----------------------------------------------------------------------------*/
	if($tarea == "E"){
		$ls_sql = "SELECT *FROM s02_persona_rol WHERE co_rol = '$co_rol' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){
				$ls_sql = "DELETE FROM s03_privilegio WHERE co_rol = '$co_rol' ";/*Se borran las pantallas asociadas al rol**/
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if ($ls_resultado != 0){
					
					$ls_sql = "DELETE FROM s04_rol WHERE co_rol = '$co_rol'";/*se borra el rol*/
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if ($ls_resultado != 0){
						$msg = "Eliminado Exitosamente!.";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
					}
				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
				}
			}else{
				$msg = "Este rol tiene usuarios asociados!.";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";						
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}
	
/*----------------------------------------------------------------------------
	RUTINA: Carga sentencia SQL para listar Roles
----------------------------------------------------------------------------*/
	$ls_sql = "SELECT tx_rol,co_rol FROM s04_rol ORDER BY tx_rol";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);

/*-------------------------------------------------------------------------------------------|
|                                 FIN DE RUTINAS PARA EL MANTENIMIENTO.                      |
|-------------------------------------------------------------------------------------------*/
?>


<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Administrar Roles
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-2">
							<button class="btn btn-success btn-sm pull-left" onClick="ir_a_pagina(1,'','sis_rol_mtto.php')">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Nuevo Rol
							</button>
						</div>
					</div>	
						
								
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Roles
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>Nombre</th>
											<th></th>											
										</tr>
									</thead>
									<tbody>	
										<?php 
											$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-1;
											$li_indice = $li_totcampos;
											fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTAR_ROL");
											$obj_miconexion->fun_closepg($li_id_conex); 	
										?>
									</tbody>
								</table>
								<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
								<input name="co_rol" type="hidden" value="<?php echo $co_rol;?>"> 
						</form>
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->

	<script src="../../js/funciones.js"></script>
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
		
			$('#dynamic-table').dataTable( {
				"lengthChange": false,
				"pageLength": 50,
				"oLanguage": {
					"sInfo": "Mostrando (_START_ hasta _END_) de un total _TOTAL_",
					"sSearch": "Buscar:",
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
				
				"columns": [
					null,						
					{ "orderable": false }
				  ]
			} );
			
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
  
	<script type="text/javascript"> 
		//SCRIPT: Funcion que invoca la pagina adecuada segun la accion del Usuario
		function ir_a_pagina(operacion,identificador,pagina){
			if (operacion == 1){//**nuevo rol
				location.href = pagina +'?tarea=N';
			}	
			if (operacion == 2){//**editos el nombre del rol
				pagina = pagina +'?tarea=E&co_rol='+identificador;
				location.href = pagina;
			}
			if (operacion == 3){//**editas los datos
				pagina = pagina +'?co_rol='+identificador;
				location.href = pagina;
			}
			if (operacion == 4){//**Listar usuarios con ese rol
				pagina = pagina +'?co_rol='+identificador;
				location.href = pagina;
			}
			
			if (operacion == 5){//**elimino los datos
				if (confirm('Desea a eliminar este rol?') == true){
					pagina = pagina +'?co_rol='+identificador+'&tarea=E';
					location.href = pagina;
				}
			}		
		}
	
	</script>

</body>
</html>