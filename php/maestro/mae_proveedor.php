<?php 
/*-------------------------------------------------------------------------------------------|
|  						Verificación Y Autentificación De Usuario.                           |
|-------------------------------------------------------------------------------------------*/ 
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
<title>La Peperana</title>
		
	<!-- bootstrap & fontawesome -->
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
/*------------------------------------------------------------------------------------------------|
|		Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					  |
|------------------------------------------------------------------------------------------------*/
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
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
/*------------------------------------------------------------------------------------------------|
|					Rutina: Se Utiliza Para Eliminar Un Proveedor.							 	  
|------------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "SELECT * FROM t20_factura WHERE fk_proveedor= '$pk_proveedor' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM t03_proveedor WHERE pk_proveedor = '$pk_proveedor' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Proveedor Eliminado Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Este Proveedor Esta Asociado A Diferentes Productos!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}

	
	
	
/*------------------------------------------------------------------------------------------------|
|					Rutina: Se Utiliza Para Buscar Y Filtrar Proveedores.					 	  |
|------------------------------------------------------------------------------------------------*/
		$i=0; $j=0;
		if($x_rif!='')$arr_criterio[$i++]= "tx_rif like '".strtoupper($x_rif)."%' ";
		if($x_nombre!='')$arr_criterio[$i++]= "nb_proveedor like '".strtoupper($x_nombre)."%' ";
		
		for($j=0;$j<$i;$j++){
			$ls_criterio = $ls_criterio.(($ls_criterio=='')?$arr_criterio[$j]:" AND ".$arr_criterio[$j]);
		}
		$ls_criterio = $ls_criterio==""?"":" WHERE ".$ls_criterio;
		$li_tampag = 100;
		$ls_sql = "SELECT substr(nb_proveedor, 0,70), tx_telefono, substr(tx_direccion, 0,70), pk_proveedor
					FROM  t03_proveedor" .$ls_criterio. " ORDER BY nb_proveedor";
		
		//echo $ls_sql ;
					
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		$li_inicio = $obj_miconexion->fun_tampagina($li_pagina, $li_tampag); 
		$li_totreg = $obj_miconexion->fun_numregistros( $ls_resultado);
		
		if ($li_totreg > 0){
			$ls_sql = $ls_sql.sprintf(" LIMIT %d OFFSET %d;", $li_tampag, $li_inicio);
			$ls_resultado= $obj_miconexion->fun_consult($ls_sql);
		}
		$li_totpag  = $obj_miconexion->fun_calcpag( $li_totreg, $li_tampag);
		
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) != 0){
				//Sin Error
			}else{
				
			}	
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}	

	
/*--------------------------------------------------------------------------------------------------|
|          							Fin De Rutinas Para El Mantenimiento.                          	|
|--------------------------------------------------------------------------------------------------*/
?>



<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Proveedores
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					<div class="row">
						<div class="col-xs-12 col-sm-2">
							<button class="btn btn-success btn-sm pull-left" onClick="Agregar_proveedor()">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Proveedor
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
							<form class="form-horizontal" name="formulario">
							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Nombre</th>
										<th class="hidden-480">Teléfono</th>
										<th class="hidden-480">Direccion</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
										$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-3;  // Columnas Que Se Muestran En La Tabla.
										$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Referencia Al Índice De La Columna Clave.
										fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTAR_PROVEEDOR"); // Dibuja La Tabla De Datos.
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado); // Cierra Conexión.
									?>
								</tbody>
							</table>
							<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
							<input type="hidden" name="pk_proveedor" value="<?php echo $pk_proveedor;?>">   

							</form>			
						</div>
					</div> <!-- /.row tabla principal -->		

				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->


		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		<script src="../../assets/js/bootstrap.min.js"></script>
		<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery.dataTables.min.js"></script>
		<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>
	

		<!-- inline scripts related to this page -->
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
						null,
						null,
						{ "orderable": false }
					  ]
				} );
		
				//or change it into a date range picker
				$('.input-daterange').datepicker({
					
					autoclose:true,
					format: "dd/mm/yyyy"
					
				});
				
			
				
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
	/*------------------------------------------------------------------------------------------------|
|	Función: 'Buscar'																		 	  |
|	Descripción: Permite Realizar Un Búsqueda De Los Proveedores En La Base De Datos.	 		  |
|------------------------------------------------------------------------------------------------*/		
	function Buscar(){	
		document.formulario.tarea.value = "B";
		document.formulario.action = "mae_proveedor.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*------------------------------------------------------------------------------------------------|
|	Función: 'Agregar_proveedor'															 	  |
|	Descripción: Envía A La Página 'ma_agregar_proveedor.php' Para Agregar Un Nuevo Proveedor	  |
|------------------------------------------------------------------------------------------------*/			
	function Agregar_proveedor(){
		document.formulario.tarea.value = "X";
		document.formulario.action = "mae_proveedor_add.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*------------------------------------------------------------------------------------------------|
|	Función: 'Editar'																	 	 	  |
|	Descripción: Envía A La Página 'ma_agregar_proveedor.php' Para Editar Los Datos Del Proveedor |
|------------------------------------------------------------------------------------------------*/			
	function Editar(pk_proveedor){
		document.formulario.tarea.value = "M";
		document.formulario.action = "mae_proveedor_add.php";
		document.formulario.pk_proveedor.value = pk_proveedor;
		document.formulario.method = "POST";
		document.formulario.submit();
	}
	

function DetalleProveedor(pk_proveedor){
		document.formulario.action = "proveedor_detalle.php";
		document.formulario.pk_proveedor.value = pk_proveedor;
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*------------------------------------------------------------------------------------------------|
|	Función: 'Eliminar'																	 		  |
|	Descripción: Permite ELiminar Un Proveedor De La Base De Datos.	 					 		  |
|------------------------------------------------------------------------------------------------*/		
	function Eliminar(pk_proveedor){
		if (confirm("¿Realmente Desea Eliminar Este Proveedor?")){ 			
			document.formulario.tarea.value = "E";
			document.formulario.action = "mae_proveedor.php";
			document.formulario.pk_proveedor.value = pk_proveedor;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}	
	
	</script>

</body>
</html>

