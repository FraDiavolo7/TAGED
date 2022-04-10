<?php

class Log
{
	const ERROR   = 'ERROR  ';
	const WARNING = 'WARNING';
	const INFO    = 'INFO   ';
	const DEBUG   = 'DEBUG  ';
	const ALL     = 'all';

	public static function fct_enter ( $Function ) {  self::debug ( "$Function IN" );	}
	public static function fct_exit  ( $Function ) {  self::debug ( "$Function OUT" );	}
	
	public static function error   ( $Data ) {  self::logText ( self::ERROR,   $Data );	}
	public static function warning ( $Data ) {  self::logText ( self::WARNING, $Data );	}
	public static function info    ( $Data ) {  self::logText ( self::INFO,    $Data );	}
	public static function debug   ( $Data ) {  self::logText ( self::DEBUG,   $Data );	}
	
	public static function logText ( $Level, $Data )
	{
		date_default_timezone_set( 'UTC' );
		$CallTrace = debug_backtrace ( DEBUG_BACKTRACE_IGNORE_ARGS );
		
		$Call = '';
		// il faut la première version dont la classe n'est pas log
		// et qui a une entrée 'file'
		foreach ( $CallTrace as $CT )
		{
		    $File = Arrays::getIfSet ( $CT, 'file', '' );
		    if ( ( $File != __FILE__ ) && ( $File != '' ) )
		    {
		        $Call = $CT;
		        break;
		    }
		} 
		    
		$CalledFunction = $Call [ 'function' ];
		$CallerLine     = $Call [ 'line'     ];
		$CallerFile     = basename ( $Call [ 'file'     ] );
		$Date = date ( DATE_W3C );
		$Debug = self::isDebug ( $Call [ 'file'     ] );
		$Caller = '';
	
// 		echo __METHOD__ . ' CT ' . print_r ( $CallTrace, TRUE ) . "<br>\n";
// 		echo __METHOD__ . ' C ' . json_encode ( $Call, 0, 1 ) . "<br>\n";
		
		if ( $Debug ) 
		{
		    $Caller = "$CallerFile:$CallerLine ";
		}

		if ( ( $Level != self::DEBUG ) || $Debug ) 
		{
    		$Text = "$Date $Level $Caller$Data\n";
    		
    		file_put_contents ( self::$LogFilePath, $Text, FILE_APPEND );
    		if ( fileowner ( self::$LogFilePath ) === getmyuid () )
    		{
                chmod ( self::$LogFilePath, 0666 );
    		}
		}
	}
	
	public static function setLogFile ( $FilePath )
	{
		self::$LogFilePath = $FilePath;
	}
	
	public static function setDebug ( $FileName = self::ALL, $View = true )
	{
	    if ( self::ALL == $FileName )
	    {
	        self::$BypassDebug = true;
	    }
	    else
	    {
	        self::$DebugList [ $FileName ] = $View;
	    }
	}
	
	protected static function isDebug ( $FileName )
	{
// 	    echo __METHOD__ . ' D ' . json_encode ( self::$DebugList, 0, 1 ) . "<br>\n";
	    $Debug = self::$BypassDebug;
	    if ( isset ( self::$DebugList [ $FileName ] ) )
	    {
	        $Debug = self::$DebugList [ $FileName ];
	    }
	    return $Debug;
	}
	
	protected static $LogFilePath = '../log/App.log';
	protected static $DebugList = array ();
	protected static $BypassDebug = false;
}


