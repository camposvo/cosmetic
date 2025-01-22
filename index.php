<?php

	/*==================================================================================================
	NOMBRE: INDEX.PHP
	DESCRIPCIN: PERMITE A LOS USUARIOS INICIAR SESION EN DEL SISTEMA, A TRAVES DE UN FORMULARIO EN
	LA CUAL SE INTRODUCE EL INDICADOR Y CONTRASEA DEL USAURIO.
	===================================================================================================*/
	
	session_start();
	include("clases/clspostgres.php");
	
	//Se limpian las variables de sesion
	/*inicializamos variable de secion*/
	$_SESSION["gs_inivitado"]   = "N";  /*no invitado*/
	$_SESSION["autentificado"]  = "NO"; /*usuario autentificado*/
	$_SESSION["li_cod_usuario"] = "";   /*codigo del usuario*/
	$_SESSION["menu"]           = "";   /*arreglo del menu*/
	$_SESSION["gs_usuario"]     = "";   /*nombre y apellido del usuario*/
	$_SESSION["usuario"] = "";   //indicador del usuario
	$_SESSION["gs_inivitado"]   = "";

	

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta charset="UTF-8" />
<title>BellingieriCosmetic</title>
<meta name="description" content="User login page" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

			
	<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.2.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="assets/fonts/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="assets/js/ace-extra.min.js"></script>
		<link rel="stylesheet" href="assets/css/chosen.min.css" />

		
				
</head>
<script type="text/javascript" src="js/funciones.js"></script>
<script language="javascript">

/*==================================================================================================
FUNCION 1: "IR"
DESCRIPCIN: VERIFICA SI LOS CAMPOS ESTAN COMPLETOS Y MANDA A LA PAGINA SESION.PHP
===================================================================================================*/
	function ir(){
		num = -1;
		if (document.formulario.o_clave.value == "")	          num = 2;
		if (document.formulario.o_usuario.value == "")         num = 1;
		
		switch (num) {
			case 1:
			   alert("Debe ingresar el nombre de Usuario")
			   break
			case 2:
			   alert("Debe ingresar el Password")
			   break
			default:
				document.formulario.action="sesion.php";
				document.formulario.submit();
		}
	}
	
</script>
<body>

<?php 

	$obj_conexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_conexion);
	
	

?>

<body class="login-layout light-login">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							<div class="center">
								<h1>
								
									<span class="red">Bellingieri</span>
								</h1>
								<h4 class="blue" id="id-company-text">@cosm.eticsca</h4>
							</div>

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger">
												<i class="ace-icon fa fa-coffee green"></i>
												Ingrese sus Datos
											</h4>

											<div class="space-6"></div>

											<form name="formulario" method="post">
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input name="o_usuario" type="text" class="form-control" placeholder="Username" />
															<i class="ace-icon fa fa-user"></i>
														</span>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input name="o_clave"type="password" class="form-control" placeholder="Password" />
															<i class="ace-icon fa fa-lock"></i>
														</span>
													</label>

													<div class="space"></div>

													<div class="clearfix">
														
														<button type="button" class="width-35 pull-right btn btn-sm btn-primary" onClick="ir()">
															<i class="ace-icon fa fa-key"></i>
															<span class="bigger-110">Login</span>
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>

											
										</div><!-- /.widget-main -->

										<div class="toolbar clearfix">
											
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->

							</div><!-- /.position-relative -->

							
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->

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

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
			 $(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible');//hide others
				$(target).addClass('visible');//show target
			 });
			});
			
			
			
			//you don't need this, just used for changing background
			jQuery(function($) {
			 $('#btn-login-dark').on('click', function(e) {
				$('body').attr('class', 'login-layout');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'blue');
				
				e.preventDefault();
			 });
			 $('#btn-login-light').on('click', function(e) {
				$('body').attr('class', 'login-layout light-login');
				$('#id-text2').attr('class', 'grey');
				$('#id-company-text').attr('class', 'blue');
				
				e.preventDefault();
			 });
			 $('#btn-login-blur').on('click', function(e) {
				$('body').attr('class', 'login-layout blur-login');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'light-blue');
				
				e.preventDefault();
			 });
			 
			});
		</script>
	</body>
</html>