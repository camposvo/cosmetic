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
	ELIMINAR UN USUARIO
--------------------------------------------------------------------------------------------*/	
	if($tarea == "E"){
		$ls_sql = "UPDATE s01_persona SET in_activo='N' WHERE co_persona= $co_usuario";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			$msg = "¡Eliminado Exitosamente!.";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}
	}
	
/*-------------------------------------------------------------------------------------------
	AGREGA UN USUARIO A LA LISTA DE CORREO
--------------------------------------------------------------------------------------------*/	
	if($tarea == "AG"){
		$ls_sql = "UPDATE s01_persona SET in_grupo_correo='S' WHERE co_persona= $co_usuario";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			$msg = "¡Asignado a la Lista de Mensaje !.";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}
	}
/*-------------------------------------------------------------------------------------------
	ELIMINA UN USUARIO DE LA LISTA DE CORREO
--------------------------------------------------------------------------------------------*/	
	if($tarea == "EG"){
		$ls_sql = "UPDATE s01_persona SET in_grupo_correo='N' WHERE co_persona= $co_usuario";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			$msg = "¡Retirado de Lista de Mensajes!.";
			echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}
	}

/*-------------------------------------------------------------------------------------------
	LEE LISTA DE USUARIOS
--------------------------------------------------------------------------------------------*/	
	$ls_sql = "SELECT to_char(co_persona,'000000'), UPPER(tx_nombre)||' ' ||UPPER(tx_apellido), tx_telefono_hab, UPPER(tx_indicador), in_grupo_correo, co_password, co_persona 
				FROM  s01_persona where in_activo = 'S' ORDER BY tx_nombre ";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	//echo $ls_sql;
	if ($ls_resultado != 0){
		$tarea      = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}	
	$co_usuario = 0;
?>


<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Administrar Usuarios
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-2">
							<button class="btn btn-success btn-sm pull-left" onclick="Agregar_usuario()">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Nuevo Usuario
							</button>
						</div>
					</div>	
						
								
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Ventas
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th class='hidden-480' >id</th>
											<th class='' >Nombre</th>
											<th class='hidden-480'>Telefono</th>
											<th class='hidden-480' >Indicador</th>
											<th >Msg</th>
											<th >Pass</th>
											<th ></th>
										</tr>
									</thead>
									<tbody>	
										<?php
											if($tarea == "M"){
												$li_totcampos =0;
												$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1;
												fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"BUSCAR_USUARIO");
											}
										?> 
									</tbody>
								</table>
								<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
								<input name="co_usuario" type="hidden" value="<?php echo $co_usuario;?>">
								<input type="hidden" id = "input_filtro" name="input_filtro" value="<?php echo $input_filtro;?>">	
						</form>
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->

	<script src="../../assets/js/jquery.2.1.1.min.js"></script>
	<script src="../../assets/js/bootstrap.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		 $(document).ready(function() {
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
				
			$('#dynamic-table').dataTable( {
				"search": {
					"search": $('#input_filtro').val()
				  },
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
			
			
			var table = $('#dynamic-table').DataTable(); 
			table.on( 'search.dt', function () {
				$('#input_filtro').val(table.search());
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


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript"> 
	function Agrega_GMensaje(identificador){
		document.formulario.action = "sis_usuario.php";
		document.formulario.co_usuario.value = identificador;
		document.formulario.tarea.value = "AG";
		document.formulario.method = "post";
		document.formulario.submit();
	}

	function Elimina_GMensaje(identificador){
		document.formulario.action = "sis_usuario.php";
		document.formulario.co_usuario.value = identificador;
		document.formulario.tarea.value = "EG";
		document.formulario.method = "post";
		document.formulario.submit();
	}

	function Foto(identificador){
		document.formulario.action = "sis_usuario_foto.php";
		document.formulario.co_usuario.value = identificador;
		document.formulario.method = "post";
		document.formulario.submit();
	}

	function Buscar(){
		document.formulario.action = "sis_usuario.php";
		document.formulario.tarea.value = "B";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function Editar(identificador){
		document.formulario.action = "sis_usuario_mtto.php";
		document.formulario.co_usuario.value = identificador;
		document.formulario.tarea.value = "M";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	function Eliminar(identificador){
		if (confirm("¿Realmente desea eliminarlo?")){
			document.formulario.action = "sis_usuario.php";
			document.formulario.tarea.value = "E";
			document.formulario.co_usuario.value = identificador;
			document.formulario.method = "post";
			document.formulario.submit();
		}
	}
	function Asignar_privilegios(identificador){
		document.formulario.action = "sis_usuario_rol.php";
		document.formulario.co_usuario.value = identificador;
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function Agregar_usuario(){
		document.formulario.tarea.value = "X";
		document.formulario.action = "sis_usuario_mtto.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function Password(identificador){
		document.formulario.tarea.value = "X";
		document.formulario.action = "sis_password_adm.php";
		document.formulario.co_usuario.value = identificador;
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function validar(e){
		tecla = (document.all) ? e.keyCode : e.which;
		if (tecla == 13){
			Buscar();
		}
		if (document.formulario.op_buscar[0].checked == true){
			return validarNum(e);
		}else{
			return validarLetras(e);
		}
	}
	
	function cambio(){
		document.formulario.o_parametro.value = "";
		document.formulario.o_parametro.focus();
	}
	
	
	</script>

</body>
</html>