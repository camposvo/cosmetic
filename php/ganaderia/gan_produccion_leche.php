<?php 
/*-------------------------------------------------------------------------------------------
	VERIFICACION Y AUTENTIFICACIN DE USUARIO. 
--------------------------------------------------------------------------------------------*/
	session_start();
	$ls_usuario = $_SESSION["li_cod_usuario"];
	include_once ("gan_utilidad.php");
	$usu_autentico = isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI"){
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
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" /> 
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" /> 
	<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
				
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
	}else{
		foreach($_GET as $nombre_campo => $valor){ 
			$asignacion = "\$" . $nombre_campo . "='" . $valor . "';";
			eval($asignacion);
		}
	}
	
	//var_dump($_POST['origen']);
	$datos = $_POST['origen'];
	
/*-------------------------------------------------------------------------------------------
	RUTINA: variables tipo arreglo para rellenar los combos en la interfaz. (VER CONFIG.PHP)
---------------------------------------------------------------------------------------------*/
	$arr_vacuna         = Combo_Vacuna();
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	ELIMINAR UN REGISTRO DE LA TABLA GAN PESO
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "DELETE FROM gan_leche
						WHERE pk_leche = $pk_leche;";		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);		
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Registro Eliminado exitosamente');</script>";	
		}
	}
	
	
/*-----------------------------------------------------------------------------------------------------------------------
	ACTUALIZA EL POTRERO ASIGNADO AL GANADO
------------------------------------------------------------------------------------------------------------------------*/
	if ($tarea == "U"){
					
		$error_sql = false;			
		
		$ls_sql = "INSERT INTO gan_leche( nu_am, nu_pm, fe_produccion, nu_dias_produccion, fk_animal ) 
			VALUES ( '$x_am','$x_pm', '$o_fecha','$o_dias', '$pk_animal')";

		if ($obj_miconexion->fun_consult(strtoupper($ls_sql))== 0)	{
			$error_sql = true;
		}
				
		if(!$error_sql){

			echo "<script language='JavaScript' type='text/JavaScript'>alert('Datos Actualizados Satisfactoriamente');</script>";
		}else{
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
		}			
		
		$tarea == "X";  //Vuelve a colocar la Tarea 
	}
	
/*-------------------------------------------------------------------------------------------
	CONSULTA LISTA DE ANIMALES
-------------------------------------------------------------------------------------------*/			
	$ls_sql = "SELECT id_numero, UPPER(nb_nombre_animal), in_sexo, to_char(fe_nacimiento, 'yyyy/mm/dd'), nb_potrero, nb_lote,
				f_grupo_etareo(fe_nacimiento,in_sexo), UPPER(gan_raza.nb_raza), pk_ganado
			FROM gan_ganado
			LEFT JOIN gan_raza ON gan_raza.pk_raza = gan_ganado.fk_raza 
			LEFT JOIN gan_potrero ON gan_potrero.pk_potrero = gan_ganado.fk_potrero 
			LEFT JOIN gan_lote ON gan_lote.pk_lote = gan_ganado.fk_lote 
			WHERE pk_ganado = $pk_animal 
			";
	
	//echo $ls_sql ;
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		$row = pg_fetch_row($ls_resultado,0);
		//$animal = $row;
		$x_numero           = $row[0];
		$x_nombre_animal    = $row[1];
		$x_sexo    	    	= $row[2];	
		$o_fecha_nac		= $row[3];
		$x_potrero     		= $row[4];
		$x_lote        		= $row[5];
		$x_grupo_etareo     = $row[6];
		$x_raza		      	= $row[7];	
		$x_imagen	      	= $row[8];			
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}

/*-------------------------------------------------------------------------------------------
	LEE REGISTRO QUE CONTIENE LA RUTA DE LA IMAGEN
-------------------------------------------------------------------------------------------*/
	$ls_sql = "SELECT tx_ruta_archivo
			FROM gan_imagen
			WHERE fk_animal = $pk_animal AND gan_imagen.in_activo='S'
			";
	//echo $ls_sql ;	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		if($obj_miconexion->fun_numregistros($ls_resultado) == 0){ 
			$x_imagen	=  "../../img/picture.svg";	//IMAGEN POR DEFECTO
			$html_img = '<img width="150" height="150" alt="150x150" src="'.$x_imagen.'" />';
					
		}else{
			$row = pg_fetch_row($ls_resultado,0);
			$x_imagen	    	= $row[0];
			$html_img = '<img class="editable img-responsive" alt="Ganado" id="avatar2" src="'.$x_imagen.'" />';
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);// enviar mensaje de error de consulta
	}
		
/*-------------------------------------------------------------------------------------------
	CONSULTA LISTA DE VACUNAS APLICADAS A ANIMALES
-------------------------------------------------------------------------------------------*/			
	$ls_sql = " SELECT fe_produccion, nu_am, nu_pm,  nu_dias_produccion, fk_animal, pk_leche FROM gan_leche	
			WHERE fk_animal = $pk_animal 
			";
	//echo $ls_sql;
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
			
	if($ls_resultado != 0){
		$tarea = "M";
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__);
	}
	
?>
<!-- Content Header (Page header) -->
	<div class="container-fluid">		
		
		<form class="form-horizontal" name="formulario"  method="post">
		<div class="row">
			<div class="col-xs-12">
				<h3 class="header blue lighter smaller">
					<i class="ace-icon fa fa-hand-o-right smaller-90"></i>
					<?php
						$i=0;
						echo "# del Ganado: <strong>".$x_numero."</strong>";
					?> 
				</h3>
			</div>
		</div><!-- ./row -->
										
		<div class="row">				
			<div class="col-xs-12 col-md-12 col-lg-12">
			
					<div class="col-xs-12 col-sm-4 center">
						<span class="profile-picture">
							<?php echo $html_img;	?> 
						</span>
		
						<div class="space space-4"></div>
							
						<span class="width-80 label label-info label-xlg  arrowed-in arrowed-in-right">
							<i class="ace-icon fa fa-circle light-green"></i>
							<span class="white">  <?php echo $x_nombre_animal; ?></span>
						</span>						
					</div><!-- /.col -->					
					
					<div class="col-xs-12 col-sm-8 ">
						<div class="form-group">
							<label class="col-sm-3 control-label no-padding-right" for="id-date-picker-1" >Fecha</label>
							<div class="col-sm-4" >	
								<div class="input-group">
									<input name="o_fecha" value="<?php echo $o_fecha;?>" class="col-xs-10 col-sm-6 form-control date-picker" id="id-date-picker-1" type="text" data-date-format="dd/mm/yyyy" readonly />
									<span class="input-group-addon">
										<i class="fa fa-calendar bigger-110"></i>
									</span>
								</div>
							</div>
						</div>	
					
						<div class="form-group">
							<label  class="col-sm-3 control-label no-padding-right"  > Prod. A.M </label>
							<div class="col-sm-7" >
								<input class="col-xs-10 col-sm-7" name="x_am" value="<?php echo $x_am;?>" type="text"  placeholder="Mañana" onKeyPress="return validardec(event)">
							</div>
						</div>
						
						<div class="form-group">
							<label  class="col-sm-3 control-label no-padding-right"  > Prod. P.M </label>
							<div class="col-sm-7" >
								<input class="col-xs-10 col-sm-7" name="x_pm" value="<?php echo $x_pm;?>" type="text"  placeholder="Tarde" onKeyPress="return validardec(event)">
							</div>
						</div>
					
						<div class="form-group">
							<label  class="col-sm-3 control-label no-padding-right"  > Dias  </label>
							<div class="col-sm-7" >
								<input class="col-xs-10 col-sm-7" name="o_dias" value="<?php echo $o_dias;?>" type="text"  placeholder="Dias" onKeyPress="return validardec(event)">
							</div>
						</div>
						
						
						<div class="form-group center ">
							<button type="button" onClick="Cancelar()" class="btn btn-sm  btn-danger">
								<i class="ace-icon fa fa-reply  bigger-110 icon-on-right"></i>
								Regresar
							</button>				
							<button type="button" class="btn btn-sm btn-info" onClick="Asignar()" >
								<i class="ace-icon fa fa-undo align-top bigger-125 "></i>
								Aplicar
							</button>				
						</div>
					</div><!-- /.col -->				
							
							
						
			</div>
		</div>
		
		<div class="space space-4"></div>

			
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<table class="table table-bordered table-striped">
					<thead class="thin-border-bottom">
						<tr>	
							<th class="">Fecha</th>
							<th class="">A.M</th>
							<th class="">P.M</th>
							<th class="">Dias</th>
							<th class=""></th>
						</tr>
					</thead>
					<tbody>	
						<?php   										
							$li_numcampo = 0;
							$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
							fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_ANIMAL_LECHE'); // Dibuja la Tabla de Datos
							$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
						?>
					</tbody>
				</table>
			</div>
		</div>
			<input type="hidden" name="pk_leche" value="<?php echo $pk_leche;?>">
			<input type="hidden" name="pk_animal" value="<?php echo $pk_animal;?>">
			<input type="hidden" name="tarea" 		 value="<?php echo $tarea;?>">
			<input type="hidden" name="filtro" 		 value="<?php echo $filtro;?>">	
			</form>		
	</div>
	
	<!-- /.VENTANA MODAL A LA DERECHA CON DATOS DEL TRABAJADOR -->
	<div id="right-menu" class="modal aside" data-body-scroll="false" data-offset="true" data-placement="right" data-fixed="true" data-backdrop="false" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<div class="table-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							<span class="white">&times;</span>
						</button>
						Datos del Ganado
					</div>
				</div>
				
				
				<div class="modal-body">		
					<h6 class="green"><b><?php echo $x_nombre_animal; ?></b></h6>
					<h6><?php echo '<b class="blue"> Sexo: </b>'.$x_sexo; ?></h6>	
					<h6><?php echo '<b class="blue"> Raza: </b>'.$x_raza; ?></h6>
					<h6><?php echo '<b class="blue"> Clasificacion: </b>'.$x_grupo_etareo; ?></h6>	
					<h6><?php echo '<b class="blue"> Edad: </b>'.$email; ?></h6>						
				</div>
			</div><!-- /.modal-content -->

			<button class="aside-trigger btn btn-info btn-app btn-xs ace-settings-btn" data-target="#right-menu" data-toggle="modal" type="button">
				<i data-icon1="fa-plus" data-icon2="fa-minus" class="ace-icon fa fa-plus bigger-110 icon-only"></i>
			</button>
		</div><!-- /.modal-dialog -->
	</div>	
			
					

</body>

	<script src="../../js/funciones.js"></script>  
	<script src="../../assets/js/jquery.2.1.1.min.js"></script>		
	<script src="../../assets/js/bootstrap.min.js"></script>	
	<script src="../../assets/js/jquery.dataTables.min.js"></script>
	<script src="../../assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>	

	<script src="../../assets/js/chosen.jquery.min.js"></script>
	<script src="../../assets/js/dataTables.tableTools.min.js"></script>
	<script src="../../assets/js/dataTables.colVis.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function (){
		   	
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			
			.next().on(ace.click_event, function(){//show datepicker when clicking on the icon
				$(this).prev().focus();
			})
			
		
			  
		});
	
	</script>
				

<script language="javascript" type="text/javascript">
		
	function Asignar(){
		if(campos_blancos(document.formulario) == false){
			if (confirm('Desea Asignar la Produccion?') == true){
				document.formulario.tarea.value = "U";
				document.formulario.action = "gan_produccion_leche.php";
				document.formulario.method = "POST";
				document.formulario.submit();
			}
		}	
	}
	
	function Eliminar_Animal_Leche(identificador){
		if (confirm('Desea Eliminar el Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.pk_leche.value = identificador;
			document.formulario.action = "gan_produccion_leche.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	function Cancelar(){
		location.href = "gan_produccion_view.php";
	}
			
</script>

</html>