<?php 
/*--------------------------------------------------------------------------------------------------|
|  	Nombre: 'ma_marcas.php'          			                         							|
|  	Descripción: Esta Interfaz Muestra El Maestro De Las Marcas, Permite Modificar Y Eliminarlas.	|
|--------------------------------------------------------------------------------------------------*/

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
<title>BellinghieriCosmetic</title>
		
	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="../../css/estilo.css" /> <!-- estilos personales ( Hover de datatable)  --> 
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />		
				
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
	}else{
		foreach($_GET as $nombre_campo => $valor){
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
	
	$arr_tipo_cuenta  =  Combo_TipoCuenta();
/*-------------------------------------------------------------------------------------------|
|	Rutina: ELIMINA UN BANCO 
|-------------------------------------------------------------------------------------------*/	
	if ($tarea == "E"){
		$ls_sql = "DELETE FROM t15_banco WHERE pk_banco = '$pk_banco'";
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
		$pk_banco='';
	}

/*-------------------------------------------------------------------------------------------|
|	Rutina:  LISTA TODOS LOS BANCOS
|-------------------------------------------------------------------------------------------*/	
	$ls_sql = " SELECT nb_banco, 'Nro. '||tx_nro_cuenta, tx_tipo, to_char(fe_apertura, 'dd/mm/yyyy'), pk_banco FROM t15_banco ORDER BY pk_banco";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
	}
/*--------------------------------------------------------------------------------------------------|
|									Fin De Rutinas Para El Mantenimiento.             				|
|--------------------------------------------------------------------------------------------------*/
?>




<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Administrar Bancos
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
					
					<div class="row">
						<div class="col-xs-12 col-sm-2">
							<button class="btn btn-success btn-sm pull-left" onClick= "Agregar()">
								<i class="ace-icon fa fa-plus align-top bigger-125 "></i>
								Nuevo Banco
							</button>
						</div>
					</div>	
						
								
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Bancos
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th >Nombre</th>
											<th >Cuenta</th>
											<th >Tipo</th>
											<th >Apertura</th>
											<th >Modificar</th>
											<th >Eliminar</th>
										</tr>
									</thead>
									<tbody>	
										<?php    
											$li_numcampo = $obj_miconexion->fun_numcampos()-1; // Columnas Que Se Muestran En La Tabla.
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia Al Índice De La Columna Clave.
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_BANCO'); // Dibuja La Tabla De Datos.
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado); 
										?>
									</tbody>
								</table>
								<input name="tarea" type="hidden" value="<?php echo $tarea;?>">
								<input type="hidden" name="pk_banco" value="<?php echo $pk_banco;?>"> 
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
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	
	<!-- ace scripts -->
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	
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
					{ "orderable": false },
					{ "orderable": false },
					{ "orderable": false }
				  ]
			} );


		} );
	</script>

		
	<script src="../../js/funciones.js"></script>
  
	<script type="text/javascript"> 
	
/*-------------------------------------------------------------------------------------------|
|	Función: 'Guardar'																		 
|	Descripción: Permite Guardar La Información Del Formulario De Marcas En La Base De Datos.
|-------------------------------------------------------------------------------------------*/
	function Agregar(){
		document.formulario.tarea.value = "A";
		document.formulario.action = "mae_banco_add.php";
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Eliminar'																		
|	Descripción: Permite Eliminar Una Marca De La Base De Datos.	 					 	
|-------------------------------------------------------------------------------------------*/	
	function Eliminar(pk_banco){
		if (confirm('¿Realmente Desea Eliminar Esta Banco?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.action = "mae_banco_view.php";
			document.formulario.pk_banco.value = pk_banco;
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Editar'																	 	 
|	Descripción: Permite En La Misma Página Editar Los Datos De Una Marca. 					 
|-------------------------------------------------------------------------------------------*/	
	function Editar(pk_banco){
		document.formulario.tarea.value = "M";
		document.formulario.action = "mae_banco_add.php"
		document.formulario.pk_banco.value = pk_banco;
		document.formulario.method = "POST";
		document.formulario.submit();
	}
/*-------------------------------------------------------------------------------------------|
|	Función: 'Limpiar'																 		
|	Descripción: Limpia La Información Introducida. 										 
|-------------------------------------------------------------------------------------------*/			
	function Limpiar(){
		document.formulario.tarea.value = "X";
		document.formulario.action = "mae_banco_view.php"
		document.formulario.pk_banco.value = '';
		document.formulario.o_nombre.value = '';
		document.formulario.o_tipo.value = '';
		document.formulario.o_fecha.value = '';
		document.formulario.o_cuenta.value = '';
		document.formulario.method = "POST";
		document.formulario.submit();
	}	
	
	</script>

</body>
</html>