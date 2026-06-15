<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: sis_rol_privilegio.php                                            
	Descripcion: Esta interfaz permite ASOCIAR/ELIMINAR privilegios de un ROL
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("sis_utilidad.php");
	require_once("../../clases/xajax/xajax_core/xajax.inc.php");
	
	$usu_autentico= isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI") {
	    session_destroy();
    	echo"<script language='JavaScript' type='text/JavaScript'>top.location.href='../../html/fin_sesion.html'</script>";
	    exit();
	}
/*-------------------------------------------------------------------------------------------|
|			Implantacin De Una Funcin Ajax Utilizando La Clase XAJAX     		     |
|-------------------------------------------------------------------------------------------*/
	/* $xajax = new xajax(); // Creo La Instancia.
	$xajax->registerFunction("Fun_Ajax_Menu"); // Asociamos La Funcin Al Objeto XAJAX.
	
	function Fun_Ajax_Menu($menu){
		$newContent = "<select name='o_sub_menu' class='col-xs-10 col-sm-7' >";
		$objResponse = new xajaxResponse();
			if ($menu == 0){
				$newContent.= "<option value='' selected>Seleccionar --></option>";
			}else{
				$newContent.= "<option value=''>Seleccionar --></option>";
			}
		$obj_miconexion = fun_crear_objeto_conexion();
		$li_id_conex = fun_conexion($obj_miconexion);
		$ls_sql = "SELECT  tx_submenu , co_menu_padre_hijo 
						FROM s06_menu_padre_hijo 
						WHERE co_menu_padre = '$menu'
						ORDER BY nu_orden ";
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			while($row = pg_fetch_row($ls_resultado)){
				$k = $row[0];
				$v = $row[1];
				$ls_cadenasel =($v == $menu)?'selected':'';
				$newContent.= "<option value='$v' $ls_cadenasel>$k</option>";                
			}
		$newContent.= "</select>";
		$objResponse->assign("Submenu","innerHTML", $newContent);
		return $objResponse;
	
	}
	
	
	$xajax->processRequest(); // Escribimos En La Capa Con id="respuesta" El Texto Que Aparece En $newContent.
 */
/*-------------------------------------------------------------------------------------------|
|											Fin XAJAX     		  						     |
|-------------------------------------------------------------------------------------------*/	
	
?>
<!DOCTYPE html>
<html>
<head>
<?php 
	// En El <head> Indicamos Al Objeto XAJAX Se Encargue De Generar El Javascript Necesario.
//	$xajax->printJavascript('../../clases/xajax'); 
?>
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
	$o_sub_menu = 0;
	$co_rol = 0;

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
		
		$o_menu = isset($o_menu )?$o_menu:0;
		
		$arr_menu_padre   =  Combo_Menu_Padre();
		
		
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para buscar el nombre del rol que se desea trabajar
-------------------------------------------------------------------------------------------*/		
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

	$ls_sql = "SELECT tx_rol FROM s04_rol WHERE co_rol = '$co_rol'";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if ($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$ls_nombre_rol = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para eliminar una funcionalidad del rol.
-------------------------------------------------------------------------------------------*/
	if($tarea == "E"){
		$ls_sql = "	DELETE FROM s03_privilegio 
					WHERE co_menu_padre_hijo = '$co_pantalla' AND co_rol = '$co_rol' ";
					
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
		}
		$tarea = "";
	}
		
/*-------------------------------------------------------------------------------------------
	RUTINA: Se utiliza para asociar un rol a una funcionalidad.
--------------------------------------------------------------------------------------------*/
		if($tarea == "A"){
			$ls_sql = "SELECT * FROM s03_privilegio 
								WHERE co_menu_padre_hijo = '$o_sub_menu' and co_rol = '$co_rol'";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if ($ls_resultado != 0){
				if($obj_miconexion->fun_numregistros($ls_resultado) == 0){
					$ls_sql = "INSERT INTO s03_privilegio(co_menu_padre_hijo   , co_rol )
													  VALUES('$o_sub_menu','$co_rol')";
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if ($ls_resultado != 0){
						$msg = "Asociada Exitosamente!.";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
					}
				}else{
					$msg = "Esta pantalla ya esta Asociada!.";
					echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
				}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
			}
		$tarea = "";
	}

	$ls_sql = "SELECT s05_menu_padre.tx_descripcion, s06_menu_padre_hijo.tx_submenu, s03_privilegio.co_menu_padre_hijo
						FROM s03_privilegio, s06_menu_padre_hijo, s05_menu_padre
						WHERE s06_menu_padre_hijo.co_menu_padre = s05_menu_padre.co_menu_padre and
								s03_privilegio.co_menu_padre_hijo = s06_menu_padre_hijo.co_menu_padre_hijo and
								co_rol = '$co_rol'
						ORDER BY s05_menu_padre.nu_orden asc,s06_menu_padre_hijo.nu_orden asc";
	//echo $ls_sql;
												
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__);
	}

		
/*-------------------------------------------------------------------------------------------
|                                    FIN DE RUTINAS PARA EL MANTENIMIENTO.       
|------------------------------------------------------------------------------------------*/
?>


<!-- Content Header (Page header) -->
<div class="container-fluid">
			<div class="page-header">
				<h1>
					Funciones
				</h1>
			</div><!-- /.page-header -->
			<div class="row"><!-- ROW CONTENT BEGINS -->
				<div class="col-xs-12">
				
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title"> <?php echo $ls_nombre_rol;?> </h4>
								</div>
					
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Menu Principal</label>
												<div class="col-sm-7">	
													<select class="col-xs-10 col-sm-7" name="o_menu"  id="o_menu" onChange="xajax_Fun_Ajax_Menu(this.value);">
														<?php
															if ($o_menu == ""){
																echo "<option value='0' selected>Seleccionar -&gt;</option>";
															}else{
																echo "<option value='0'>Seleccionar -&gt;</option>";
															}
															foreach($arr_menu_padre as $k => $v) {
																$ls_cadenasel =($k == $o_menu)?'selected':'';
																echo "<option value='$k' $ls_cadenasel>$v</option>";                
															}
														?>
													</select>
												</div>
											</div>										
																							
												
											<div class="form-group">
												<label  class="col-sm-3 control-label no-padding-right"  for="o_cantidad"> Sub Menu </label>
												<div class="col-sm-7" >
													<div  id="Submenu"></div>
												</div>
											</div>
											
																			
										
										<div class="form-group center">												
											<button type="button" onClick="Atras('<?php echo "tarea=B"; ?>')" class="btn btn-sm  btn-danger">
												<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
												Regresar
											</button>
											
											<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
												<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
												Guardar
											</button>																								
										</div>
											
										<input name="co_rol" type="hidden" value="<?php echo $co_rol;?>">
										<input name="co_pantalla" type="hidden" value="<?php echo $co_pantalla;?>">
										<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
											
										</form>
									</div>
								</div>
						</div>
					</div>
					
			
					<div class="space-4"></div>
			
					<div class="row">
						<div class="col-xs-12">
							<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
								<thead>
									<tr class="bg-primary" >
										<th>Menu Principal</th>
										<th>Menu Secundario</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php   
											$li_numcampo = $obj_miconexion->fun_numcampos()-1; // Columnas que se muestran en la Tabla
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_PRIVILEGIO'); // Dibuja la Tabla de Datos
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
									?>
								</tbody>
						</table>
					</div> 
				</div>
				</div>
			</div> <!-- /.row tabla principal -->
		</div> <!-- /.page-content -->


</body>

<!--  SISTEMA   -->	
	<script src="../../js/funciones.js"></script>  
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
			
		$('#dynamic-table').dataTable( {
			"lengthChange": false,
			"pageLength": 50,
			"aaSorting": [ [0,'asc'] ],
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
						
			window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
		
		} );
		
	</script>
				
				

<script type="text/javascript">
	    
	function Eliminar(identificador){
		if (confirm('Desea eliminar esta pantalla?') == true){
				document.formulario.co_pantalla.value = identificador;
				document.formulario.action = 'sis_rol_privilegio.php';
				document.formulario.tarea.value = "E";
				document.formulario.method = "post";
				document.formulario.submit();
			}
	}
	function Guardar(){
		if(document.formulario.o_menu.value != 0){
			if(document.formulario.o_sub_menu.value != 0){
				document.formulario.action = "sis_rol_privilegio.php";
				document.formulario.tarea.value = "A";
				document.formulario.method = "post";
				document.formulario.submit();
			}else{
				alert('Este dato es requerido!.');
				document.formulario.o_sub_menu.focus();
			}		
		}else{
			alert('Este dato es requerido!.');
			document.formulario.o_menu.focus();
		}
	}	
	

	function Atras(parametros){
		document.formulario.tarea.value = "X";
		document.formulario.action = "sis_rol.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}	
	
</script>
<?php 
	/* echo 'Valor menu principal'.$o_menu;
	echo "<script type='text/javascript'>xajax_Fun_Ajax_Menu($o_menu);</script>"; */
?>
</html>
 
