# 7)
######################## DATA WAREHOUSE ##########################
/* Create Data Warehouse.*/

/* FACT'S TABLES */
DROP TABLE IF EXISTS max_lance;
CREATE TABLE max_lance(
    date_id         INT(11) NOT NULL,
    space_id        VARCHAR(80) NOT NULL,
    maximum_lance   INT(11) NOT NULL
);

/* DIMENSIONS TABLES */
DROP TABLE IF EXISTS date_dimension;
CREATE TABLE date_dimension(
    date_id         INT(11) NOT NULL UNIQUE,
    date_year       INT(11) NOT NULL,
    date_month_number       INT(11) NOT NULL,
    date_month_name VARCHAR(80) NOT NULL,
    date_month_day  INT(11) NOT NULL
);

DROP TABLE IF EXISTS space_dimension;
CREATE TABLE space_dimension(
    space_id        VARCHAR(80) NOT NULL UNIQUE,
    region          VARCHAR(80) NOT NULL,   #Nutt III Region!!!!
    minor_district  VARCHAR(80) NOT NULL    #"Concelho"equivalent!!!!!
);

###################### LOAD DATA WAREHOUSE ########################

/* Data Warehouse Load/Refresh Queries */
TRUNCATE date_dimension;
INSERT INTO date_dimension
SELECT DISTINCT DATE_FORMAT(dia,"%Y%m%d"),DATE_FORMAT(dia,"%Y"),DATE_FORMAT(dia,"%m"),DATE_FORMAT(dia,"%M"),DATE_FORMAT(dia,"%d")
FROM leilao
WHERE dia IS NOT NULL;

TRUNCATE space_dimension;
INSERT INTO space_dimension
SELECT DISTINCT CONCAT(concelho,regiao), regiao, concelho
FROM leiloeira
WHERE regiao IS NOT NULL AND concelho IS NOT NULL;

TRUNCATE max_lance;
INSERT INTO max_lance 
SELECT DATE_FORMAT(dia,"%Y%m%d"), CONCAT(concelho,regiao), MAX(valor) 
FROM leilao NATURAL JOIN leilaor NATURAL JOIN leiloeira ,concorrente NATURAL JOIN lance
WHERE leilao = lid AND dia IS NOT NULL AND concelho IS NOT NULL AND regiao IS NOT NULL
GROUP BY leilao;


# 8)
########################   QUERY  #################################


SELECT  minor_district,date_year,date_month_name, SUM(maximum_lance) AS Total
FROM max_lance NATURAL JOIN space_dimension NATURAL JOIN date_dimension
WHERE date_year = 2012 OR date_year = 2013
GROUP BY minor_district, date_year, date_month_name WITH ROLLUP;


####################### END QUERY #################################