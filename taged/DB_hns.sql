

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
   Place VARCHAR(50) NOT NULL,
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
	
CREATE VIEW vw_hns_stat AS SELECT 

    COUNT ( DISTINCT Id_Perso ) as count_perso

FROM vw_hero;

CREATE VIEW vw_inventaire AS SELECT
   Id_Perso,
   NomEquip,
   po.Place,
   Cote
FROM Porte po 
JOIN Equip e on po.Id_Equip = e.Id_Equip;

create view vw_build as 
select p.id_perso,
head.nomequip      as Head        ,
torso.nomequip     as Torso       ,
feet.nomequip      as Feet        ,
legs.nomequip      as Legs        ,
hands.nomequip     as Hands       ,
bracers.nomequip   as Bracers     ,
mainHand.nomequip  as MainHand    ,
offHand.nomequip   as OffHand     ,
shoulders.nomequip as Shoulders   ,
neck.nomequip      as Neck        ,
lFinger.nomequip   as LeftFinger  ,
rFinger.nomequip   as RightFinger  

from perso p
left join vw_inventaire head      on p.id_perso = head.id_perso      and head.place      = 'head'
left join vw_inventaire torso     on p.id_perso = torso.id_perso     and torso.place     = 'torso'
left join vw_inventaire feet      on p.id_perso = feet.id_perso      and feet.place      = 'feet'
left join vw_inventaire legs      on p.id_perso = legs.id_perso      and legs.place      = 'legs'
left join vw_inventaire hands     on p.id_perso = hands.id_perso     and hands.place     = 'hands'
left join vw_inventaire bracers   on p.id_perso = bracers.id_perso   and bracers.place   = 'bracers'
left join vw_inventaire mainHand  on p.id_perso = mainHand.id_perso  and mainHand.place  = 'mainHand'
left join vw_inventaire offHand   on p.id_perso = offHand.id_perso   and offHand.place   = 'offHand' 
left join vw_inventaire shoulders on p.id_perso = shoulders.id_perso and shoulders.place = 'shoulders'
left join vw_inventaire neck      on p.id_perso = neck.id_perso      and neck.place      = 'neck'
left join vw_inventaire lFinger   on p.id_perso = lFinger.id_perso   and lFinger.place   = 'Finger' and lFinger.cote = 'Left'
left join vw_inventaire rFinger   on p.id_perso = rFinger.id_perso   and rFinger.place   = 'Finger' and rFinger.cote = 'Right'
;

CREATE VIEW vw_diablo3 AS SELECT
    p.Parangon,
    p.Serveur,
    p.Classe,
    150 - p.Faille as Faille,
    p.TempsFaille,
	p.Rang,
    CONCAT ( 
			head
	, ', ', torso
	, ', ', feet
	, ', ', legs
	, ', ', hands
	, ', ', bracers
	, ', ', shoulders
	, ', ', mainHand
	, ', ', offHand
	)     as Build
FROM Perso p
JOIN vw_Build b on p.id_perso = b.id_perso
WHERE head <> ''
AND torso <> ''
AND feet <> ''
AND legs <> ''
AND hands <> ''
AND bracers <> ''
AND shoulders <> ''
;


TRUNCATE Affecte, Porte, Equip, Comp, Perso, Joueur;
