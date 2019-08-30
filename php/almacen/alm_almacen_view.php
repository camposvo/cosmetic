<?php 
/*--------------------------------------------------------------------------------------------------|
|  	Nombre: 'ma_almacen.php'          			                         							|
|  	Descripción: Esta Interfaz Muestra Y Permite Filtrar Todos Los Almacenes.			  			|
|--------------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------|
|  						Verificación Y Autentificación De Usuario.                           |
|-------------------------------------------------------------------------------------------*/ 
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
<title>La Peperana</title>

	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
	<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
				
</head>
<body>

<?php 
/*-------------------------------------------------------------------------------------------|
|					Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 				 |
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
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------|
|					Rutina: Se Utiliza Para Eliminar Un Almacén.							 |
|-------------------------------------------------------------------------------------------*/
	if($tarea == "E"){
		$ls_sql = "SELECT pk_ubicacion
					FROM t10_ubicacion WHERE fk_almacen = '$co_almacen' ";
	
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){
				$ls_sql = "DELETE FROM t09_almacen WHERE pk_almacen = '$co_almacen' ";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if ($ls_resultado != 0){
					$msg = "¡Almacén Eliminado Exitosamente!";
					echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
				}else{
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);  // Envía Mensaje De Error De Consulta.
				}
			}else{
				$msg = "¡Existen Ubicaciones Asociadas!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}
	}
	
/*-------------------------------------------------------------------------------------------|
|						Rutina: Se Utiliza Dibujar La Bandeja De Almacenes.				  	 |
|-------------------------------------------------------------------------------------------*/
	$li_tampag = 100;
	$ls_sql = " SELECT nb_almacen, tx_descripcion, pk_almacen
				FROM   t09_almacen 		
				ORDER BY nb_almacen";
	
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
			// Dibuja La Tabla Sin Ningun Dato, Esto Si No Hay Almacenes Registrados.
			$bandera=1;
		}	
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}	
/*-------------------------------------------------------------------------------------------|
|						Rutina: Se Utiliza Para Buscar Y Filtrar Almacenes.				  	 |
|-------------------------------------------------------------------------------------------*/
	if($tarea == "B"){
		$i=0; $j=0;
		if($co_almacen!= 0)$arr_criterio[$i++]= "t09_almacen.pk_almacen = ".$co_almacen;
		if($x_nombre!='')$arr_criterio[$i++]= "t09_almacen.nb_almacen like '".ucwords($x_nombre)."%' ";
				
		for($j=0;$j<$i;$j++){
			$ls_criterio = $ls_criterio.(($ls_criterio=='')?$arr_criterio[$j]:" AND ".$arr_criterio[$j]);
		}
		$ls_criterio = $ls_criterio==""?"":" WHERE ".$ls_criterio;
		$li_tampag = 100;
		$ls_sql = " SELECT nb_almacen, tx_descripcion, pk_almacen
					FROM   t09_almacen "
					.$ls_criterio." ORDER BY nb_almacen";
	
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
				$msg = "¡No Encontrado, Cambie El Criterio De Búsqueda E Intente Nuevamente!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}	
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}	
		$co_almacen = '';
	}

/*--------------------------------------------------------------------------------------------------|
|          						Fin De Rutinas Para El Mantenimiento.         	                 	|
|--------------------------------------------------------------------------------------------------*/
?>



<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<div class="row">
					<div class="col-xs-12 col-sm-2">
						<button class="btn btn-success btn-sm pull-left" onclick="Agregar_almacen()">
							<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
							Almacen
						</button>
					</div>
				</div>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					
					
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Almacenes
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>Nombre</th>
											<th class='hidden-480' >Descripcion</th>
											<th></th>
										</tr>
									</thead>
									<tbody>	
										<?php    
											$li_totcampos = $obj_miconexion->fun_numcampos($ls_resultado)-2;  // Columnas Que Se Muestran En La Tabla.
											$li_indice = $obj_miconexion->fun_numcampos($ls_resultado)-1; // Referencia Al Índice De La Columna Clave.
											fun_dibujar_tabla($obj_miconexion,$li_totcampos,$li_indice,"LISTAR_ALMACEN"); // Dibuja La Tabla De Datos.
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado); // Cierra Conexión.
										?>
									</tbody>
								</table>
								<input type="hidden" name="tarea" value="<?php echo $tarea; ?>">
								<input type="hidden" name="co_almacen" value="<?php echo $co_almacen; ?>">
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
	<script src="../../assets/js/daterangepicker.min.js"></script>
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>				
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
	
	function Buscar(){	
		document.formulario.tarea.value = "B";
		document.formulario.action = "alm_almacen_view.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*------------------------------------------------------------------------------------------------|
|	Función: 'Agregar_almacen'																 	  |
|	Descripción: Envía A La Página 'ma_agregar_almacen.php' Para Agregar Un Nuevo Almacén		  |
|------------------------------------------------------------------------------------------------*/			
	function Agregar_almacen(){
		document.formulario.tarea.value = "A";
		document.formulario.action = "alm_almacen_add.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}

/*-------------------------------------------------------------------------------------------------|
|	Función: 'Agregar_ubicacion'																   |
|	Descripción: Envía A La Página 'ma_ubicaciones.php' Para Agregar Ubicaciones Dentro Del Almacén|
|-------------------------------------------------------------------------------------------------*/		
	function Agregar_ubicacion(co_almacen){
		document.formulario.tarea.value = "X";
		document.formulario.action = "alm_ubicacion.php";
		document.formulario.co_almacen.value = co_almacen;
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*------------------------------------------------------------------------------------------------|
|	Función: 'Editar'																	 	 	  |
|	Descripción: Envía A La Página 'ma_agregar_almacen_mtto.php' Para Editar Los Datos Del Almacén|
|------------------------------------------------------------------------------------------------*/		
	function Editar(co_almacen){
		document.formulario.tarea.value = "M";
		document.formulario.action = "alm_almacen_mod.php";
		document.formulario.co_almacen.value = co_almacen;
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*------------------------------------------------------------------------------------------------|
|	Función: 'Eliminar'																		 	  |
|	Descripción: Permite ELiminar Un Almacén De La Base De Datos.	 					 		  |
|------------------------------------------------------------------------------------------------*/		
	function Eliminar(co_almacen){
	  if (confirm('¿Realmente Desea Eliminar Este Almacén?') == true){	
		  document.formulario.tarea.value = "E";
		  document.formulario.action = "alm_almacen_view.php";		  
		  document.formulario.co_almacen.value = co_almacen;
		  document.formulario.method = "POST";
		  document.formulario.submit();
	  }	
	}	
	
</script>

</body>
</html>
