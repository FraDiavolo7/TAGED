

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
   ID_Joueur VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_Partie),
   FOREIGN KEY(ID_Joueur) REFERENCES Joueur(ID_Joueur)
);

CREATE TABLE Tour(
   Id_Tour SERIAL,
   Num_Tour INT,
   Duree DECIMAL(15,2),
   Heure BIGINT,
   Id_Partie INT NOT NULL,
   PRIMARY KEY(Id_Tour),
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
   Id_Tour INT NOT NULL,
   PRIMARY KEY(Id_Beam),
   FOREIGN KEY(Id_Tour) REFERENCES Tour(Id_Tour)
);
