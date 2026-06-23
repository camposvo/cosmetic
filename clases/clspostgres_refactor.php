<?php
/*----------------------------------------------------------------------------------------------------------------------|
|   Nombre: clspostgres.php                                                                                             |
|   Descripción: Clase basada en la class.php, actualizada y optimizada para PHP moderno (compatible con Intelephense).  |
-----------------------------------------------------------------------------------------------------------------------*/

class cls_posgres {
    /*--------------------------------------------------------------------------------------------------------------------
        Atributos de la clase (Visibilidad pública para mantener compatibilidad con código heredado)
    --------------------------------------------------------------------------------------------------------------------*/
    public $ls_nbbasedat  = "cosmetic";
    public $ls_puerto     = "5432";         
    public $ls_nbservidor = "localhost";    
    public $ls_usuario    = "postgres";     
    public $ls_clave      = "postgres";       
    public $ls_regactual  = -1;             
    
    /** @var resource|\PgSql\Connection|null Identificador de conexión */
    public $li_idconex;
    
    /** @var resource|\PgSql\Result|null Identificador de la última consulta */
    public $li_idconsult;
    
    public $li_errnum = 0;   
    public $ls_errmsg = "";  

    /*------------------------------------------------------------------------------------------------------------------------
        Objetivo: Establecer una conexión no persistente con una base de datos postgresql
    ------------------------------------------------------------------------------------------------------------------------*/
    /**
     * @return resource|\PgSql\Connection|int
     */
    public function fun_conectarpg() {
        $connection_string = "host={$this->ls_nbservidor} dbname={$this->ls_nbbasedat} user={$this->ls_usuario} password={$this->ls_clave} port={$this->ls_puerto}";
        
        $this->li_idconex = @pg_connect($connection_string);

        if (!$this->li_idconex) {
            die('No se pudo Conectar con el Servidor de Base de Datos');
        }

        return $this->li_idconex;
    }

    /*------------------------------------------------------------------------------ 
        Objetivo: Cerrar la conexión y liberar el recurso de la consulta
    -------------------------------------------------------------------------------*/
    /**
     * @param resource|\PgSql\Connection|null $id_conex
     * @param resource|\PgSql\Result|string|null $ls_result
     * @return int
     */
    public function fun_closepg($id_conex, $ls_result = null) {
        // Soporte por si en código antiguo pasan un string vacío ""
        if ($ls_result === "") {
            $ls_result = null;
        }

        if (!$id_conex) {
            if ($ls_result) {
                @pg_free_result($ls_result);
            } elseif ($this->li_idconsult) {
                @pg_free_result($this->li_idconsult);
            }
            if ($this->li_idconex) {
                @pg_close($this->li_idconex);
            }
            return 1;
        } else {
            if ($ls_result) {
                @pg_free_result($ls_result);
            }
            @pg_close($id_conex);
            return 1;
        }
    }

    /*----------------------------------------------------------------------------------
        Objetivo: Ejecutar una consulta en Postgresql
    ------------------------------------------------------------------------------------*/
    /**
     * @param string $ls_sql
     * @return resource|\PgSql\Result|int
     */
    public function fun_consult($ls_sql = "") {
        if (trim($ls_sql) == "") {
            return 0;
        }
        
        $this->li_idconsult = @pg_query($this->li_idconex, $ls_sql);
        
        if (!$this->li_idconsult) {
            $this->ls_errmsg = pg_last_error($this->li_idconex);
            return 0;      
        } else {
            return $this->li_idconsult;
        }
    } 

    /*----------------------------------------------------------------------------
        Objetivo: Devolver el número de campos de una consulta o tabla
    ------------------------------------------------------------------------------*/
    /**
     * @return int
     */
    public function fun_numcampos() {
        return $this->li_idconsult ? pg_num_fields($this->li_idconsult) : 0;
    }

    /*----------------------------------------------------------------------------
        Objetivo: Devuelve el número de registros de una consulta
    ------------------------------------------------------------------------------*/
    /**
     * @return int
     */
    public function fun_numregistros() {
        return $this->li_idconsult ? pg_num_rows($this->li_idconsult) : 0;
    }

    /*--------------------------------------------------------------------------
        Objetivo: Devolver el nombre de un campo de una consulta
    ----------------------------------------------------------------------------*/
    /**
     * @param int $li_num_campo
     * @return string
     */
    public function fun_nbcampo($li_num_campo) {
        return $this->li_idconsult ? pg_field_name($this->li_idconsult, $li_num_campo) : '';
    }

    /*--------------------------------------------------------------------------
        Métodos de movimiento de punteros
    ----------------------------------------------------------------------------*/
    public function fun_mueve_primero() {
        if ($this->li_idconsult == null) return 0;
        $this->fun_set_fila(0);
        return 1;
    }

    public function fun_mueve_ultimo() {
        if ($this->li_idconsult == null) return 0;
        $this->fun_set_fila($this->fun_numregistros() - 1);
        return 1;
    }

    public function fun_mueve_proximo() {
        if ($this->li_idconsult == null) return 0;
        if ($this->ls_regactual < $this->fun_numregistros() - 1) {
            $this->fun_set_fila($this->ls_regactual + 1);
            return 1;
        }
        return 0;
    }

    public function fun_mueve_anterior() {
        if ($this->ls_regactual > 0) {
            $this->fun_set_fila($this->ls_regactual - 1);
            return 1;
        }
        return 0;
    }

    public function fun_set_fila($li_idconsult) {
        $this->ls_regactual = $li_idconsult;
    }

    /**
     * Imprime una tabla con los datos de la consulta.
     * @param int $li_columnas
     */
    public function fun_datos($li_columnas) {
        if (!$this->li_idconsult) return;
        
        while ($row = pg_fetch_row($this->li_idconsult)) {
            echo "<tr>";
            for ($i = 0; $i < $li_columnas; $i++) {
                $valor = isset($row[$i]) ? htmlspecialchars($row[$i], ENT_QUOTES, 'UTF-8') : '';
                echo "<td class='cont_plain'><div align=\"left\">" . $valor . "</div></td>";
            }
            echo "</tr>";
        }
    }  

    /*-------------------------------------------------------------------------------------------------
        Objetivo: Determinar la paginación de registros en PHP.
    -------------------------------------------------------------------------------------------------*/
    public function fun_tampagina($li_paginas, $li_tam_pag) {
        if (!$li_paginas || !is_numeric($li_paginas)) {
            $li_inicial = 0;
        } else {
            $li_inicial = ($li_paginas - 1) * $li_tam_pag;
        }
        return $li_inicial;
    }  

    /*---------------------------------------------------------------------------------------------------------
        Objetivo: Calcula el número de páginas en las cuales se mostraran los resultados.
    -------------------------------------------------------------------------------------------------------------*/
    public function fun_calcpag($li_tot_reg, $li_tam_pag) {
        if ($li_tam_pag <= 0) return 1;
        return (int) ceil($li_tot_reg / $li_tam_pag);
    }

    /*-------------------------------------------------------------------------------------------------------
        Objetivo: Muestrar los distintos índices de las páginas
    ---------------------------------------------------------------------------------------------------------*/
    public function fun_indexpag($li_totpag, $li_pagina, $ls_nbpagina, $ls_txtcriterio) {
        echo "<tr align='center'>";
        if ($li_totpag > 1) {
            for ($li_i = 1; $li_i <= $li_totpag; $li_i++) {
                if ($li_pagina == $li_i) {
                    echo "<td width='5'>" . (int)$li_pagina . " </td>";
                } else {
                    echo "<td width='5'>";
                    echo "<a href='" . htmlspecialchars($ls_nbpagina, ENT_QUOTES, 'UTF-8') . "?li_pagina=" . $li_i . htmlspecialchars($ls_txtcriterio, ENT_QUOTES, 'UTF-8') . "'>" . $li_i . "</a> ";
                    echo "</td>";
                }
            }
        }
        echo "</tr>";
    }
}


/*------------------------------------------------------------------------------------------------------------------------
                                    FUNCIONES DE UTILIDAD FUERA DE LA CLASE POSTGRES
------------------------------------------------------------------------------------------------------------------------*/

/**
 * @param cls_posgres $objconexion
 * @return resource|\PgSql\Connection|int
 */
function fun_conexion($objconexion) {
    return $objconexion->fun_conectarpg();
}

/**
 * @return cls_posgres
 */
function fun_crear_objeto_conexion() {
    return new cls_posgres();
}

/*------------------------------------------------------------------------------------------------------------------------
    FUNCIÓN fun_cambiaf_a_postgresql: Reemplazada la función obsoleta 'ereg' por 'preg_match'
------------------------------------------------------------------------------------------------------------------------*/
function fun_cambiaf_a_postgresql($fecha) {
    if (!empty($fecha) && preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha, $mifecha)) {
        return $mifecha[3] . "-" . $mifecha[2] . "-" . $mifecha[1];
    }
    return $fecha;
}

/*------------------------------------------------------------------------------------------------------------------------
    FUNCIÓN fun_cambiaf_a_normal: Reemplazada la función obsoleta 'ereg' por 'preg_match'
------------------------------------------------------------------------------------------------------------------------*/
function fun_cambiaf_a_normal($fecha) {
    if (!empty($fecha) && preg_match("/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/", $fecha, $mifecha)) {
        return $mifecha[3] . "/" . $mifecha[2] . "/" . $mifecha[1];
    }
    return $fecha;
}

/*------------------------------------------------------------------------------------------------------------------------
    FUNCIÓN fun_error: Formatea la salida de un Error de Base de Datos
------------------------------------------------------------------------------------------------------------------------*/
function fun_error($li_indice, $li_conect, $ls_cadenasql, $pagina, $linea = '') {
    if ($li_indice == 1) {      
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <link href="../css/style.css" rel="stylesheet" type="text/css">
        <title>Error de Base de Datos</title>
    </head>
    <body class="cont_plain">
        <table width="90%" align="center" style="margin-top: 20px;">
            <tr>
                <td> 
                    <table width="100%" border="0" cellpadding="5">
                        <tr class="error" style="background-color: #ffcccc; color: #cc0000; font-weight: bold;"> 
                            <td>Error en Base de Datos</td>
                        </tr>
                        <tr class="cont_plain"> 
                            <td><strong>PÁGINA:</strong> <?php echo htmlspecialchars($pagina, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <tr class="cont_plain"> 
                            <td><strong>LÍNEA:</strong> <?php echo htmlspecialchars($linea, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <tr class="cont_plain"> 
                            <td><strong>QUERY FALLA:</strong> <pre><?php echo htmlspecialchars($ls_cadenasql, ENT_QUOTES, 'UTF-8'); ?></pre></td>
                        </tr>
                    </table>    
                </td>
            </tr>
        </table>    
    </body>
    </html>
    <?php 
    }
    exit; 
}
?>