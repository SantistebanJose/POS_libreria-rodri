--CREDENCIALES
-- BD: bd_vysam
-- Puerto : 5432
-- Usuario: postgres
-- contraseña: VYSAM032025


-- CAMBIOS 

ALTER TABLE movimiento ADD COLUMN medidas VARCHAR[];

UPDATE movimiento set medidas = '{"A0","A1","A2","A3","A4","A5","A6"}';


insert into movimiento
(descripcion,medidas)
values
('ANILLADOS','{"A0","A1","A2","A3","A4","A5","A6"}');

insert into movimiento
(descripcion,medidas)
values
('COPIAS','{"A0","A1","A2","A3","A4","A5","A6"}');

insert into movimiento
(descripcion,medidas)
values
('SOLO CORTE','{"A0","A1","A2","A3","A4","A5","A6"}');





CREATE OR REPLACE FUNCTION actualizar_saldo_caja_grande_forma_pago() 
RETURNS TRIGGER AS $$
BEGIN
	CASE 
		WHEN NEW.tipo_movimiento = 'EGRESO'  AND NEW.movimiento_caja_v2 = 'EGRESO DE CAJA' THEN
			UPDATE forma_pago 
			SET 
				monto = COALESCE(monto,0) - NEW.monto
			WHERE id = NEW.forma_pago_id;
		WHEN NEW.tipo_movimiento = 'INGRESO' AND NEW.movimiento_caja_v2 = 'INGRESO DE CAJA' THEN
			UPDATE forma_pago 
			SET 
			monto = COALESCE(monto,0) + NEW.monto
			WHERE id = NEW.forma_pago_id;
		ELSE
		
		--INGRESO DE CAJA
			--UPDATE forma_pago 
			--SET 
			--	monto = COALESCE(monto,0) + NEW.monto
			--WHERE id = NEW.forma_pago_id;
	END CASE;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
---------------------

DROP VIEW view_articulos;


CREATE OR REPLACE VIEW view_articulos AS
SELECT a.id,
a.nombre AS articulo,
concat(a.nombre, ' ', a.marca, ' ', a.color) AS articulo_color_marca,
c.abreviatura AS categoria,
	CASE
		WHEN length(t.abreviatura::text) > 0 THEN t.abreviatura
		ELSE '-'::character varying
	END AS tipo,
	CASE
		WHEN length(d.medida::text) > 0 THEN d.medida
		ELSE '-'::character varying
	END AS dimension,
	CASE
		WHEN length(e.abreviatura::text) > 0 THEN e.abreviatura
		ELSE '-'::character varying
	END AS escala,
a.stock,
a.precio_venta,
a.corte,
CASE 
	WHEN a.color IS NULL THEN
		'SIN COLOR'
	ELSE
		a.color
END color
FROM articulo a
 JOIN categoria c ON a.categoria_id = c.id
 LEFT JOIN tipo t ON a.tipo_id = t.id
 LEFT JOIN dimension d ON a.dimension_id = d.id
 LEFT JOIN escala e ON a.escala_id = e.id
WHERE a.deleted_at IS NULL AND a.disponibilidad_venta_fh IS NULL
ORDER BY a.precio_venta DESC;

--modalSoloCorteMaquina2

--DELETE FROM movimiento where id = 11;
INSERT INTO movimiento
(id,descripcion)
VALUES
(15,'IMPRESION MAQUINA 3D');


VACUUM FULL;
REINDEX DATABASE bd_vysam;