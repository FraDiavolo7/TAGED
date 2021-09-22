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

define('_MMN_FILES_PATH_', 'files/');

$appRootPath = array_key_exists('CONTEXT_DOCUMENT_ROOT', $_SERVER) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] : $_SERVER['DOCUMENT_ROOT'];
require_once $appRootPath . '/config/main.inc.php';

require _MMN_RECHERCHE_THESE_DIR_ . 'config/main.inc.php';

// ***

require _MMN_RECHERCHE_THESE_INCLUDE_DIR_ . 'database-psdm.inc.php';

/* Pokemon. */
$query = 'SELECT DISTINCT P.pokemon_id, P.ref FROM Pokemon P
          INNER JOIN OneVsAll O1 ON P.pokemon_id = O1.pokemon_id
          INNER JOIN AllVsOne A1 ON O1.pokemon_id = A1.attacker ORDER BY P.num;';

//var_dump($query);
$result = pg_query($link, $query);
if (!$result) {
    echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
} else {
    while ($row = pg_fetch_assoc($result)) {
        $pokemons[] = $row;
    }

    $pokemon_filename = _MMN_FILES_PATH_ . 'pokemons.csv';
    // File creation
    $pokemon_file = fopen($pokemon_filename,"w");

    foreach ($pokemons as $line) {
        fputcsv($pokemon_file, $line);
    }

    fclose($pokemon_file);

    // Download
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=" . $pokemon_filename);
    header("Content-Type: application/csv; ");

    readfile($pokemon_filename);

    // Deleting file
    //unlink($pokemon_filename);
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