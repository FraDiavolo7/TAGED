

DROP DATABASE taged_collection;

CREATE DATABASE taged_collection;

\c taged_collection;

CREATE TABLE combat
(
   Id_Combat SERIAL,
   Resultat VARCHAR(50),
   Tier INT,
   Rules VARCHAR(250),
   Classe VARCHAR(50),
   PRIMARY KEY(Id_Combat)
);

CREATE TABLE pokemon(
   Nom VARCHAR(50),
   PRIMARY KEY(Nom)
);

CREATE TABLE utilisateur(
   Nom VARCHAR(50),
   Avatar VARCHAR(50),
   PRIMARY KEY(Nom)
);

CREATE TABLE equipe(
   Id_Equipe SERIAL,
   Nombre INT,
   PRIMARY KEY(Id_Equipe),
);

CREATE TABLE engage
(
   Id_Combat INT,
   Nom VARCHAR(50),
   Id_Equipe INT,
   ELO INT,
   PRIMARY KEY(Id_Combat, Nom, Id_Equipe),
   FOREIGN KEY(Id_Combat) REFERENCES combat(Id_Combat),
   FOREIGN KEY(Nom) REFERENCES utilisateur(Nom),
   FOREIGN KEY(Id_Equipe) REFERENCES equipe(Id_Equipe)
);

CREATE TABLE Aligne(
   Id_aligne SERIAL,
   Ordre INT NOT NULL,
   Nom VARCHAR(50) NOT NULL,
   Id_Equipe INT NOT NULL,
   PRIMARY KEY(Id_aligne),
   FOREIGN KEY(Nom) REFERENCES Pokemon(Nom),
   FOREIGN KEY(Id_Equipe) REFERENCES Equipe(Id_Equipe)
);

CREATE VIEW vw_equipe AS SELECT C.id_combat, resultat, tier, rules, classe, U.nom,  Q.id_equipe, nombre, liste
	FROM engage E
	JOIN combat C ON E.Id_Combat = C.Id_Combat
	JOIN utilisateur U on E.Nom = U.Nom
	JOIN equipe Q on E.Id_Equipe = Q.Id_Equipe;
