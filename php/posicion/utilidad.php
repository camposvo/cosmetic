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
		$egreso = $row[$li_indice-2];     // Campo que identifica el registro clave
		$ingreso = $row[$li_indice-3];  // Campo que identifica el registro clave - opcional
		
		// SE ESTABLECE EL COLOR DE LINEA POR DEFECTO
		$color_linea = ($sw==0)?"<tr class='Tabla_fila_claro'>":"<tr class='Tabla_fila_blanco'>";
		$sw = ($sw==0)?1:0; 
		
		
		// SE ESCRIBEN LOS VALORES EN LAS COLUMNAS
		echo $color_linea;
		for ($i = 0; $i < $li_columnas; $i++){
			$temp = is_numeric($row[$i])?number_format($row[$i],2,",","."):$row[$i];
			echo "<td class='font_fila'><div align=\"left\">" .$temp."</div></td>";
		}
		
			//PAGINA: adm_listar_plantel
		if(strtoupper($operacion)=='LISTAR_CUENTA'){
			
			$egreso  = $egreso==0?'':number_format($egreso*(-1),2,",",".");
			$ingreso = $ingreso==0?'':number_format($ingreso,2,",",".");
			$ls_cod1 = number_format($ls_cod1,2,",",".");
			echo "<td class=''>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			echo "<td class='hidden-480'>" . $row[$i++] . "</td>"; 
			
			echo "<td class='font_fila'><div align=\"left\">" .$ingreso."</div></td>";
			/*echo "<td class='font_fila'><div align=\"left\"><font color=\"red\"> " .$egreso."</div></td>";*/
			echo "<td class='font_fila'><div align=\"left\">".$egreso."</div></td>";
			echo "<td class='font_fila'><div align=\"left\">" .$ls_cod1."</div></td>";
			
			echo '<td>
					<div class="action-buttons">
					
						<a class="blue tooltip-info  open-event" data-placement="left" href="#" title="Detalle" onClick=\'Mostrar_Info("'.$ls_cod.'","'.$ingreso.'","'.$egreso.'");return false;\'>
							<i class="ace-icon fa fa-search-plus bigger-130"></i>
						</a>
						
						
					</div>
					
				</td>';
			
			//echo "<td style=\"CURSOR: hand\" onClick=\"Mostrar_Info('".$ls_cod."','".$ingreso."','".$egreso."');\"><div align=\"center\" title=\"Detalle de la Operacion \"><img src=\"../../img/iconos_pagina/ficha.png\" width=\"25\" border=\"0\" ></div></td>";
		}		
		echo "</tr>";
    }
	

} 
?>