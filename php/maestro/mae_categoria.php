<?php 

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
|				Rutina: Se Utiliza Para Recibir Las Variables Por La URL. 					 |
|-------------------------------------------------------------------------------------------*/
	if (!$_GET)	{
		foreach($_POST as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_POST['modo'])?$_POST['modo']:'Insertar Nuevo Registro';
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
		$modo = isset($_GET['modo'])?$_GET['modo']:'Insertar Nuevo Registro';
	}
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$arr_categoria      =   Combo_Categoria_Articulo();
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Para Agregar Una Nueva Marca						 	 |
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		
		$o_nombre = mb_convert_case($o_nombre, MB_CASE_TITLE, "UTF-8"); 
		if($pk_categoria==''){
			$ls_sql = "SELECT pk_categoria FROM t21_categoria	WHERE UPPER(nb_categoria) ='".strtoupper($o_nombre)."'";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			echo $ls_sql ;
			if($ls_resultado != 0){
				if($obj_miconexion->fun_numregistros() == 0){
					
					$ls_sql = " INSERT INTO t21_categoria (nb_categoria) 
								VALUES ('".$o_nombre."')";
					
					echo $ls_sql ;
					$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado == 0){
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}else{echo "<script language='javascript' type='text/javascript'>alert('¡Nueva Clase Ingresada Satisfactoriamente!');</script>";}
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('¡Nombre Duplicado!');</script>";
				}
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}else{
			$ls_sql = " UPDATE t21_categoria SET nb_categoria = '".strtoupper($o_nombre)."' 
						WHERE pk_categoria = $pk_categoria ";
			$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			if($ls_resultado != 0){
				echo "<script language='javascript' type='text/javascript'>alert('Clase Actualizada Satisfactoriamente!');</script>";
			}else{
				fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
			}
		}
		$pk_categoria = '';
		$o_nombre = '';
		$modo = 'Insertar Nuevo Registro';
	}
	
/*-------------------------------------------------------------------------------------------|
|								Rutina: Para Eliminar Una Marca		
|-------------------------------------------------------------------------------------------*/	
	if ($tarea == "E"){
		
		$ls_sql = "SELECT * FROM t21_categoria WHERE fk_categoria= '$pk_categoria' ";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if ($ls_resultado != 0){
			if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
				$ls_sql = "DELETE FROM t21_categoria WHERE pk_categoria = '$pk_categoria'";
				$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
					if($ls_resultado != 0){
						$msg = "¡Proveedor Eliminado Exitosamente!";
						echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
					}else{
						fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
					}
			}else{
				$msg = "¡Imposible Eliminar, Este Clasificacion esta Asociado a un Producto!";
				echo "<script language='javascript' type='text/javascript'>alert('$msg');</script>";
			}
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		
		$modo = 'Insertar Nuevo Registro';
		
	}
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Permite Colocar Los Datos En Modo Edición En La Misma Página			 |
|-------------------------------------------------------------------------------------------*/		
	if ($tarea == "M"){
		$ls_sql = "SELECT nb_categoria, fk_categoria FROM t21_categoria WHERE pk_categoria = $pk_categoria";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			$row = pg_fetch_row($ls_resultado,0);
			$o_nombre = $row[0];
			$o_categoria = $row[1];
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$modo = "Editar Registro";
	}
	
/*-------------------------------------------------------------------------------------------|
|	Rutina: Permite Cargar En La Interfaz Los Registros De La Tabla 't05_clase' 		 |
|-------------------------------------------------------------------------------------------*/	
	$ls_sql = " SELECT nb_categoria, pk_categoria 
	FROM t21_categoria 
	ORDER BY nb_categoria";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}
	
	
	$color_modo = $modo == "Editar Registro" ?"widget-color-red":"widget-color-green";
	
?>
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Categoria
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
				
				<div class="row">
						<div class="col-xs-12 ">
								<div class="alert alert-block alert-success">							
									<i class="ace-icon fa fa-check green"></i>							
									<strong class="green">
										La Categoria
									</strong>,
									 Representa el nivel mas alto jerarquia, que permite agrupar los articulos de una forma mas general
								</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 widget-container-col">
							<div class="widget-box <?php echo $color_modo;?>">
								<div class="widget-header widget-header-small">
									
									<h4 class="widget-title"> <?php echo $modo?> </h4>
									
								</div>
								
								<div class="widget-body">
									<div class="widget-main">
										<form class="form-horizontal" name="formulario">

											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" >Categoria</label>
												<div class="col-sm-6">
													<input  class="input-sm form-control" name="o_nombre"  value="<?php echo $o_nombre;?>" id="factura" placeholder="Categoria" type="text"  />
												</div>
											</div>
											
																						
											<div class="form-group center">
												<button type="button" onClick="Atras();" class="btn btn-sm btn-danger">
													<i class="ace-icon fa fa-arrow-left icon-on-right bigger-110"></i>
													Atras
												</button>		
												<button type="button" onClick="Limpiar();" class="btn btn-sm btn-info">
													<i class="ace-icon fa fa-undo   bigger-110 icon-on-right"></i>
													Limpiar
												</button>
												
												<button type="button" onClick="Guardar();" class="btn btn-sm btn-success">
													<i class="ace-icon fa fa-check  icon-on-right bigger-110"></i>
													Guardar
												</button>																								
											</div>
											
											<div class="space-4"></div>
											
											<input type="hidden" name="pk_categoria" value="<?php echo $pk_categoria;?>">
											<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
											<input type="hidden" name="modo" value="<?php echo $modo;?>">   				
										
									</form>
									</div>
								</div>
							</div>
						</div>
					
					</div>	
						
								
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Clasificacion
							</div>

							<table id="dynamic-table" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Categoria</th>
										<th></th>
									</tr>
								</thead>
								<tbody>	
									<?php    
										$li_numcampo = $obj_miconexion->fun_numcampos()-2; // Columnas Que Se Muestran En La Tabla.
										$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia Al Índice De La Columna Clave.
										fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_CLASE'); // Dibuja La Tabla De Datos.
										$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado); 
									?>
								</tbody>
							</table>
							
							
							
						</div>
					</div> <!-- /.row tabla principal -->		
			
				</div>
			</div> <!-- ROW CONTENT END -->
		</div> <!-- /.page-content -->
	</div> <!-- /.main-content-inner -->
</div> <!-- /.main-content-inner -->

	<script src="../../js/funciones.js"></script>
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

		
	
  
	<script type="text/javascript"> 
		function Guardar(){
			if(campos_blancos(document.formulario) == false){
				if (confirm('¿Está Conforme Con Los Datos Ingresados?') == true){	
					document.formulario.tarea.value = "A";
					document.formulario.action = "mae_categoria.php";
					document.formulario.method = "POST";
					document.formulario.submit();
				}
			}
		}
	
		function Eliminar(pk_categoria){
			if (confirm('¿Realmente Desea Eliminar Esta Marca?') == true){
				document.formulario.tarea.value = "E";
				document.formulario.action = "mae_categoria.php";
				document.formulario.pk_categoria.value = pk_categoria;
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	/*-------------------------------------------------------------------------------------------|
	|	Función: 'Editar'																	 	 |
	|	Descripción: Permite En La Misma Página Editar Los Datos De Una Marca. 					 |
	|-------------------------------------------------------------------------------------------*/	
		function Editar(pk_categoria){
			document.formulario.tarea.value = "M";
			document.formulario.action = "mae_categoria.php"
			document.formulario.pk_categoria.value = pk_categoria;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	/*-------------------------------------------------------------------------------------------|
	|	Función: 'Limpiar'																 		 |
	|	Descripción: Limpia La Información Introducida. 										 |
	|-------------------------------------------------------------------------------------------*/			
		function Limpiar(){
			document.formulario.modo.value = "Insertar Nuevo Registro";
			document.formulario.tarea.value = "X";
			document.formulario.action = "mae_categoria.php"
			document.formulario.pk_categoria.value = '';
			document.formulario.o_nombre.value = '';
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
		function Atras(){
			document.formulario.tarea.value = "X";
			document.formulario.action = "mae_articulo_view.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}	
		
	</script>

</body>
</html>