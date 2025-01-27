-- View: public.vm02_edo_cuenta

-- DROP MATERIALIZED VIEW IF EXISTS public.vm02_edo_cuenta;

CREATE MATERIALIZED VIEW IF NOT EXISTS public.vm02_edo_cuenta
TABLESPACE pg_default
AS
 SELECT t20_factura.pk_factura,
    t01_detalle.nu_referencia AS ref,
    to_char(t01_detalle.fe_fecha_registro::timestamp with time zone, 'yyyy-mm-dd'::text)::date AS fecha,
    t01_detalle.tx_observacion,
    t20_factura.tx_tipo,
        CASE
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN t20_factura.tx_concepto::text
            ELSE upper((s01_persona.tx_nombre::text || ' '::text) || s01_persona.tx_apellido::text)
        END AS cliente,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN 'CREDITO'::text
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN 'PRESTAMO'::text
            WHEN t20_factura.tx_tipo::text = 'NOMINA'::text THEN 'NOMINA'::text
            WHEN t20_factura.tx_tipo::text = 'INVERSION'::text THEN 'INVERSION'::text
            ELSE 'NA'::text
        END AS operacion,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text OR t20_factura.tx_tipo::text = 'INVERSION'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0::numeric
        END AS ingreso,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text OR t20_factura.tx_tipo::text = 'NOMINA'::text THEN t01_detalle.nu_cantidad * t01_detalle.nu_precio
            ELSE 0::numeric
        END AS egreso,
    f_cuenta(t01_detalle.nu_referencia, to_char(t01_detalle.fe_fecha_registro::timestamp with time zone, 'yyyy-mm-dd'::text)::date) AS f_cuenta
   FROM t01_detalle
     LEFT JOIN t20_factura ON t20_factura.pk_factura = t01_detalle.fk_factura
     LEFT JOIN s01_persona ON t20_factura.fk_cliente = s01_persona.co_persona
     LEFT JOIN t03_proveedor ON t20_factura.fk_proveedor = t03_proveedor.pk_proveedor
  WHERE t20_factura.tx_tipo::text <> 'VENTA'::text AND t20_factura.tx_tipo::text <> 'GASTO'::text
UNION ALL
 SELECT t20_factura.pk_factura,
    t04_abono.nu_referencia AS ref,
    to_char(t04_abono.fe_fecha::timestamp with time zone, 'yyyy-mm-dd'::text)::date AS fecha,
    t04_abono.tx_observacion,
    t20_factura.tx_tipo,
        CASE
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN t20_factura.tx_concepto::text
            ELSE upper((s01_persona.tx_nombre::text || ' '::text) || s01_persona.tx_apellido::text)
        END AS cliente,
        CASE
            WHEN t20_factura.tx_tipo::text = 'VENTA'::text THEN 'VENTA'::text
            WHEN t20_factura.tx_tipo::text = 'GASTO'::text THEN 'GASTO'::text
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text THEN 'PAGO CRED.'::text
            WHEN t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN 'COBR PREST.'::text
            WHEN t20_factura.tx_tipo::text = 'INVERSION'::text THEN 'INVERSION'::text
            ELSE 'NA'::text
        END AS operacion,
        CASE
            WHEN t20_factura.tx_tipo::text = 'VENTA'::text OR t20_factura.tx_tipo::text = 'CTAXCOBRAR'::text THEN t04_abono.nu_monto + t04_abono.nu_interes
            ELSE 0::numeric
        END AS ingreso,
        CASE
            WHEN t20_factura.tx_tipo::text = 'CTAXPAGAR'::text OR t20_factura.tx_tipo::text = 'INVERSION'::text OR t20_factura.tx_tipo::text = 'GASTO'::text THEN t04_abono.nu_monto + t04_abono.nu_interes
            ELSE 0::numeric
        END AS egreso,
    f_cuenta(t04_abono.nu_referencia, to_char(t04_abono.fe_fecha::timestamp with time zone, 'yyyy-mm-dd'::text)::date) AS f_cuenta
   FROM t04_abono
     LEFT JOIN t20_factura ON t20_factura.pk_factura = t04_abono.fk_factura
     LEFT JOIN s01_persona ON t20_factura.fk_cliente = s01_persona.co_persona
     LEFT JOIN t03_proveedor ON t20_factura.fk_proveedor = t03_proveedor.pk_proveedor
  WHERE t20_factura.in_pedido::text = 'N'::text
  ORDER BY 3 DESC, 2 DESC
WITH DATA;

ALTER TABLE IF EXISTS public.vm02_edo_cuenta
    OWNER TO postgres;