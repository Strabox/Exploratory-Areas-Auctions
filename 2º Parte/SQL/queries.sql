-- SQL

-- 1 - Quais os participantes inscritos em leilões mas sem lances até à data? 
SELECT pessoa
FROM concorrente
WHERE pessoa NOT IN (
	SELECT pessoa
	FROM lance
);


-- 2 - Qual o nome das pessoas coletivas com exatamente duas inscrições em leilões?
SELECT nome
FROM concorrente C, pessoac PC NATURAL JOIN pessoa
WHERE C.pessoa = PC.nif
GROUP BY pessoa
HAVING COUNT(DISTINCT leilao) = 2;


-- 3 - Qual o leilão com o maior rácio (valor do melhor lance)/(valor base)?
SELECT dia, nrleilaonodia, nif, nome, valor/valorbase
FROM lance L, leilaor Lr NATURAL JOIN leilao
WHERE L.leilao = Lr.lid
AND valor/valorbase >= ALL (
	SELECT valor/valorbase AS racio
	FROM lance L2, leilaor Lr2 NATURAL JOIN leilao
	WHERE L2.leilao = Lr2.lid );


-- 4 - Quais AS pessoas coletivas com o mesmo capital social?
-- funciona mas nao emparelha, mas menos confuso
SELECT nif, nome, capitalsocial
FROM pessoa NATURAL JOIN pessoac 
WHERE capitalsocial in (
	SELECT capitalsocial
	FROM pessoac
	GROUP BY capitalsocial
	HAVING COUNT(*) >1)
ORDER BY capitalsocial;

-- funciona e emparelha, mas mais confuso
SELECT PC1.nif, PC2.nif, PC1.capitalsocial
FROM pessoac PC1, pessoac PC2
WHERE PC1.nif < PC2.nif
AND PC1.capitalsocial = PC2.capitalsocial
ORDER BY PC1.capitalsocial;