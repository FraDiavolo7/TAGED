#!/usr/bin/php
<?php
/**
 * @package TAGED\Scripts
 */

include '/opt/taged/taged/commun/src/Arrays.class.php';

$Result = Arrays::getCSVLine ( '/opt/taged/taged/taged/application/cfg/pokedex.csv', 'Moltres', 2, 1000, ';' );

print_r ( $Result );
