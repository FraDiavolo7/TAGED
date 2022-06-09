

DROP DATABASE taged_hns;

CREATE DATABASE taged_hns;

\c taged_hns;

CREATE TABLE Joueur(
   Id_Joueur SERIAL,
   Nom VARCHAR(64),
   Tag VARCHAR(64),
   Clan VARCHAR(64),
   PRIMARY KEY(Id_Joueur)
);
alter table joueur add column Tag VARCHAR(64);
alter table joueur add column Clan VARCHAR(64);

CREATE TABLE Perso(
   Id_Perso SERIAL,
   NomPerso VARCHAR(256),
   Serveur VARCHAR(50),
   Rang INT,
   Faille INT,
   tempsfaille INT,
   Classe VARCHAR(50) NOT NULL,
   Niveau INT,
   Parangon INT,
   Force INT,
   Dexterite INT,
   Intelligence INT,
   Vitalite INT,
   Degats INT,
   Robustesse INT,
   Recuperation INT,
   Vie INT,
   Ressource_principale INT,
   Ressource_Secondaire INT,
   Id_Joueur INT NOT NULL,
   PRIMARY KEY(Id_Perso),
   FOREIGN KEY(Id_Joueur) REFERENCES Joueur(Id_Joueur)
);

CREATE TABLE Comp(
   Id_Comp SERIAL,
   NomComp VARCHAR(256) NOT NULL,
   TypeComp VARCHAR(50),
   PRIMARY KEY(Id_Comp)
);

CREATE TABLE Equip(
   Id_Equip SERIAL,
   NomEquip VARCHAR(256) NOT NULL,
   Place VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_Equip)
);

CREATE TABLE Affecte(
   Id_Perso INT,
   Id_Comp INT,
   Rune VARCHAR(256),
   Ordre INT,
   PRIMARY KEY(Id_Perso, Id_Comp),
   FOREIGN KEY(Id_Perso) REFERENCES Perso(Id_Perso),
   FOREIGN KEY(Id_Comp) REFERENCES Comp(Id_Comp)
);

CREATE TABLE Porte(
   Id_Porte SERIAL,
   Cote VARCHAR(50),
   Id_Equip INT NOT NULL,
   Id_Perso INT NOT NULL,
   PRIMARY KEY(Id_Porte),
   FOREIGN KEY(Id_Equip) REFERENCES Equip(Id_Equip),
   FOREIGN KEY(Id_Perso) REFERENCES Perso(Id_Perso)
);

CREATE TABLE CaracEquip(
   NomCarac VARCHAR(50),
   PRIMARY KEY(NomCarac)
);

CREATE TABLE Carac(
   Id_Porte INT,
   NomCarac VARCHAR(50),
   Valeur INT,
   PRIMARY KEY(Id_Porte, NomCarac),
   FOREIGN KEY(Id_Porte) REFERENCES Porte(Id_Porte),
   FOREIGN KEY(NomCarac) REFERENCES CaracEquip(NomCarac)
);
  
 
CREATE VIEW vw_hero AS SELECT 
   Id_Perso,
   NomPerso,
   Serveur,
   Rang,
   Faille,
   tempsfaille,
   Classe,
   Niveau,
   Parangon,
   Force,
   Dexterite,
   Intelligence,
   Vitalite,
   Degats,
   Robustesse,
   Recuperation,
   Vie,
   Ressource_principale,
   Ressource_Secondaire,
   J.Id_Joueur,
   Nom,
   Tag,
   Clan
	FROM perso P
	JOIN joueur J ON P.Id_Joueur = J.Id_Joueur
	order by Serveur, Classe, Rang;
	
	
TRUNCATE Affecte, Porte, Equip, Comp, Perso, Joueur;
