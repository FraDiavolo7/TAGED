<?php

/**
 * Classe g�rant une liste d'agr�gats.
 * 
 * Cette classe permet de r�cup�rer la liste des agr�gats disponibles depuis un r�pertoire sp�cifi�.
 * Elle fournit �galement des m�thodes pour obtenir le contenu d'un fichier d'agr�gat sp�cifique,
 * ainsi que pour obtenir la valeur d'un champ sp�cifique dans le fichier d'agr�gat.
 * 
 * @package TAGED\Aggregates
 */
class AggregateList
{
    /**
     * R�pertoire o� se trouvent les fichiers d'agr�gats.
     * @var string
     */
    protected static $Folder = AGGREGATE_FOLDER_DESC;
    
    /**
     * Constructeur de la classe AggregateList.
     * 
     * Initialise un nouvel objet AggregateList en r�cup�rant la liste des agr�gats disponibles
     * dans le r�pertoire sp�cifi�.
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
     * Obtient la liste des agr�gats disponibles.
     * 
     * @return array Liste des agr�gats disponibles.
     */
    public function getList ()
    {
        return $this->List;
    }

    /**
     * Obtient le contenu d'un fichier d'agr�gat sp�cifique.
     * 
     * @param string $Name Nom du fichier d'agr�gat (sans extension .ini).
     * @return array|false Tableau associatif contenant le contenu du fichier d'agr�gat, ou FALSE en cas d'�chec.
     */
    public function getFileContent ( $Name )
    {
        $FilePath = static::$Folder . $Name . '.ini';
        return parse_ini_file ( $FilePath );
    }
    
    /**
     * Obtient la valeur d'un champ sp�cifique dans le fichier d'agr�gat.
     * 
     * @param string $Name Nom du fichier d'agr�gat (sans extension .ini).
     * @param string $Field Nom du champ � r�cup�rer dans le fichier d'agr�gat.
     * @return mixed|null Valeur du champ sp�cifi� dans le fichier d'agr�gat, ou NULL si le champ n'existe pas.
     */
    public function getFileField ( $Name, $Field )
    {
        $Data = $this->getFileContent ( $Name );
        return Arrays::getIfSet ( $Data, $Field, NULL );
    }
    
    /**
     * Liste des agr�gats disponibles.
     * @var array
     */
    protected $List;
} // AggregateList
