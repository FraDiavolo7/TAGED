<?php
/**
 * @package TAGED
 */

$NewPath = get_include_path();
$NewPath .= PATH_SEPARATOR . '.';
$NewPath .= PATH_SEPARATOR . '../src';
$NewPath .= PATH_SEPARATOR . '../src/aggregates';
$NewPath .= PATH_SEPARATOR . '../src/aggregates/Coll';
$NewPath .= PATH_SEPARATOR . '../src/aggregates/HnS';
$NewPath .= PATH_SEPARATOR . '../src/aggregates/Match3';
$NewPath .= PATH_SEPARATOR . '../src/algo';
$NewPath .= PATH_SEPARATOR . '../src/modules';
$NewPath .= PATH_SEPARATOR . '../src/models';
$NewPath .= PATH_SEPARATOR . '../src/models/Coll';
$NewPath .= PATH_SEPARATOR . '../src/models/HnS';
$NewPath .= PATH_SEPARATOR . '../src/models/Match3';
$NewPath .= PATH_SEPARATOR . '../src/pages';
$NewPath .= PATH_SEPARATOR . '../src/parse';
$NewPath .= PATH_SEPARATOR . '../src/ws';
$NewPath .= PATH_SEPARATOR . '../../../commun/src';

set_include_path ( $NewPath );
date_default_timezone_set( 'Europe/Paris' );

spl_autoload_register(function ($ClassName) {
    $Extensions = array ( '.class.php', '.trait.php', '.php' );

    $FileName = $ClassName;
    foreach ( $Extensions as $Ext )
    {
        $TmpName = $ClassName . $Ext;

        if ( stream_resolve_include_path ( $TmpName ) )
        {
            $FileName = $TmpName;
        }
    }
    //echo "$ClassName -> $FileName<br>";

    include $FileName;
});


define ( 'DATA_HOME', "/home/taged/data" );
define ( 'SCRIPT_HOME', "../script" );
define ( 'LOG_HOME', "../log/" );
define ( 'CONFIG_HOME', "../cfg/" );

define ( 'LOG_FILE', LOG_HOME . 'App_' . date("Ymd") . '.log' );

define ( 'APP_LIST', 'app_list' );
define ( 'APP_NAME_COLLECTION', 'collection' );
define ( 'APP_NAME_MATCH3', 'match3' );
define ( 'APP_NAME_HACK_N_SLASH', 'hackNslash' );
define ( 'APP_NAMES', array ( APP_NAME_COLLECTION, APP_NAME_MATCH3, APP_NAME_HACK_N_SLASH ) );

define ( 'DATA_TMP_HNS', DATA_HOME . '/' . APP_NAME_HACK_N_SLASH . '/' );
define ( 'DATA_TMP_HNS_ADDR', DATA_TMP_HNS . 'addr/' );
define ( 'DATA_TMP_HNS_FILES', DATA_TMP_HNS . 'files/' );
define ( 'DATA_ARCHIVE_HNS', DATA_HOME . '/archive/' . APP_NAME_HACK_N_SLASH . '/' );
define ( 'DATA_ERRORS_HNS', DATA_HOME . '/errors/' . APP_NAME_HACK_N_SLASH . '/' );

define ( 'STATS_GET_SCRIPT', SCRIPT_HOME . "/getStats.sh" );
define ( 'STATS_SEPARATOR', " : " );

define ( 'AGGREGATE_FOLDER_DESC',     '/home/taged/data/aggregates/list/' );
define ( 'AGGREGATE_FOLDER_TMP',      '/home/taged/data/aggregates/tmp/' );
define ( 'AGGREGATE_FOLDER_RESULTS',  '/home/taged/data/aggregates/results/' );
define ( 'AGGREGATE_FOLDER_REQUESTS', '/home/taged/data/aggregates/requests/' );
define ( 'ANALYSIS_ALGO', '/opt/taged/taged/TagedAlgo/exe/taged' );
define ( 'ANALYSIS_PARAM_M', 200 );
define ( 'ANALYSIS_PARAM_N', 200 );


Log::setLogFile ( LOG_FILE );
//Log::setDebug ( Log::ALL );
//Log::setDebug ( '/opt/taged/taged/taged/application/src/models/Coll/CollGame.class.php' );

define ( 'APP_NAME', 'APP_NAME' );
define ( 'STATS_FILE', 'Disque' );
define ( 'STATS_DB', 'Base de données' );
define ( 'STATS_COLS', 'STATS_COLS' );
define ( 'STATS_DATA', 'STATS_DATA' );

$GLOBALS [ APP_LIST ] [] = array ( APP_NAME => APP_NAME_COLLECTION,   STATS_FILE => TRUE,  STATS_DB => 'CollTable' );
$GLOBALS [ APP_LIST ] [] = array ( APP_NAME => APP_NAME_MATCH3,       STATS_FILE => FALSE, STATS_DB => 'M3Game' );
$GLOBALS [ APP_LIST ] [] = array ( APP_NAME => APP_NAME_HACK_N_SLASH, STATS_FILE => TRUE,  STATS_DB => 'Hero' );
