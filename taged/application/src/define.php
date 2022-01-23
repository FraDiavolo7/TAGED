<?php
set_include_path ( get_include_path() . ":.:../src:../src/modules:../src/models:../src/pages:../src/parse:../../../commun/src");

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

    include $FileName;
});


define ( 'DATA_HOME', "/home/taged/data" );
define ( 'SCRIPT_HOME', "../script" );

define ( 'APP_LIST', 'app_list' );
define ( 'APP_NAME_COLLECTION', 'collection' );
define ( 'APP_NAME_MATHC3', 'match3' );
define ( 'APP_NAME_HACK_N_SLASH', 'hackNslash' );

define ( 'STATS_GET_SCRIPT', SCRIPT_HOME . "/getStats.sh" );
define ( 'STATS_SEPARATOR', " : " );

$GLOBALS [ APP_LIST ] [] = APP_NAME_COLLECTION;
$GLOBALS [ APP_LIST ] [] = APP_NAME_MATHC3;
$GLOBALS [ APP_LIST ] [] = APP_NAME_HACK_N_SLASH;
