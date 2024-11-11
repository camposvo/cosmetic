<?php
/*----------------------------------------------------------------------------------------------------------------------|
|	Nombre: clspostgres.php                                                                                             |
|	Descripci�n: Clase basada en la class.php del curso dictado por PHP de venezuela                                    |
|                                                          |
-----------------------------------------------------------------------------------------------------------------------*/
class cls_posgres{
/*------------------------------------------------------------------------------------------------------------------------
	Atributos de la clase postgres
------------------------------------------------------------------------------------------------------------------------*/
	//var $ls_nbbasedat  = "siscamp_desarrollo"; // Se refierre al nombre de la base de datos
	var $ls_nbbasedat  = "cosmetic";
	
	var $ls_puerto     = "5432";         // Se refierre al nombre de la base de datos
	var $ls_nbservidor = "localhost";    // Almacena el nombre del servidor
	var $ls_usuario    = "postgres";     // Almacena el identificador del usuario
	var $ls_clave      = "postgres";       // Almacena la clave del usuario
	var $ls_regactual  = -1;             //
	
	// Variables de identificador de conexi�n y consulta
	var $li_idconex;
	var $li_idconsult;
	var $li_errnum = 0;   // Almacena el numero del error
	var $ls_errmsg = "";  // Almacena el mensaje de error

/*------------------------------------------------------------------------------------------------------------------------
	Objetivo: Establecer una conexi�n no persistente con una base de datos postgresql
	Entrada:  ls_host: nombre del servidor, ls_bd: nombre de la base de datos, ls_user: usuario, ls_pass: clave.
	Salida:   $li_idconex: identificador de la conexi�n con la base de datos.
------------------------------------------------------------------------------------------------------------------------*/
	function fun_conectarpg(){
		$connection_string = "host=$this->ls_nbservidor  dbname=$this->ls_nbbasedat user=$this->ls_usuario password=$this->ls_clave port=$this->ls_puerto";
		$this->li_idconex= pg_connect($connection_string) or die('No se pudo Conectar con el Servidor de Base de Datos');

		if ($this->li_idconex){
			 return $this->li_idconex;
		}else{
			return 0;
		}
	}

/*------------------------------------------------------------------------------ 
Objetivo: Cerrar la conexi�n y liberar el recurso de la consulta
-------------------------------------------------------------------------------*/
	function fun_closepg($id_conex,$ls_result=""){
		 if (!$id_conex){
			 pg_free_result($ls_result);
			 pg_close($id_conex);
			 return 1;
		 }else{
			  return 0; // No hay conexi�n abierta
		 }
	}

/*----------------------------------------------------------------------------------
Objetivo: Ejecutar una consulta en Postgresql
Entradas: $ls_sql: es un string que contiene la consulta a ejecutar.
Salida: $li_idconsult:  identificador de la consulta
------------------------------------------------------------------------------------*/
	function fun_consult($ls_sql = ""){
         if ($ls_sql == ""){
             return 0;
         }
         	// ejecutamos la consulta
         $this->li_idconsult = pg_query($ls_sql);
         if (!$this->li_idconsult){
             return 0;      // ha fallado la consulta
         }else {
         	return $this->li_idconsult;
         }
	}

/*----------------------------------------------------------------------------
Objetivo: Devolver el n�mero de campos de una consulta o tabla
Salidas:  $li_numcam: el total de campos de una tabla
------------------------------------------------------------------------------*/
	function fun_numcampos(){
		return pg_num_fields($this->li_idconsult);
	}

/*----------------------------------------------------------------------------
Objetivo: Devuelve el n�mero de registros de una consulta
Salidas:  $li_numreg: el numero total de registros de una tabla o consulta
------------------------------------------------------------------------------*/
	function fun_numregistros(){
		return pg_num_rows($this->li_idconsult);
	}

/*--------------------------------------------------------------------------
Objetivo: Devolver el nombre de un campo de una consulta
Entradas: $li_numcampo:  el numero del campo a identificar
Salidas:  $ls_nbcampos: el nombre del campo
----------------------------------------------------------------------------*/
	function fun_nbcampo($li_num_campo){
		return pg_field_name($this->li_idconsult, $li_num_campo);
	}


	function fun_mueve_primero(){
         if ($this->li_idconsult == null) return 0;
         else{
             $this->fun_set_fila(0);
              return 1;
         }
	}

	function fun_mueve_ultimo() {
        if ($this->li_idconsult == null) return 0;
        else {
          $this->fun_set_fila($this->numRegistros($this->li_idconsult)-1);
          return 1;
         }
	}

	function fun_mueve_proximo() {
         // Si no es el �ltimo, avanza al siguiente
        if ($this->ls_regactual < $this->numRegistros($this->li_idconsult)-1) {
           $this->fun_set_fila($this->ls_regactual +1);
           return 1;
         }
        else return 0;
    }

	function fun_mueve_anterior() {
         // Si no es el Primer Registro, Entonces devuelve el registro anterior
         if ($this->ls_regactual > 0) {
            $this->fun_set_fila($this->ls_regactual -1);
            return 1;
         }
         else return 0;
	}

	function fun_set_fila($li_idconsult){
         $this->ls_regactual = $li_idconsult;
	}

	// Imprime una tabla con los datos de la consulta.
	function fun_datos($li_columnas){
		while($row = pg_fetch_row($this->li_idconsult)){
			echo "<tr>";
			for ($i = 0; $i < $li_columnas; $i++){
				echo "<td class='cont_plain'><div align=\"left\">" . $row[$i]. "</div></td>";
			}
			echo "</tr>";
		}
	}  

/*-----------------------------------------------------------------------------------------------------------------
Objetivo: Determinar la paginaci�n de registros en PHP.
Entrada: $li_pagina: valor que representa el n�mero de la p�gina actual visitada, de la paginaci�n
        $li_tampag: valor que representa el n�mero total de registros a mostrar en una p�gina.
Salida: $li_nicio: valor que representa el inicio de la paginaci�n en que se divide el resultado de una consulta.
-----------------------------------------------------------------------------------------------------------------*/
	function fun_tampagina( $li_paginas,$li_tam_pag){
        //examino la p�gina a mostrar y el inicio del registro a mostrar
        // validar que li_pagina sea un entero
        if (!$li_paginas) {
           $li_inicial = 0;
           $li_paginas=1;
        }else {
           $li_inicial = ($li_paginas - 1) * $li_tam_pag;
        }
        return  $li_inicial;
	}  

/*---------------------------------------------------------------------------------------------------------
Objetivo: Calcula el n�mero de p�ginas en las cuales se mostraran los resultados.
          se examina la p�gina a mostrar y el inicio del registro a mostrar
Entrada:  $li_totreg: valor que representa el total de registros
          $li_tampag: es el numero que limita la busqueda o el total de registro a mostrar en una p�gina.
Salida: el total de p�ginas a mostrar
-------------------------------------------------------------------------------------------------------------*/
	function  fun_calcpag($li_tot_reg, $li_tam_pag){
        //calculo el total de p�ginas
        $li_tot_pag = ceil($li_tot_reg / $li_tam_pag);
        return  $li_tot_pag;
	}

/*-------------------------------------------------------------------------------------------------------
Objetivo: Muestrar los distintos �ndices de las p�ginas, si es que hay varias p�ginas
Entrada:  $li_totpag: el total de p�ginas a mostrar
          $li_pagina: valor que representa el n�mero de la p�gina actual visitada, de la paginaci�n
          $ls_nbpagina: nombre de la p�gina que se llama al hacer un submit.
          $ls_criterio y $ls_txtcriterio: el criterio de busqueda por el cual se mostraran los resultados
Salida : Los indices de las p�ginas con sus respectivos link.
---------------------------------------------------------------------------------------------------------*/
	function fun_indexpag($li_totpag, $li_pagina, $ls_nbpagina, $ls_txtcriterio){
		echo "<tr aling='center'>";
		if ($li_totpag > 1){
			for ($li_i=1;$li_i<=$li_totpag;$li_i++){
				if ($li_pagina == $li_i){
					//si muestro el �ndice de la p�gina actual, no coloco enlace
					echo "<td width='5'>" . $li_pagina . " " . "</td>";
				}else{
					echo "<td width='5'>";
					//si el �ndice no corresponde con la p�gina mostrada actualmente, coloco el enlace para ir a esa p�gina
					echo "<a href='".$ls_nbpagina."?li_pagina=".$li_i.$ls_txtcriterio."'>".$li_i."</a> ";
					echo "</td>";
				}
			}
		}
		echo "</tr>";
	}
	
} // FIN DE LA CLASE cls_posgres


/*------------------------------------------------------------------------------------------------------------------------
									FUNCIONES DE UTILIDA FUERA DE LA CLASE POSTGRES
------------------------------------------------------------------------------------------------------------------------*/
/*------------------------------------------------------------------------------------------------------------------------
	FUNCI�N fun_conexion: esta funci�n se encarga de realizar la conexi�n al bd.
------------------------------------------------------------------------------------------------------------------------*/
function fun_conexion($objconexion){
	$li_id_conex=$objconexion->fun_conectarpg();
	return $li_id_conex;
	
}

/*------------------------------------------------------------------------------------------------------------------------
	FUNCI�N fun_crear_objeto_conexion: esta funci�n se encarga de crear un objeto para la conexi�n.
------------------------------------------------------------------------------------------------------------------------*/
function fun_crear_objeto_conexion(){
	$objconexion = new cls_posgres();
	return $objconexion;
}

/*------------------------------------------------------------------------------------------------------------------------
	FUNCI�N fun_cambiaf_a_postgresql: esta funci�n cambia el formato de la fecha al formato reconocido por postgresql
------------------------------------------------------------------------------------------------------------------------*/
function fun_cambiaf_a_postgresql($fecha){
	if($fecha!=''){
		ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha);
		$lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
		return $lafecha;
	}else{
		return $fecha;
	}
}

/*------------------------------------------------------------------------------------------------------------------------
	FUNCI�N fun_cambiaf_a_normal: esta funci�n cambia del formato de fecha de postgresql al formato normal
------------------------------------------------------------------------------------------------------------------------*/
function fun_cambiaf_a_normal($fecha){
	if($fecha!=''){
		ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
		$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
		return $lafecha;
	}else{
		return $fecha;
	}
}

/*------------------------------------------------------------------------------------------------------------------------
	FUNCI�N fun_error: Formatea la salida de un Error de Base de Datos
------------------------------------------------------------------------------------------------------------------------*/
function fun_error($li_indice, $li_conect, $ls_cadenasql,$pagina, $linea = '' ){
	?>
	<html>
	<head>
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body  class="cont_plain" align="center">
	<?php
		if($li_indice==1){      
	 ?>
	 <form name="frm_usuario"  >
  	<table width="90%"  align="center" >
    <tr>
		<td> 
  			<table width="100%" >
    			<tr  class="error"  > 
      				<td>Error en Base de Datos </td>
    			</tr>
				<tr class="cont_plain"> 
      				<td><?php echo ("PAGINA: ".$pagina); ?></td>
    			</tr>
				<tr class="cont_plain"> 
      				<td><?php echo ("LINEA: ".$linea); ?></td>
    			</tr>
 				<tr class="cont_plain"> 
      				<td><?php echo ("QUERY FALLA: ".$ls_cadenasql); ?></td>
    			</tr>
			</table>	
		</td>
	</tr>
	</table>	
	<?php 
	}
	exit; 
	?>
	</form>
	</body>
	</html>
<?php 
}
?>
