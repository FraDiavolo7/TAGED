<?php

/**
 * Classe pour l'agrégation de données provenant d'une table dans une base de données.
 * 
 * Cette classe permet de récupérer et d'agrégé des données à partir d'une table dans une base de données.
 * Elle peut également exporter les données agrégées sous forme de fichiers CSV.
 * @package Commun
 */
class Aggregate
{
    /**
     * Le nom de la table dans la base de données.
     * @var string
     */
    protected static $Table = "aggregate";
    
    /**
     * Le nom de la classe de base de données utilisée pour exécuter les requêtes SQL.
     * @var string
     */
    protected static $DBClass = "Database";
    
    /**
     * Un tableau contenant les données agrégées.
     * @var array
     */
    protected $Data;
    
    /**
     * Un tableau contenant les noms des attributs (colonnes) de la table.
     * @var array
     */
    protected $Attributes;
    
    /**
     * Le nombre total de tuples (enregistrements) de la table.
     * @var int
     */
    protected $NbTuples;
    
    /**
     * Un tableau associatif contenant les valeurs uniques pour chaque attribut.
     * @var array
     */
    protected $AttributeValues;
    
    /**
     * Un tableau pour les attributs qui ne sont pas utilisés comme numériques.
     * @var array
     */
    protected $AttributeIgnored;
    
    /**
     * Un booléen indiquant si les valeurs doivent être traitées comme numériques.
     * @var bool
     */
    protected $AsNumerics;
    
    /**
     * Constructeur de la classe Aggregate.
     *
     * @param bool $AsNumerics Indique si les valeurs doivent être traitées comme numériques.
     */
    public function __construct ( $AsNumerics = FALSE )
    {
        $this->Data = array ();
        $this->Attributes = array ();
        $this->NbTuples = 0;
        $this->AttributeValues = array ();
        $this->AttributeIgnored = array ();
        $this->AsNumerics = $AsNumerics;
        $this->fill ();
    }

    /**
     * Destructeur de la classe Aggregate.
     */
    public function __destruct()
    {
        unset ( $this->Data );
        unset ( $this->Attributes );
        unset ( $this->NbTuples );
    }
    
    /**
     * Convertit l'objet Aggregate en une chaîne de caractères.
     *
     * @return string La représentation de l'objet Aggregate en chaîne de caractères.
     */
    public function __toString ()
    {
        $String = '';
        // TODO: Compléter la représentation en chaîne de caractères de l'objet.
        
        return $String;
    }
    
    /**
     * Affiche l'en-tête du tableau pour la représentation HTML.
     *
     * @return string Le contenu HTML pour l'en-tête du tableau.
     */
    protected function showTableHeader ()
    {
        $Content  = HTML::startTable ();
        $Content .= HTML::startTR ();
        $Content .= HTML::th ( 'Tuple' );
        foreach ( $this->Attributes as $Attr )
        {
            $Content .= HTML::th ( $Attr );
        }
        $Content .= HTML::endTR ();
        return $Content;
    }
    
    /**
     * Affiche le pied du tableau pour la représentation HTML.
     *
     * @return string Le contenu HTML pour le pied du tableau.
     */
    protected function showTableFooter ()
    {
        return HTML::endTable ();
    }
    
    /**
     * Affiche le contenu du tableau pour la représentation HTML.
     *
     * @return string Le contenu HTML pour le corps du tableau.
     */
    protected function showAsTableContent ()
    {
        $Content = '';
        foreach ( $this->Data as $TupleNum => $Data )
        {
            $Content .= HTML::startTR ();
            $Content .= HTML::td ( $TupleNum + 1 );
            foreach ( $this->Attributes as $Attr )
            {
                $Value = $Data [$Attr] ?? '&nbsp';
                $Content .= HTML::td ( $Value );
            }
            $Content .= HTML::endTR ();
        }
        return $Content;
    }
    
    /**
     * Affiche l'objet Aggregate sous forme de tableau HTML.
     *
     * @return string Le contenu HTML représentant l'objet Aggregate.
     */
    public function show ()
    {
        $Content  = HTML::div ( $this->NbTuples . ' tuples', array ( 'class' => 'tuples_nb' ) );
        $Content .= $this->showTableHeader ();
        
        $Content .= $this->showAsTableContent ();

        $Content .= $this->showTableFooter ();
        return $Content;
    }
    
    /**
     * Exporte les données agrégées sous forme de fichiers CSV.
     *
     * @param string $FilePath Le chemin du dossier où les fichiers CSV seront exportés.
     * @param array $RelationCols Les colonnes à exporter pour les relations.
     * @param array $MeasureCols Les colonnes à exporter pour les mesures.
     * @param bool $ExportNbTuples Indique si le nombre total de tuples doit être exporté dans un fichier séparé.
     */
    public function export ( $FilePath, $RelationCols, $MeasureCols, $ExportNbTuples = true )
    {
        $TupleFile = $FilePath . '.nbtuple';
        $AttrValues = $FilePath . '.attr.';
        $RelPath = $FilePath . '.rel';
        $MesPath = $FilePath . '.mes';
        
        if ( $this->AsNumerics )
        {
            foreach ( $this->AttributeValues as $Attr => $Values )
            {
                $AttrValuesFile = $AttrValues . $Attr;
                
                if ( file_exists ( $AttrValuesFile ) )
                {
                    unlink ( $AttrValuesFile );
                }

                if ( ! isset ( $this->AttributeIgnored [$Attr] ) )
                {
                    foreach ( $Values as $Key => $Val )
                    {
                        file_put_contents ( $AttrValuesFile, "$Key=$Val" . PHP_EOL, FILE_APPEND );
                    }
                }
            }
        }

        if ( $ExportNbTuples ) file_put_contents ( $TupleFile, $this->NbTuples . PHP_EOL );
       
        Arrays::exportAsCSV ( $this->Data, ' ', $RelationCols, Arrays::EXPORT_ROW_NO_HEADER, $RelPath, array (), array (), ';' );

        Arrays::exportAsCSV ( $this->Data, ' ', $MeasureCols, Arrays::EXPORT_ROW_NO_HEADER, $MesPath, array (), array (), ';' );
    }
    
    /**
     * Retourne les données agrégées sous forme de tableau associatif.
     *
     * @return array Les données agrégées sous forme de tableau associatif.
     */
    public function getData ( ) 
    {
        return $this->Data;
    }
    
    /**
     * Retourne le nombre total d'attributs (colonnes) de la table.
     *
     * @return int Le nombre total d'attributs.
     */
    public function getNbAttributes ( )
    {
        return count ( $this->Attributes );
    }
    
    /**
     * Retourne les noms des attributs (colonnes) de la table.
     *
     * @return array Les noms des attributs (colonnes).
     */
    public function getAttributes ( )
    {
        return $this->Attributes;
    }
    
    /**
     * Retourne les valeurs uniques pour chaque attribut.
     *
     * @return array Les valeurs uniques pour chaque attribut.
     */
    public function getAttributeValues ( )
    {
        return $this->AttributeValues;
    }
    
    /**
     * Retourne le nombre total de tuples (enregistrements) de la table.
     *
     * @return int Le nombre total de tuples.
     */
    public function getNbTuples ( )
    {
        return $this->NbTuples;
    }
    
    /**
     * Retourne les données d'un tuple spécifique.
     *
     * @param int $NumTuple Le numéro du tuple souhaité.
     * @return array Les données du tuple spécifié.
     */
    public function getTuple ( $NumTuple ) 
    {
        return $this->Data [$NumTuple] ?? array ();
    }
    
    /**
     * Vérifie si les données agrégées sont valides.
     *
     * @throws Exception Si les données agrégées ne sont pas valides (par exemple, si aucune donnée n'est disponible).
     */
    public function check ( )
    {
        if (    0 == $this->NbTuples     ) throw new Exception ( 'No Tuple'      );
        if ( empty ( $this->Data       ) ) throw new Exception ( 'No Data'       );
        if ( empty ( $this->Attributes ) ) throw new Exception ( 'No Attributes' );
    }
    
    /**
     * Convertit une valeur en numérique si nécessaire et met à jour les valeurs uniques pour l'attribut.
     *
     * @param mixed $Value La valeur à convertir.
     * @param string $Attribute Le nom de l'attribut (colonne) auquel appartient la valeur.
     * @return mixed La valeur convertie si nécessaire.
     */
    protected function convertToNumerics ( $Value, $Attribute )
    {
        $Result = $Value;
        
        if ( $this->AsNumerics )
        {
            $Attr = rtrim ( $Attribute, "0123456789" );
            if ( ! isset ( $this->AttributeValues [$Attr] ) ) 
            {
                if ( is_numeric ( $Value ) )
                {
                    $this->AttributeIgnored [$Attr] = TRUE;
                }
                else
                {
                    $this->AttributeValues [$Attr] = array ();
                    $Result = 1;
                }
            }
            elseif ( ! isset ( $this->AttributeIgnored [$Attr] ) )
            {
                $Result = array_search ( $Value, $this->AttributeValues [$Attr] );
                if ( FALSE === $Result )
                {
                    $Result = count ( $this->AttributeValues [$Attr] ) + 1;
                }
            }
            
            $this->AttributeValues [$Attr] [$Result] = $Value; 
        }
        
        return $Result;
    }
    
    /**
     * Récupère les données de la base de données.
     *
     * @return array|false Les données récupérées depuis la base de données ou false en cas d'erreur.
     */
    protected function retrieveData ()
    {
        Log::fct_enter ( __METHOD__ );
        $DbClass = static::$DBClass;
        
        $DbClass::execute ( "SELECT * FROM " . static::$Table . ";" );
        
        $Results = $DbClass::getResults ( );
        
        Log::fct_exit ( __METHOD__ );
        
        return $Results;
    }
    
    /**
     * Remplit l'objet Aggregate avec les données récupérées de la base de données.
     */
    protected function fill ()
    {
        Log::fct_enter ( __METHOD__ );

        $Results = $this->retrieveData ( );
        
        $CurrentTuple = 0;
        
        if ( NULL != $Results )
        {
            foreach ( $Results as $Result )
            {
                foreach ( $Result as $Key => $Value )
                {
                    if ( ! is_numeric ( $Key ) )
                    { 
                        if ( ! in_array ( $Key, $this->Attributes ) ) $this->Attributes [] = $Key;
                         
                        $this->Data [ $CurrentTuple ] [ $Key ] = $this->convertToNumerics ( $Value, $Key );
                    }
                }
                $CurrentTuple++;
            }
        }
        
//        print_r ( $this->AttributeValues );
        
        $this->NbTuples += $CurrentTuple;

        Log::fct_exit ( __METHOD__ );
    }
    
    
} // Aggregate

