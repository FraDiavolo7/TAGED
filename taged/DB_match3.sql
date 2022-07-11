

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

CREATE VIEW vw_m3_data AS SELECT 
   j.ID_Joueur,
   j.IP_addr,
   p.Id_Partie,
   p.Date_Partie,
   p.Num_Tour,
   p.Date_Tour,
   c.Id_Coup,
   c.Num_Coup,
   c.Duree,
   c.Heure,
   b.Id_Beam,
   b.Num_Match,
   b.Couleur,
   b.Longueur,
   b.Forme,
   b.Score,
   b.Score_total,
   b.Barre,
   b.Temps,
   b.Temps_Restant,
   b.Temps_En_Jeu
FROM joueur j
JOIN partie p ON j.id_joueur = p.id_joueur
JOIN coup   c ON p.id_partie = c.id_partie 
JOIN beam   b ON c.id_coup   = b.id_coup;

CREATE VIEW vw_m3_stat AS SELECT 

    COUNT ( DISTINCT id_joueur ) as count_joueur, 
	COUNT ( DISTINCT id_partie) as count_partie, 
	COUNT ( DISTINCT id_coup ) as count_coup, 
	COUNT ( DISTINCT id_beam ) as count_beam
FROM vw_m3_data;
