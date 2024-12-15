<?php 
/*-------------------------------------------------------------------------------------------
	Descripcion: Contiene Funciones/Constantes para Configurar todos los Modulos del Sistema
--------------------------------------------------------------------------------------------*/
	$rol_defaut = "INVITADO";
	$rol_status = 0;	
	date_default_timezone_set('America/La_Paz');
/*-------------------------------------------------------------------------------------------
CONFIGURACIÓN DE LA RUTA HACIA LA CLASE 
		2)- $ls_ruta_cls_excel_workbook: corresponde a la direccion de la clase excel workbook.
		3)- $ls_ruta_cls_excel_worksheet: corresponde a la direccion de la clase excel worksheet.
--------------------------------------------------------------------------------------------*/
	$ls_ruta_cls_fpdf = "../../clases/fpdf/fpdf.php";
	$ls_ruta_cls_excel_workbook  = "../../clases/ExcelWriter/class.writeexcel_workbook.inc.php";
	$ls_ruta_cls_excel_worksheet = "../../clases/ExcelWriter/class.writeexcel_worksheet.inc.php";

function Combo_Abono(){
	return(array("DEBE"=>"DEBE","PAGO"=>"PAGO"));
}

function Combo_Asignacion(){
	return(array("0"=>"Bono Alimentacion",
				"1"=>"Feriado",
				"2"=>"Bono Produccion",
				"3"=>"Ayuda Unica"							
				));
}

function Combo_Almacen(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_almacen, nb_almacen FROM t09_almacen ORDER BY nb_almacen ASC";    

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Admin(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT  s01_persona.co_persona,  UPPER(tx_nombre) || ' ' || UPPER(tx_apellido)
			FROM s02_persona_rol
			INNER JOIN s01_persona ON s02_persona_rol.co_persona = s01_persona.co_persona
			WHERE co_rol = 22
			ORDER BY tx_nombre";
	/* El co_rol =  22 especifica el ROL CLIENTE, segun los cargado en la TABLA ROL */		

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Articulo(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
		$ls_sql = "SELECT
					a.pk_articulo,
					a.nb_articulo AS articulo
					FROM
						t13_articulo a
					LEFT JOIN t05_clase c ON a.fk_clase = c.pk_clase
					LEFT JOIN t21_categoria ca ON c.fk_categoria = ca.pk_categoria				
					ORDER BY
						a.nb_articulo ASC;";
	

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Articulo_Gasto(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
		$ls_sql = "SELECT
					a.pk_articulo,
					CONCAT(LPAD(ca.pk_categoria::text, 3, '0'), '-', LPAD(a.pk_articulo::text, 3, '0'), ' ', a.nb_articulo, ' (', a.nb_presentacion,')') AS articulo
					FROM
						t13_articulo a
					LEFT JOIN t05_clase c ON a.fk_clase = c.pk_clase
					LEFT JOIN t21_categoria ca ON c.fk_categoria = ca.pk_categoria
					WHERE
						a.in_gasto = 'on'
					ORDER BY
						a.nb_articulo ASC;";
	

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Articulo_Venta(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
		$ls_sql = "SELECT
					a.pk_articulo,
					CONCAT(LPAD(ca.pk_categoria::text, 3, '0'), '-', LPAD(a.pk_articulo::text, 3, '0'), ' ', a.nb_articulo, ' (', a.nb_presentacion,')') AS articulo
					FROM
						t13_articulo a
					LEFT JOIN t05_clase c ON a.fk_clase = c.pk_clase
					LEFT JOIN t21_categoria ca ON c.fk_categoria = ca.pk_categoria
					WHERE
						a.in_venta = 'on'
					ORDER BY
						a.nb_articulo ASC;";
	
	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
			
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Articulo_Precio_Venta(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
		$ls_sql = " SELECT pk_articulo, nu_precio_venta FROM t13_articulo 
		WHERE in_venta = 'on'";
	
	/* El co_rol =  40 especifica el ROL FINANCIADOR, segun los cargado en la TABLA ROL */		

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$precio = $row[1];
			$arr[$cod] = $precio; 
			
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Articulo_Precio_Compra(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
		$ls_sql = " SELECT pk_articulo, nu_precio_compra FROM t13_articulo 
		WHERE in_gasto = 'on'";
	

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$precio = $row[1];
			$arr[$cod] = $precio; 
			
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}



function Combo_Categoria_Articulo(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_categoria, nb_categoria
				FROM t21_categoria
				ORDER BY nb_categoria ASC";    

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Categoria_Proyecto(){
	return(array("INVERSION"=>"INVERSION","GASTO"=>"GASTO"));
}


function Combo_Clase_Proyecto(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT in_clase_proyecto,  in_clase_proyecto
			FROM t02_proyecto ORDER BY in_clase_proyecto";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


 
function Combo_Clasificacion(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_clase, nb_clase
				FROM t05_clase
				ORDER BY nb_clase ASC";    

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Cliente(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT  s01_persona.co_persona,  UPPER(tx_nombre) || ' ' || UPPER(tx_apellido)
			FROM s02_persona_rol
			INNER JOIN s01_persona ON s02_persona_rol.co_persona = s01_persona.co_persona
			WHERE co_rol = 39
			ORDER BY tx_nombre";
	/* El co_rol =  39 especifica el ROL CLIENTE, segun los cargado en la TABLA ROL */		

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Condicion(){
	return(array("Trabajador"=>"Trabajador","Cliente"=>"Cliente","Administrador"=>"Administrador"));
}



function Combo_Deduccion(){
	return(array("0"=>"Seguro Social",
				"1"=>"FAOV",
				"2"=>"Prestaciones",
				"3"=>"Otros"						
				));
}


function Combo_Empleado(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT  s01_persona.co_persona,  UPPER(tx_nombre) || ' ' || UPPER(tx_apellido)
			FROM s02_persona_rol
			INNER JOIN s01_persona ON s02_persona_rol.co_persona = s01_persona.co_persona
			WHERE co_rol = 46
			ORDER BY tx_nombre";
	/* El co_rol =  39 especifica el ROL CLIENTE, segun los cargado en la TABLA ROL */		

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Estatus(){
	return(array("ABIERTO"=>"ABIERTO","PROGRESO"=>"PROGRESO","PENDIENTE"=>"PENDIENTE","CERRADO"=>"CERRADO"));
}


function Combo_Financiador(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT  s01_persona.co_persona,  UPPER(tx_nombre) || ' ' || UPPER(tx_apellido)
			FROM s02_persona_rol
			INNER JOIN s01_persona ON s02_persona_rol.co_persona = s01_persona.co_persona
			WHERE co_rol = 40
			ORDER BY tx_nombre";
	/* El co_rol =  40 especifica el ROL FINANCIADOR, segun los cargado en la TABLA ROL */		

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Grupo_Etareo(){
		$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_grupo_etareo, nb_grupo_etareo
				  FROM gan_grupo_etareo
				ORDER BY nb_grupo_etareo";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}



function Combo_Gan_Madre(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_ganado, nb_nombre_animal
				  FROM gan_ganado
				  WHERE in_sexo = 'HEMBRA'
				ORDER BY nb_nombre_animal";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Gan_Padre(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_ganado, nb_nombre_animal
				  FROM gan_ganado
				  WHERE in_sexo = 'MACHO'
				ORDER BY nb_nombre_animal";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}



function Combo_Menu_Padre(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT DISTINCT s05_menu_padre.co_menu_padre, s05_menu_padre.tx_descripcion, s05_menu_padre.nu_orden 
				FROM s06_menu_padre_hijo,s05_menu_padre 
				WHERE in_activo = 'S' and (s06_menu_padre_hijo.co_menu_padre = s05_menu_padre.co_menu_padre) 
				ORDER BY s05_menu_padre.nu_orden";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Lote(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_lote, nb_lote
				FROM gan_lote
				ORDER BY nb_lote";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Potrero(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	 $ls_sql = "SELECT pk_potrero, nb_potrero
				FROM gan_potrero
				ORDER BY nb_potrero";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Prioridad(){ 
	return(array("Urgente" => "Urgente","Alta" => "Alta","Mediana" => "Mediana","Baja" => "Baja","Muy Baja" => "Muy Baja"));
}

function Combo_Proveedor(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT  s01_persona.co_persona,  UPPER(tx_nombre) || ' ' || UPPER(tx_apellido)
			FROM s02_persona_rol
			INNER JOIN s01_persona ON s02_persona_rol.co_persona = s01_persona.co_persona
			WHERE co_rol = 47
			ORDER BY tx_nombre";
	/* El co_rol =  39 especifica el ROL CLIENTE, segun los cargado en la TABLA ROL */		

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_profesion(){
	return(array("ABOGADO"=>"ABOGADO","ARQUITECTO"=>"ARQUITECTO","BANQUETERO"=>"BANQUETERO","CARPINTERO"=>"CARPINTERO","CHOFER-CONDUCTOR"=>"CHOFER-CONDUCTOR","CONSTRUCTOR"=>"CONSTRUCTOR","CONTADOR-AUDITOR"=>"CONTADOR-AUDITOR","DENTISTA"=>"DENTISTA","ELECTRICISTA"=>"ELECTRICISTA","FOTÓRAFO-CAMARÓGRAFO"=>"FOTóGRAFO-CAMARÓGRAFO","HOGAR"=>"HOGAR","INGENIERO"=>"INGENIERO","MÉDICO"=>"MÉDICO","MECÁNICO"=>"MECÁNICO","MILITAR"=>"MILITAR","MÚSICO-CANTANTE"=>"MÚSICO-CANTANTE","NUTRICIONISTA"=>"NUTRICIONISTA","OBRERO"=>"OBRERO","OPERADOR"=>"OPERADOR","PROFESOR-EDUCADOR"=>"PROFESOR-EDUCADOR","POLÍTICO"=>"POLÍTICO","LICENCIADO"=>"LICENCIADO","SECRETARIA-OFICINISTA"=>"SECRETARIA-OFICINISTA","TÉCNICO"=>"TÉCNICO","TSU"=>"TSU","OTRO"=>"OTRO"));
}

function Combo_Raza(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT pk_raza, UPPER(nb_raza) FROM gan_raza 
			 ORDER BY nb_raza";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}


function Combo_Rubro(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT pk_proyecto, UPPER(tx_nombre) FROM t02_proyecto 
			WHERE in_proy_activo = 'S' ORDER BY tx_nombre";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Salida(){
	return(array("ACTIVO"=>"ACTIVO","VENTA"=>"VENTA","MUERTE POR ACCIDENTE"=>"MUERTE POR ACCIDENTE",
	"MUERTE POR ENFERMEDAD"=>"MUERTE POR ENFERMEDAD","MUERTE POR DESNUTRICION"=>"MUERTE POR DESNUTRICION","MUERTE DESCONOCIDA"=>"MUERTE POR DESCONOCIDA",
	"PERDIDO"=>"PERDIDO"));
}


function Combo_Sector(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql = " SELECT pk_sector, nb_sector FROM gan_sector ORDER BY nb_sector";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Sexo(){	
	return(array("M" => "MASCULINO","F" => "FEMENINO"));
}

function Combo_Sexo_Animal(){	
	return(array("HEMBRA" => "HEMBRA","MACHO" => "MACHO"));
}

function Combo_si_no(){
	return(array("S"=>"SI","N"=>"NO"));
}

function Combo_TipoCuenta(){ 
	return(array("AHORRO" => "AHORRO","CORRIENTE" => "CORRIENTE"));
}


function Combo_Tipo_Diagnostico(){
	return(array("REPRODUCTIVO"=>"REPRODUCTIVO",
				"NO REPRODUCTIVO"=>"NO REPRODUCTIVO"
				));
}



function Combo_Tipo_Ganado(){
	return(array("LECHERO"=>"LECHERO","CARNE"=>"CARNE","DOBLE PROPOSITO"=>"DOBLE PROPOSITO","MESTIZO"=>"MESTIZO"));
}


function Combo_TipoNomina(){
	return(array("DIARIA"=>"DIARIA","SEMANAL"=>"SEMANAL","QUINCENAL"=>"QUINCENAL","MESNUAL"=>"MESNUAL"));
}



function Combo_TipoMovimiento(){
	return(array("NOMINA"=>"NOMINA","CREDITO"=>"CREDITO","PAGO CRED."=>"PAGO CRED.","GASTO"=>"GASTO","PRESTAMO"=>"PRESTAMO","COBR PREST."=>"COBR PREST.","VENTA"=>"VENTA"));
}


function Combo_Tipo_Palpacion(){
	return(array("Preñada"=>"Preñada",
				"Vacia"=>"Vacia",
				"Vacia con Problemas"=>"Vacia con Problemas",
				));
}

function Combo_Tipo_Parto(){
	return(array("Normal"=>"Normal",
				"Con Ayuda"=>"Con Ayuda",
				"Distocico"=>"Distocico",
				"Mortinato"=>"Mortinato",
				"Otros"=>"Otros"				
				));
}


function Combo_Tipo_Rubro(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT pk_tipo_rubro,  nb_tipo_rubro
			FROM t08_tipo_proyecto ORDER BY nb_tipo_rubro";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}



function Combo_Vacuna(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT pk_vacuna, UPPER(nb_vacuna) FROM gan_vacuna 
			ORDER BY nb_vacuna";

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function Combo_Vendedor(){
	$obj_miconexion = fun_crear_objeto_conexion();
	$li_id_conex = fun_conexion($obj_miconexion);
									
	$ls_sql ="SELECT  s01_persona.co_persona,  UPPER(tx_nombre) || ' ' || UPPER(tx_apellido)
			FROM s02_persona_rol
			INNER JOIN s01_persona ON s02_persona_rol.co_persona = s01_persona.co_persona
			WHERE co_rol = 45
			ORDER BY tx_nombre";
	/* El co_rol =  39 especifica el ROL CLIENTE, segun los cargado en la TABLA ROL */		

	$ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
	if($ls_resultado != 0){
		while($row = pg_fetch_row($ls_resultado)){
			$cod = $row[0];
			$nombre = $row[1];
			$arr[$cod] = $nombre; 
		}
	}else{
		fun_error(1,$li_id_conex,$ls_sql,$_SERVER['PHP_SELF']);
	}
	return($arr);
}

function mes_letras($numero)
	{if($numero=='01') $mes="Enero";
	if($numero=='02') $mes="Febrero";
	if($numero=='03') $mes="Marzo";
	if($numero=='04') $mes="Abril";
	if($numero=='05') $mes="Mayo";
	if($numero=='06') $mes="Junio";
	if($numero=='07') $mes="Julio";
	if($numero=='08') $mes="Agosto";
	if($numero=='09') $mes="Septiembre";
	if($numero=='10') $mes="Octubre";
	if($numero=='11') $mes="Noviembre";
	if($numero=='12') $mes="Diciembre";
	return $mes;
	}
/*------------------------------------------------------------------------------------------------------------------------
	FUNCIÓN: fun_dia_letras
	DESCRIPCION: esta funcion pasa el dia de numero a letras.
	UTILIZADO:
		inscripcion.php
------------------------------------------------------------------------------------------------------------------------*/
function dia_letras($numero)
	{$cadena="";
	$temp[0]=($numero/10);
	$temp[1]=($numero%10);
	$j=0;
	while($j<2)
		{if($numero<10){$j++;}
		if($numero>15 && $numero<20){ $cadena="dieci"; $j++;}
		if($numero>20 && $numero<30){ $cadena= "venti"; $j++;}
		if($numero>30 && $numero<32){ $cadena= "treinta y"; $j++;}
		if($temp[$j]==1){$cadena=$cadena+"uno"; $j++;}
		else if($temp[$j]==2){$cadena=$cadena."dos"; $j++;}
		else if($temp[$j]==3){$cadena=$cadena."tres"; $j++;}
		else if($temp[$j]==4){$cadena=$cadena."cuatro"; $j++;}
		else if($temp[$j]==5){$cadena=$cadena."cinco"; $j++;}
		else if($temp[$j]==6){$cadena=$cadena."seis";$j++;}
		else if($temp[$j]==7){$cadena=$cadena."siete"; $j++;}
		else if($temp[$j]==8){$cadena=$cadena."ocho"; $j++;}
		else if($temp[$j]==9){$cadena=$cadena."nueve"; $j++;}
		if($numero==10){$cadena="diez"; $j++;}
		if($numero==11){$cadena="once"; $j++;}
		if($numero==12){$cadena="doce"; $j++;}
		if($numero==13){$cadena="trece"; $j++;}
		if($numero==14){$cadena="catorce"; $j++;}
		if($numero==15){$cadena="quince"; $j++;}
		if($numero==20){$cadena="veinte"; $j++;}
		if($numero==30){$cadena="treinta"; $j++;}
		}//fin del while
	return $cadena;
	}

	function dias_transcurridos($fecha_i,$fecha_f){
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias 	= abs($dias); $dias = floor($dias);		
		return $dias;
	}
	?>