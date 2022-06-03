<?php

class Log
{
	const ERROR   = 'ERROR  ';
	const WARNING = 'WARNING';
	const INFO    = 'INFO   ';
	const DEBUG   = 'DEBUG  ';
	const ALL     = 'all';

	public static function fct_enter ( $Function ) 
	{
	    self::$FunctionTimer [ $Function ] = microtime ( TRUE );
	    self::debug ( "$Function IN" );	
	}
	
	public static function fct_exit  ( $Function ) 
	{  
	    $Time = microtime ( TRUE ) - self::$FunctionTimer [ $Function ];
	    self::debug ( "$Function OUT ( $Time )" );	
	}
	
	public static function error   ( $Data ) {  self::logText ( self::ERROR,   $Data );	}
	public static function warning ( $Data ) {  self::logText ( self::WARNING, $Data );	}
	public static function info    ( $Data ) {  self::logText ( self::INFO,    $Data );	}
	public static function debug   ( $Data ) {  self::logText ( self::DEBUG,   $Data );	}
	public static function logVar  ( $Name, $Var ) 
	{
	    if ( is_string ( $Var ) ) self::logText ( self::DEBUG, "$Name = $Var" );
	    else  self::logText ( self::DEBUG, "$Name = " . print_r ( $Var, TRUE ) );
	}
	
	public static function logText ( $Level, $Data )
	{
		$CallTrace = debug_backtrace ( DEBUG_BACKTRACE_IGNORE_ARGS );
		
		$Call = '';
		// il faut la premi�re version dont la classe n'est pas log
		// et qui a une entr�e 'file'
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
		$DateT = DateTime::createFromFormat ( 'U.u', microtime ( TRUE ) );
		
		if ( FALSE === $DateT )
		{
		    usleep ( 1 );
		    $DateT = DateTime::createFromFormat ( 'U.u', microtime ( TRUE ) );
		}
		
		$DateT->setTimeZone(new DateTimeZone('Europe/Paris'));
		
		$Date = $DateT->format ( 'd/m/Y H:i:s:u' );
		//$Date = date ( DATE_W3C );
		$Debug = self::isDebug ( $Call [ 'file'     ] );
		$Caller = '';
	
// 		echo __METHOD__ . ' CT ' . print_r ( $CallTrace, TRUE ) . "<br>\n";
// 		echo __METHOD__ . ' C ' . json_encode ( $Call, 0, 1 ) . "<br>\n";
		
		if ( $Debug ) 
		{
		    $Caller = "$CallerFile:$CallerLine ";
		}

//		echo "if ( ( $Level != self::DEBUG ) || $Debug ) <br>";
		if ( ( $Level != self::DEBUG ) || $Debug ) 
		{
    		$Text = "$Date $Level $Caller$Data\n";
    		$FileExists = file_exists ( self::$LogFilePath );
    		
    		//echo 'file_put_contents (' .  self::$LogFilePath . ' , ' . $Text . ', FILE_APPEND )' . "<br>\n";
    		file_put_contents ( self::$LogFilePath, $Text, FILE_APPEND );
    		//echo "if ( " . fileowner ( self::$LogFilePath ) ." === ". posix_getuid () .")<br>";
    		if ( ( ! $FileExists ) && ( fileowner ( self::$LogFilePath ) === posix_getuid () ) )
    		{
//                echo 'MINE!!! chmod<br>';
                chmod ( self::$LogFilePath, 0666 );
                if ( self::$LogLinkSet )
                {
                    unlink ( self::$LogLinkPath );
                    symlink ( self::$LogFilePath, self::$LogLinkPath );
                }
    		}
		}
	}
	
	public static function setLogFile ( $FilePath )
	{
		self::$LogFilePath = $FilePath;
	}
	
	public static function setLogLink ( $LinkPath )
	{
	    self::$LogLinkPath = $LinkPath;
	    self::$LogLinkSet = TRUE;
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
	    $Debug = self::$BypassDebug;
	    if ( isset ( self::$DebugList [ $FileName ] ) )
	    {
	        $Debug = self::$DebugList [ $FileName ];
	    }
// 	    echo __METHOD__ . ' D ' . Strings::bool ( $Debug ) .  ' <= ' . Strings::bool ( self::$BypassDebug ) . ' ' . json_encode ( self::$DebugList, 0, 1 ) . "<br>\n";
	    return $Debug;
	}
	
	protected static $LogLinkSet  = '../log/App.log';
	protected static $LogLinkPath = '../log/App.log';
	protected static $LogFilePath = '../log/App.log';
	protected static $DebugList = array ();
	protected static $BypassDebug = false;
	protected static $FunctionTimer = array ();
}


