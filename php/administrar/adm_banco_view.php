<?php 

	session_start();
	include_once ("adm_utilidad.php");
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
| Rutina:  ACTUALIZA EL MONTO  |
|-------------------------------------------------------------------------------------------*/
	if ($tarea == "A"){
		$ls_sql = " UPDATE t15_banco SET 
						nu_capital      = ".$o_capital.",
						fe_update       = NOW()
						WHERE pk_banco = $pk_banco ";
		//echo $ls_sql;			
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado != 0){
			echo "<script language='javascript' type='text/javascript'>alert('¡Monto Actualizado Exitosamente !');</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
		}
	}

/*-------------------------------------------------------------------------------------------|
|		Rutina: Permite Cargar En La Interfaz Los Registros De La Tabla 't07_marca' 		 |
|-------------------------------------------------------------------------------------------*/	
	$ls_sql = " SELECT to_char(fe_update, 'dd/mm/yyyy  HH:mi'), nb_banco, 'Nro. '||tx_nro_cuenta, tx_tipo, nu_capital, pk_banco FROM t15_banco ORDER BY pk_banco";
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		//Sin Error
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF'], __LINE__); //  Envía Mensaje De Error De Consulta.
	}

?>
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			
			<div class="page-header">
				<h1>
					Bancos
				</h1>
			</div><!-- /.page-header -->
			
			<div class="row">
				<div class="col-xs-12"> <!-- ROW CONTENT BEGINS -->
				
							
													
					<div class="row">
						<div class="col-xs-12">
							
							<div class="clearfix">
								<div class="pull-right tableTools-container"></div>
							</div>
							
							<div class="table-header">
								Lista de Bancos
							</div>
							<form role="form" name="formulario">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th class='hidden-480'>Ultima Actualizacion</th>
											<th>Banco</th>
											<th class='hidden-480'>Nro. de Cuenta</th>
											<th class='hidden-480'>Tipo</th>
											<th>Capital</th>
											<th>Actualizar</th>
										</tr>
									</thead>
									<tbody>	
										<?php    
											$li_numcampo = $obj_miconexion->fun_numcampos()-6; // Columnas Que Se Muestran En La Tabla.
											$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia Al Índice De La Columna Clave.
											fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_BANCO',0); // Dibuja La Tabla De Datos.
											$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado); 
										?>
									</tbody>
								</table>
								<input type="hidden" name="tarea" value="<?php echo $tarea;?>"> 
								<input type="hidden" name="o_capital" value="<?php echo $o_capital;?>"> 
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
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>

		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
				
				
			
			} );
			
			
			
			
			
		</script>


		
		
	<!-- <script src="../../js/funciones.js"></script> -->
  
	<script type="text/javascript"> 

		function Guardar(){
		if(campos_blancos(document.formulario) == false){
			if (confirm('¿Está Conforme Con Los Datos Ingresados?') == true){	
				document.formulario.tarea.value = "A";
				document.formulario.action = "banco.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}

/*-------------------------------------------------------------------------------------------|
|	Función: 'Editar'																	 	 |
|	Descripción: Permite En La Misma Página Editar Los Datos De Una Marca. 					 |
|-------------------------------------------------------------------------------------------*/	
	function Editar_Capital(pk_banco){
	
		var valor = prompt("Ingrese el Valor:", "0.0");
		//Se valida si es numerico
		if ( valor !=null){
			if(!isNaN(valor) &&  valor !=null ) {
				document.formulario.tarea.value = "A";
				document.formulario.action = "adm_banco_view.php"
				document.formulario.pk_banco.value = pk_banco;
				document.formulario.o_capital.value = valor;
				document.formulario.method = "POST";
				document.formulario.submit();
			}else{
				alert('Valor Invalido');
			}
		}
	}

	
	</script>

</body>
</html>
