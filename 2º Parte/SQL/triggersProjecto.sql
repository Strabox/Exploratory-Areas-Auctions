# TRIGGERS PROJECTO

DROP TRIGGER IF EXISTS lance_trigger;
DELIMITER //
CREATE TRIGGER lance_trigger BEFORE INSERT ON lance
FOR EACH ROW
BEGIN
	DECLARE aux INTEGER DEFAULT 0;
	
    SELECT valorbase into aux
    FROM leilaor lr, leilao l
    WHERE lr.dia = l.dia and lr.nrleilaonodia = l.nrleilaonodia and lr.nif = l.nif and lr.lid = NEW.leilao;
    
	IF(NEW.valor < aux) THEN
		CALL erro();
	END IF;
    
    SET aux = 0;
    SELECT COUNT(*) INTO aux
    FROM lance l
	WHERE l.leilao = NEW.leilao AND l.valor >= NEW.valor;
	
    IF(aux > 0) THEN
		CALL erro();
	END IF;
END //
DELIMITER ;
