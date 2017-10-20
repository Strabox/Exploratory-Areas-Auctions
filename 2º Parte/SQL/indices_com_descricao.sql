-- INDICES

-- 3 - Qual o leilão com o maior rácio (valor do melhor lance)/(valor base)?
-- SELECT dia, nrleilaonodia, nif, nome, valor/valorbase
-- FROM lance L, leilaor Lr NATURAL JOIN leilao
-- WHERE L.leilao = Lr.lid
-- AND valor/valorbase >= ALL (
--	SELECT valor/valorbase AS racio
--	FROM lance L2, leilaor Lr2 NATURAL JOIN leilao
--	WHERE L2.leilao = Lr2.lid );


+----+-------------+--------+--------+---------------+---------+---------+-----------------------------------------------------------------+------+-------------+
| id | select_type | table  | type   | possible_keys | key     | key_len | ref                                                             | rows | Extra       |
+----+-------------+--------+--------+---------------+---------+---------+-----------------------------------------------------------------+------+-------------+
|  1 | PRIMARY     | L      | index  | NULL          | PRIMARY | 12      | NULL                                                            |    3 | Using index |
|  1 | PRIMARY     | Lr     | eq_ref | PRIMARY,lid   | lid     | 4       | ist169537.L.leilao                                              |    1 | Using index |
|  1 | PRIMARY     | leilao | eq_ref | PRIMARY       | PRIMARY | 11      | ist169537.Lr.nif,ist169537.Lr.dia,ist169537.Lr.nrleilaonodia    |    1 | Using where |
|  2 | SUBQUERY    | L2     | index  | NULL          | PRIMARY | 12      | NULL                                                            |    3 | Using index |
|  2 | SUBQUERY    | Lr2    | eq_ref | PRIMARY,lid   | lid     | 4       | ist169537.L2.leilao                                             |    1 | Using index |
|  2 | SUBQUERY    | leilao | eq_ref | PRIMARY       | PRIMARY | 11      | ist169537.Lr2.nif,ist169537.Lr2.dia,ist169537.Lr2.nrleilaonodia |    1 |             |
+----+-------------+--------+--------+---------------+---------+---------+-----------------------------------------------------------------+------+-------------+
-- sem indices adicionados: entre 0.00180700 e 0.00237200 segundos



-- 4 - Quais as pessoas coletivas com o mesmo capital social?

-- ##############
-- versão 1: não emparelha, mais legível
-- ##############

-- select nif, nome, capitalsocial
-- from pessoa natural join pessoac 
-- where capitalsocial in (
-- 	select capitalsocial
-- 	from pessoac
-- 	group by capitalsocial
-- 	having count(*) >1)
-- order by capitalsocial;

+----+--------------------+---------+--------+---------------+---------+---------+----------------------+------+---------------------------------+
| id | select_type        | table   | type   | possible_keys | key     | key_len | ref                  | rows | Extra                           |
+----+--------------------+---------+--------+---------------+---------+---------+----------------------+------+---------------------------------+
|  1 | PRIMARY            | pessoa  | ALL    | PRIMARY       | NULL    | NULL    | NULL                 |  273 | Using temporary; Using filesort |
|  1 | PRIMARY            | pessoac | eq_ref | PRIMARY       | PRIMARY | 4       | ist169537.pessoa.nif |    1 | Using where                     |
|  2 | DEPENDENT SUBQUERY | pessoac | ALL    | NULL          | NULL    | NULL    | NULL                 |  274 | Using temporary; Using filesort |
+----+--------------------+---------+--------+---------------+---------+---------+----------------------+------+---------------------------------+

-- Antes do índice: 0.19708200 segundos

-- Índice:
CREATE INDEX IndexCapitalSocial
	ON pessoac(capitalsocial)
	USING BTREE;

-- Depois do índice: 0.04165000 segundos

+----+--------------------+---------+--------+---------------+--------------------+---------+-----------------------+------+--------------------------+
| id | select_type        | table   | type   | possible_keys | key                | key_len | ref                   | rows | Extra                    |
+----+--------------------+---------+--------+---------------+--------------------+---------+-----------------------+------+--------------------------+
|  1 | PRIMARY            | pessoac | index  | PRIMARY       | IndexCapitalSocial | 4       | NULL                  |  274 | Using where; Using index |
|  1 | PRIMARY            | pessoa  | eq_ref | PRIMARY       | PRIMARY            | 4       | ist169537.pessoac.nif |    1 |                          |
|  2 | DEPENDENT SUBQUERY | pessoac | index  | NULL          | IndexCapitalSocial | 4       | NULL                  |    1 | Using index              |
+----+--------------------+---------+--------+---------------+--------------------+---------+-----------------------+------+--------------------------+


-- ##############
-- versão 2: emparelha, quase ilegível
-- ##############

-- SELECT PC1.nif, PC2.nif, PC1.capitalsocial
-- FROM pessoac PC1, pessoac PC2
-- WHERE PC1.nif < PC2.nif
-- AND PC1.capitalsocial = PC2.capitalsocial
-- ORDER BY PC1.capitalsocial;

+----+-------------+-------+------+---------------+------+---------+------+------+---------------------------------+
| id | select_type | table | type | possible_keys | key  | key_len | ref  | rows | Extra                           |
+----+-------------+-------+------+---------------+------+---------+------+------+---------------------------------+
|  1 | SIMPLE      | PC1   | ALL  | PRIMARY       | NULL | NULL    | NULL |  274 | Using temporary; Using filesort |
|  1 | SIMPLE      | PC2   | ALL  | PRIMARY       | NULL | NULL    | NULL |  274 | Using where; Using join buffer  |
+----+-------------+-------+------+---------------+------+---------+------+------+---------------------------------+

-- Antes do índice: 0.01766500

-- Índice:
CREATE INDEX IndexCapitalSocial
	ON pessoac(capitalsocial)
	USING BTREE;
	
-- Depois do índice : 0.00515500

+----+-------------+-------+-------+----------------------------+--------------------+---------+-----------------------------+------+--------------------------+
| id | select_type | table | type  | possible_keys              | key                | key_len | ref                         | rows | Extra                    |
+----+-------------+-------+-------+----------------------------+--------------------+---------+-----------------------------+------+--------------------------+
|  1 | SIMPLE      | PC1   | index | PRIMARY,IndexCapitalSocial | IndexCapitalSocial | 4       | NULL                        |  274 | Using index              |
|  1 | SIMPLE      | PC2   | ref   | PRIMARY,IndexCapitalSocial | IndexCapitalSocial | 4       | ist169537.PC1.capitalsocial |    1 | Using where; Using index |
+----+-------------+-------+-------+----------------------------+--------------------+---------+-----------------------------+------+--------------------------+