<?php

/**
 * Classe gérant une liste d'agrégats.
 * 
 * Cette classe permet de récupérer la liste des agrégats disponibles depuis un répertoire spécifié.
 * Elle fournit également des méthodes pour obtenir le contenu d'un fichier d'agrégat spécifique,
 * ainsi que pour obtenir la valeur d'un champ spécifique dans le fichier d'agrégat.
 * 
 * @package TAGED\Aggregates
 */
class AggregateList
{
    /**
     * Répertoire où se trouvent les fichiers d'agrégats.
     * @var string
     */
    protected static $Folder = AGGREGATE_FOLDER_DESC;
    
    /**
     * Constructeur de la classe AggregateList.
     * 
     * Initialise un nouvel objet AggregateList en récupérant la liste des agrégats disponibles
     * dans le répertoire spécifié.
     */
    public function __construct ( )
    {
        $this->List = array ();
        $Files = array_diff ( scandir ( static::$Folder ), array ( '.', '..' ) );
        
        foreach ( $Files as $File )
        {
            if ( pathinfo ( $File, PATHINFO_EXTENSION ) == "ini" )
            {
                $FileName = pathinfo ( $File, PATHINFO_FILENAME );
                $this->List [ $FileName ] = Strings::convertCase ( $FileName, Strings::CASE_NATURAL, Strings::CASE_CAMEL_LOWER );
            }
        }
    }
    
    /**
     * Obtient la liste des agrégats disponibles.
     * 
     * @return array Liste des agrégats disponibles.
     */
    public function getList ()
    {
        return $this->List;
    }

    /**
     * Obtient le contenu d'un fichier d'agrégat spécifique.
     * 
     * @param string $Name Nom du fichier d'agrégat (sans extension .ini).
     * @return array|false Tableau associatif contenant le contenu du fichier d'agrégat, ou FALSE en cas d'échec.
     */
    public function getFileContent ( $Name )
    {
        $FilePath = static::$Folder . $Name . '.ini';
        return parse_ini_file ( $FilePath );
    }
    
    /**
     * Obtient la valeur d'un champ spécifique dans le fichier d'agrégat.
     * 
     * @param string $Name Nom du fichier d'agrégat (sans extension .ini).
     * @param string $Field Nom du champ à récupérer dans le fichier d'agrégat.
     * @return mixed|null Valeur du champ spécifié dans le fichier d'agrégat, ou NULL si le champ n'existe pas.
     */
    public function getFileField ( $Name, $Field )
    {
        $Data = $this->getFileContent ( $Name );
        return Arrays::getIfSet ( $Data, $Field, NULL );
    }
    
    /**
     * Liste des agrégats disponibles.
     * @var array
     */
    protected $List;
} // AggregateList
