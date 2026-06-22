<?php
/*------------------------------------------------------------------------------------------
    Nombre: sesion.php                                                    
    Descripcion: contiene las funcionalidades para crear una sesión a un usuario y permitir 
    acceso a la Aplicación (Versión Asegurada y Optimizada)
--------------------------------------------------------------------------------------------*/

session_start();
include_once ("clases/clspostgres.php");

/*-------------------------------------------------------------------------------------------
    INICIALIZACION: asigna los valores iniciales a las VARIABLES DE SESION
--------------------------------------------------------------------------------------------*/
$_SESSION["gs_inivitado"]   = "N";  
$_SESSION["autentificado"]  = "NO"; 
$_SESSION["li_cod_usuario"] = "";   
$_SESSION["menu"]           = array(); // Inicializar como array limpio
$_SESSION["gs_usuario"]     = "";   
$_SESSION["usuario"]        = "";   
$_SESSION["num_mensaje"]    = 0;   

// Captura segura de credenciales (evitamos eval por completo)
$o_usuario = isset($_POST['o_usuario']) ? trim($_POST['o_usuario']) : '';
$o_clave   = isset($_POST['o_clave']) ? $_POST['o_clave'] : '';

// Instancia única de conexión para reutilizar en el script
$obj_miconexion = fun_crear_objeto_conexion();
$li_id_conex = fun_conexion($obj_miconexion);

// Sanitización estricta para evitar Inyección SQL usando la conexión nativa de PgSQL
// Nota: Si tu clase provee un método de escape, usa $obj_miconexion->escape($o_usuario)
if (function_exists('pg_escape_string')) {
    $o_usuario_escaped = pg_escape_string($o_usuario);
} else {
    $o_usuario_escaped = addslashes($o_usuario); // Alternativa fallback menos ideal
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Inicio de Sesión</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript">
        function ir(){
            document.formulario.action = "interface.php";
            document.formulario.submit();
        }
    </script>
</head>
<body>
<form name="formulario" method="POST">
<?php   

if (!empty($o_usuario) && !empty($o_clave)) {
    
    $sw = false;
    // Pasamos el objeto de conexión para no abrir múltiples conexiones innecesarias
    if(dir_local($o_usuario_escaped, $o_clave, $obj_miconexion)){ 
        $sw = true;
    }

    $co_usuario = -1;
    if($sw == true) {
        $co_usuario = get_nombre_usuarios($o_usuario_escaped, $obj_miconexion);
    }

    if($co_usuario > 0){
        // El ID de usuario al ser entero se castea obligatoriamente a (int) para blindar el SQL
        $co_usuario = (int)$co_usuario;
        $fecha = date('Y-m-d H:i:s');
        $usuario_log = strtoupper($o_usuario_escaped);

        $ls_sql = "INSERT INTO t11_bitacora(co_persona, fe_fecha, tx_tabla, tx_accion, tx_sql) 
                   VALUES ($co_usuario, '$fecha', 'INICIO DE SESION', 'L', '$usuario_log')";
        
        $ls_resultado =  $obj_miconexion->fun_consult($ls_sql);
        if($ls_resultado == 0){
            fun_error(1, $li_id_conex, $ls_sql, $_SERVER['PHP_SELF']);
        }
        
        /*------------------------------------------------------------------------|
            Carga Variables de Sesión (Corregido el operador '=' )
        |------------------------------------------------------------------------*/
        $_SESSION["gs_inivitado"]   = "S"; 
        $_SESSION["autentificado"]  = "SI";
        $_SESSION["usuario"]        = $o_usuario; 
        $_SESSION["li_cod_usuario"] = $co_usuario;
        $_SESSION["menu"]           = Cargar_Menu($co_usuario, $obj_miconexion);
        
        echo "<script type='text/javascript'>ir();</script>"; 
        exit;
    } else {
        $_SESSION["autentificado"] = "NO";
        echo "<script type='text/javascript'>location.href='html/error_login.html';</script>";
        exit;
    }
} else {
    // Si se accede al archivo directamente sin POST, redirigir o denegar
    $_SESSION["autentificado"] = "NO";
}

/*--------------------------------------------------------------------------------------
    FUNCIONES REFACTORIZADAS (Reciben la conexión existente para optimizar memoria)
---------------------------------------------------------------------------------------*/

function dir_local($uid, $pwd, $obj_conexion){
    $uid = strtoupper($uid);
    // Nota de seguridad futura: Se mantiene MD5 porque la BD actual los almacena así, 
    // pero se limpió la variable $uid mitigando la inyección.
    $ls_sql = "SELECT co_password FROM s01_persona 
                WHERE UPPER(tx_indicador) = '$uid' AND co_password = MD5('$pwd') AND in_activo = 'S'";
    
    $ls_resultado = $obj_conexion->fun_consult($ls_sql);
    if ($ls_resultado != 0){
        if ($obj_conexion->fun_numregistros() != 0){
            return true;
        }
    }
    return false;
}

function get_nombre_usuarios($o_usuario, $obj_conexion){
    $nombre_apellido_usuario = "";
    $co_usuario = -1;
    $o_usuario = strtoupper($o_usuario);
    
    $ls_sql = "SELECT co_persona, tx_nombre, tx_apellido 
                FROM s01_persona 
                WHERE UPPER(tx_indicador) = '$o_usuario' AND in_activo = 'S'";

    $ls_resultado = $obj_conexion->fun_consult($ls_sql);
    
    if ($ls_resultado != 0){
        if ($obj_conexion->fun_numregistros() != 0){
            $row = pg_fetch_row($ls_resultado, 0);
            $co_usuario = $row[0];
            $nombre_apellido_usuario = trim($row[1]) . " " . trim($row[2]);
        }
    }
    
    $_SESSION["gs_usuario"] = $nombre_apellido_usuario;
    return $co_usuario;
}

function Cargar_Menu($co_usuario, $obj_conexion){
    $arr_menu = array();
    $co_usuario = (int)$co_usuario; // Blindaje estricto numérico
    
    $ls_sql = "SELECT DISTINCT s05_menu_padre.co_menu_padre, s06_menu_padre_hijo.tx_submenu,
                    s06_menu_padre_hijo.tx_pagina, s06_menu_padre_hijo.tx_icono, s05_menu_padre.nu_orden, s06_menu_padre_hijo.nu_orden 
                FROM s03_privilegio 
                    INNER JOIN (s06_menu_padre_hijo INNER JOIN s05_menu_padre ON s06_menu_padre_hijo.co_menu_padre = s05_menu_padre.co_menu_padre)
                    ON s06_menu_padre_hijo.co_menu_padre_hijo = s03_privilegio.co_menu_padre_hijo
                    INNER JOIN (s04_rol INNER JOIN s02_persona_rol ON s04_rol.co_rol = s02_persona_rol.co_rol)
                    ON s03_privilegio.co_rol = s04_rol.co_rol
                WHERE s02_persona_rol.co_persona = $co_usuario AND s06_menu_padre_hijo.in_activo = 'S'     
                ORDER BY s05_menu_padre.nu_orden ASC, s06_menu_padre_hijo.nu_orden ASC";
    
    $ls_resultado = $obj_conexion->fun_consult($ls_sql);
    if ($ls_resultado != 0){
        if ($obj_conexion->fun_numregistros() != 0){
            $i = 0;
            while($fila = pg_fetch_row($ls_resultado)){
                $arr_menu[$i][0] = $fila[0];
                $arr_menu[$i][1] = $fila[1];
                $arr_menu[$i][2] = $fila[2];
                $arr_menu[$i][3] = $fila[3];
                $i++;
            }
        } else {
            $_SESSION["autentificado"] = "NO";
            echo "<script type='text/javascript'>location.href='html/error_permiso.html';</script>";               
            exit;
        }
    }
    return $arr_menu;
}
?>
</form>
</body>
</html>