<?php
/*----------------------------------------------------------------------------------------------------------------------|
|        Nombre: Utilidad.php                                                                                                |
|        Descripción: este archivo contine funciones para el manejo de la conexión a la base de datos, ademas                |
|                             maneja funciones relacionadas con los errores y arreglos para el relleno de los combox         |
-----------------------------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------------------------------
        FUNCIÓN fun_conexion: esta función se encarga de realizar la conexión al bd.
------------------------------------------------------------------------------------------------------------------------*/
function fun_dibujar_tabla($rs,$li_totcampos,$li_indice,$ls_funcion, $operacion, $pagina_mtto){
	$sw = 0; 
	while ($row = $rs->fetchrow()){
		$ls_cod = $row[$li_indice]; // Campo que identifica el registro 
		$ls_cod1 = $row[$li_indice-1];

 		$color_linea = ($sw==0)?"<tr class='Tabla_fila_claro'>":"<tr class='Tabla_fila_blanco'>";
		$sw = ($sw==0)?1:0;
		echo $color_linea;
		
		if($ls_cod1=='A'){
			for ($i = 0; $i < $li_totcampos; $i++){			
	       		echo "<td class='cont_plain' bgcolor='#FFFFCC'><div align=\"center\">".$row[$i]."</div></td>";
     		}
		}else{
			for ($i = 0; $i < $li_totcampos; $i++){			
	       		echo "<td class='cont_plain' ><div align=\"center\">" . $row[$i] . "</div></td>";
     		}
		}
		
		$li_totcampos_aux=$li_totcampos;
		//Listar  Solicitud
		if(strtoupper($operacion)=='VER_SOLICITUD'){ 
			if ($ls_cod1=="NUEVA"){
				$li_totcampos_aux=$li_totcampos_aux+5;
				echo "<td style=\"CURSOR: hand\" onClick=\"Carga_familiar('".$ls_cod."','".$ls_cod1."');\"><div align=\"center\" title=\"Carga_familiar \"><img src=\"../../img/asignar1.png\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: hand\" onClick=\"Anexar_Archivo('".$ls_cod."');\"><div align=\"center\" title=\"Anexar Archivo \"><img src=\"../../img/archivo.png\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
	    		echo "<td style=\"CURSOR: hand\" onClick=\"Editar('".$ls_cod."');\"><div align=\"center\" title=\"Ver datos \"><img src=\"../../img/editar.jpg \" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: hand\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/eliminar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				echo "<td style=\"CURSOR: hand\" onClick=\"Enviar('".$ls_cod."');\"><div align=\"center\" title=\"Enviar \"><img src=\"../../img/sobre_enviar.gif\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				
			}else{
			$li_totcampos_aux=$li_totcampos_aux+5;
				echo "<td colspan='5' style=\"CURSOR: hand\" onClick=\"Ver_Solicitud('".$ls_cod."');\"><div align=\"center\" title=\"Ver_Parametros \"><img src=\"../../img/ver.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}
	 	}
		//mtto carga familiar
		if(strtoupper($operacion)=='ELIMINAR'){ 
		    $li_totcampos_aux=$li_totcampos_aux+1;
			echo "<td style=\"CURSOR: hand\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/eliminar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";

        }
		//PAGINA: VA A LA PAG. MTTO PARAMETROS 
		if(strtoupper($operacion)=='PARAMETROS'){ 
		    $li_totcampos_aux=$li_totcampos_aux+2;
	    	echo "<td style=\"CURSOR: hand\" onClick=\"Configurar('".$ls_cod."');\"><div align=\"center\" title=\"Configurar_Parametros \"><img src=\"../../img/editar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			echo "<td style=\"CURSOR: hand\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/eliminar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";

        }
		
		//VER PARAMETROS
		if(strtoupper($operacion)=='VER_PARAMETROS'){ 
		    $li_totcampos_aux;
        }
		
		if(strtoupper($operacion)=='CONFIG.PARAMETROS'){ 
			$li_totcampos_aux=$li_totcampos_aux+1;
			echo "<td style=\"CURSOR: hand\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/eliminar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
        }
		
		//Mtto_Matriz
		if(strtoupper($operacion)=='MATRIZ.EVAL'){ 
			$li_totcampos_aux=$li_totcampos_aux + 3;
			if($ls_cod1=='N'){
				echo "<td  style=\"CURSOR: hand\" onClick=\"Ver_Parametros('".$ls_cod."');\"><div align=\"center\" title=\"Ver_Parametros \"><img src=\"../../img/ver.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				echo "<td  style=\"CURSOR: hand\" onClick=\"Agregar_Parametros('".$ls_cod."');\"><div align=\"center\" title=\"Agregar_Parametros \"><img src=\"../../img/agregar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				echo "<td  style=\"CURSOR: hand\" onClick=\"Activar('".$ls_cod."');\"><div align=\"center\" title=\"Activar_Matriz \"><img src=\"../../img/activar.jpg \" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}else{
				echo "<td style=\"CURSOR: hand\" onClick=\"Ver_Parametros('".$ls_cod."');\"><div align=\"center\" title=\"Ver_Parametros \"><img src=\"../../img/ver.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				//echo "<td style=\"CURSOR: hand\" onClick=\"Agregar_Parametros('".$ls_cod."');\"><div align=\"center\" title=\"Agregar_Parametros \"><img src=\"../../img/agregar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				//echo "<td style=\"CURSOR: hand\" onClick=\"Activar('".$ls_cod."');\"><div align=\"center\" title=\"Activar_Matriz \"><img src=\"../../img/activar.jpg \" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}	
        }
		
		//listar Aprobador
		if(strtoupper($operacion)=='LISTAR_APROBADOR'){ 
			if($_SESSION['sw_sesion']==false){
				echo "<td style=\"CURSOR: hand\" onClick=\"Ver_solicitud('".$ls_cod1."');\"><div align=\"center\" title=\"Ver_solicitud \"><img src=\"../../img/ver.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			    echo "<td style=\"CURSOR: hand\" onClick=\"Anexar_Archivo('".$ls_cod1."');\"><div align=\"center\" title=\"Anexar Archivo \"><img src=\"../../img/archivo.png\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
	    		echo "<td style=\"CURSOR: hand\" onClick=\"Aprobar('".$ls_cod."');\"><div align=\"center\" title=\"Aprobar \"><img src=\"../../img/Revisado.jpeg \" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}else{
				echo "<td style=\"CURSOR: hand\" onClick=\"Mensaje_Error();\"><div align=\"center\" title=\"Ver_solicitud \"><img src=\"../../img/ver.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			    echo "<td style=\"CURSOR: hand\" onClick=\"Mensaje_Error();\"><div align=\"center\" title=\"Anexar Archivo \"><img src=\"../../img/archivo.png\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
	    		echo "<td style=\"CURSOR: hand\" onClick=\"Mensaje_Error();\"><div align=\"center\" title=\"Aprobar \"><img src=\"../../img/Revisado.jpeg \" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}
	 	}
		
		//mtto carga familiar para ell ver....
		if(strtoupper($operacion)=='LISTAR_FAMILIA'){ 
			//echo "<td style=\"CURSOR: hand\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/eliminar.gif_old\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
        }
		
		if(strtoupper($operacion)=='SOLICITUD_EXTERNA'){ // Donde los datos de la persona externa
				$li_totcampos_aux=$li_totcampos_aux+3;
				echo "<td style=\"CURSOR: hand\" onClick=\"Solicitud('".$ls_cod."');\"><div align=\"center\" title=\"Carga_familiar \"><img src=\"../../img/asignar1.png\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
				//echo "<td style=\"CURSOR: hand\" onClick=\"Eliminar('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/eliminar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
		}
		
		// 
		if(strtoupper($operacion)=='SOL_EXTERNA'){ // Donde se crea la solicitud
			if(strtoupper($ls_cod1)=='NUEVA'){
			    echo "<td style=\"CURSOR: hand\" onClick=\"Aprobar('".$ls_cod."');\"><div align=\"center\" title=\"Aprobar \"><img src=\"../../img/Revisado.jpeg \" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}else{
				echo "<td style=\"CURSOR: hand\" onClick=\"Ver_Solicitud('".$ls_cod."');\"><div align=\"center\" title=\"Ver \"><img src=\"../../img/ver.jpg \" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}				
			
				
		}
		
		
			
        echo "</tr>";

		//echo "<tr>";
        for ($i = 0; $i < $li_totcampos_aux ; $i++){echo "<td bgcolor=\"#ECECEC\"></td>";}
		echo "</tr>";
    } 
} 

// Es utilizada para listar los archivos que se descargan del servidor
function gestion_archivos($rs,$li_totcampos,$li_indice,$ls_funcion, $operacion, $pagina_mtto){
	$sw = 0; 
	while ($row = $rs->fetchrow()){
 		$color_linea = ($sw==0)?"<tr class='Tabla_fila_claro'>":"<tr class='Tabla_fila_blanco'>";
		$sw = ($sw==0)?1:0;
		echo $color_linea;
		for ($i = 0; $i < $li_totcampos; $i++){
	       //echo "<td><div align=\"left\"><a href=". $row[$i] .">".$row[$i]."</a></div></td>";
		   echo "<td><div align=\"left\">". $row[$i] ."</div></td>";
		   
     	}
		$ruta= $row[0];
     	$ls_cod = $row[$li_indice]; // Campo que identifica el registro 

		if(strtoupper($operacion)=='ELIMINA_ARCHIVO'){ 
				echo "<td style=\"CURSOR: hand\" align=\"center\" ><a href=\"descargar.php?doc=".$ruta."\"><div align=\"center\" title=\"Descargar \"><img src=\"../../img/descarga.jpg\" width=\"22\" height=\"22\" border=\"0\" ></div></a></td>";
			if(strtoupper($row[1])==strtoupper($_SESSION["usuario"])){	
				echo "<td style=\"CURSOR: hand\" onClick=\"elimina_archivo('".$ls_cod."');\"><div align=\"left\" title=\"Eliminar \"><img src=\"../../img/eliminar.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}else{
				echo "<td><div align=\"left\" title=\"Eliminar \"><img src=\"../../img/eliminar2.jpg\" width=\"16\" height=\"16\" border=\"0\" ></div></td>";
			}	
        }
        echo "</tr>";
    } 
} 
/*-----------------------------------------------------------------------------------------------------------
FUNCION : ARREGLOS
------------------------------------------------------------------------------------------------------------*/

function Combo_condicion_hab(){
        return(array("AYU.ALQUILER" => "AYU.ALQUILER","ALQUILADA" => "ALQUILADA","PROPIA" => "PROPIA","OTRO" => "OTRO"));
}

function Combo_tipo_solicitud(){
        return(array("ASIGNACION" => "ASIGNACION","REUBICACION" => "REUBICACION","TRANSFERENCIA" => "TRANSFERENCIA"));
}

function Combo_nivel_acad(){
        return(array("BACHILLER" => "BACHILLER","UNIVERSITARIO" => "UNIVERSITARIO","TSU" => "TSU","OTRO" => "OTRO"));
}
function Combo_Booleano(){
return (array ("SI"=>"SI","NO"=>"NO"));

}
function Combo_nomina(){
	return(array("MAYOR" => "MAYOR","MENOR" => "MENOR","DIARIA" => "DIARIA","OTRO" => "OTRO"));
}
function Combo_condicion_cont(){
	return(array("PERMANENTE" => "PERMANENTE","TEMPORAL" => "TEMPORAL","CONSULTORA" => "CONSULTORA"));
}

function Combo_cargo(){
	return(array("PUNTO FOCAL DIST." => "PUNTO FOCAL DIST.","SUPERINTENDENTE" => "SUPERINTENDENTE","SUPERVISOR" => "SUPERVISOR","ANALISTA" => "ANALISTA","OBRERO" => "OBRERO","MEDICO" => "MEDICO","EMFERMERA" => "ENFERMERA","OPERADOR" => "OPERADOR"));
}

function Combo_Parentesco(){
	return(array("PADRE"=>"PADRE","MADRE"=>"MADRE","HIJO(A)"=>"HIJO(A)","HERMANO(A)"=>"HERMANO(A)","SOBRINO(A)"=>"SOBRINO(A)","PRIMO(A)"=>"PRIMO(A)","NIETO(A)"=>"NIETO(A)","ABUELO(A)"=>"ABUELO(A)","OTRO"=>"OTRO","ESPOSO(A)"=>"ESPOSO(A)"));
}

/* function Combo_jornada(){

		$db = ADONewConnection($bdtype); 
		$db->Connect($dbHost, $dbUser, $dbPassword, $dbName);
		$ls_sql= fun_cadena_sql(12,$ls_campos);
		
		while($row = $rs_1->fetchrow()){
   			$ls_array[$row[0]]= $row[0];
		//echo $ls_nombre;
	    }
		
	return(ls_array);
 */	//("DIURNA" => "DIURNA","NO HACE GUARDIA" => "NO HACE GUARDIA","POR GUARDIAS" => "POR GUARDIAS"));
//}


function Combo_sexo(){
	return(array("FEMENINO" => "FEMENINO","MASCULINO" => "MASCULINO"));
}
/* function Combo_tipo_actividad(){
	return(array("APOYO HABILITADORAS"=> "APOYO HABILITADORAS","TECNICA"=>"TECNICA" ,"PERFORACION MEDICO"=>"PERFORACION MEDICO","OPER TyD AIT"=>"OPER TyD AIT"));
}
 */
 /////////////////////////////////////////////////////////////////////////////////////////////////
/*
Objetivo: Determinar la paginaci?n de registros en PHP.
Entrada: $li_pagina: valor que representa el n?mero de la p?gina actual visitada, de la paginaci?n
        $li_tampag: valor que representa el n?mero total de registros a mostrar en una p?gina.
Salida: $li_nicio: valor que representa el inicio de la paginaci?n en que se divide el resultado de una consulta.
*/

function fun_tampagina( $li_paginas,$li_tam_pag){
        //examino la p?gina a mostrar y el inicio del registro a mostrar
        // validar que li_pagina sea un entero
        if (!$li_paginas) {
           $li_inicial = 0;
           $li_paginas=1;
        }
        else {
           $li_inicial = ($li_paginas - 1) * $li_tam_pag;
        }
        return  $li_inicial;
}  // fin de function fun_tampagina()


///////////////////////////////////////////////////////////////////////////////////////////
/*
Objetivo: Calcula el n?mero de p?ginas en las cuales se mostraran los resultados.
          se examina la p?gina a mostrar y el inicio del registro a mostrar
Entrada:  $li_totreg: valor que representa el total de registros
          $li_tampag: es el numero que limita la busqueda o el total de registro a mostrar en una p?gina.

Salida: el total de p?ginas a mostrar
*/
function  fun_calcpag($li_tot_reg, $li_tam_pag){
        //calculo el total de p?ginas
        $li_tot_pag = ceil($li_tot_reg / $li_tam_pag);
        return  $li_tot_pag;
}

/*
//////////////////////////////////////////////////////////////////////////////////////
Objetivo: Muestrar los distintos ?ndices de las p?ginas, si es que hay varias p?ginas
Entrada:  $li_totpag: el total de p?ginas a mostrar
          $li_pagina: valor que representa el n?mero de la p?gina actual visitada, de la paginaci?n
          $ls_nbpagina: nombre de la p?gina que se llama al hacer un submit.
          $ls_criterio y $ls_txtcriterio: el criterio de busqueda por el cual se mostraran los resultados
Salida : Los indices de las p?ginas con sus respectivos link.

*/
function fun_indexpag($li_totpag, $li_pagina, $ls_nbpagina, $ls_txtcriterio){
       if ($li_totpag > 1)
       {
        for ($li_i=1;$li_i<=$li_totpag;$li_i++)
        {
          if ($li_pagina == $li_i){
            //si muestro el ?ndice de la p?gina actual, no coloco enlace
            echo "<td>" . $li_pagina . " " . "</td>";
          }
          else{
               echo "<td>";
               //si el ?ndice no corresponde con la p?gina mostrada actualmente, coloco el enlace para ir a esa p?gina
               echo "<a href='".$ls_nbpagina."?li_pagina=".$li_i.$ls_txtcriterio."'>".$li_i."</a> ";
               echo "</td>";
          }
         } // fin del for
        }  // fin del if
} // fin de la funcion


// returna el formato fecha completa y además el día de la semana
	function fun_fechaweek(){
    	$t_fechahoy = fecha_corta();
        $t_dia = date("w", $t_fechahoy);
        $t_dia = NombreDiaSemana($t_dia);
    	$t_fechahoy =  $t_dia . ", " .$t_fechahoy;
        return  $t_fechahoy;
	}

 
?>