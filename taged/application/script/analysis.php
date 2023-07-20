#!/bin/php
<?php
/**
 * @package TAGED\Scripts
 */

// Lancé avec comme argument quelle ensemble de données doit être utlisé. 
// Ce nom correspond à un fichier dans le dossier analysis
// Le fichier contient les données nécessaires au run
//
// Etape 1 lire l'argument
// Etape 2 lire le fichier
// Etape 3 extraire les données de la base
// Etape 4 lancer l'analyse TAGED ( FileName.rel NbAttr NbTuples [-mMinSup1] [-nMinSup2] [-sZipfFactor] [-dCardMaxAttr]

// Etape 5 exporter le résultat

chdir ( realpath ( dirname ( __FILE__ ) ) ); // change to script dir to enable relative path used for web

include '../src/define.php';

$Algo = '/opt/taged/taged/TagedAlgo/exe/taged';

$Target = $argv [1];

$Analysis = new Analysis ( $Target . '.ini' );

$Analysis->run ( $Algo, 200, 200 );

