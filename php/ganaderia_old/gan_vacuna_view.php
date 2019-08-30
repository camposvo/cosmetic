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
	$o_cantidad  = 0;
	$o_cantidad2 = 0;
	$x_proyecto     = 0;
	
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
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para ELIMINAR una actividad 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT * FROM gan_animal_vacuna WHERE fk_vacuna= '$pk_vacuna' ";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM gan_vacuna WHERE pk_vacuna = '$pk_vacuna' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Registro Eliminado Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Esta Asociado a un Ganado!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}
		
/*-------------------------------------------------------------------------------------------
RUTINAS: Consulta  de registros de la busqueda
-------------------------------------------------------------------------------------------*/	
		
	$ls_sql = "SELECT UPPER(nb_vacuna), tx_descripcion, tx_dosis, pk_vacuna 
				FROM gan_vacuna	";
		
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}

?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-12 ">
						<button class="btn btn-success btn-sm pull-left " onclick="Agregar()">
							<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
							Vacuna
						</button>
					</div>
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> 
																		
					<div class="row">
						<div class="col-xs-12">							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Vacunas
							</div>
							<form class="form-horizontal" name="formulario">
							<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr>
										<th class="">Nombre</th>
										<th class="">Dosis</th>
										<th class="">Descripcion</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										$li_numcampo = 0; // Columnas que se muestran en la Tabla
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_VACUNA',0); // Dibuja la Tabla de Datos
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);

									?>
								</tbody>
							</table>
						</div>
					</div> <!-- /.row tabla principal -->

					<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
					<input type="hidden" name="pk_vacuna" value="<?php echo $pk_vacuna; ?>">	
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
			
	function Agregar(){
		document.formulario.tarea.value = "X";
		document.formulario.action = "gan_vacuna_add.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}		
	
			
	function Eliminar_Vacuna(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.pk_vacuna.value = identificador;
			document.formulario.action = "gan_vacuna_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	function Editar_Vacuna(identificador){
		document.formulario.tarea.value = "M";
		document.formulario.pk_vacuna.value = identificador;
		document.formulario.action = "gan_vacuna_add.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}		
	
</script>

</body>
</html>