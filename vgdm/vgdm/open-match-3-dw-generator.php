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
 * @author Mickaël Martin Nevot
 */

define('rGj65F1w', 'mickael-martin-nevot.com');

require_once $_SERVER['DOCUMENT_ROOT'] . '/_assets/config/main.inc.php';
require _MMN_INCLUDE_DIR_ . 'header.inc.php';

require _MMN_RECHERCHE_THESE_DIR_ . 'config/main.inc.php';

// ***

require _MMN_RECHERCHE_THESE_INCLUDE_DIR_ . 'database-vgdm.inc.php';

// Dimensions.

// DPlayer.
$query = 'TRUNCATE TABLE DPlayer RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'INSERT INTO DPlayer (player_id, country, region, city, latitude, longitude, ipaddress, platform, browser, lang)
SELECT P.player_id, L.country, L.region, L.city, L.latitude, L.longitude, L.ipaddress, P.platform, P.browser, P.lang
FROM Localization L
  INNER JOIN Player P
    ON L.ipaddress = P.ipaddress
ORDER BY P.player_id, L.ipaddress, L.country, L.region, L.city, L.latitude, L.longitude, P.platform, P.browser, P.lang ASC;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'DLocalization generated' . '<br/>';
}


// DRound.
$query = 'TRUNCATE TABLE DRound RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'INSERT INTO DRound (round_id, round_num, round_date, game_id, game_date)
SELECT R.round_id, R.round_num,
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(R.round_date / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS round_date, 
       G.game_id, 
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(G.game_date / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS game_date
FROM Round R
  INNER JOIN GameRound GR
    ON R.round_id = GR.round_id
  INNER JOIN Game G
    ON GR.game_id = G.game_id
ORDER BY R.round_id, R.round_num, round_date, G.game_id, game_date ASC;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'DRound generated' . '<br/>';
}

// DMatch.
$query = 'TRUNCATE TABLE DMatch RESTART IDENTITY;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'INSERT INTO DMatch (match_id, match_num, color, length, shape, score, score_total, special_four, beam, match_date, 
                              stroke_id, stroke_num, stroke_duration, stroke_date, 
                              round_time_left, round_in_game_time)
SELECT M.match_id, M.match_num, M.color, M.length, M.shape, M.score, M.score_total, M.special_four, M.beam, 
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP((R.round_date + M.in_game_time) / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS match_date, 
       S.stroke_id, S.stroke_num, S.duration, 
       TO_TIMESTAMP(TO_CHAR(TO_TIMESTAMP(S.time / 1000), \'yyyy/mm/dd HH24:MI:SS\'), \'yyyy/mm/dd HH24:MI:SS\') AS stroke_date, 
       M.time_left, M.in_game_time
FROM GameMatch M
  INNER JOIN StrokeGameMatch SM
    ON M.match_id = SM.match_id
  INNER JOIN Stroke S
    ON SM.stroke_id = S.stroke_id
  INNER JOIN RoundStroke RS
    ON S.stroke_id = RS.stroke_id
  INNER JOIN Round R
    ON RS.round_id = R.round_id
ORDER BY M.match_id, M.match_num, M.color, M.length, M.shape, M.score, M.score_total, M.special_four, M.beam, match_date, 
         S.stroke_id, S.stroke_num, S.duration, stroke_date, 
         M.time_left, M.in_game_time ASC;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'DStroke generated' . '<br/>';
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
WHERE match_num = 1
)
SELECT T.player_id, T.round_id, T.match_id,
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
GROUP BY CUBE (T.ipaddress, T.player_id, T.game_id, T.round_id, T.stroke_id, T.match_id);';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    echo 'FactOM3 generated' . '<br/>';
}

// ***

require _MMN_INCLUDE_DIR_ . 'footer.inc.php';
