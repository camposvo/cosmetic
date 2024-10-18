<?php 
/*-------------------------------------------------------------------------------------------
	Nombre: adm_venta.php                                                    
	Descripcion: 
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	include_once ("nom_utilidad.php");
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
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	
	<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/chosen.min.css" /> -->
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/bootstrap-timepicker.min.css" /> -->
		<link rel="stylesheet" href="../../assets/css/daterangepicker.min.css" />
		<!-- <link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.min.css" /> -->
		<!-- <link rel="stylesheet" href="../../assets/css/colorpicker.min.css" /> -->

	<!-- text fonts -->
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<!-- <script src="../../assets/js/ace-extra.min.js"></script> -->
				
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
	$li_id_conex    = fun_conexion($obj_miconexion);

	$co_usuario     =  $_SESSION["li_cod_usuario"];
	$x_fecha_actual =  date('Y/m/d');
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: para ELIMINAR una actividad 
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "DELETE FROM t06_permiso WHERE pk_permiso = '$x_permiso' ";
		//echo $ls_sql;
		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}
	}
	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: AGREGAR DATOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){
		$o_total = number_format($o_total,2,".","");
		$o_pago = number_format($o_pago,2,".","");
		$o_tipo_gasto = 4;
		$o_cantidad = 1;
		$o_unidad  =  'UND';
		
		$ls_sql = "INSERT INTO t06_permiso(
					fe_inicio_permiso, fe_fin_permiso, tx_observacion, 
					fk_contrato
					)
					VALUES ('$o_fe_ini_permiso','$o_fe_fin_permiso', '$x_observacion', $co_contrato);";
						
		//	echo $ls_sql;
						
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Permiso Emitido Exitosamente');</script>";
		}
		//$tarea = 'A';
	}
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: MOSTRAR DATOS
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT tx_cedula, UPPER(tx_nombre), UPPER(tx_apellido), nu_salario, tx_nro_contrato, 
					to_char(fe_inicio, 'dd/mm/yyyy'), to_char(fe_fin, 'dd/mm/yyyy'), tx_descripcion, 
					extract(days from now()- fe_inicio) +2 as DiasTrabajados,
					(case when (t12_contrato.fe_fin <= now() ) then 'Activo' else 'Vencido' end) AS 	EstadoContrato,
					to_char(fe_inicio, 'yyyy/mm/dd') as fe_ini_temp
					FROM t12_contrato 
				INNER JOIN s01_persona ON s01_persona.co_persona = t12_contrato.fk_trabajador
				WHERE t12_contrato.pk_contrato=  $co_contrato";
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$x_cedula           = $row[0];
		$x_nombre	        = $row[1];
		$x_apellido    	    = $row[2];	
		$x_salario_mensual  = $row[3];
		$x_nro_contrato     = $row[4];
		$x_fecha_ini        = $row[5];
		$x_fecha_fin        = $row[6];
		$x_descripcion      = $row[7];
		$dias_trabajados    = $row[8];
		$estatus_contrato    = $row[9];
		$fe_ini_temp		= $row[10];	
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
	//echo interval_date($x_fecha_ini,$x_fecha_actual);
	
	
	
	$date = date("Y/n/d");
	$activationdate = date("Y/n/d", strtotime ($fe_ini_temp));

	$years= date("Y", strtotime("now")) - date("Y", strtotime($activationdate));
	// 

	if (date ("Y", strtotime($date)) == date ("Y", strtotime($activationdate))){
		$months = date ("m", strtotime($date)) - date ("m", strtotime($activationdate));
	}	elseif ($years == "1"){
		$months = (date ("m", strtotime("December")) - date ("m", strtotime($activationdate))) + (date ("m"));
	}	elseif($years >= "2"){
		$months = ($years*12) + (date ("m", strtotime("now")) - date ("m", strtotime($activationdate)));
	}

	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS: DIAS DE PERMISO
-------------------------------------------------------------------------------------------*/
	$ls_sql ="SELECT sum(fe_fin_permiso-fe_inicio_permiso +1) as dias
			FROM t06_permiso 
		WHERE fk_contrato= $co_contrato";
	//echo $ls_sql;
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		$dias_permiso           = $row[0];
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
	
	
/*-------------------------------------------------------------------------------------------
	RUTINAS:Para Mostrar las ultimas Pagos
-------------------------------------------------------------------------------------------*/
	$i=0;
	$li_tampag = 50;
		
	$ls_sql = "SELECT to_char(fe_inicio_permiso, 'dd/mm/yyyy'), to_char(fe_fin_permiso, 'dd/mm/yyyy'), 
					fe_fin_permiso - fe_inicio_permiso + 1 as PermisoDia,
					tx_observacion, to_char(fe_inicio_permiso, 'mm'), 
					pk_permiso
					FROM t06_permiso
			WHERE fk_contrato= $co_contrato
			ORDER BY fe_inicio_permiso DESC";
	
	//echo $ls_sql;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	$li_inicio = $obj_miconexion->fun_tampagina($li_pagina, $li_tampag); 
	$li_totreg = $obj_miconexion->fun_numregistros($ls_resultado);
	
	if ($li_totreg > 0){ // Reescribe la consulta para un tamao de pagina definido
		$ls_sql = $ls_sql.sprintf(" LIMIT %d OFFSET %d ", $li_tampag, $li_inicio);
		$ls_resultado= $obj_miconexion->fun_consult($ls_sql);
	}
	$li_totpag  = $obj_miconexion->fun_calcpag( $li_totreg, $li_tampag);
	

	$x_fecha_registro = date('d/m/Y H:i');
	$tarea = 'I';

/*-------------------------------------------------------------------------------------------
                        FIN DE RUTINAS PARA EL MANTENIMIENTO.                                            
|------------------------------------------------------------------------------------------*/
?>


<!-- Content Header (Page header) -->
<div class="container-fluid">
	<div class="page-header">
		<h1>
			<?php echo  "Permisos";?>
		</h1>
	</div><!-- /.page-header -->
	<div class="row"><!-- ROW CONTENT BEGINS -->
		<div class="col-xs-12">
		
			<div class="row">
				<div class="col-xs-12 col-sm-8 widget-container-col">
					<div class="widget-box ">
						<div class="widget-header widget-header-small">
							<h4 class="widget-title"> <?php echo "Emitir Permiso"?> </h4>
						</div>
			
						<div class="widget-body">
							<div class="widget-main">
								<form class="form-horizontal" name="formulario">
									
									<div class="form-group">
										<label  class="col-sm-3 control-label no-padding-right" for="x_observacion" >Descripcion</label>
										<div class="col-sm-9" >
											<textarea name="x_observacion" cols="50" id="x_observacion" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_observacion;?></textarea>
										</div>
									</div>	
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right">Fecha</label>
										<div class="col-sm-6">	
											<div class="input-daterange input-group">
												<input class="input-sm form-control" name="o_fe_ini_permiso" value="<?php echo $o_fe_ini_permiso;?>" placeholder="Desde" type="text" readonly />
												<span class="input-group-addon">
													<i class="fa fa-exchange"></i>
												</span>
												<input class="input-sm form-control" name="o_fe_fin_permiso" value="<?php echo $o_fe_fin_permiso;?>" placeholder="Hasta" type="text" readonly />
											</div>
										</div>
									</div>
									
									<input type="hidden" name="tarea" value="<?php echo $tarea;?>">
									<input type="hidden" name="modo" value="<?php echo $modo;?>">   
									<input name="co_trabajador" type="hidden" value="<?php echo $co_trabajador;?>">
									<input name="co_contrato" type="hidden" value="<?php echo $co_contrato;?>">
									<input name="x_permiso" type="hidden" value="<?php echo $x_permiso;?>">
									
								</form>
							</div>
						</div>
					</div>
				</div>
			
				<div class="col-xs-12 col-sm-4">
					<div class="profile-user-info profile-user-info-striped">
						<div class="profile-info-row">
							<div class="profile-info-name"> Nombre </div>

							<div class="profile-info-value">
								<span class="blue" style="font-weight: bold;" id="username"><?php echo $x_nombre.' '.$x_apellido; ?></span> 

							</div>
						</div>

						<div class="profile-info-row">
							<div class="profile-info-name"> Cedula </div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $x_cedula;?></span>
							</div>
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name"> Estatus </div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $estatus_contrato;?></span>
							</div>
							
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name">Fecha de Ingreso</div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $x_fecha_ini;?></span>
							</div>
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name"> Tiempo </div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $months.' months '.$years; ?></span> 
							</div>
						</div>
						
						<div class="profile-info-row">
							<div class="profile-info-name"> Total de Dias</div>

							<div class="profile-info-value">
								<span class="blue" id="age"><?php echo $dias_permiso." Dias";?></span>
							</div>
						</div>
						
					</div>
				</div>
			
			</div> <!-- /.Row datos -->
			
	
			<div class="space-4"></div>
	
			<div class="row">
				<div class="col-xs-12 ">
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
				</div>
			</div>
	
			<div class="space-4"></div>
	
			<div class="row">
				<div class="col-xs-12">
					<table id="simple-table" class="table table-striped table-bordered table-hover">
						<thead>
							<tr class="bg-primary" >
								<th>Fecha Inicio</th>
								<th>Fecha Fin</th>
								<th class="hidden-480">Dias</th>
								<th class="hidden-480">Observacion</th>
								<th class="hidden-480">Mes</th>
								<th>Eliminar</th>
							</tr>
						</thead>
						<tbody>	
							<?php   
								$li_numcampo = $obj_miconexion->fun_numcampos()-4; // Columnas que se muestran en la Tabla
								$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
								fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_PERMISO'); // Dibuja la Tabla de Datos
								$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
							?>
						</tbody>
				</table>
			</div> 
			</div>
		</div> <!-- /.row Datos  -->
	</div> <!-- /.row tabla principal -->
</div> <!-- /.page-content -->

</body>



		<script src="../../assets/js/jquery.2.1.1.min.js"></script>
		
		<script src="../../assets/js/bootstrap.min.js"></script>

		
		<script src="../../assets/js/daterangepicker.min.js"></script>
		<script src="../../assets/js/bootstrap-datepicker.min.js"></script>

		
	<!-- page specific plugin scripts -->
		<script src="../../assets/js/jquery-ui.custom.min.js"></script>


		
		<!-- ace scripts -->
		<script src="../../assets/js/ace-elements.min.js"></script>
		<script src="../../assets/js/ace.min.js"></script>


		<!-- inline scripts related to this page -->
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

	function Guardar(identificador){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Esta conforme con los Datos Ingresados?') == true){	
				document.formulario.tarea.value = identificador;
				document.formulario.action = "nom_permiso_add.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}
	}
	
	
	
	function Editar(identificador){
			document.formulario.tarea.value = "M";
			document.formulario.x_cod_actividad.value = identificador;
			document.formulario.action = "mae_actividad.php"
			document.formulario.method = "POST";
			document.formulario.submit();
	}	
	
	function EliminarPermiso(identificador){
		if (confirm('Desea Eliminar este Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.x_permiso.value = identificador;
			document.formulario.action = "nom_permiso_add.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	
	/*-------------------------------------------------------------------------------------------|
|	Funcin: 'Atras'															 	  		 |
|	Descripcin: Permite Regresar A La Pgina De Maestro De Almacenes.						 |
|-------------------------------------------------------------------------------------------*/
	function Atras(parametros){
		location.href = "nom_trabajador_view.php?" + parametros;
	}	
	
	
</script>

</html>
