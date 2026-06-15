<?php
/*-------------------------------------------------------------------------------------------
	Nombre: adm_utilidad.php                                                     
	Descripcion: Contiene Funciones de Utilidad para el modulo << ADMINISTRAR >>
--------------------------------------------------------------------------------------------*/
include("../../clases/clspostgres.php");
include("../../config/config.php");

/*----------------------------------------------------------------------------------------
 FUNCIÓN fun_conexion: esta función se encarga de realizar la conexión al bd.
-----------------------------------------------------------------------------------------*/
function fun_dibujar_tabla($rs,$li_columnas,$li_indice, $operacion){
	$sw = 0; 
    $j=0;
	while ($row = pg_fetch_row($rs->li_idconsult)){
		// CAMPOS CLAVES PARA LOS BOTONES DE ENLACE - Deben Estar al final de la clausula select
     	$ls_cod = $row[$li_indice];     // Campo que identifica el registro clave
		$ls_cod1 = $row[$li_indice-1];  // Campo que identifica el registro clave - opcional
		
		// SE ESTABLECE EL COLOR DE LINEA POR DEFECTO
		$color_linea = ($sw==0)?"<tr class='Tabla_fila_claro'>":"<tr class='Tabla_fila_blanco'>";
		$sw = ($sw==0)?1:0; 
		
		
		// SE ESCRIBEN LOS VALORES EN LAS COLUMNAS
		echo $color_linea;
		for ($i = 0; $i < $li_columnas; $i++){
			echo "<td class='cont_plain'><div align=\"left\">" . $row[$i] . "</div></td>";
		}
		
			//PAGINA: adm_listar_plantel
		if(strtoupper($operacion)=='LISTAR_ACTIVIDAD'){ 
			echo "<td style=\"CURSOR: hand\" onClick=\"AgregarRegistro('".$ls_cod."');\"><div align=\"center\" title=\"Historico \"><img src=\"../../img/iconos_pagina/revisado.png\" width=\"21\" border=\"0\" ></div></td>";
			echo "<td style=\"CURSOR: hand\" onClick=\"EditarActividad('".$ls_cod."');\"><div align=\"center\" title=\"Editar \"><img src=\"../../img/iconos_pagina/editar.png\" width=\"21\" border=\"0\" ></div></td>";
			echo "<td style=\"CURSOR: hand\" onClick=\"EliminarActividad('".$ls_cod."');\"><div align=\"center\" title=\"Eliminar \"><img src=\"../../img/iconos_pagina/eliminar.png\" width=\"21\" border=\"0\" ></div></td>";
			
		}	


				
		echo "</tr>";
    } 
} 
?>