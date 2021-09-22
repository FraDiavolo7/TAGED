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

// ***

require _MMN_RECHERCHE_THESE_INCLUDE_DIR_ . 'database-psdm.inc.php';

/*
Source : The Skyline Operator

TRUNCATE TABLE Skyline RESTART IDENTITY CASCADE;

INSERT INTO Skyline(pokemon_id, ref)
SELECT DISTINCT P.pokemon_id, P.ref
FROM Pokemon P
  INNER JOIN OneVsAll O1 ON P.pokemon_id = O1.pokemon_id
  INNER JOIN AllVsOne A1 ON O1.pokemon_id = A1.attacker
WHERE NOT EXISTS (
  SELECT * FROM Pokemon PT
  INNER JOIN OneVsAll O2 ON PT.pokemon_id = O2.pokemon_id
  INNER JOIN AllVsOne A2 ON O2.pokemon_id = A2.attacker
  WHERE O2.damage_percent_max >= O1.damage_percent_max
    AND A2.damage_percent_max <= A1.damage_percent_max
    AND (O2.damage_percent_max > O1.damage_percent_max
    AND A2.damage_percent_max < A1.damage_percent_max)
);
 */

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