<?php

/** 
 * @author uidq3545
 * 
 */
abstract class Database
{
    protected static function init ( )
    {
        if ( NULL == self::$PDO )
        {
            self::$PDO = new PDO ( static::$DBserver, static::$DBuser, static::$DBpwd );
        }
        
    }
    
    public static function execute ( $Query, $Parameters = array () )
    {
        self::init ();
        
        if ( is_array ( $Parameters ) && ! empty ( $Parameters ) )
        {
            Log::debug ( "execute $Query (prepare)" );
            
            self::$PDOStatement = self::$PDO->prepare ( $Query );
            
            self::$PDOStatement->execute ( $Parameters );
        }
        else
        {
            Log::debug ( "query $Query" );
            self::$PDOStatement = self::$PDO->query ( $Query );
        }
        
        if ( FALSE !== self::$PDO          ) Log::debug ( json_encode ( self::$PDO->errorInfo ( ) ) ); 
        if ( FALSE !== self::$PDOStatement ) Log::debug ( json_encode ( self::$PDOStatement->errorInfo ( ) ) );
    }
    
    public static function getResults ( )
    {
        $Results = NULL;
        
        if ( NULL != self::$PDOStatement )
        {
            $Results = self::$PDOStatement->fetchAll () ;
        }
        
        return $Results;
    }
    
    public static function escape4HTML ( $Text )
    {
        $HTMLSpeChars = htmlentities ( $Text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        
        $Escaped = $HTMLSpeChars;
        
        return $Escaped;
    }
    
    public static function escape4Text ( $Text )
    {
        $Escaped = $Text;
        
        return $Escaped;
    }
    
    
    public static function testEscapeText ( $InputText, $Awaited )
    {
        $Result = false;
        
        $Res = self::escape4HTML ($InputText);
        
        $Result = ( $Res == $Awaited );
        echo "$InputText -> $Res / $Awaited " . HTML::bool ($Result) . "<br>";
        
        return $Result;
    }
    
    public static function testEscape4HTML ()
    {
        self::testEscapeText ( 'a', 'a' );
        self::testEscapeText ( '&', '&amp;' );
        self::testEscapeText ( "é", '&eacute;' );
        self::testEscapeText ( '"', '&quot;' );
        self::testEscapeText ( '/', '&sol;' );
        self::testEscapeText ( "'", '&apos;' );
    }
    
    protected static $DBserver = "";
    protected static $DBuser = "";
    protected static $DBpwd = "";
    protected static $PDO = NULL;
    protected static $PDOStatement = NULL;
}

