

DROP DATABASE taged_collection;

CREATE DATABASE taged_collection;

\c taged_collection;

CREATE TABLE combat
(
   Id_Combat SERIAL,
   Resultat VARCHAR(50),
   Tier INT,
   Tours INT,
   Rules VARCHAR(250),
   Classe VARCHAR(50),
   Gagnant INT, 
   PRIMARY KEY(Id_Combat)
);

CREATE TABLE pokemon(
   Nom VARCHAR(50),
   Generation SMALLINT,
   Rarete VARCHAR(50),
   Drop_Rate DECIMAL(9,8),
   PRIMARY KEY(Nom)
);

CREATE TABLE utilisateur(
   Nom VARCHAR(50),
   Avatar VARCHAR(50),
   ELO INT DEFAULT 1000,
   PRIMARY KEY(Nom)
);

CREATE TABLE equipe(
   Id_Equipe SERIAL,
   Nombre INT,
   Liste VARCHAR(500),
   Liste2 VARCHAR(500),
   Liste3 VARCHAR(500),
   Liste4 VARCHAR(500),
   Liste5 VARCHAR(500),
   Drop_Rate DECIMAL(9,8),
   Drop_Rate2 DECIMAL(9,8),
   Drop_Rate3 DECIMAL(9,8),
   Drop_Rate4 DECIMAL(9,8),
   Drop_Rate5 DECIMAL(9,8),
   PRIMARY KEY(Id_Equipe)
);

CREATE TABLE engage
(
   Id_Combat INT,
   Nom VARCHAR(50),
   Id_Equipe INT,
   ELO INT,
   Numero INT,
   Victoire INT,
   PRIMARY KEY(Id_Combat, Nom, Id_Equipe),
   FOREIGN KEY(Id_Combat) REFERENCES combat(Id_Combat),
   FOREIGN KEY(Nom) REFERENCES utilisateur(Nom),
   FOREIGN KEY(Id_Equipe) REFERENCES equipe(Id_Equipe)
);

CREATE TABLE aligne(
   Id_aligne SERIAL,
   Ordre INT NOT NULL,
   Nom VARCHAR(50) NOT NULL,
   Id_Equipe INT NOT NULL,
   PRIMARY KEY(Id_aligne),
   FOREIGN KEY(Nom) REFERENCES Pokemon(Nom),
   FOREIGN KEY(Id_Equipe) REFERENCES Equipe(Id_Equipe)
);

CREATE VIEW vw_equipe AS SELECT C.id_combat, resultat, tier, rules, classe, tours, U.nom, numero, victoire, Q.id_equipe, nombre, liste3 as liste, drop_rate3 as drop_rate
	FROM engage E
	JOIN combat C ON E.Id_Combat = C.Id_Combat
	JOIN utilisateur U on E.Nom = U.Nom
	JOIN equipe Q on E.Id_Equipe = Q.Id_Equipe;

CREATE VIEW vw_combat AS SELECT C.id_combat, resultat, tier, rules, classe, 
U1.nom as Nom1, E1.elo as elo1, Q1.id_equipe as Eq1, Q1.Liste as Liste1,
U2.nom as Nom2, E2.elo as elo2, Q2.id_equipe as Eq2, Q2.Liste as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN equipe Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN equipe Q2 on E2.Id_Equipe = Q2.Id_Equipe;



CREATE VIEW vw_equipeS AS SELECT    Id_Equipe, Nombre,  
regexp_matches (liste, E'^(\\w+,\\w+).*$') as Liste2,
regexp_matches (liste, E'^(\\w+,\\w+,\\w+).*$') as Liste3,
regexp_matches (liste, E'^(\\w+,\\w+,\\w+,\\w+).*$') as Liste4,
regexp_matches (liste, E'^(\\w+,\\w+,\\w+,\\w+,\\w+).*$') as Liste5,
regexp_matches (liste, E'^(\\w+,\\w+,\\w+,\\w+,\\w+,\\w+).*$') as Liste6
 FROM equipe;

CREATE VIEW vw_combat_12 AS 
SELECT C.id_combat, gagnant, tier, rules, classe, tours, 
U1.nom as Nom1, E1.elo as elo1, Q1.id_equipe as Eq1, Q1.Liste2 as Liste1,
U2.nom as Nom2, E2.elo as elo2, Q2.id_equipe as Eq2, Q2.Liste2 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe
UNION
SELECT C.id_combat, CASE WHEN (resultat = '1') THEN 2 WHEN (resultat = '2') THEN 1 ELSE 0 END as gagnant, tier, rules, classe, tours, 
U2.nom as Nom1, E2.elo as elo1, Q2.id_equipe as Eq1, Q2.Liste2 as Liste1,
U1.nom as Nom2, E1.elo as elo2, Q1.id_equipe as Eq2, Q1.Liste2 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe;

CREATE VIEW vw_combat_L3 AS 
SELECT C.id_combat, gagnant, tier, rules, classe, tours, 
U1.nom as Nom1, E1.elo as elo1, Q1.id_equipe as Eq1, Q1.Liste3 as Liste1, Q1.Drop_Rate3 as Drop_Rate1,
U2.nom as Nom2, E2.elo as elo2, Q2.id_equipe as Eq2, Q2.Liste3 as Liste2, Q1.Drop_Rate3 as Drop_Rate2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN equipe Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN equipe Q2 on E2.Id_Equipe = Q2.Id_Equipe
UNION
SELECT C.id_combat, CASE WHEN (resultat = '1') THEN 2 WHEN (resultat = '2') THEN 1 ELSE 0 END as gagnant, tier, rules, classe, tours, 
U2.nom as Nom1, E2.elo as elo1, Q2.id_equipe as Eq1, Q1.Liste3 as Liste1, Q1.Drop_Rate3 as Drop_Rate1,
U1.nom as Nom2, E1.elo as elo2, Q1.id_equipe as Eq2, Q2.Liste3 as Liste2, Q1.Drop_Rate3 as Drop_Rate2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN equipe Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN equipe Q2 on E2.Id_Equipe = Q2.Id_Equipe;

CREATE VIEW vw_combat_L2 AS 
SELECT C.id_combat, gagnant, tier, rules, classe, tours, 
U1.nom as Nom1, E1.elo as elo1, Q1.id_equipe as Eq1, Q1.Liste2 as Liste1,
U2.nom as Nom2, E2.elo as elo2, Q2.id_equipe as Eq2, Q2.Liste2 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe
UNION
SELECT C.id_combat, CASE WHEN (resultat = '1') THEN 2 WHEN (resultat = '2') THEN 1 ELSE 0 END as gagnant, tier, rules, classe, 
U2.nom as Nom1, E2.elo as elo1, Q2.id_equipe as Eq1, Q2.Liste2 as Liste1,
U1.nom as Nom2, E1.elo as elo2, Q1.id_equipe as Eq2, Q1.Liste2 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe;

CREATE VIEW vw_combat_L3 AS 
SELECT C.id_combat, gagnant, tier, rules, classe, 
U1.nom as Nom1, E1.elo as elo1, Q1.id_equipe as Eq1, Q1.Liste3 as Liste1,
U2.nom as Nom2, E2.elo as elo2, Q2.id_equipe as Eq2, Q2.Liste3 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe
UNION
SELECT C.id_combat, CASE WHEN (resultat = '1') THEN 2 WHEN (resultat = '2') THEN 1 ELSE 0 END as gagnant, tier, rules, classe, 
U2.nom as Nom1, E2.elo as elo1, Q2.id_equipe as Eq1, Q2.Liste3 as Liste1,
U1.nom as Nom2, E1.elo as elo2, Q1.id_equipe as Eq2, Q1.Liste3 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe;

CREATE VIEW vw_combat_L4 AS 
SELECT C.id_combat, gagnant, tier, rules, classe, 
U1.nom as Nom1, E1.elo as elo1, Q1.id_equipe as Eq1, Q1.Liste4 as Liste1,
U2.nom as Nom2, E2.elo as elo2, Q2.id_equipe as Eq2, Q2.Liste4 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe
UNION
SELECT C.id_combat, CASE WHEN (resultat = '1') THEN 2 WHEN (resultat = '2') THEN 1 ELSE 0 END as gagnant, tier, rules, classe, 
U2.nom as Nom1, E2.elo as elo1, Q2.id_equipe as Eq1, Q2.Liste4 as Liste1,
U1.nom as Nom2, E1.elo as elo2, Q1.id_equipe as Eq2, Q1.Liste4 as Liste2
FROM combat C 
JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
JOIN utilisateur U1 on E1.Nom = U1.Nom
JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
JOIN utilisateur U2 on E2.Nom = U2.Nom
JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe;




WITH TCombat AS (
 SELECT C.id_combat AS IdCombat, resultat, tier, rules, classe, 
 U1.nom as Nom1, E1.elo as elo1, Q1.id_equipe as Eq1, Q1.Liste3 as Liste1,
 U2.nom as Nom2, E2.elo as elo2, Q2.id_equipe as Eq2, Q2.Liste3 as Liste2
 FROM combat C 
 JOIN engage E1 ON C.Id_Combat = E1.Id_Combat AND E1.Numero = 1
 JOIN utilisateur U1 on E1.Nom = U1.Nom
 JOIN vw_equipes Q1 on E1.Id_Equipe = Q1.Id_Equipe
 JOIN engage E2 ON C.Id_Combat = E2.Id_Combat AND E2.Numero = 2
 JOIN utilisateur U2 on E2.Nom = U2.Nom
 JOIN vw_equipes Q2 on E2.Id_Equipe = Q2.Id_Equipe
), TNbVictoire AS (
 SELECT TC.Liste1 AS Liste1, 
     TC.Liste2 AS Liste2,
     COUNT(*) AS NbVictoire
 FROM TCombat TC
 WHERE TC.resultat = '0' OR TC.resultat = '1'
 GROUP BY Liste1, Liste2
), TNbCombat AS (
 SELECT TC.Liste1 AS Liste1, 
     TC.Liste2 AS Liste2,
     COUNT(*) AS NbCombat
 FROM TCombat TC
 GROUP BY Liste1, Liste2
)


WITH TNbVictoire AS (
    SELECT CASE WHEN (TC.elo1 <= 1100) THEN 'Novice' ELSE 'Expert' END AS Elo,
           TC.Liste1 AS Liste1,
           TC.Liste2 AS Liste2,
		   Classe,
		   Drop_Rate1,
		   Drop_Rate2,
		   AVG (Tours) as Tours, 
           COUNT(*) AS NbVictoire
    FROM vw_combat_L3 TC
    WHERE TC.gagnant IN (0, 1)
    GROUP BY Elo, Liste1, Liste2, Classe, Drop_Rate1, Drop_Rate2
        ), 
TNbCombat AS (
    SELECT CASE WHEN (TC.elo1 <= 1100) THEN 'Novice' ELSE 'Expert' END AS Elo,
           TC.Liste1 AS Liste1,
           TC.Liste2 AS Liste2,
		   Classe,
		   Drop_Rate1,
		   Drop_Rate2,
		   AVG (Tours) as Tours, 
           COUNT(*) AS NbCombat
    FROM vw_combat_L3 TC
    GROUP BY Elo, Liste1, Liste2, Classe, Drop_Rate1, Drop_Rate2
        ), 
TMain AS (
    SELECT TNV.Elo AS ELoRank,
		   TNV.Classe,
		   TNV.Tours,
           TNV.Liste1 AS Seq1,
           TNV.Liste2 AS Seq2, 
		   TNV.Drop_Rate1,
		   TNV.Drop_Rate2,
           NbVictoire AS NbV,
           NbCombat AS NbC,
           CASE WHEN (TNV.Elo = 'Expert') THEN 0 ELSE 100 - ( CAST(ROUND(CAST(NbVictoire AS DECIMAL) / CAST(NbCombat AS DECIMAL), 2) * 100 AS INTEGER) ) END AS TxVictoire1,
           CASE WHEN (TNV.Elo = 'Novice') THEN 0 ELSE 100 - ( CAST(ROUND(CAST(NbVictoire AS DECIMAL) / CAST(NbCombat AS DECIMAL), 2) * 100 AS INTEGER) ) END AS TxVictoire2
    FROM TNbVictoire TNV, TNbCombat TNC
    WHERE TNV.Elo = TNC.Elo
    AND TNV.Liste1 = TNC.Liste1
    AND TNV.Liste2 = TNC.Liste2
    GROUP BY ELoRank, TNV.Classe, TNV.Tours, Seq1, Seq2, TNV.Drop_Rate1, TNV.Drop_Rate2, NbV, NbC, TxVictoire1, TxVictoire2
        )
SELECT Classe as Format, Seq1 as Joueur, Seq2 as Adversaire, ELoRank as Rang, Drop_Rate1 as Rarete, Tours as Duree, nbv, nbc, TxVictoire1 as Echec1, TxVictoire2 as Echec2
FROM TMain
WHERE NbC > 3
ORDER BY Rang DESC, Joueur, Adversaire;

CREATE VIEW vw_coll_stat AS 
  SELECT 
	( SELECT COUNT ( DISTINCT p.nom ) FROM pokemon p ) as count_pokemon,
	( SELECT COUNT ( DISTINCT Id_Equipe ) FROM equipe ) as count_equipe,
	( SELECT COUNT ( DISTINCT id_combat ) FROM combat ) as count_combat,
	( SELECT COUNT ( DISTINCT u.nom ) FROM utilisateur u ) as count_utilisateur;
  
DROP VIEW vw_equipe, vw_combat, vw_equipes, vw_combat_12, vw_coll_stat;
DROP TABLE aligne, combat, engage, equipe, utilisateur, pokemon;

-- DROP VIEW vw_equipe;
-- DROP VIEW vw_combat;
-- ALTER TABLE equipe

-- TRUNCATE aligne, combat, engage, equipe, pokemon, utilisateur;
-- ALTER SEQUENCE aligne_id_aligne_seq  RESTART WITH 1;
-- ALTER SEQUENCE combat_id_combat_seq  RESTART WITH 1;
-- ALTER SEQUENCE equipe_id_equipe_seq  RESTART WITH 1;

-- ALTER TABLE combat
-- ADD COLUMN Tours INT;
