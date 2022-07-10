

DROP DATABASE taged_match3;

CREATE DATABASE taged_match3;

\c taged_match3;

CREATE TABLE Joueur(
   ID_Joueur VARCHAR(50),
   IP_addr VARCHAR(50),
   PRIMARY KEY(ID_Joueur)
);

CREATE TABLE Partie(
   Id_Partie SERIAL,
   Date_Partie BIGINT,
   Num_Tour INT,
   Date_Tour BIGINT,
   ID_Joueur VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_Partie),
   FOREIGN KEY(ID_Joueur) REFERENCES Joueur(ID_Joueur)
);

CREATE TABLE Coup(
   Id_Coup SERIAL,
   Num_Coup INT,
   Duree DECIMAL(15,2),
   Heure BIGINT,
   Id_Partie INT NOT NULL,
   PRIMARY KEY(Id_Coup),
   FOREIGN KEY(Id_Partie) REFERENCES Partie(Id_Partie)
);

CREATE TABLE Beam(
   Id_Beam SERIAL,
   Num_Match INT,
   Couleur VARCHAR(50),
   Longueur INT,
   Forme VARCHAR(50),
   Score INT,
   Score_total INT,
   Barre BOOLEAN,
   Temps DECIMAL(15,2),
   Temps_Restant DECIMAL(15,2),
   Temps_En_Jeu DECIMAL(15,2),
   Id_Coup INT NOT NULL,
   PRIMARY KEY(Id_Beam),
   FOREIGN KEY(Id_Coup) REFERENCES Coup(Id_Coup)
);

