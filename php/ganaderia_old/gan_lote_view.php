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
<title>BellinghieriCosmetic</title>
	
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
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para ELIMINAR 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT * FROM gan_ganado WHERE fk_lote = '$pk_lote' ";
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM gan_lote WHERE pk_lote = '$pk_lote' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "Registro Eliminado Exitosamente!";
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
		
	$ls_sql = "SELECT to_char(pk_lote,'T000'), UPPER(nb_lote), tx_descripcion_lote,	pk_lote
			FROM gan_lote
			";
		
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
					<div class="col-xs-12"> 
						
						<div class="btn-group">
							<button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle">
								<i class="ace-icon fa fa-cubes align-top bigger-125 "></i>
								Gestion de Ubicaciones
								<i class="ace-icon fa fa-angle-down icon-on-right"></i>
							</button>

							<ul class="dropdown-menu dropdown-info ">
								<li>
									<a href="#" onclick="Ver_Sector()">Administrar Sector</a>
								</li>
								
								<li>
									<a href="#" onclick="Ver_Potrero()">Administrar Potrero</a>
								</li>

								
								
								<li>
									<a href="#" onclick="Ver_Lote()">Administrar Lote</a>
								</li>
								
							</ul>
						</div><!-- /.btn-group -->							
						
					</div><!-- /.page-header -->
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12 col-sm-12 ">
					<button class="btn btn-success btn-sm pull-left " onclick="Agregar_Lote()">
						<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
						Lote
					</button>					
				</div>
			</div>	
			
			<div class="row">
				<div class="col-xs-12"> 
					
																		
					<div class="row">
						<div class="col-xs-12">							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lotes
							</div>
							<form class="form-horizontal" name="formulario">
							<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr>
										<th class="">ID</th>
										<th class="">Nombre</th>
										<th class="">Descripcion</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										if($tarea == "M"){
											
											$li_numcampo = 0; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_LOTE',0); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
										}
									?>
								</tbody>
							</table>
						</div>
					</div> <!-- /.row tabla principal -->

					<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
					<input type="hidden" name="pk_lote" value="<?php echo $pk_lote; ?>">	
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
			
			$('[data-rel=tooltip]').tooltip({container:'body'});
			$('[data-rel=popover]').popover({container:'body'});
			
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

				
		function Agregar_Lote(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_lote_add.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}		
		
				
		function Eliminar_Lote(identificador){
			if (confirm('Desea Eliminar este Registro?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.pk_lote.value = identificador;
				document.formulario.action = "gan_lote_view.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
		
		function Editar_Lote(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.pk_lote.value = identificador;
			document.formulario.action = "gan_lote_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
/*-------------------------------------------------------------------------------------------
	BOTONES DEL HEADER
-------------------------------------------------------------------------------------------*/	
		function Ver_Sector(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_sector_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Ver_Potrero(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_potrero_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Ver_Lote(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "gan_lote_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		
				
	</script>
</body>
</html>