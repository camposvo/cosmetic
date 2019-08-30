<?php
	session_start();
	include_once ("clases/clspostgres.php");
	/*----------------------------------------------------------------------------------------------------------------------|
	| VERIFICACION Y AUTENTIFICACIN DE USUARIO.                                                                            |
	|----------------------------------------------------------------------------------------------------------------------*/

	$usu_autentico = isset($_SESSION['autentificado'])?$_SESSION['autentificado']:'';
	if ($usu_autentico != "SI"){
		session_destroy();
    	echo"<script language='JavaScript' type='text/JavaScript'>top.location.href='/pages/fin_sesion.html'</script>";
		exit();
	}

	/*-----------------------------------------------------------------------------------------------------------------------
		RUTINA: Se utiliza para recibir las variables por la url.
	------------------------------------------------------------------------------------------------------------------------*/
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
    
	$co_usuario = $_SESSION["li_cod_usuario"];
	$arrmenu=$_SESSION["menu"];
	
	$obj_conexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_conexion);
	
	if ($li_id_conex != 0){
		$ls_sql = "SELECT DISTINCT s05_menu_padre.co_menu_padre,s05_menu_padre.tx_descripcion, 
							s05_menu_padre.nu_orden, tx_icono_padre, s05_menu_padre.tx_clase
					FROM s03_privilegio 
						INNER JOIN (s06_menu_padre_hijo INNER JOIN s05_menu_padre ON s06_menu_padre_hijo.co_menu_padre = s05_menu_padre.co_menu_padre)
						ON s06_menu_padre_hijo.co_menu_padre_hijo= s03_privilegio.co_menu_padre_hijo
						INNER JOIN (s04_rol INNER JOIN s02_persona_rol ON s04_rol.co_rol = s02_persona_rol.co_rol)
						ON s03_privilegio.co_rol= s04_rol.co_rol
					WHERE s02_persona_rol.co_persona = $co_usuario 	
					ORDER BY s05_menu_padre.nu_orden ASC";
		
	
		$ls_resultado =  $obj_conexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if ($obj_conexion->fun_numregistros() != 0){
				$i=0;
				while($fila = pg_fetch_row($ls_resultado)){
					$menu_padre[$i][0]=$fila[0];//co padre
					$menu_padre[$i][1]=$fila[1];//descripcion padre
					$menu_padre[$i][2]=$fila[2];//nu orden.
					$menu_padre[$i][3]=$fila[3];//descripcion padre
					$menu_padre[$i][4]=$fila[4];//style class del menu padre -- NO ESTA EN FUNCIONAMIENTO
					$i++;
				}
			}
		}	
		
		//Datos del usuario
		$ls_sql = "SELECT tx_nombre || ' ' || tx_apellido, tx_dir_foto
					FROM s01_persona 
					WHERE s01_persona.co_persona = $co_usuario";
		
	
		$ls_resultado =  $obj_conexion->fun_consult($ls_sql);
		if ($ls_resultado != 0){
			if ($obj_conexion->fun_numregistros() != 0){
				$i=0;
				while($fila = pg_fetch_row($ls_resultado)){
					$nombre=ucwords(strtolower($fila[0]));
					$foto=strtolower($fila[1]);
					
					$i++;
				}
			}
		}
		
		$foto = 'foto5.jpg'; // Esto carga la misma foto para todos 
			
		
	}

		
	$ls_sql = "SELECT to_char(t16_mensaje.fe_registro, 'dd/mm/yyyy'), 
		UPPER(Emisor.tx_indicador), 
		t16_mensaje.tx_mensaje,
		UPPER(Destinatario.tx_nombre||' '|| Destinatario.tx_apellido) as NombreDestino, t17_mensaje_persona.fe_fecha_leido, 
		in_leido, t16_mensaje.pk_mensaje, Emisor.co_persona,
		UPPER(Emisor.tx_nombre||' '||Emisor.tx_apellido)
		FROM t17_mensaje_persona
		INNER JOIN s01_persona AS Destinatario ON t17_mensaje_persona.fk_destinatario = Destinatario.co_persona 
		INNER JOIN t16_mensaje ON t17_mensaje_persona.fk_mensaje = t16_mensaje.pk_mensaje
		INNER JOIN s01_persona AS Emisor ON t16_mensaje.fk_emisor = Emisor.co_persona
		WHERE in_leido = 'N' and Destinatario.co_persona = ".$co_usuario."
		ORDER BY t16_mensaje.fe_registro DESC
		";
	
	$ls_resultado =  $obj_conexion->fun_consult($ls_sql);
	$num_reg = $obj_conexion->fun_numregistros($ls_resultado);
	
	//echo $ls_sql;
	
	$arrmenu=$_SESSION["menu"];
	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>La PPreña</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />		
		<link rel="stylesheet" href="assets/font-awesome/4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="assets/fonts/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="assets/js/ace-extra.min.js"></script>
		
		

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
		
<script language="JavaScript">
	//Ajusta el tamaño de un iframe al de su contenido interior para evitar scroll
	function autofitIframe(id){		
			
		if (!window.opera && document.all && document.getElementById){
			id.style.height=id.contentWindow.document.body.scrollHeight;
		} else if(document.getElementById) {
			//alert(id.contentDocument.body.scrollwidth)
			id.style.height=id.contentDocument.body.scrollHeight+800+"px";
		}
	}
</script>
		

	</head>

	<body class="no-skin">
		<div id="navbar" class="navbar navbar-fixed-top">			

			<div class="navbar-container" id="navbar-container">
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>
				</button>

				<div class="navbar-header pull-left">
					<a href="index.html" class="navbar-brand">
						<small>
							<i class="fa fa-leaf"></i>
							Finca La PPreña
						</small>
					</a>
				</div>

				<div class="navbar-buttons navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						

						<li class="purple">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#">
								<i class="ace-icon fa fa-bell icon-animated-bell"></i>
								<span class="badge badge-important">8</span>
							</a>

							<ul class="dropdown-menu-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
								<li class="dropdown-header">
									<i class="ace-icon fa fa-exclamation-triangle"></i>
									0 Notifications
								</li>

								<li class="dropdown-content">
									<ul class="dropdown-menu dropdown-navbar navbar-pink">
										
										<li>
											<a href="#">
												<i class="btn btn-xs btn-primary fa fa-user"></i>
												Notificacion ...
											</a>
										</li>
										
									</ul>
								</li>

								<li class="dropdown-footer">
									<a href="#">
										Ver todas las Notificaciones
										<i class="ace-icon fa fa-arrow-right"></i>
									</a>
								</li>
							</ul>
						</li>

						<li class="green">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#">
								<i class="ace-icon fa fa-envelope icon-animated-vertical"></i>
								<span class="badge badge-success"><div id="resp-nro_msg1"></div>  </span>
							</a>

							<ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
								<li class="dropdown-header">
									
									<div id="resp-nro_msg"></div>  
								</li>

								<li class="dropdown-content">
									<div id="resp-msg"></div>  
								</li>

								<li class="dropdown-footer">
									<a href="php/mantenimiento/man_mensaje_view.php" target="area_trabajo"  >
										Ir a todos los Mensajes
										<i class="ace-icon fa fa-arrow-right"></i>
									</a>
								</li>
							</ul>
						</li>

						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="<?php echo 'php/foto/'.$foto; ?>" alt="Photo" />
								<span class="user-info">
									<small>Bienvenido,</small>
									<?php echo $nombre; ?>
								</span>

								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="php/sistema/sis_profile.php" target="area_trabajo">
										<i class="ace-icon fa fa-user"></i>
										Profile
									</a>
								</li>

								<li class="divider"></li>

								<li>
									<a href="html/fin_sesion.html">
										<i class="ace-icon fa fa-power-off"></i>
										Salir
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div><!-- /.navbar-container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<div id="sidebar" class="sidebar responsive">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>
				
				<!--  MENU DE ACCESO DIRECTO -->
				<div class="sidebar-shortcuts" id="sidebar-shortcuts">
					<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
						<button class="btn btn-success" onClick="document.getElementById('miFrame').src='php/administrar/adm_venta_view.php'">							
							<i class="ace-icon fa  fa-bolt"></i>
						</button>

						<button class="btn btn-danger  " onClick="document.getElementById('miFrame').src='php/administrar/adm_gasto_view.php'">
							<i class="ace-icon fa fa-shopping-cart"></i>
						</button>

						<button class="btn btn-warning" onClick="document.getElementById('miFrame').src='php/proyecto/pro_proyecto_view.php'">
							<i class="ace-icon fa fa-bar-chart"></i>
						</button>

						<button class="btn btn-info" onClick="document.getElementById('miFrame').src='php/posicion/pos_estado_cta_view.php'" >
								<i class="ace-icon fa fa-globe"></i>
						</button>
					</div>

					<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
						<span class="btn btn-success"></span>

						<span class="btn btn-info"></span>

						<span class="btn btn-warning"></span>

						<span class="btn btn-danger"></span>
					</div>
				</div><!-- /.sidebar-shortcuts -->
				
				<!--  MENU PRINCIPAL -->
				<ul class="nav nav-list">
					<li class="active">
						<a href="php/dashboard/dashboard.php" target="area_trabajo">
							<i class="menu-icon fa fa-tachometer"></i>
							<span class="menu-text"> Dashboard </span>
						</a>

						<b class="arrow"></b>
					</li>
				
					<?php
						for($i=0;$i<=count($menu_padre)-1;$i++){
							echo '<li class="">';
							echo '<a href="#" class="dropdown-toggle">';

							echo '<i class="menu-icon fa '.$menu_padre[$i][3].'"></i>';
							echo '<span class="menu-text">'.$menu_padre[$i][1].'</span>';
							echo '<b class="arrow fa fa-angle-down"></b></a>';	
							echo '<b class="arrow"></b>';	
							echo '<ul class="submenu">';
								for($j=0;$j<=count($arrmenu)-1;$j++){							
									if($arrmenu[$j][0]==$menu_padre[$i][0]){	
										echo '<li class="prueba">';
										echo '<a  id="'.$menu_padre[$i][1].';'.$arrmenu[$j][1].';'.$menu_padre[$i][3].'"  href="'.$arrmenu[$j][2].'" target="area_trabajo">';
										echo '<i class="menu-icon fa fa-caret-right"></i>';
										echo($arrmenu[$j][1]);
										echo '</a>';
										echo '<b class="arrow"></b>';
										echo '</li>';
									} 
								}
							echo '</ul>';
						}
					?>				
				</ul><!-- /.nav-list -->

				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>

				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>

			
			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs fixed" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>
						<div id="recargar_titulo"></div>						
					</div>
					
					<div class="page-content">
						<!--   MENU DE CONFIGURACION    -->
						<div class="ace-settings-container" id="ace-settings-container">
							
							<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
								<i class="ace-icon fa fa-cog "></i>
							</div>

							<div class="ace-settings-box clearfix" id="ace-settings-box">
								<div class="pull-left width-50">
									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
										<label class="lbl" for="ace-settings-sidebar"> Fijar Menu</label>
									</div>

									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
										<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
									</div>
								</div><!-- /.pull-left -->

								<div class="pull-left width-50">
									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" />
										<label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
									</div>

									<div class="ace-settings-item">
										<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" />
										<label class="lbl" for="ace-settings-compact"> Compact Menu</label>
									</div>
								</div><!-- /.pull-left -->
							</div><!-- /.ace-settings-box -->
						</div><!-- /.ace-settings-container -->
						
							<!-- Main content -->
							
							<section>
									<!-- <iframe id="miFrame" name="area_trabajo" src='../../html/inicio.html' width="100%" height="0" frameborder="0" onload="setIframeHeight(this);" ></iframe> -->
									<iframe  id="miFrame" name="area_trabajo" src='php/dashboard/dashboard.php' width="100%" height="0" frameborder="0" onload="autofitIframe(this);" ></iframe>  
									<!-- <object id="miFrame" name="area_trabajo" type="text/html" data='html/inicio.html'  width="100%" height="1000"> < /object>  -->
							
							</section>
						
							<!-- /.content -->
					</div><!-- /.main-content -->

					<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
						<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
					</a>
				</div><!-- /.main-conten inner -->
			</div>
			<div class="footer">
				<div class="footer-inner">
					<div class="footer-content">
						<span class="bigger-120">
							<span class="blue bolder">Finca La PPreña</span>
							2017-2019
						</span>

						
					</div>
				</div>
			</div>
		</div><!-- /.main-container -->
		<input type="hidden" id="usuario" name="co_usuario" value="<?php echo $co_usuario;?>">
		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="assets/js/jquery.2.1.1.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery.1.11.1.min.js"></script>
<![endif]-->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->

		<!--[if lte IE 8]>
		  <script src="assets/js/excanvas.min.js"></script>
		<![endif]-->

		<script src="assets/js/jquery-ui.custom.min.js"></script>
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>


		<script type="text/javascript">		
			timer = window.setInterval("ActulizarMensaje();", 4000);
		
			$(document).ready(function(){			
				//esta funcion es invocada desde el iframe para colocar es scroll vertical a cero es decir arriba
				window.ScrollToTop = function(){
				  $('html,body', window.document).animate({
					scrollTop: '0px'
				  }, 'fast');
				};
				
				$("a").click(function(){
					var oID = $(this).attr("id");
					resul = oID.split(';') 
					
					$("#recargar_titulo").html(
							'<ul class="breadcrumb"><li><i class="ace-icon fa '+resul[2]+
							' fa-fw"></i><a href="#">&nbsp;'+resul[0]+'</a></li><li class="active">'+resul[1]+'</li></ul>'
					);
				});
				
				ace.settings.set("sidebar","fixed");
				
				//Refresca el menu mensajes
				ActulizarMensaje();
								
			});	

			function ActulizarMensaje(){
				
				$.ajax({
					// la URL para la petición
					url: "php/mantenimiento/man_msg.php", 
					
					data: "co_usuario="+$("#usuario").val(),					
					 
					type: "get",
					
					// el tipo de información que se espera de respuesta
					dataType : 'json',
					
					// código a ejecutar si la petición es satisfactoria;
					success: function(result){
						 if(result.num_reg>0){							
							 $("#resp-msg").html('<ul class="dropdown-menu dropdown-navbar">'+result.datos+'</ul>');
							 $("#resp-nro_msg").html('<i class="ace-icon fa fa-envelope-o"></i>'+result.num_reg+' Mensajes');
							 $("#resp-nro_msg1").html(result.num_reg);
						 }else{
							 $("#resp-msg").html('<ul class="dropdown-menu dropdown-navbar"></ul>');
							 $("#resp-nro_msg").html('<i class="ace-icon fa fa-envelope-o"></i>'+result.num_reg+' Mensajes');
							 $("#resp-nro_msg1").html(result.num_reg);
							 
						 }
					},
					
					error : function(xhr, status) {
						alert('Disculpe, existió un problema ');
					}	
				});
			
								
			}

		</script>
	</body>
</html>
