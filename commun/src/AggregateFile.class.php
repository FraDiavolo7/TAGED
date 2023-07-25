<?php

/**
 * Classe étendant Aggregate pour agréger des données à partir d'un fichier ou d'une table dans une base de données.
 * 
 * Cette classe permet d'agrégé des données à partir d'un fichier ou d'une table dans une base de données.
 * Elle utilise la classe Database pour exécuter les requêtes SQL.
 *
 * @package Commun
 */
class AggregateFile extends Aggregate
{
    /**
     * Le dossier de destination pour les requêtes de fichier.
     * @var string
     */
    protected static $Folder = AGGREGATE_FOLDER_REQUESTS;
    
    /**
     * Le chemin complet du fichier contenant la requête SQL.
     * @var string
     */
    protected $File;
    
    /**
     * Le nom de la table dans la base de données.
     * @var string
     */
    protected $DBTable;
    
    /**
     * Le nom de la classe de base de données utilisée pour exécuter les requêtes SQL.
     * @var mixed
     */
    protected $ClassDB;
    
    /**
     * Constructeur de la classe AggregateFile.
     *
     * @param mixed $ClassDB Le nom de la classe de base de données utilisée pour exécuter les requêtes SQL.
     * @param string $File Le chemin complet du fichier contenant la requête SQL.
     * @param string $Table Le nom de la table dans la base de données.
     * @param bool $AsNumerics Indique si les valeurs doivent être traitées comme numériques.
     */
    public function __construct ( $ClassDB, $File, $Table, $AsNumerics = FALSE )
    {
        $this->File = $File;
        $this->DBTable = $Table;
        $this->ClassDB = $ClassDB;
        
        parent::__construct ( $AsNumerics );
    }
    
    /**
     * Récupère les données de la base de données ou du fichier, selon ce qui est disponible.
     *
     * @return array|false Les données récupérées depuis la base de données ou le fichier, ou false en cas d'erreur.
     */
    protected function retrieveData ()
    {
        Log::fct_enter ( __METHOD__ );
        
        $DbClass = $this->ClassDB;
        
        $Request = $this->getRequest ( );
        
        $DbClass::execute ( $Request );
         
        $Results = $DbClass::getResults ( );
        
        Log::fct_exit ( __METHOD__ );
        
        return $Results;
    }
    
    /**
     * Récupère la requête SQL à partir du fichier, ou utilise le nom de la table par défaut.
     *
     * @return string La requête SQL à exécuter.
     */
    public function getRequest ( )
    {
        $Request = "SELECT * FROM " . $this->DBTable . ";";
        
        if ( empty ( $this->File ) )
        {
            Log::debug ( __METHOD__ . " File not provided, using Table name " . $this->DBTable );
        }
        else if ( !file_exists ( $this->File ) )
        {
            Log::error ( __METHOD__ . " File " . $this->File . " is empty, not bothering..." );
        }
        else
        {
            $TmpRequest = file_get_contents ( $this->File );
            
            if ( "" != $TmpRequest ) $Request = $TmpRequest;
        }
        
        return $Request;
    }
    
    /**
     * Modifie la requête SQL stockée dans le fichier par la nouvelle requête spécifiée.
     *
     * @param string $NewRequest La nouvelle requête SQL à stocker dans le fichier.
     */
    public function setRequest ( $NewRequest )
    {
        file_put_contents ( $this->File, $NewRequest );
    }
} // AggregateFile

 