-------------------------------------------------------
-- SCRIPT PARA INICIALIZAR LA BASE DE DATOS DEL SISTEMA
-------------------------------------------------------

-- Deshabilitar Trigger
ALTER TABLE t01_detalle DISABLE TRIGGER ALL;
ALTER TABLE t20_factura DISABLE TRIGGER ALL;
ALTER TABLE t16_mensaje DISABLE TRIGGER ALL;
ALTER TABLE t04_abono   DISABLE TRIGGER ALL;


------------------------------------
-- BORRAR EL CONTENIDO DE LAS TABLAS
------------------------------------
truncate t16_mensaje,
	t14_ruta_archivo,
	t17_mensaje_persona,
	t18_evento,
	t04_abono, 
	t01_detalle,
	t20_factura, 
	t03_proveedor, 
	t12_contrato, 
	t06_permiso, 
	t05_clase, 
	t13_articulo,
	t19_galeria, 
	t08_tipo_proyecto, 
	t02_proyecto,
	t15_banco, 
	t11_bitacora, 
	t10_ubicacion, 
	t09_almacen,
	s02_persona_rol, 
	s01_persona,
	t21_categoria,
	t22_nomina,
	gan_sector,
	gan_raza,
	gan_potrero,
	gan_peso,
	gan_lote,
	gan_grupo_etareo,
	gan_movimiento,
	gan_animal_vacuna,
	gan_imagen,
	gan_leche,
	gan_palpacion,
	gan_partos,
	gan_secar_animal,
	gan_ganado,
	gan_diagnostico;
	

--------------------------
-- INICIALIZAR SECUENCIAS
--------------------------
ALTER SEQUENCE t18s_pk_evento RESTART WITH 1;
ALTER SEQUENCE t17s_pk_mensaje_persona RESTART WITH 1;
ALTER SEQUENCE t14s_pk_ruta_archivo RESTART WITH 1;
ALTER SEQUENCE t16s_pk_mensaje RESTART WITH 1;

ALTER SEQUENCE t04s_pk_abono RESTART WITH 1;
ALTER SEQUENCE t01s_pk_detalle RESTART WITH 1;
ALTER SEQUENCE t20s_pk_factura RESTART WITH 1;

ALTER SEQUENCE t03s_pk_proveedor RESTART WITH 1;

ALTER SEQUENCE t06s_pk_permiso RESTART WITH 1;
ALTER SEQUENCE t12s_pk_contrato RESTART WITH 1;

ALTER SEQUENCE t05s_pk_clase RESTART WITH 1;
ALTER SEQUENCE t13s_pk_articulo RESTART WITH 1;

ALTER SEQUENCE t19s_pk_galeria RESTART WITH 1;

ALTER SEQUENCE t08s_pk_tipo_proyecto RESTART WITH 1;
ALTER SEQUENCE t02s_pk_proyecto RESTART WITH 1;

ALTER SEQUENCE t15s_pk_banco RESTART WITH 1;
ALTER SEQUENCE t11s_co_bitacora RESTART WITH 1;


ALTER SEQUENCE t10s_pk_ubicacion RESTART WITH 1;
ALTER SEQUENCE t09s_pk_almacen RESTART WITH 1;

ALTER SEQUENCE t21s_pk_categoria RESTART WITH 1; 
ALTER SEQUENCE t22s_pk_nomina RESTART WITH 1; 

ALTER SEQUENCE s02s_co_persona_rol RESTART WITH 1;
ALTER SEQUENCE s01s_co_persona RESTART WITH 1;

ALTER SEQUENCE sec_factura RESTART WITH 1;

ALTER SEQUENCE sec_foto RESTART WITH 1;
ALTER SEQUENCE sec_img RESTART WITH 1;
ALTER SEQUENCE sec_referencia RESTART WITH 1; 

ALTER SEQUENCE gas_pk_ganado RESTART WITH 1; 
ALTER SEQUENCE gas_pk_grupo_etareo RESTART WITH 1; 
ALTER SEQUENCE gas_pk_lote RESTART WITH 1; 
ALTER SEQUENCE gas_pk_potrero RESTART WITH 1; 
ALTER SEQUENCE gas_pk_raza RESTART WITH 1; 
ALTER SEQUENCE gas_pk_sector RESTART WITH 1; 
ALTER SEQUENCE gas_pk_imagen RESTART WITH 1; 
ALTER SEQUENCE gas_pk_diagnostico RESTART WITH 1;
ALTER SEQUENCE gas_pk_leche RESTART WITH 1;
ALTER SEQUENCE gas_pk_movimiento RESTART WITH 1;
ALTER SEQUENCE gas_pk_palpacion RESTART WITH 1;
ALTER SEQUENCE gas_pk_partos RESTART WITH 1;
ALTER SEQUENCE gas_pk_peso RESTART WITH 1;
ALTER SEQUENCE gas_pk_secar_animal RESTART WITH 1;
ALTER SEQUENCE gas_pk_vacuna RESTART WITH 1;
ALTER SEQUENCE gas_pk_animal_vacuna RESTART WITH 1;

-- habilitar Trigger
ALTER TABLE t01_detalle ENABLE TRIGGER ALL;
ALTER TABLE t20_factura ENABLE TRIGGER ALL;
ALTER TABLE t16_mensaje ENABLE TRIGGER ALL;
ALTER TABLE t04_abono ENABLE TRIGGER ALL;

------------------------------------------
-- CREAR CUENTA ADMINISTRADORA POR DEFECTO
------------------------------------------
INSERT INTO s01_persona(
            tx_nombre, 
            tx_apellido, 
            tx_indicador, in_activo,  
            co_password,
            tx_cedula
            )
    VALUES ('Administrador','','Admin','S', md5('Admin'),'1');

INSERT INTO s02_persona_rol(co_persona ,co_rol)
	 VALUES(1,22);


-----------------------------------
-- CREAR GRUPOS ETAREOS POR DEFECTO
-----------------------------------
INSERT INTO gan_grupo_etareo(
            pk_grupo_etareo, 
            nb_grupo_1, tx_descripcion_grupo_1, nu_edad_ini_1, nu_edad_fin_1,
            nb_grupo_2, tx_descripcion_grupo_2, nu_edad_ini_2, nu_edad_fin_2,
            nb_grupo_3, tx_descripcion_grupo_3, nu_edad_ini_3, nu_edad_fin_3,  
            nb_grupo_4, tx_descripcion_grupo_4, nu_edad_ini_4, nu_edad_fin_4,    
            nb_grupo_5, tx_descripcion_grupo_5, nu_edad_ini_5, nu_edad_fin_5 )
    VALUES (1, 
	   'BECERRO', '', 0, 240, 
	   'MAUTES DESTETE', '', 240, 365,	
	   'MAUTES LEVANTE', '', 365, 600,
           'MAUTES CEBA', '', 600, 1080,
           'TORO', '', 1080, 16000
            );


INSERT INTO gan_grupo_etareo(
            pk_grupo_etareo, 
            nb_grupo_1, tx_descripcion_grupo_1, nu_edad_ini_1, nu_edad_fin_1,
            nb_grupo_2, tx_descripcion_grupo_2, nu_edad_ini_2, nu_edad_fin_2,
            nb_grupo_3, tx_descripcion_grupo_3, nu_edad_ini_3, nu_edad_fin_3,  
            nb_grupo_4, tx_descripcion_grupo_4, nu_edad_ini_4, nu_edad_fin_4,    
            nb_grupo_5, tx_descripcion_grupo_5, nu_edad_ini_5, nu_edad_fin_5 )
    VALUES (2, 
	   'BECERRA', '', 0, 240, 
	   'NOVILLAS DESTETE', '', 240, 365,	
	   'NOVILLAS LEVANTE', '', 365, 600,
           'NOVILLAS CEBA', '', 600, 1080,
           'VACA', '', 1080, 16000
            );

---------------------------------------
-- CREAR TIPO DE PROYECTO (OBLIGATORIO)
---------------------------------------
INSERT INTO t08_tipo_proyecto(
            pk_tipo_rubro, 
            nb_tipo_rubro, 
            tx_descripcion )
    VALUES (1, 
	    'NOMINA', 
            'TIPO POR DEFECTO QUE AGRUPA LA NOMINA');

INSERT INTO t02_proyecto(
            pk_proyecto, tx_nombre, fe_inicial, 
            fe_final, tx_descripcion, fk_responsable, 
            fk_tipo_rubro, in_proy_activo,  tx_categoria_proyecto)
    VALUES (1, 'NOMINA', '01-01-2000', 
	   '01-01-2040', 'AGRUPA TODOS LOS GASTOS POR NOMINA', 1, 
	    1,'S', 'GASTO');


-------------------------------------
-- REFRESCA LAS VISTAS MATERIALIZADAS
-------------------------------------
REFRESH MATERIALIZED VIEW vm02_edo_cuenta;
UPDATE s07_variable SET in_vista=1;





