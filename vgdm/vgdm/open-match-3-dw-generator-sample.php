<?php
/*
 * Copyright © 2013 Diveen
 * All Rights Reserved.
 *
 * This software is proprietary and confidential to Diveen ReplayParser
 * and is protected by copyright law as an unpublished work.
 *
 * Unauthorized access and disclosure strictly forbidden.
 */

/**
 * @author Mickaël Martin-Nevot
 */

define('rGj65F1w', 'mickael-martin-nevot.com');

require_once $_SERVER['DOCUMENT_ROOT'] . '/_assets/config/main.inc.php';
require _MMN_INCLUDE_DIR_ . 'header.inc.php';

require _MMN_RECHERCHE_THESE_DIR_ . 'config/main.inc.php';

// ***

require _MMN_RECHERCHE_THESE_INCLUDE_DIR_ . 'database-vgdm.inc.php';

// Dimensions.

$player1 = '33cbe1d718e13fc8caaebd9f7cedb8b7';
$player2 = '1519f090f98190a14c74b7cd9ac25502';
$player3 = 'a1a7627509acf758aa2130819a123138';

// DLocalization.
$query = 'DROP VIEW IF EXISTS VDPlayer;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'CREATE VIEW VDPlayer (country, region, city, ipaddress, platform, browser, lang, player_loc) AS
SELECT L.country, L.region, L.city, L.ipaddress, P.platform, SUBSTR(P.browser, 0, STRPOS(P.browser, \'version\')), P.lang,
       P.player_id
FROM Localization L
  INNER JOIN Player P
    ON L.ipaddress = P.ipaddress
--WHERE L.ipaddress = \'139.124.242.125\' OR  L.ipaddress = \'92.88.91.80\'
WHERE P.player_id = \'' . $player1 . '\' OR P.player_id = \'' . $player2 . '\' OR P.player_id = \'' . $player3 . '\' 
ORDER BY L.country, L.region, L.city, L.ipaddress, P.platform, P.browser, P.lang, P.player_id ASC;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'DPlayer generated' . '<br/>';
}
$query = 'TRUNCATE TABLE DPlayer RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'SELECT generate_dplayer();';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

// DRound.

$query = 'DROP VIEW IF EXISTS VDRound;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'CREATE VIEW VDRound (game_id, game_date, round_num, round_date) AS
SELECT G.game_id, 
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(G.game_date / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS game_date,
       R.round_num,
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(R.round_date / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS round_date
FROM Round R
  INNER JOIN GameRound GR
    ON R.round_id = GR.round_id
  INNER JOIN Game G
    ON GR.game_id = G.game_id
  INNER JOIN PlayerGame PG
    ON G.game_id = PG.game_id
  INNER JOIN Player P
    ON PG.player_id = P.player_id
--WHERE round_date >= 1513246819240
WHERE P.player_id = \'' . $player1 . '\' OR P.player_id = \'' . $player2 . '\' OR P.player_id = \'' . $player3 . '\' 
ORDER BY G.game_id, game_date, R.round_id, R.round_num, round_date ASC;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'DRound generated' . '<br/>';
}
$query = 'TRUNCATE TABLE DRound RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'SELECT generate_dround();';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

// DMatch.
$query = 'DROP VIEW IF EXISTS VDMatch;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'CREATE VIEW VDMatch (stroke_id, stroke_date, match_num, color, special_four, beam, match_date) AS
SELECT S.stroke_id, 
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(S.time / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS stroke_date, 
       M.match_num, M.color, M.special_four, M.beam, 
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP((R.round_date + M.in_game_time) / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS match_date
FROM GameMatch M
  INNER JOIN StrokeGameMatch SM
    ON M.match_id = SM.match_id
  INNER JOIN Stroke S
    ON SM.stroke_id = S.stroke_id
  INNER JOIN RoundStroke RS
    ON S.stroke_id = RS.stroke_id
  INNER JOIN Round R
    ON RS.round_id = R.round_id
  INNER JOIN GameRound GR
    ON R.round_id = GR.round_id
  INNER JOIN Game G
    ON GR.game_id = G.game_id
  INNER JOIN PlayerGame PG
    ON G.game_id = PG.game_id
  INNER JOIN Player P
    ON PG.player_id = P.player_id
--WHERE round_date >= 1513246819240
WHERE P.player_id = \'' . $player1 . '\' OR P.player_id = \'' . $player2 . '\' OR P.player_id = \'' . $player3 . '\'
  AND S.stroke_id <> \'2003\' 
ORDER BY S.stroke_id, stroke_date, M.match_num, M.color, M.special_four, M.beam, match_date ASC;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'DStroke generated' . '<br/>';
}
$query = 'TRUNCATE TABLE DMatch RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'SELECT generate_dmatch();';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

// Facts.

// FactOM3.
$query = 'TRUNCATE TABLE FactOM3 RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'INSERT INTO FactOM3 (player_id, round_id, match_id, time, duration, length, score, shape)
WITH T AS (
SELECT L.ipaddress, P.player_id, G.game_id, R.round_id, S.stroke_id, M.match_id, round_num, round_date, stroke_num, duration, match_num, color, length, shape, score, score_total, special_four, beam, M.time, time_left in_game_time
FROM Round R
  INNER JOIN GameRound GR
    ON R.round_id = GR.round_id
  INNER JOIN Game G
    ON GR.game_id = G.game_id
  INNER JOIN PlayerGame PG
    ON G.game_id = PG.game_id
  INNER JOIN Player P
    ON PG.player_id = P.player_id
  INNER JOIN Localization L
    ON P.ipaddress = L.ipaddress
  INNER JOIN RoundStroke RS
    ON R.round_id = RS.round_id
  INNER JOIN Stroke S
    ON RS.stroke_id = S.stroke_id
  INNER JOIN StrokeGameMatch SM
    ON S.stroke_id = SM.stroke_id
  INNER JOIN GameMatch M
    ON SM.match_id = M.match_id
), U AS (
SELECT round_id, COUNT(*) AS cpt 
FROM T
WHERE shape = \'horizontal\'
GROUP BY round_id
), V AS (
SELECT round_id, COUNT(*) AS cpt 
FROM T
GROUP BY round_id
), W AS (
SELECT U.round_id, ((U.cpt * 1.0) / (V.cpt * 1.0)) AS cpt
FROM U
  INNER JOIN V
     ON U.round_id = V.round_id
), X AS (
SELECT T.round_id, length AS len
FROM T
)
SELECT DPH.node_id, DRH.node_id, DMH.node_id,
       ROUND(CAST(SUM(time) AS NUMERIC), 2), 
       ROUND(CAST(AVG(duration) AS NUMERIC), 2), 
       ROUND(CAST(AVG(len) AS NUMERIC), 2),
       MAX(score_total), 
       ROUND(CAST(AVG(cpt) AS NUMERIC), 2)
  FROM T 
     INNER JOIN W 
       ON T.round_id = W.round_id
     INNER JOIN X 
       ON W.round_id = X.round_id
     INNER JOIN DRoundHierarchy DRH
       ON T.game_id = DRH.name
     INNER JOIN DMatchHierarchy DMH
       ON T.stroke_id = DMH.name
     INNER JOIN DPlayerHierarchy DPH
       ON T.player_id = DPH.name
WHERE T.player_id = \'' . $player1 . '\' OR T.player_id = \'' . $player2 . '\' OR T.player_id = \'' . $player3 . '\'
GROUP BY (DPH.node_id, DRH.node_id, DMH.node_id)
ORDER BY DRH.node_id, DMH.node_id;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'FactOM3 generated' . '<br/>';
}

// FactOM3.
$query = 'TRUNCATE TABLE FactOM3Lbl RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'INSERT INTO FactOM3Lbl (player_id, round_id, match_id, time, duration, length, score, shape)
SELECT  CASE WHEN P.player_loc = \'33cbe1d718e13fc8caaebd9f7cedb8b7\' THEN \'$P_1$\'
             WHEN P.player_loc = \'1519f090f98190a14c74b7cd9ac25502\' THEN \'$P_2$\'
             WHEN P.player_loc = \'a1a7627509acf758aa2130819a123138\' THEN \'$P_3$\'
        END, 
        R.date, match_id, time, duration, length, score, shape
FROM FactOM3 F
  INNER JOIN DPlayer P
    ON F.player_id = P.player_loc_id
  INNER JOIN DRound R
    ON F.round_id = R.round_date_id;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'FactOM3Lbl generated' . '<br/>';
}

// Cube.

$query = 'TRUNCATE TABLE CubeOM3 RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'INSERT INTO CubeOM3 (player_id, round_id, match_id, time, duration, length, score, shape)
WITH T AS (
SELECT L.ipaddress, P.player_id, G.game_id, R.round_id, S.stroke_id, M.match_id, round_num, round_date, stroke_num, duration, match_num, color, length, shape, score, score_total, special_four, beam, M.time, time_left in_game_time
FROM Round R
  INNER JOIN GameRound GR
    ON R.round_id = GR.round_id
  INNER JOIN Game G
    ON GR.game_id = G.game_id
  INNER JOIN PlayerGame PG
    ON G.game_id = PG.game_id
  INNER JOIN Player P
    ON PG.player_id = P.player_id
  INNER JOIN Localization L
    ON P.ipaddress = L.ipaddress
  INNER JOIN RoundStroke RS
    ON R.round_id = RS.round_id
  INNER JOIN Stroke S
    ON RS.stroke_id = S.stroke_id
  INNER JOIN StrokeGameMatch SM
    ON S.stroke_id = SM.stroke_id
  INNER JOIN GameMatch M
    ON SM.match_id = M.match_id
), U AS (
SELECT round_id, COUNT(*) AS cpt 
FROM T
WHERE shape = \'horizontal\'
GROUP BY round_id
), V AS (
SELECT round_id, COUNT(*) AS cpt 
FROM T
GROUP BY round_id
), W AS (
SELECT U.round_id, ((U.cpt * 1.0) / (V.cpt * 1.0)) AS cpt
FROM U
  INNER JOIN V
     ON U.round_id = V.round_id
), X AS (
SELECT T.round_id, length AS len
FROM T
)
SELECT DPH.node_id, DRH.node_id, DMH.node_id,
       ROUND(CAST(SUM(time) AS NUMERIC), 2), 
       ROUND(CAST(AVG(duration) AS NUMERIC), 2), 
       ROUND(CAST(AVG(len) AS NUMERIC), 2),
       MAX(score_total), 
       ROUND(CAST(AVG(cpt) AS NUMERIC), 2)
  FROM T 
     INNER JOIN W 
       ON T.round_id = W.round_id
     INNER JOIN X 
       ON W.round_id = X.round_id
     INNER JOIN DRoundHierarchy DRH
       ON T.game_id = DRH.name
     INNER JOIN DMatchHierarchy DMH
       ON T.stroke_id = DMH.name
     INNER JOIN DPlayerHierarchy DPH
       ON T.player_id = DPH.name
WHERE T.player_id = \'' . $player1 . '\' OR T.player_id = \'' . $player2 . '\' OR T.player_id = \'' . $player3 . '\'
GROUP BY CUBE (DPH.node_id, DRH.node_id, DMH.node_id)
ORDER BY DPH.node_id NULLS LAST, DRH.node_id NULLS LAST, DMH.node_id NULLS LAST;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'CubeOM3 generated' . '<br/>';
}

// ***

require _MMN_INCLUDE_DIR_ . 'footer.inc.php';
