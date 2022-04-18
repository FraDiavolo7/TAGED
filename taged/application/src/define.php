<?php
$NewPath = get_include_path();
$NewPath .= PATH_SEPARATOR . '.';
$NewPath .= PATH_SEPARATOR . '../src';
$NewPath .= PATH_SEPARATOR . '../src/modules';
$NewPath .= PATH_SEPARATOR . '../src/models';
$NewPath .= PATH_SEPARATOR . '../src/models/Coll';
$NewPath .= PATH_SEPARATOR . '../src/models/HnS';
$NewPath .= PATH_SEPARATOR . '../src/models/Match3';
$NewPath .= PATH_SEPARATOR . '../src/pages';
$NewPath .= PATH_SEPARATOR . '../src/parse';
$NewPath .= PATH_SEPARATOR . '../../../commun/src';

set_include_path ( $NewPath );

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

define ( 'LOG_FILE', LOG_HOME . 'App_' . date("Ymd") . '.log' );

define ( 'APP_LIST', 'app_list' );
define ( 'APP_NAME_COLLECTION', 'collection' );
define ( 'APP_NAME_MATHC3', 'match3' );
define ( 'APP_NAME_HACK_N_SLASH', 'hackNslash' );

define ( 'DATA_TMP_HNS', DATA_HOME . '/hns_tmp/' );

define ( 'STATS_GET_SCRIPT', SCRIPT_HOME . "/getStats.sh" );
define ( 'STATS_SEPARATOR', " : " );

Log::setLogFile ( LOG_FILE );
Log::setDebug ( Log::ALL );


$GLOBALS [ APP_LIST ] [] = APP_NAME_COLLECTION;
$GLOBALS [ APP_LIST ] [] = APP_NAME_MATHC3;
$GLOBALS [ APP_LIST ] [] = APP_NAME_HACK_N_SLASH;
