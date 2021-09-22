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

$appRootPath = array_key_exists('CONTEXT_DOCUMENT_ROOT', $_SERVER) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] : $_SERVER['DOCUMENT_ROOT'];
require_once $appRootPath . '/config/main.inc.php';

require _MMN_RECHERCHE_THESE_DIR_ . 'config/main.inc.php';

require 'json.php';

// ***

require _MMN_RECHERCHE_THESE_INCLUDE_DIR_ . 'database-psdm.inc.php';

/* Damage calculation rules : https://bulbapedia.bulbagarden.net/wiki/Damage. */

define('_MMN_PSDM_TYPE_CHART_', 'data/type-chart.json');

/* JSON objects. */

$json = new Services_JSON();

/*
// Encode example.
$value = array('foo', 'bar', array(1, 2, 'baz'), array(3, array(4)));
$output = $json->encode($value);
print($output);
*/

$input = file_get_contents(_MMN_PSDM_TYPE_CHART_);
$type_chart = $json->decode($input);

//var_dump($type_chart);

/* Helpers functions. */

function stats_determination($pokemon, $stat_lbl = 'hp', $level = 100, $generation = 1) {
    /* Determination of stats : https://bulbapedia.bulbagarden.net/wiki/Statistic */

    if ($generation == 1) {
        $iv = 15;
        $ev = 252;

        //var_dump($pokemon);

        // Approximation of reality (in order to match with Pokémon Showdown) :
        // - in Generations I and II, the formula should be (with sqrt function in addition): floor(((($pokemon[$stat_lbl] + $iv) * 2 + floor(sqrt($ev) / 4)) * $level)/ 100) + 5
        // - in Generation III onward, another one too
        $stat = floor(((($pokemon[$stat_lbl] + $iv) * 2 + floor(($ev) / 4)) * $level) / 100) + 5;
        if ($stat_lbl == 'hp') {
            $stat = $stat + $level + 5;
        }
    }

    return $stat;
}

function type_effectiveness($move, $defender) {
    global $type_chart;

    //return 1;
    //var_dump($move['type']);
    //var_dump($defender['type1']);
    //var_dump($defender['type2']);

    return (isset($type_chart->{$move['type']}->{$defender['type1']}) ? $type_chart->{$move['type']}->{$defender['type1']} : 1) * (isset($type_chart->{$move['type']}->{$defender['type2']}) ? $type_chart->{$move['type']}->{$defender['type2']} : 1);
}

function modifier_calculation($attacker, $defender, $move, $randomType = true) {
    /* Modifier. */
    $targets = 1; // Targets is 0.75 if the move has more than one target, and 1 otherwise. (In Generation III, it is 0.5 for moves that target all adjacent foes with more than one target, and 1 otherwise.)
    $weather = 1; // Weather is 1.5 if a Water-type move is being used during rain or a Fire-type move during harsh sunlight, and 0.5 if a Water-type move is used during harsh sunlight or a Fire-type move during rain, and 1 otherwise.
    $badge = 1; // Badge is applied in Generation II only. It is 1.25 if the attacking Pokémon is controlled by the player and if the player has obtained the Badge corresponding to the used move's type, and 1 otherwise.
    $critical = 1; // Critical is applied starting in Generation II. It is 2 for a critical hit in Generations II-V, 1.5 for a critical hit from Generation VI onward, and 1 otherwise.

    if ($randomType) {
        // random is a random factor between 0.85 and 1.00 (inclusive):
        //  - From Generation III onward, it is a random integer percentage between 0.85 and 1.00 (inclusive)
        //  - In Generations I and II, it is realized as a multiplication by a random uniformly distributed integer between 217 and 255 (inclusive), followed by an integer division by 255
        $random = 217 / 255;
    } else {
        $random = 255 / 255;
    }

    // STAB is the same-type attack bonus. This is equal to 1.5 if the move's type matches any of the user's types, 2 if the user of the move additionally has Adaptability, and 1 if otherwise.
    if ($attacker['type1'] == $move['type'] || $attacker['type2'] == $move['type']) {
        $stab = 1.5;
    } else {
        $stab = 1;
    }

    $type = type_effectiveness($move, $defender); // Type is the type effectiveness. This can be 0 (ineffective); 0.25, 0.5 (not very effective); 1 (normally effective); 2, or 4 (super effective), depending on both the move's and target's types.

    $burn = 1; // Burn is 0.5 (from Generation III onward) if the attacker is burned, its Ability is not Guts, and the used move is a physical move (other than Facade from Generation VI onward), and 1 otherwise.
    $other = 1; // other is 1 in most cases, and a different multiplier when specific interactions of moves, Abilities or items take effect:
    $modifier = $targets * $weather * $badge * $critical * $random * $stab * $type * $burn * $other;
    return $modifier;
}

/* Clear tables. */
$query = 'TRUNCATE TABLE OneVsAll RESTART IDENTITY CASCADE;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'TRUNCATE TABLE AllVsOne RESTART IDENTITY CASCADE;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}

/* Store pokemons. */
$query = 'SELECT * FROM Pokemon ORDER BY num DESC;';

//var_dump($query);
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    while ($row = pg_fetch_assoc($result)) {
        $pokemons[] = $row;
    }
}

//var_dump($pokemons);

$i = 0;
foreach ($pokemons as &$pokemon) {
    /* For each Pokemon. */
    $level = 100;
    $a = $pokemon['atk'];

    $query = 'SELECT * FROM Move WHERE move_id IN (SELECT move_id FROM Learnset WHERE pokemon_id = ' . $pokemon['pokemon_id'] . ');';

    //var_dump($query);
    $result2 = pg_query($link, $query);
    if (!$result2) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        while ($row2 = pg_fetch_assoc($result2)) {
            /* For each Pokemon's move. */
            $direct = false;
            $power = $row2['basepower'];

            $rowCpt = 0;
            foreach ($pokemons as &$pokemonT) {
                $d = ($pokemonT['def'] == 0 ? 1 : $pokemonT['def']);
                $modifierMin = modifier_calculation($pokemon, $pokemonT, $row2);
                $modifierMax = modifier_calculation($pokemon, $pokemonT, $row2, false);
                $damageMin = (((((2 * $level) / 5 + 2) * $power * $a / $d) / 50 + 2) * $modifierMin);
                $damageMax = (((((2 * $level) / 5 + 2) * $power * $a / $d) / 50 + 2) * $modifierMax);
                $damagePercentMin = ($damageMin == 0 ? 0 : ($damageMin / stats_determination($pokemonT) * 100));
                $damagePercentMax = ($damageMax == 0 ? 0 : ($damageMax / stats_determination($pokemonT) * 100));

                /* INSERT OneVsAll - Begin.*/
                $query = 'INSERT INTO OneVsAll (pokemon_id, defender, move_id, damage_min, damage_max, damage_percent_min, damage_percent_max)
                          VALUES (' . $pokemon['pokemon_id'] . ', ' . $pokemonT['pokemon_id'] . ', ' . $row2['move_id'] . ', ' . $damageMin . ', ' . $damageMax . ', ' . $damagePercentMin . ', ' . $damagePercentMax . ');';

                //var_dump($query);
                $result = pg_query($link, $query);
                if (!$result) {
                    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
                }

                /* INSERT OneVsAll - End. */

                /* INSERT AllVsOne - Begin.*/
                $query = 'INSERT INTO AllVsOne (pokemon_id, attacker, move_id, damage_min, damage_max, damage_percent_min, damage_percent_max)
                          VALUES (' . $pokemonT['pokemon_id'] . ', ' . $pokemon['pokemon_id'] . ', ' . $row2['move_id'] . ', ' . $damageMin . ', ' . $damageMax . ', ' . $damagePercentMin . ', ' . $damagePercentMax . ');';

                //var_dump($query);
                $result = pg_query($link, $query);
                if (!$result) {
                    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
                }

                /* INSERT AllVsOne - End. */

                ++$rowCpt;
                if ($rowCpt == 10) {
                    break;
                }

                //echo $pokemon['ref'] . ' (hp :' . stats_determination($pokemon, 'hp') . ', atk : ' . stats_determination($pokemon, 'atk') . ', def : ' . stats_determination($pokemon, 'def') . ', spa : ' . stats_determination($pokemon, 'spa') . ', spd : ' . stats_determination($pokemon, 'spd') . ', spe : ' . stats_determination($pokemon, 'spe') . ')' .  '-> ' . $row2['ref'] . ' Vs ' . $pokemonT['ref'] . ' (hp :' . stats_determination($pokemonT, 'hp') . ', atk : ' . stats_determination($pokemonT, 'atk') . ', def : ' . stats_determination($pokemonT, 'def') . ', spa : ' . stats_determination($pokemonT, 'spa') . ', spd : ' . stats_determination($pokemonT, 'spd') . ', spe : ' . stats_determination($pokemonT, 'spe') . ') = ' . number_format($damageMin) . '-' . number_format($damageMax) . ' (' . number_format($damagePercentMin, 1) . '% - ' . number_format($damagePercentMax, 1) . '%) : ' . ($damagePercentMax == 0 ? 0 : ceil(100 / $damagePercentMax)) . 'HKO' . '<br/>';
            }
        }
    }
    ++$i;
    if ($i == 10) {
        break;
    }
}

echo '<br/>';

// ***

?>
                    <p>
                        <a href="pokemon-showdown-datamining.php">Pokémon Showdown Data mining</a><br/>
                        <a href="pokemon-showdown-damage.php">Pokémon Showdown Damage</a><br/>
                        <a href="pokemon-showdown-skyline.php">Pokémon Showdown Skyline</a><br/>
                        <a href="pokemon-showdown-export-csv.php">Pokémon Showdown : export au format CSV</a><br/>
                    </p>

<?php
// ***

//phpinfo();