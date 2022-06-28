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
/*

require_once $_SERVER['DOCUMENT_ROOT'] . '/_assets/config/main.inc.php';
//require _MMN_INCLUDE_DIR_ . 'header.inc.php';

require _MMN_RECHERCHE_THESE_DIR_ . 'config/main.inc.php';
*/
// ***

require '../includes/database-vgdm.inc.php';

//var_dump($_GET['d']);

$data = json_decode($_POST['d']);

//var_dump($data);

//echo $data->player->ipaddress;

// Localization.
$query = 'INSERT INTO Localization VALUES (\'' . addslashes($data->player->ipaddress) . '\', \'' . addslashes($data->player->country)  . '\', \'' . addslashes($data->player->region) . '\', \'' . addslashes($data->player->city) . '\', ' . $data->player->latitude . ', ' . $data->player->longitude . ') ON CONFLICT DO NOTHING;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

// Player.
$query = 'INSERT INTO Player VALUES (\'' . $data->player->player_id . '\', \'' . $data->player->ipaddress . '\', \'' . detect_os() . '\', \'' . detect_browser() . '\', \'' . substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2). '\') ON CONFLICT DO NOTHING;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

// Game.
$gameId = null;
$query = 'INSERT INTO Game VALUES (DEFAULT, ' . $data->game->game_date . ') ON CONFLICT DO NOTHING;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    $query = 'SELECT CURRVAL(\'Game_game_id_seq\') AS lastinsertid';
    $result = pg_query($link, $query);
    if ($result) {
        if ($row = pg_fetch_assoc($result)) {
            $gameId = $row['lastinsertid'];
        }
    }
}

// PlayerGame.
$query = 'INSERT INTO PlayerGame VALUES (\'' . $data->player->player_id . '\', ' . $gameId . ') ON CONFLICT DO NOTHING;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

// Round.
$roundId = null;
$query = 'INSERT INTO Round VALUES (DEFAULT, ' . $data->round->round_num . ', ' . $data->round->round_date . ')';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    $query = 'SELECT CURRVAL(\'Round_round_id_seq\') AS lastinsertid';
    $result = pg_query($link, $query);
    if ($result) {
        if ($row = pg_fetch_assoc($result)) {
            $roundId = $row['lastinsertid'];
        }
    }
}

// GameRound.
$query = 'INSERT INTO GameRound VALUES (' . $gameId  . ', ' . $roundId . ');';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

for ($i = 0; $i < count($data->round->stroke); ++$i) {
    // Stroke.
    $strokeId = null;
    $query = 'INSERT INTO Stroke VALUES (DEFAULT, ' . $data->round->stroke[$i]->stroke_num . ', ' . $data->round->stroke[$i]->duration . ', ' . $data->round->stroke[$i]->time . ')';
    $result = pg_query($link, $query);
    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        $query = 'SELECT CURRVAL(\'Stroke_stroke_id_seq\') AS lastinsertid';
        $result = pg_query($link, $query);
        if ($result) {
            if ($row = pg_fetch_assoc($result)) {
                $strokeId = $row['lastinsertid'];
            }
        }
    }

    // RoundStroke.
    $query = 'INSERT INTO RoundStroke VALUES (' . $roundId . ', ' . $strokeId . ');';
    $result = pg_query($link, $query);
    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    }

    for ($j = 0; $j < count($data->round->stroke[$i]->match); ++$j) {
        // GameMatch.
        $matchId = null;
        $query = 'INSERT INTO GameMatch VALUES (DEFAULT, ' .$data->round->stroke[$i]->match[$j]->match_num . ', \'' .$data->round->stroke[$i]->match[$j]->color . '\', ' .$data->round->stroke[$i]->match[$j]->length . ', \'' .$data->round->stroke[$i]->match[$j]->shape . '\', ' .$data->round->stroke[$i]->match[$j]->score . ', ' .$data->round->stroke[$i]->match[$j]->score_total  . ', ' .$data->round->stroke[$i]->match[$j]->special_four . ', ' .$data->round->stroke[$i]->match[$j]->beam . ', ' .$data->round->stroke[$i]->match[$j]->time . ', ' .$data->round->stroke[$i]->match[$j]->time_left . ', ' .$data->round->stroke[$i]->match[$j]->in_game_time . ');';
        $result = pg_query($link, $query);
        if (!$result) {
            echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
        } else {
            $query = 'SELECT CURRVAL(\'GameMatch_match_id_seq\') AS lastinsertid';
            $result = pg_query($link, $query);
            if ($result) {
                if ($row = pg_fetch_assoc($result)) {
                    $matchId = $row['lastinsertid'];
                }
            }
        }

        // StrokeGameMatch.
        $query = 'INSERT INTO StrokeGameMatch VALUES (' . $strokeId . ', ' . $matchId . ');';
        $result = pg_query($link, $query);
        if (!$result) {
            echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
        }
    }
}

// ***

//require _MMN_INCLUDE_DIR_ . 'footer.inc.php';