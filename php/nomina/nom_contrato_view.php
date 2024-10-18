<?php 

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
	
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
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
	$encontrado = false;
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
	RUTINA: Se utiliza para ELIMINAR un USUARIO
--------------------------------------------------------------------------------------------*/	
	if($tarea == "E"){
		$ls_sql = "UPDATE t12_contrato SET in_activo='N' WHERE pk_contrato= $co_contrato";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			$msg = "¡Eliminado Exitosamente!.";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: ACTUALIZA ESTATUS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "S"){
		$ls_sql = "SELECT in_activo FROM t12_contrato 
		WHERE pk_contrato = '$co_contrato' ";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$status_old  = $row[0];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
		}
	
		$status_new = $status_old=='S'?'N':'S'; 
		
		$ls_sql = "UPDATE t12_contrato SET  in_activo='".$status_new."'
			WHERE pk_contrato = '$co_contrato' ";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Estado Actualizado!');</script>";
		}

	}	

/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para buscar y filtrar USUARIOS
--------------------------------------------------------------------------------------------*/	

	$ls_sql = "SELECT to_char(t20_factura.pk_factura,'0000000'), tx_cedula, UPPER(tx_nombre) || ' ' || UPPER(tx_apellido), 
				to_char(fe_inicio,'dd-TMMon-yyyy'),	to_char(fe_fin,'dd-TMMon-yyyy'), 
				t12_contrato.in_activo, pk_contrato
				FROM t12_contrato 
				INNER JOIN s01_persona ON s01_persona.co_persona = t12_contrato.fk_trabajador
				LEFT JOIN t20_factura ON t20_factura.fk_contrato =  t12_contrato.pk_contrato
				 ORDER BY fe_inicio";
	
	//echo $ls_sql;
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado) != 0){
			$tarea      = "M";
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}	
	$co_usuario = 0;
		

/*------------------------------------------------------------------------------------------
|                  FIN DE RUTINAS PARA EL MANTENIMIENTO.             
|------------------------------------------------------------------------------------------*/
?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Administrar Contratos
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-2">
							<button class="btn btn-success btn-sm pull-left" onclick="Agregar()">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Contrato
							</button>
						</div>
					</div>
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Conratos
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th class='hidden-480' >id</th>
											<th >Cedula</th>
											<th >Nombre(S)</th>
											<th class='hidden-480'>Inicia</th>
											<th class='hidden-480'>Vence</th>
											<th class='hidden-480'>Estado</th>
											<th ></th>
										</tr>
									</thead>
									<tbody>	
										<?php
											if($tarea == "M"){
												$li_totcampos = 0;
												$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1;
												fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTA_CONTRATO");
											}
										?> 
									</tbody>
								</table>
							<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
							<input name="co_usuario" type="hidden" value="<?php echo $co_usuario;?>">
							<input name="co_contrato" type="hidden" value="<?php echo $co_contrato;?>">
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
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>


	<script type="text/javascript">
		 
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			// Datatable	
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
					null,
					null,
					null,
					{ "orderable": false },
					{ "orderable": false },
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

		function Buscar(){
			document.formulario.action = "nom_contrato_view.php";
			document.formulario.tarea.value = "B";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		function EditarContrato(identificador){	
			document.formulario.action = "nom_contrato_add.php";
			document.formulario.co_contrato.value = identificador;
			document.formulario.tarea.value = "M";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		function Agregar(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "nom_contrato_add.php";
			document.formulario.method = "post";
			document.formulario.submit();
		}
		
		
		function Eliminar(identificador){
			if (confirm("¿Realmente Desea Eliminar Este Contrato?")){ 			
				document.formulario.tarea.value = "E";
				document.formulario.action = "nom_contrato_view.php";
				document.formulario.co_contrato.value = identificador;
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}	

		function Actualizar_Estatus(identificador){
			if (confirm('Desea Actualizar el Estado del Contrato?') == true){
				document.formulario.tarea.value = "S";
				document.formulario.co_contrato.value = identificador;
				document.formulario.action = "nom_contrato_view.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}		
		
	</script>

</body>
</html>

