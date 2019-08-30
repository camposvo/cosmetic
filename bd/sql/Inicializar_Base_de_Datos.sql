-- Limpia la tabla ABONO
DELETE FROM t04_abono;
ALTER SEQUENCE t04s_pk_abono restart 1;




-- Limpia la tabla MOVIMIENTO 

DELETE FROM t01_movimiento; 
ALTER SEQUENCE t01s_pk_movimiento restart 1;
ALTER SEQUENCE sec_referencia restart 1;


-- Limpia la tabla PROVEEDORES 
DELETE FROM t03_proveedor; 
ALTER SEQUENCE t03s_pk_proveedor restart 1;



-- Limpia la tabla CLASE 
DELETE FROM t05_clase; 
ALTER SEQUENCE t05s_pk_clase restart 1;


-- Limpia la tabla CLASE 
DELETE FROM t11_bitacora; 
ALTER SEQUENCE t11s_co_bitacora restart 1;



-- Limpia la tabla EVENTO
DELETE FROM t18_evento;
ALTER SEQUENCE t18s_pk_evento restart 1;

-- Limpia la tabla PROYECTO
DELETE FROM t02_proyecto;
ALTER SEQUENCE t02s_pk_rubro restart 1; 

-- Limpia la tabla TIPO RUBRO O PROYECTO 
DELETE FROM t08_tipo_rubro; 
ALTER SEQUENCE t08s_pk_tipo_rubro restart 1;




-- Limpia la tabla TIPO CLASE DE GASTO
DELETE FROM t13_tipo_clase; 
ALTER SEQUENCE t13s_pk_tipo_clase restart 1;


 




-- Limpia la tabla MARCA
DELETE FROM t07_marca;
ALTER SEQUENCE t07s_pk_marca restart 1;


-- Limpia la tabla UBICACIONES DE ALMACEN
DELETE FROM t10_ubicacion;
ALTER SEQUENCE t10s_pk_ubicacion restart 1;

-- Limpia la tabla ALMACEN
DELETE FROM t09_almacen;
ALTER SEQUENCE t09s_pk_almacen restart 1;



-- Limpia la tabla PERMISO
DELETE FROM t06_permiso;
ALTER SEQUENCE t06s_pk_permiso restart 1;

-- Limpia la tabla CONTRATO
DELETE FROM t12_contrato;
ALTER SEQUENCE t12s_pk_contrato restart 1;

-- Limpia la tabla BANCO
DELETE FROM t15_banco;
ALTER SEQUENCE t15s_pk_banco restart 1;


-- Limpia la tabla MENSAJE PERSONA
DELETE FROM t17_mensaje_persona;
ALTER SEQUENCE t17s_pk_mensaje_persona restart 1;

-- Limpia la tabla MENSAJE PERSONA
DELETE FROM t16_mensaje;
ALTER SEQUENCE t16s_pk_mensaje restart 1;


