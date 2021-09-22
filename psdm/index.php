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

?>
                    <p>
                        <a href="pokemon-showdown-datamining.php">Pokémon Showdown Data mining</a><br/>
                        <a href="pokemon-showdown-damage.php">Pokémon Showdown Damage</a><br/>
                        <a href="pokemon-showdown-skyline.php">Pokémon Showdown Skyline</a><br/>
                        <a href="pokemon-showdown-export-csv.php">Pokémon Showdown Export CSV</a><br/>
                        <a href="pokemon-showdown-replays-parser.php">Pokémon Showdown Replays parser</a><br/>
                    </p>

<?php
// ***
