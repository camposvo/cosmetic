<?php 
/*-------------------------------------------------------------------------------------------|
|  						Verificación Y Autentificación De Usuario.                           |
|-------------------------------------------------------------------------------------------*/ 
	session_start();
	include_once ("pro_utilidad.php");
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
/*-------------------------------------------------------------------------------------------|
|	Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					 |
|-------------------------------------------------------------------------------------------*/
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Nuevo Tipo';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Nuevo Tipo';
	}
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	INGRESAR UNA NUEVA CATEGORIA DE PROYECTOS A LA BASE DE DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){		
	
		$ls_sql = "SELECT pk_tipo_rubro FROM t08_tipo_proyecto	WHERE (nb_tipo_rubro) = '".strtoupper($o_nombre)."'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros() == 0){
				
				$ls_sql = " INSERT INTO t08_tipo_proyecto (nb_tipo_rubro, tx_descripcion) 
							VALUES ('".strtoupper($o_nombre)."','".$o_descripcion.")";
				
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
				if($ls_resultado == 0){
					fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('¡Ingresada Satisfactoriamente!');location.href='pro_categoria_view.php?$parametros';</script>";
				}
			}else{
				echo "<script language='javascript' type='text/javascript'>alert('¡Nombre Duplicado!');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		
		$o_nombre = '';
		$modo = 'Nuesvo Tipo de Proyecto';
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: Muestra los Datos para Actualizar un Registro
-------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
		$id_factura = $x_movimiento;
		
		$ls_sql = " UPDATE t08_tipo_proyecto SET 
						nb_tipo_rubro = '".strtoupper($o_nombre)."', 
						tx_descripcion = '".$o_descripcion."' 					
					WHERE pk_tipo_rubro = $pk_tipo_rubro ";
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			echo "<script language='javascript' type='text/javascript'>alert('¡Actualizada Satisfactoriamente!');location.href='pro_categoria_view.php?$parametros';</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}

		$tarea = 'M';
	}
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Permite Colocar Los Datos En Modo Edición En La Misma Página	
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT nb_tipo_rubro,tx_descripcion		
			FROM t08_tipo_proyecto WHERE pk_tipo_rubro = $pk_tipo_rubro";
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_nombre = $row[0];
			$o_descripcion = $row[1];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}

		$tarea = 'U';
		$modo= 'Actualizar Datos';
	}
	
	
	if ($tarea == "A"){
		$tarea = 'I';
		$modo= 'Ingresar Nueva Venta';
	}
	
	
	$color_modo = $modo == "Editar Registro" ?"widget-color-orange":"widget-color-green";
		
?>

<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Tipos de Proyecto
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box ">
								<div class="widget-header widget-header-small">
									<h4 class="widget-title">Tipo de Proyecto</h4>									
								</div>
								
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">

											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Tipo de Proyecto</label>
												<div class="col-sm-6">
													<input  class="input-sm form-control" name="o_nombre"  value="<?php echo $o_nombre;?>" id="factura" placeholder="Nombre " type="text"  />
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Tipo de Proyecto</label>
												<div class="col-sm-6">
													<input  class="input-sm form-control" name="o_descripcion"  value="<?php echo $o_descripcion;?>" placeholder="Descripcion " type="text"  />
												</div>
											</div>
																					
											<div class="form-group center">												
												<button type="button" onClick="Atras();" class="btn btn-sm  btn-danger">
													<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
													Atras
												</button>
												
												<button type="button" onClick="Guardar('<?php echo $tarea;?>');" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
											
											<div class="space-4"></div>
											
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
											<input type="hidden" name="pk_tipo_rubro" value="<?php echo $pk_tipo_rubro;?>">       			
										
									</form>
									</div>
								</div>
							</div>
						</div>
					
					</div>	
						
				
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- /.main-content-inner -->
		
	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>	
	
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>


		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
				
			} );
		</script>


		
		
	<script src="../../js/funciones.js"></script>  
	<script type="text/javascript"> 

		function Guardar(Identificador){		
			if(campos_blancos(document.formulario) == false){
				if (confirm('Esta conforme con los Datos Ingresados?') == true){	
					document.formulario.tarea.value = Identificador;						
					document.formulario.action = "pro_categoria_mtto.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}	
		}		
	
	
		function Atras(parametros){
			document.formulario.tarea.value = "X";
			document.formulario.action = "pro_categoria_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}			
		
	</script>

</body>
</html>