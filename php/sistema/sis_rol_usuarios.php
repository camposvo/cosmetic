<?php
/*-------------------------------------------------------------------------------------------
	Nombre: sis_usuario_funciones.php                                            
	Descripcion: Esta interfaz MUESTRA los USUARIOS de un determinado ROL
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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

/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para recibir las variables por la url.
-------------------------------------------------------------------------------------------*/
	if (!$_GET){
		foreach($_POST as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$li_pagina=1;
		$li_totpag = isset($_POST['li_totpag'])?$_POST['']:'';
		$li_totreg = isset($_POST['li_totreg'])?$_POST['']:'';	
	}else{
		foreach($_GET as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$li_pagina = isset($_GET['li_pagina'])?$_GET['li_pagina']:'';
		$li_totpag = isset($_GET['li_totpag'])?$_GET['']:'';
		$li_totreg = isset($_GET['li_totreg '])?$_GET['li_totreg ']:'';
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para buscar el nombre del rol que se desea MOSTRAR
--------------------------------------------------------------------------------------------*/		
	$obj_miconexion_1 = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion_1);

	$ls_sql = "SELECT tx_rol FROM s04_rol WHERE co_rol = '$co_rol' ";
		
	$ls_resultado =  $obj_miconexion_1->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$ls_nombre_rol = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINA: Construye el criterio dependiendo si se selecciono un plantel
--------------------------------------------------------------------------------------------*/	

	$temp1 = " INNER JOIN c009t_persona_plantel ON s02_persona_rol.co_persona = c009t_persona_plantel.co_persona
			WHERE co_rol = '$co_rol' ";

	$temp2 = "WHERE co_rol = '$co_rol'";
			
	$criterio = $o_plantel==0?$temp2:$temp1.'	AND c009t_persona_plantel.co_plantel = '.$o_plantel;


	$li_tampag = 500; // Limite del nmero de filas a mostrar en el grid o tabla
				$obj_miconexion = fun_crear_objeto_conexion();
				$li_id_conex = fun_conexion($obj_miconexion);
				$ls_sql = "SELECT tx_cedula, tx_nombre, tx_apellido,	 
								 tx_indicador,
								s02_persona_rol.co_persona
							FROM s02_persona_rol 
							INNER JOIN s01_persona ON s02_persona_rol.co_persona = s01_persona.co_persona "
							.$criterio."	ORDER BY tx_cedula";
							echo $ls_sql;
				
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				$li_inicio = $obj_miconexion->fun_tampagina($li_pagina, $li_tampag); 
				$li_totreg = $obj_miconexion->fun_numregistros($ls_resultado);
				if ($li_totreg > 0){
					$ls_sql = $ls_sql." LIMIT %d OFFSET %d;";
					$ls_sql = sprintf($ls_sql, $li_tampag, $li_inicio);
					$ls_resultado = $obj_miconexion->fun_consult($ls_sql);
				}
				$li_totpag  = $obj_miconexion->fun_calcpag( $li_totreg, $li_tampag);
				if ($ls_resultado == 0){
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
				}
	
	
	
/*-------------------------------------------------------------------------------------------
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.             
|------------------------------------------------------------------------------------------*/
?>


<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Rol
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
				
							
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="form-group left">												
								<button type="button" onClick="location.href = 'sis_rol.php'"  class="btn btn-sm  btn-danger">
									<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
									Regresar
								</button>																		
							</div>
						
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Rol: <?php echo $ls_nombre_rol;?>
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>Cedula</th>
											<th>Nombre</th>
											<th>Apellido</th>
											<th>Indicador</th>
										</tr>
									</thead>
									<tbody>	
										<?php 
											$li_numcampo = $obj_miconexion->fun_numcampos()-1; // Columnas que se muestran en la Tabla
											$li_indicecampo = $li_numcampo;  // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'USUARIO_ROL'); // Dibuja la Tabla de Datos
										?>
									</tbody>
								</table>
								<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
								<input name="co_rol" type="hidden" value="<?php echo $co_rol;?>">
								<input name="co_usuario" type="hidden" value="<?php echo $co_usuario;?>">
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
						null
					  ]
				} );
		
								
			
			} );
			
			
			
			
			
		</script>


		
		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript">
	//SCRIPT: Retorna la pagina que la llamo
	function Cancelar(){
		co_plantel = document.formulario.o_plantel.value;
		location.href = 'sis_rol.php';
	}
	
	function Exportar(){
		document.formulario.tarea.value = "B";
		document.formulario.action = "sis_usuario_funciones_excel.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
	function Buscar(){
		document.formulario.action = "sis_usuario_funciones.php";
		document.formulario.method = "post";
		document.formulario.submit();
	}
	
</script>

</body>
</html>