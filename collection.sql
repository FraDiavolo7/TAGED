

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
   Pokemon1 VARCHAR(50),
   Pokemon2 VARCHAR(50),
   Pokemon3 VARCHAR(50),
   Pokemon4 VARCHAR(50),
   Pokemon5 VARCHAR(50),
   Pokemon6 VARCHAR(50),
   PRIMARY KEY(Id_Equipe),
   FOREIGN KEY(Pokemon1) REFERENCES pokemon(Nom),
   FOREIGN KEY(Pokemon2) REFERENCES pokemon(Nom),
   FOREIGN KEY(Pokemon3) REFERENCES pokemon(Nom),
   FOREIGN KEY(Pokemon4) REFERENCES pokemon(Nom),
   FOREIGN KEY(Pokemon5) REFERENCES pokemon(Nom),
   FOREIGN KEY(Pokemon6) REFERENCES pokemon(Nom)
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

