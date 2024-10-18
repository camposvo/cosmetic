<?php 
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("mae_utilidad.php");
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
	$o_cantidad  = 0;
	$o_cantidad2 = 0;
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
	$x_responsable  =  $_SESSION["li_cod_usuario"];
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: permite MOSTRAR LOS DATOS
-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT nb_proveedor, tx_rif, tx_telefono, tx_direccion, tx_correo, tx_sitio_web
				   FROM   t03_proveedor
				   WHERE pk_proveedor = '$pk_proveedor'";
				   
	//echo $ls_sql;
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		
		if($ls_resultado != 0){
			$row           = pg_fetch_row($ls_resultado,0);
			$o_nombre      = $row[0];
			$o_rif         = $row[1];
			$x_nit         = $row[2];
			$x_telefono    = $row[3];
			$x_direccion   = $row[4];
			$x_correo      = $row[5];
			$x_web         = $row[6];
			
		$obj_miconexion->fun_closepg($li_id_conex); 
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Env�a Mensaje De Error De Consulta.
		}		

/*-------------------------------------------------------------------------------------------
	RUTINAS: RESUMEN DE GASTOS
-------------------------------------------------------------------------------------------*/
	$i=0;
	$li_tampag = 50;
	$ls_sql = "SELECT distinct nb_articulo
				  FROM t01_detalle
				  inner join t13_articulo ON t01_detalle.fk_articulo  = t13_articulo.pk_articulo
				  inner join t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
				inner join t03_proveedor ON t03_proveedor.pk_proveedor = t20_factura.fk_proveedor
				WHERE t20_factura.tx_tipo='GASTO' AND t20_factura.fk_proveedor = ".$pk_proveedor.
				" ORDER BY nb_articulo ";

	//	echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	
		
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}	
	
/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>
<div class="container-fluid">
	<div class="page-header">
		<h1>
			<?php echo  "Proveedor";?>
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
			<div class="row">
			
				<div class="col-xs-12 col-sm-8">
					<div class="profile-user-info profile-user-info-striped">
						<div class="profile-info-row">
							<div class="profile-info-name"> Nombre </div>

							<div class="profile-info-value">
								<span class="blue" style="font-weight: bold;" ><?php echo $o_nombre;?></span> 

							</div>
						</div>

						<div class="profile-info-row">
							<div class="profile-info-name"> Telefono </div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $x_telefono;?></span>
							</div>
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name"> Direccion </div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $x_direccion;?></span>
							</div>							
						</div>
						
					</div>
				</div>
			
			</div> <!-- /.Row datos -->
			
	
			<div class="space-4"></div>
	
			<div class="row">
				<div class="col-xs-12 ">
					<div class="form-group center">												
						<button type="button" onClick="Atras()" class="btn btn-sm  btn-danger">
							<i class="ace-icon fa fa-arrow-left  bigger-110 icon-on-right"></i>
							Regresar
						</button>
					</div>
				</div>
			</div>
	
			<div class="space-4"></div>
	
			<div class="row">
				<div class="col-xs-12">
				<form class="form-horizontal" name="formulario">
					<table id="simple-table" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Articulo</th>

							</tr>
						</thead>
						<tbody>	
							<?php   
								$li_numcampo = $obj_miconexion->fun_numcampos(); // Columnas que se muestran en la Tabla
								$li_indicecampo = $obj_miconexion->fun_numcampos(); // Referencia al indice de la columna clave
								fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'NO_LISTAR'); // Dibuja la Tabla de Datos
								$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
							?>
						</tbody>
				</table>
				<input type="hidden" name="pk_proveedor" value="<?php echo $pk_proveedor;?>">
				<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
				<input type="hidden" name="modo" value="<?php echo $modo;?>">  
				</form>				
			</div> 
			</div>
		</div> <!-- /.row Datos  -->
	</div> <!-- /.row tabla principal -->
</div> <!-- /.page-content -->

</body>



	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>
	<!-- page specific plugin scripts -->
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>
	
<!-- page specific plugin scripts -->
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/daterangepicker.min.js"></script>
	<script src="../../assets/js/jquery.autosize.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>

		<script type="text/javascript">
			 $(document).ready(function() {
				window.parent.ScrollToTop(); // invoca la funcion ScrollToTop que se encuentra en interace.php para posicionar el scroll vertical
			
			
				
				//or change it into a date range picker
				$('.input-daterange').datepicker({
					
					autoclose:true,
					format: "dd/mm/yyyy"
					
				});
				
			} );
		</script>


				
<script src="../../js/funciones.js"></script>  
<script type="text/javascript">

	
	function Atras(){
		location.href = "mae_proveedor.php";
	}
	

	
</script>

</html>