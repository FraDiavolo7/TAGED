DROP TABLE IF EXISTS StrokeGameMatch;
DROP TABLE IF EXISTS GameMatch;
DROP TABLE IF EXISTS RoundStroke;
DROP TABLE IF EXISTS Stroke;
DROP TABLE IF EXISTS GameRound;
DROP TABLE IF EXISTS Round;
DROP TABLE IF EXISTS PlayerGame;
DROP TABLE IF EXISTS Game;
DROP TABLE IF EXISTS Player;
DROP TABLE IF EXISTS Localization;

CREATE TABLE Localization (
  ipaddress VARCHAR(15) NOT NULL PRIMARY KEY,
  country   VARCHAR(127),
  region    VARCHAR(127),
  city      VARCHAR(127),
  latitude  FLOAT,
  longitude FLOAT
);

CREATE TABLE Player (
  player_id CHAR(32) NOT NULL PRIMARY KEY,
  ipaddress VARCHAR(15),
  platform  VARCHAR(127),
  browser   VARCHAR(127),
  lang      VARCHAR(127),
  CONSTRAINT fk_loc FOREIGN KEY (ipaddress) REFERENCES Localization (ipaddress)
);

CREATE TABLE Game (
  game_id   SERIAL NOT NULL PRIMARY KEY,
  game_date BIGINT NOT NULL
);

CREATE TABLE PlayerGame (
  player_id CHAR(32) NOT NULL,
  game_id   INTEGER  NOT NULL,
  CONSTRAINT pk_gam PRIMARY KEY (player_id, game_id),
  CONSTRAINT fk_pla FOREIGN KEY (player_id) REFERENCES Player (player_id),
  CONSTRAINT fk_gam FOREIGN KEY (game_id) REFERENCES Game (game_id)
);

CREATE TABLE Round (
  round_id   SERIAL  NOT NULL PRIMARY KEY,
  round_num  INTEGER NOT NULL,
  round_date BIGINT
);

CREATE TABLE GameRound (
  game_id   INTEGER  NOT NULL,
  round_id  INTEGER  NOT NULL,
  CONSTRAINT pk_rou PRIMARY KEY (game_id, round_id),
  CONSTRAINT fk_pla FOREIGN KEY (game_id) REFERENCES Game (game_id),
  CONSTRAINT fk_rou FOREIGN KEY (round_id) REFERENCES Round (round_id)
);

CREATE TABLE Stroke (
  stroke_id  SERIAL  NOT NULL PRIMARY KEY,
  stroke_num INTEGER NOT NULL,
  duration   FLOAT,
  time       BIGINT
);

CREATE TABLE RoundStroke (
  round_id  INTEGER NOT NULL,
  stroke_id INTEGER NOT NULL,
  CONSTRAINT pk_rs PRIMARY KEY (round_id, stroke_id),
  CONSTRAINT fk_rou FOREIGN KEY (round_id) REFERENCES Round (round_id),
  CONSTRAINT fk_str FOREIGN KEY (stroke_id) REFERENCES Stroke (stroke_id)
);

CREATE TABLE GameMatch (
  match_id     SERIAL  NOT NULL PRIMARY KEY,
  match_num    INTEGER NOT NULL,
  color        VARCHAR(7),
  length       INTEGER,
  shape        VARCHAR(11),
  score        INTEGER,
  score_total  INTEGER,
  special_four BOOLEAN,
  beam         BOOLEAN,
  time         FLOAT,
  time_left    FLOAT,
  in_game_time FLOAT
);

CREATE TABLE StrokeGameMatch (
  stroke_id INTEGER NOT NULL,
  match_id  INTEGER NOT NULL,
  CONSTRAINT pk_sm PRIMARY KEY (stroke_id, match_id),
  CONSTRAINT fk_str FOREIGN KEY (stroke_id) REFERENCES Stroke (stroke_id),
  CONSTRAINT fk_mat FOREIGN KEY (match_id) REFERENCES GameMatch (match_id)
);

-- ****

DROP VIEW  IF EXISTS VGame;
DROP VIEW  IF EXISTS VRound;

CREATE VIEW VGame(ipaddress, city, region, country, latitude, longitude, player_id, lang, browser, platform, game_id, date) AS
  SELECT L.ipaddress, city, region, country, latitude, longitude, P.player_id, lang, browser, platform, G.game_id, TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(game_date / 1000), 'yyyy-mm-dd HH24:MI:SS'), 'yyyy-mm-dd HH24:MI:SS')
  FROM localization L
    INNER JOIN Player P
      ON L.ipaddress = P.ipaddress
    INNER JOIN PlayerGame PG
      ON P.player_id = PG.player_id
    INNER JOIN Game G
      ON PG.game_id = G.game_id;

CREATE VIEW VRound(player_id, game_id, game_date, round_id, round_num, round_date) AS
  SELECT P.player_id, G.game_id, TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(game_date / 1000), 'yyyy-mm-dd HH24:MI:SS'), 'yyyy-mm-dd HH24:MI:SS'), R.round_id, round_num, TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(round_date / 1000), 'yyyy-mm-dd HH24:MI:SS'), 'yyyy-mm-dd HH24:MI:SS')
  FROM Player P
    INNER JOIN PlayerGame PG
      ON P.player_id = PG.player_id
    INNER JOIN Game G
      ON PG.game_id = G.game_id
    INNER JOIN Gameround GR
      ON G.game_id = GR.game_id
    INNER JOIN Round R
      ON GR.round_id = R.round_id;