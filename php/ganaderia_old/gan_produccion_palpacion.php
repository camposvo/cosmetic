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
<title>La Peperana</title>
		
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../../assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
	<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="../../assets/css/chosen.min.css" /> 
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
	$arr_tipo_palpacion  = Combo_Tipo_Palpacion();
	$obj_miconexion  = fun_crear_objeto_conexion();
	$li_id_conex 	 = fun_conexion($obj_miconexion);

/*-------------------------------------------------------------------------------------------
	ELIMINAR UN REGISTRO DE LA TABLA DE PARTOS
-------------------------------------------------------------------------------------------*/
	if ($tarea == "E"){
		$ls_sql = "DELETE FROM gan_palpacion
						WHERE pk_palpacion = $pk_palpacion;";		
		$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);		
		if($ls_resultado == 0){
			fun_error(1,$li_id_conex,$ls_sql,$_SERVER[PHP_SELF], __LINE__); //  Envía Mensaje De Error De Consulta.
		}else{
			echo "<script language='javascript' type='text/javascript'>alert('Registro Eliminado exitosamente');</script>";	
		}
	}
	
/*-----------------------------------------------------------------------------------------------------------------------
	INSERTA UN NUEVO PARTO ASOCIADO AL ANIMAL
------------------------------------------------------------------------------------------------------------------------*/
	if ($tarea == "I"){
					
		$error_sql = false;			
		
		$ls_sql = "INSERT INTO gan_palpacion( fe_fecha_palpacion, in_resultado, tx_comentario, in_diagnostico, nu_dias_gestacion,  fk_ganado)
    VALUES ( '$o_fecha', '$o_resultado', '$x_comentario', '$x_diagnostico', $x_dias, $pk_animal);";

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
	CONSULTA LOS DATOS DEL ANIMAL 
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
	CONSULTA LISTA DE PARTOS DEL ANIMAL
-------------------------------------------------------------------------------------------*/				
	$ls_sql = "SELECT to_char(fe_fecha_palpacion, 'yyyy/mm/dd'), in_resultado, in_diagnostico, nu_dias_gestacion, pk_palpacion
				FROM gan_palpacion
			  WHERE fk_ganado = $pk_animal ";

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
				
			<div class="col-xs-12 col-sm-8 center">	
											
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
					<label class="col-sm-3 control-label no-padding-right" > Resultado </label>
					<div class="col-sm-7" >	
						<select name="o_resultado" class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Selecciona un Cliente...">
							<?php
								if ($o_resultado == ""){
									echo "<option value='0' selected></option>";
								}else{
									echo "<option value='0'></option>";
								}
								foreach($arr_tipo_palpacion as $k => $v) {
									$ls_cadenasel =($k == $o_resultado)?'selected':'';
									echo "<option value='$k' $ls_cadenasel>$v</option>";                
								}
							?>							
						</select>
					</div>													
				</div>
				
				<div class="form-group">
					<label  class="col-sm-3 control-label no-padding-right"  > Dias Gestacion </label>
					<div class="col-sm-7" >
						<input class="col-xs-10 col-sm-7" name="x_dias" value="<?php echo $x_dias;?>" type="text"  placeholder="Dias de gestacion" onKeyPress="return validardec(event)">
					</div>
				</div>
				
				
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" >Diagnostico </label>
					<div class="col-sm-7" >	
						<select name="x_diagnostico" class="col-xs-10 col-sm-7 chosen-select " data-placeholder="Selecciona un Cliente...">
							<?php
								if ($x_diagnostico == ""){
									echo "<option value='0' selected></option>";
								}else{
									echo "<option value='0'></option>";
								}
								foreach($arr_tipo_parto as $k => $v) {
									$ls_cadenasel =($k == $x_diagnostico)?'selected':'';
									echo "<option value='$k' $ls_cadenasel>$v</option>";                
								}
							?>							
						</select>
					</div>													
				</div>
				
				<div class="form-group">
					<label  class="col-sm-3 control-label no-padding-right" >Comentario</label>
					<div class="col-sm-9" >
						<textarea name="x_comentario" cols="10" class="form-control" rows="1" onKeyPress="return validarAlfa(event)"placeholder="Enter ..."><?php echo $x_comentario;?></textarea>
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
							
							
					
			</div>
		</div>
			
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">										
				<div class="table-header">
					Registros de Partos del Animal
				</div>

				<table id="dynamic-table" class="table table-striped table-bordered table-hover ">
					<thead>
						<tr>	
							<th class="">Fecha</th>
							<th class="">Resultado</th>
							<th class="">Diagnostivo</th>
							<th class="">Dias de Gestacion</th>
							<th class=""></th>
						</tr>
					</thead>
					<tbody>	
						<?php   										
							$li_numcampo = 0;
							$li_indicecampo = $obj_miconexion->fun_numcampos()-1; // Referencia al indice de la columna clave
							fun_dibujar_tabla($obj_miconexion,$li_numcampo,$li_indicecampo, 'LISTAR_PALPACION'); // Dibuja la Tabla de Datos
							$obj_miconexion->fun_closepg($li_id_conex,$ls_resultado);
						?>
					</tbody>
				</table>
			</div>
		</div>
			<input type="hidden" name="pk_parto" value="<?php echo $pk_parto;?>">
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
	<script src="../../assets/js/jquery-ui.custom.min.js"></script>
	<script src="../../assets/js/bootstrap-datepicker.min.js"></script>
	<script src="../../assets/js/ace-elements.min.js"></script>
	<script src="../../assets/js/ace.min.js"></script>
	<script src="../../assets/js/chosen.jquery.min.js"></script>
	
	
	<script type="text/javascript">
		$(document).ready(function (){
		   	
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
			
			.next().on(ace.click_event, function(){//show datepicker when clicking on the icon
				$(this).prev().focus();
			})
			
			var table = $('#dynamic-table').DataTable( {
				"lengthChange": false,
				"pageLength": 10,
				"aaSorting": [ [1,'desc'] ],
				"oLanguage": {
					"sInfo": "De _START_ hasta _END_ de un total de _TOTAL_",
					"sInfoFiltered": " ( filtrado de _MAX_ registros )",
					"sSearch": "Filtro:",						
					"spaginate": {
					  "next": "Próximo",
					  "previous": "Previo"
					}
				},
				"select": true,
								
				"columns": [					
					null,
					null,
					null,
					{ "orderable": false }
				  ]				  				  
			} );
			
					
			
			var tableTools_obj = new $.fn.dataTable.TableTools( table, {					
				"sRowSelect": "multi",
				"fnRowSelected": function(row) {
					//check checkbox when row is selected
					try { $(row).find('input[type=checkbox]').get(0).checked = true}
					catch(e) {}
				},
				"fnRowDeselected": function(row) {
					//uncheck checkbox
					try { $(row).find('input[type=checkbox]').get(0).checked = false }
					catch(e) {}
				},
				"sSelectedClass": "success"			       
			 } );
				
				
			if(!ace.vars['touch']) {
			$('.chosen-select').chosen({allow_single_deselect:true}); 
		
			//resize chosen on sidebar collapse/expand
			
			$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
				if(event_name != 'sidebar_collapsed') return;
				$('.chosen-select').each(function() {
					 var $this = $(this);
					 $this.next().css({'width': $this.parent().width()});
				})
			});	

			$('#chosen-multiple-style .btn').on('click', function(e){
				var target = $(this).find('input[type=radio]');
				var which = parseInt(target.val());
				if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
				 else $('#form-field-select-4').removeClass('tag-input-style');
			});
			}		
		 		
			  
		});
	
	</script>
				

<script language="javascript" type="text/javascript">
		
	function Asignar(){
			document.formulario.tarea.value = "I";
			document.formulario.action = "gan_produccion_palpacion.php";
			document.formulario.method = "POST";
			document.formulario.submit();
	}
	
	function Eliminar_Parto(identificador){
		if (confirm('Desea Eliminar el Registro?') == true){
			document.formulario.tarea.value = "E";
			document.formulario.pk_parto.value = identificador;
			document.formulario.action = "gan_produccion_parto.php";
			document.formulario.method = "POST";
			document.formulario.submit();
		}
	}
	
	function Cancelar(){
		location.href = "gan_produccion_view.php";
	}
			
</script>

</html>