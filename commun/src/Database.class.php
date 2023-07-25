<?php

/**
 * Classe abstraite de gestion de la base de données.
 *
 * Cette classe fournit des fonctionnalités de base pour gérer les connexions
 * à la base de données et exécuter des requêtes.
 * @package Commun
 */
abstract class Database
{
    /**
     * Initialise la connexion à la base de données.
     * Si la connexion n'existe pas, elle sera créée en utilisant les informations statiques
     * de la classe enfant (hôte, utilisateur, mot de passe).
     */
    protected static function init ( )
    {
        if ( NULL == self::$PDO )
        {
            self::$PDO = new PDO ( static::$DBserver, static::$DBuser, static::$DBpwd );
        }
    }
    
    /**
     * Ferme la connexion à la base de données en mettant l'objet PDO à NULL.
     */
    public static function close ( )
    {
        self::$PDO = NULL;
    }
    
    /**
     * Exécute une requête sur la base de données.
     * Si des paramètres sont fournis, la requête est préparée avant d'être exécutée.
     *
     * @param string $Query La requête SQL à exécuter.
     * @param array $Parameters (Optionnel) Les paramètres de la requête préparée.
     */
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
        
//        if ( FALSE !== self::$PDO          ) Log::error ( json_encode ( self::$PDO->errorInfo ( ) ) ); 
        if ( FALSE === self::$PDOStatement ) Log::error ( json_encode ( self::$PDO->errorInfo ( ) ) );
    }

    /**
	 * WIP
	 */
    public static function stats ()
    {
    }
    
    /**
     * Retourne les résultats de la dernière requête exécutée.
     *
     * @return array|null Les résultats de la requête sous forme de tableau, ou NULL s'il n'y a pas de résultats.
     */
    public static function getResults ( )
    {
        $Results = NULL;
        
        if ( NULL != self::$PDOStatement )
        {
            $Results = self::$PDOStatement->fetchAll () ;
        }
        
        return $Results;
    }
    
    /**
     * Échappe les caractères spéciaux d'une chaîne pour une utilisation sûre dans du HTML.
     *
     * @param string $Text La chaîne à échapper.
     * @return string La chaîne échappée pour le HTML.
     */
    public static function escape4HTML ( $Text )
    {
        $HTMLSpeChars = htmlentities ( $Text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false );
        
        $Escaped = $HTMLSpeChars;
        
        return $Escaped;
    }
    
    /**
     * Échappe les caractères spéciaux d'une chaîne pour une utilisation sûre dans du texte brut.
     *
     * @param string $Text La chaîne à échapper.
     * @return string La chaîne échappée pour le texte brut.
     */
    public static function escape4Text ( $Text )
    {
        $Escaped = $Text;
        
        return $Escaped;
    }
    
    
    /**
     * Teste si l'échappement de texte fonctionne correctement pour une chaîne donnée.
     *
     * @param string $InputText La chaîne d'entrée à tester.
     * @param string $Awaited La chaîne attendue après l'échappement.
     * @return bool Renvoie true si l'échappement est correct, sinon false.
     */
    public static function testEscapeText ( $InputText, $Awaited )
    {
        $Result = false;
        
        $Res = self::escape4HTML ($InputText);
        
        $Result = ( $Res == $Awaited );
        echo "$InputText -> $Res / $Awaited " . HTML::bool ($Result) . "<br>";
        
        return $Result;
    }
    
    /**
     * Teste l'échappement pour les caractères HTML spéciaux.
     */
    public static function testEscape4HTML ()
    {
        self::testEscapeText ( 'a', 'a' );
        self::testEscapeText ( '&', '&amp;' );
        self::testEscapeText ( "é", '&eacute;' );
        self::testEscapeText ( '"', '&quot;' );
        self::testEscapeText ( '/', '&sol;' );
        self::testEscapeText ( "'", '&apos;' );
    }
    
    /** @var string Le serveur de la base de données. */
    protected static $DBserver = "";

    /** @var string Le nom d'utilisateur pour se connecter à la base de données. */
    protected static $DBuser = "";

    /** @var string Le mot de passe pour se connecter à la base de données. */
    protected static $DBpwd = "";

    /** @var PDO|null L'objet PDO représentant la connexion à la base de données. */
    protected static $PDO = NULL;

    /** @var PDOStatement|null L'objet PDOStatement contenant la dernière requête préparée ou exécutée. */
    protected static $PDOStatement = NULL;
}

//Log::setDebug ( __FILE__ );
