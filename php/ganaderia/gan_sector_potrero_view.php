<?php 
	session_start();
	include_once ("gan_utilidad.php");
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
	
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		
</head>
<body>
<?php 
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nuevo Registro';
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
		$filtro = isset($filtro)?$filtro:'NO_ALL';
	}
	
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$co_usuario  =  $_SESSION["li_cod_usuario"];
	$arr_cliente =  Combo_Cliente();
	
/*-------------------------------------------------------------------------------------------|
| Rutina: Para Eliminar Un Potrero
|-------------------------------------------------------------------------------------------*/	
	if ($tarea == "E"){
		$ls_sql = "SELECT * FROM  gan_ganado WHERE fk_potrero= '$pk_potrero' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM gan_potrero WHERE pk_potrero = '$pk_potrero' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Ubicación Eliminada Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Esta Ubicacón Esta Asociada A Diferentes Productos!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}

	
	$ls_sql = "SELECT nb_sector, tx_descripcion
				   FROM   gan_sector
				   WHERE pk_sector = '$pk_sector'";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	
	if($ls_resultado != 0){
		$row             = pg_fetch_row($ls_resultado,0);
		$o_sector        = $row[0];
		$x_comentario    = $row[1];
		
	$obj_miconexion->fun_closepg($li_id_conex); 
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}		
	
	
/*-------------------------------------------------------------------------------------------|
           LEE LISTA DE POTREROS
|-------------------------------------------------------------------------------------------*/	
	$ls_sql = " SELECT to_char(gan_potrero.pk_potrero,'P000'), gan_potrero.nb_potrero, nu_hectareas, tx_ubicacion, 
				gan_potrero.pk_potrero
				FROM gan_potrero
				INNER JOIN gan_sector ON gan_potrero.fk_sector= gan_sector.pk_sector
				WHERE pk_sector = '$pk_sector' 
				ORDER BY nb_potrero";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="form-group left">												
				<button type="button" onClick="Atras()" class="btn btn-sm  btn-danger">
					<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
					Regresar
				</button>
				
																						
			</div>			
			
			<div class="col-sm-6">
				<div class="row">
					<div class="col-xs-11 label label-lg label-info arrowed-in arrowed-right">
						<b>Datos del Sector</b>
					</div>
				</div>

				<div>
					<ul class="list-unstyled spaced">
						<li>
							<i class="ace-icon fa fa-caret-right blue"></i>
							<b class="blue"><?php echo $o_sector; ?></b>
						</li>
						
						<li>
							<i class="ace-icon fa fa-caret-right blue"></i>
							Descripcion:
							<b class="black"><?php echo $x_comentario; ?></b>
						</li>
						
					</ul>
				</div>
			</div><!-- /.col -->
			
			<div class="row">
				<div class="col-xs-12"> 
																		
					<div class="row">
						<div class="col-xs-12">							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Potreros
							</div>
							<form class="form-horizontal" name="formulario">
							<table id="" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr>
										<th class="">ID</th>
										<th class="">Nombre</th>
										<th class="">Hcta.</th>
										<th class="">Descripcion</th>
									</tr>
								</thead>
								<tbody>	
									<?php   
											
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'VER_SECTOR_POTRERO',0); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
									?>
								</tbody>
							</table>
						</div>
					</div> <!-- /.row tabla principal -->

					<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
					<input type="hidden" name="pk_potrero" value="<?php echo $pk_potrero; ?>">	
					</form>								
							
						
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->


	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	
	<script src="../../assets/js/ace.min.js"></script> 
	<script src="../../assets/js/chosen.jquery.min.js"></script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical			
			
			$('#dynamic-table').dataTable( {
				"lengthChange": false,
				"pageLength": 50,
				"aaSorting": [ [0,'desc'] ],
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
					null,
					null,
					null,
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
				
		function Eliminar_Potrero(identificador){
			if (confirm('¿Realmente Desea Eliminar Esta Ubicación?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.action = "gan_potrero_view.php";
				document.formulario.pk_potrero.value = identificador;
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
		
		function Agregar_Potrero(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_potrero_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Editar_Potrero(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.action = "gan_potrero_add.php";
			document.formulario.pk_potrero.value = identificador;
			document.formulario.method = "POST";
			document.formulario.submit();
		}	

		function Ver_Lote(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_lote_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Ver_Sector(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_sector_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Atras(){
			location.href = "gan_sector_view.php";
		}	
				
	</script>
</body>
</html>