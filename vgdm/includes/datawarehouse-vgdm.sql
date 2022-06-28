DROP TABLE IF EXISTS DPlayerHierarchy;
DROP TABLE IF EXISTS DPlayer;
DROP TABLE IF EXISTS DMatchHierarchy;
DROP TABLE IF EXISTS DMatch;
DROP TABLE IF EXISTS DRoundHierarchy;
DROP TABLE IF EXISTS DRound;
DROP TABLE IF EXISTS CubeOM3;
DROP TABLE IF EXISTS FactOM3Lbl;
DROP TABLE IF EXISTS FactOM3;

CREATE TABLE FactOM3 (
  id            SERIAL NOT NULL PRIMARY KEY,
  player_id     INTEGER,
  round_id      INTEGER,
  match_id      INTEGER,
  time          FLOAT,
  duration      FLOAT,
  length        FLOAT,
  score         INTEGER,
  shape         FLOAT
);

CREATE TABLE FactOM3Lbl (
  id            SERIAL NOT NULL PRIMARY KEY,
  player_id     VARCHAR(127),
  round_id      TIMESTAMP WITHOUT TIME ZONE,
  match_id      INTEGER,
  time          FLOAT,
  duration      FLOAT,
  length        FLOAT,
  score         INTEGER,
  shape         FLOAT
);

CREATE TABLE CubeOM3 (
  id            SERIAL NOT NULL PRIMARY KEY,
  player_id     INTEGER,
  round_id      INTEGER,
  match_id      INTEGER,
  time          FLOAT,
  duration      FLOAT,
  length        FLOAT,
  score         INTEGER,
  shape         FLOAT
);

CREATE TABLE DRound (
  round_date_id SERIAL NOT NULL PRIMARY KEY,
  game_id       INTEGER,
  round_num     INTEGER,
  turn          INTEGER,
  date          TIMESTAMP WITHOUT TIME ZONE
);

CREATE TABLE DRoundHierarchy (
  node_id         INTEGER NOT NULL,
  parent_node_id  INTEGER NOT NULL,
  name            INTEGER,
  PRIMARY KEY(node_id, parent_node_id)
);

CREATE TABLE DMatch (
  match_id            SERIAL NOT NULL PRIMARY KEY,
  stroke_id           INTEGER,
  match_num           INTEGER,
  elem                INTEGER,
  color               VARCHAR(7),
  special_four        BOOLEAN,
  beam                BOOLEAN,
  date                TIMESTAMP WITHOUT TIME ZONE
);

CREATE TABLE DMatchHierarchy (
  node_id         INTEGER NOT NULL,
  parent_node_id  INTEGER NOT NULL,
  name            INTEGER,
  PRIMARY KEY(node_id, parent_node_id)
);

CREATE TABLE DPlayer (
  player_loc_id   SERIAL NOT NULL PRIMARY KEY,
  country         INTEGER,
  region          INTEGER,
  city            INTEGER,
  ipaddress       INTEGER,
  platform        INTEGER,
  browser         INTEGER,
  lang            INTEGER,
  player_name     INTEGER,
  player_loc      VARCHAR(127)
);

CREATE TABLE DPlayerHierarchy (
  node_id         INTEGER NOT NULL,
  parent_node_id  INTEGER NOT NULL,
  name            VARCHAR(127),
  PRIMARY KEY(node_id, parent_node_id)
);