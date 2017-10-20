DROP INDEX IndexCapitalSocial ON pessoac;

CREATE INDEX IndexCapitalSocial
	ON pessoac(capitalsocial)
	USING BTREE;