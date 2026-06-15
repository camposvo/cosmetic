 SELECT t04_abono.nu_referencia,
    to_char(t04_abono.fe_fecha::timestamp with time zone, 'dd-TMMon-yyyy'::text) AS fecha,
    nu_interes,
    t04_abono.nu_monto AS monto_a,
    0 AS monto_b,
    upper(s01_persona.tx_nombre::text) AS nombre,
    t04_abono.tx_observacion,
    t04_abono.pk_abono AS id,
    t04_abono.fk_factura,
    'A'::text AS tipo
   FROM t04_abono
     JOIN s01_persona ON s01_persona.co_persona = t04_abono.fk_indicador
UNION
 SELECT t01_detalle.nu_referencia,
    to_char(t01_detalle.fe_fecha_registro, 'dd-TMMon-yyyy'::text) AS fecha,
    0,
    0 AS monto_a,
    t01_detalle.nu_precio AS monto_b,
    upper(s01_persona.tx_nombre::text) AS nombre,
    t01_detalle.tx_observacion,
    t01_detalle.pk_detalle AS id,
    t01_detalle.fk_factura,
    'B'::text AS tipo
   FROM t01_detalle
     JOIN s01_persona ON s01_persona.co_persona = t01_detalle.fk_responsable
  ORDER BY 1 DESC;