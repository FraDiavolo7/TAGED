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

/* Document constants. */

// Srce : pokemon-showdown/mods/gen1/pokedex.js
// Dest : data/gen1-pokedex.json
// Rp-1 : ([a-z1-9]*):
// By-1 : "\1":
// Rp-2 : \,\n\t\}
// By-2 : \n\t\}
// Rp-3 : \,\n\}
// By-3 : \n\}

define('_MMN_PSDM_GEN1_POKEDEX_', 'data/gen1-pokedex.json');

// Srce : pokemon-showdown/mods/gen1/formats-data.js
// Dest : data/gen1-formats-data.json
// Rp-1 : ([a-z1-9]*):
// By-1 : "\1":
// Rp-2 : \,\n\t\}
// By-2 : \n\t\}
// Rp-3 : \,\n\}
// By-3 : \n\}
// Rp-4 : \]\},\n\t\t\]
// By-4 : \]\}\n\t\t\]

define('_MMN_PSDM_GEN1_FORMATS_DATA_', 'data/gen1-formats-data.json');

// Srce : pokemon-showdown/mods/gen2/learnsets.js
// Dest : data/gen2-learnsets.json
// Rp-1 : ([a-z1-9]*):
// By-1 : "\1":
// Rp-2 : \,\n\t\}
// By-2 : \n\t\}
// Rp-3 : \,\n\}
// By-3 : \n\}

define('_MMN_PSDM_GEN2_LEARNSETS_', 'data/gen2-learnsets.json');

// Srce : pokemon-showdown/data/moves.js
// Dest : data/moves.json
// console.log(JSON.stringify(BattleMovedex))

define('_MMN_PSDM_MOVES_', 'data/moves.json');

/* JSON objects. */

$json = new Services_JSON();

/*
// Encode example.
$value = array('foo', 'bar', array(1, 2, 'baz'), array(3, array(4)));
$output = $json->encode($value);
print($output);
*/

$input = file_get_contents(_MMN_PSDM_GEN1_POKEDEX_);
$gen1_pokedex = $json->decode($input);

$input = file_get_contents(_MMN_PSDM_GEN1_FORMATS_DATA_);
$gen1_formats_data = $json->decode($input);

$input = file_get_contents(_MMN_PSDM_GEN2_LEARNSETS_);
$gen2_learnsets = $json->decode($input);

$input = file_get_contents(_MMN_PSDM_MOVES_);
$moves = $json->decode($input);

/* */

//echo $input;

//var_dump($gen1_pokedex);
//var_dump($gen1_formats_data);
//var_dump($gen1_pokedex);
//var_dump($moves);

/* Pokemon */
echo 'Pokemons' . '<br/>';

$query = 'TRUNCATE TABLE Pokemon RESTART IDENTITY CASCADE;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'TRUNCATE TABLE Evo RESTART IDENTITY CASCADE;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$query = 'TRUNCATE TABLE EggGroup RESTART IDENTITY CASCADE;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$i = 0;
foreach ($gen1_pokedex as &$pokemon) {
    //var_dump($pokemon);
    //var_dump($gen1_formats_data->{strtolower($pokemon->species)});
    //var_dump((isset($gen1_formats_data->{strtolower($pokemon->species)}) ? $gen1_formats_data->{strtolower($pokemon->species)}->tier : 'null'));

    $query = 'INSERT INTO Pokemon (num, ref, species, generation, tier, type1, type2, gender, hp, atk, def, spa, spd, spe, ability0, ability1, abilityh, heightm, weightkg, color, evolevel)
                VALUES (' . $pokemon->num . ', \''
                          . array_keys(get_object_vars($gen1_pokedex))[$i] . '\', \''
                          . pg_escape_string($pokemon->species) . '\', '
                          . '1, '
                          . (isset($gen1_formats_data->{strtolower($pokemon->species)}) ? '\'' . $gen1_formats_data->{strtolower($pokemon->species)}->tier . '\'' : 'NULL') . ', \''
                          . (isset($pokemon->types[0]) ? $pokemon->types[0] : 'NULL') . '\', \''
                          . (isset($pokemon->types[1]) ? $pokemon->types[1] : 'NULL') . '\', \''
                          . $pokemon->gender . '\', '
                          . $pokemon->baseStats->hp . ', '
                          . $pokemon->baseStats->atk . ', '
                          . $pokemon->baseStats->def . ', '
                          . $pokemon->baseStats->spa . ', '
                          . $pokemon->baseStats->spd . ', '
                          . $pokemon->baseStats->spe . ', '
                          . 'NULL' . ', '
                          . 'NULL' . ', '
                          . 'NULL' . ', '
                          . $pokemon->heightm . ', '
                          . $pokemon->weightkg . ', \''
                          . $pokemon->color . '\', '
                          . (isset($pokemon->evoLevel) ? $pokemon->evoLevel : 'NULL')
                          . ');';

    //var_dump($query);
    $result = pg_query($link, $query);
    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        echo '--Pokemon ' . $pokemon->species . ' generated' . '<br/>';
    }

    /* Evo */
    if (isset($pokemon->evos)) {
        foreach ($pokemon->evos as &$evo) {
            $query = 'INSERT INTO Evo (pokemon_id, evo) VALUES (' . $pokemon->num . ', \'' . $evo . '\');';

            //var_dump($query);
            $result = pg_query($link, $query);
            if (!$result) {
                echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
            } else {
                echo '----Evo ' . $pokemon->species . ' -> ' . $evo . ' generated' . '<br/>';
            }
        }
    }

    /* EggGroup */
    if (isset($pokemon->eggGroups)) {
        foreach ($pokemon->eggGroups as &$eggGroup) {
            $query = 'INSERT INTO EggGroup (pokemon_id, egggroup) VALUES (' . $pokemon->num . ', \'' . $eggGroup . '\');';

            //var_dump($query);
            $result = pg_query($link, $query);
            if (!$result) {
                echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
            } else {
                echo '----EggGroup ' . $pokemon->species . ' : ' . $eggGroup . ' generated' . '<br/>';
            }
        }
    }
    ++$i;
}
echo '<br/>';

/* Move */
echo 'Moves' . '<br/>';

$query = 'TRUNCATE TABLE Move RESTART IDENTITY CASCADE;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
$i = 0;
foreach ($moves as &$move) {
    //var_dump($move);

    $query = 'INSERT INTO Move (num, ref, id, name, category, type, accuracy, basePower, description, shortDesc, pp, priority)
              VALUES (' . $move->num . ', \''
                        . array_keys(get_object_vars($moves))[$i] . '\', \''
                        . $move->id . '\', \''
                        . pg_escape_string($move->name) . '\', \''
                        . $move->category . '\', \''
                        . $move->type . '\', '
                        . ($move->accuracy === true ? 0 : $move->accuracy) . ', '
                        . $move->basePower . ', \''
                        . (isset($move->desc) ? pg_escape_string($move->desc) : 'NULL') . '\', \''
                        . (isset($move->shortDesc) ? pg_escape_string($move->shortDesc) : 'NULL') . '\', '
                        . $move->pp . ', '
                        . $move->priority
                        . ');';

    //var_dump($query);
    $result = pg_query($link, $query);
    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        echo '--Move ' . $move->name . ' generated' . '<br/>';
    }
    ++$i;
}
echo '<br/>';

/* Move */
echo 'LearnSets' . '<br/>';

$query = 'TRUNCATE TABLE LearnSet RESTART IDENTITY CASCADE;';
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
}
//var_dump(array_keys(get_object_vars($gen2_learnsets)));
foreach ($gen2_learnsets as $gen2_learnsets_name => &$gen2_learnset) {
    //var_dump(array_keys(get_object_vars($gen2_learnsets))[$i]);

    //$gen2_learnsets_name = array_keys(get_object_vars($gen2_learnsets))[$i];
    $query = 'SELECT pokemon_id FROM Pokemon WHERE ref = \'' . $gen2_learnsets_name . '\';';
    $result = pg_query($link, $query);
    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        $row = pg_fetch_assoc($result);
        $pokemon_id = $row['pokemon_id'];

        if ($pokemon_id != '') {
            //var_dump($gen2_learnset->learnset);
            foreach ($gen2_learnset->learnset as $learnset_name => &$learnset) {
                //var_dump($learnset);

                if (strpos($learnset[0], '1') === 0) {
                    $query = 'SELECT move_id FROM Move WHERE ref = \'' . $learnset_name . '\';';
                    $result = pg_query($link, $query);
                    if (!$result) {
                        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
                    } else {
                        $row = pg_fetch_assoc($result);
                        $move_id = $row['move_id'];

                        $query = 'INSERT INTO LearnSet (pokemon_id, move_id) VALUES (' . $pokemon_id . ', ' . $move_id . ');';

                        //var_dump($query);
                        $result = pg_query($link, $query);
                        if (!$result) {
                            echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
                        } else {
                            echo '--LearnSet ' . $gen2_learnsets_name . ' : ' . $learnset_name . ' generated' . '<br/>';
                        }
                    }
                }
            }
        }
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