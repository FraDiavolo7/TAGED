#!/bin/php
<?php

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

$Algo = './taged';
$AlgoOpt = '';

$Target = $argv [1];
$TargetBase = './Analysis/' . $Target;
$TargetFile =  $TargetBase . ".ini";
$TmpFolder = $TargetBase . ".tmp";

if ( file_exists ( $TargetFile ) )
{
    $Message = "Executing analysis on $Taget"; 
    
    Log::info ( $Message );
    echo $Message;

    mkdir ( $TmpFolder );
    
    $AnalysisData = parse_ini_file ( $TargetFile );
    
    $Class = $AnalysisData [ 'AggregateClass' ] ?? NULL;
    
    if ( NULL != $Class )
    {
        $AggrgateFile = "$TmpFolder/aggregate";
        
        $Aggregate = new $Class ();
        
        $NbTuples = $Aggregate->getNbTuples ();
        $NbAttributes = $Aggregate->getNbAttributes ();
        
        $Aggregate->export ( $AggrgateFile, false, false );
        
        $Command = "$Algo $AggregateFile $NbAttributes $NbTuples $AlgoOpt"; 
        
        echo $Command;
        // unlink ( $AlgoInput );
    }
    
    // rmdir ( $TmpFolder );
}

?>
