<?php

/**
 * Classe utilitaire pour la gestion des logs dans une application PHP.
 *
 * @package Commun
 */
class Log
{
    /**
     * Niveau de log pour les erreurs.
     */
	const ERROR   = 'ERROR  ';

    /**
     * Niveau de log pour les avertissements.
     */
	const WARNING = 'WARNING';

    /**
     * Niveau de log pour les informations.
     */
	const INFO    = 'INFO   ';

    /**
     * Niveau de log pour le débogage.
     */
	const DEBUG   = 'DEBUG  ';

    /**
     * Constante spéciale pour activer le débogage pour tous les fichiers.
     */
	const ALL     = 'all';

    /**
     * Démarre le chronomètre pour une fonction donnée et affiche un message de log de début.
     *
     * @param string $Function Le nom de la fonction qui démarre.
     */
	public static function fct_enter ( $Function ) 
	{
	    self::$FunctionTimer [ $Function ] = microtime ( TRUE );
	    self::debug ( "$Function IN" );	
	}
	
    /**
     * Arrête le chronomètre pour une fonction donnée et affiche un message de log de fin avec le temps d'exécution.
     *
     * @param string $Function Le nom de la fonction qui se termine.
     */
	public static function fct_exit  ( $Function ) 
	{  
	    $Time = microtime ( TRUE ) - self::$FunctionTimer [ $Function ];
	    self::debug ( "$Function OUT ( $Time )" );	
	}
	
    /**
     * Affiche un message de log de niveau ERROR.
     *
     * @param mixed $Data Le contenu du message de log.
     */
	public static function error   ( $Data ) {  self::logText ( self::ERROR,   $Data );	}
	
    /**
     * Affiche un message de log de niveau WARNING.
     *
     * @param mixed $Data Le contenu du message de log.
     */
	public static function warning ( $Data ) {  self::logText ( self::WARNING, $Data );	}
	
    /**
     * Affiche un message de log de niveau INFO.
     *
     * @param mixed $Data Le contenu du message de log.
     */
	public static function info    ( $Data ) {  self::logText ( self::INFO,    $Data );	}

    /**
     * Affiche un message de log de niveau DEBUG.
     *
     * @param mixed $Data Le contenu du message de log.
     */
	public static function debug   ( $Data ) {  self::logText ( self::DEBUG,   $Data );	}

    /**
     * Affiche le contenu d'une variable dans les logs (niveau DEBUG).
     *
     * @param string $Name Le nom de la variable.
     * @param mixed $Var La variable à afficher.
     */
	public static function logVar  ( $Name, $Var ) 
	{
	    if ( is_string ( $Var ) ) self::logText ( self::DEBUG, "$Name = $Var" );
	    else  self::logText ( self::DEBUG, "$Name = " . print_r ( $Var, TRUE ) );
	}
	
    /**
     * Affiche un message de log avec le niveau spécifié, la date/heure, le fichier et la ligne d'appel.
     *
     * @param string $Level Le niveau de log (ERROR, WARNING, INFO, DEBUG).
     * @param mixed $Data Le contenu du message de log.
     */
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
		$TimeU = microtime ( TRUE );
		$DateT = DateTime::createFromFormat ( 'U.u', $TimeU );
		
		if ( FALSE === $DateT )
		{
		    $TimeU2 =  $TimeU . ".0";
		    $DateT = DateTime::createFromFormat ( 'U.u', $TimeU2 );
		}
		
		$DateT->setTimeZone(new DateTimeZone('Europe/Paris'));
		
		$Date = $DateT->format ( 'd/m/Y H:i:s:u' );
		//$Date = date ( DATE_W3C );
		$Debug = self::isDebug ( $Call [ 'file'     ] );
		$Caller = '';
	
// 		echo __METHOD__ . ' CT ' . print_r ( $CallTrace, TRUE ) . "<br>\n";
// 		echo __METHOD__ . ' C ' . json_encode ( $Call, 0, 1 ) . "<br>\n";
//		echo __METHOD__ . ' C ' . $Call [ 'file'     ] . "<br>\n";
		
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
	
    /**
     * Définit le chemin du fichier de log.
     *
     * @param string $FilePath Le chemin du fichier de log.
     */
	public static function setLogFile ( $FilePath )
	{
		self::$LogFilePath = $FilePath;
	}
	
    /**
     * Définit le chemin du lien symbolique vers le fichier de log.
     *
     * @param string $LinkPath Le chemin du lien symbolique vers le fichier de log.
     */
	public static function setLogLink ( $LinkPath )
	{
	    self::$LogLinkPath = $LinkPath;
	    self::$LogLinkSet = TRUE;
	}
	
    /**
     * Active ou désactive le mode de débogage pour un fichier spécifié.
     *
     * @param string $FileName Le nom du fichier pour lequel activer le débogage (ou 'all' pour tout activer).
     * @param bool $View Indique si le débogage doit être activé ou désactivé.
     */
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
	
    /**
     * Vérifie si le débogage est activé pour un fichier spécifié.
     *
     * @param string $FileName Le nom du fichier à vérifier.
     * @return bool Vrai si le débogage est activé pour le fichier, faux sinon.
     */
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


