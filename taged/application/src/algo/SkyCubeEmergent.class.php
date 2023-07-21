<?php

/**
 * Class handling an Emergent SkyCube
 * 
 * Composed of 2 SkyCubes, presenting the initial state and the final state of the data
 * @package TAGED\Algo
 */
class SkyCubeEmergent extends SkyCube
{
    /**
     * Le nom de la classe Skycube utilisée pour la construction des SkyCubes internes.
     */
    const SKYCUBE = 'SkyCubeBlocNestedLoop';
    
	    /**
     * Constructeur de la classe SkyCubeEmergent.
     *
     * @param array $Data Les données brutes à utiliser dans l'espace multidimensionnel.
     * @param array $RelationCols Les colonnes d'identification de relation.
     * @param array $MeasureCols Les colonnes d'identification d'attribut (mesure).
     * @param int $MinMax Indicateur pour la valeur maximale ou minimale lors de la construction des Cuboides.
     * @param bool $ComputeAccordCuboides Indicateur pour calculer les Cuboides en accord.
     */
    public function __construct ( $Data, $RelationCols, $MeasureCols, $MinMax = Cuboide::TO_MAX, $ComputeAccordCuboides = FALSE )
    {
        Log::fct_enter ( __METHOD__ );
        $this->SkyCube1 = NULL;
        $this->SkyCube2 = NULL;
        $this->MinMax = $MinMax;
        $this->IsValid = FALSE;
        $this->Emergence = array ();
        $this->ComputeAccordCuboides = $ComputeAccordCuboides;
        
        $this->computeDataSet ( $Data, $RelationCols, $MeasureCols );
        Log::fct_exit ( __METHOD__ );
    }
    
	    /**
     * Méthode protégée pour obtenir l'ID d'une colonne.
     *
     * @param string $ColHeader Le nom de la colonne.
     * @param array $MeasureCols Les colonnes d'identification d'attribut (mesure).
     * @return string L'ID de la colonne.
     */
    protected function getColID ( $ColHeader, $MeasureCols )
    {
        static $CurrentColID = self::MIN_COLID;

        static $Stock = array ();
        
        $ColID = $CurrentColID;

        if ( isset ( $Stock [$ColHeader] ) ) 
        {
            $ColID = $Stock [$ColHeader];
        }
        else
        {
            $LastChar = substr ( $ColHeader, -1 );
            $Col2 = substr_replace ( $ColHeader, 2, -1 );
            $Col2Found = in_array ( $Col2, $MeasureCols );
            
            if ( ( $LastChar == 1 ) && ( $Col2Found ) )
            {
                $this->DenumberedColIDs [$CurrentColID] = substr ( $ColHeader, 0, -1 );
                $ColID  = $CurrentColID . '1';
                $ColID2 = $CurrentColID . '2';
                $CurrentColID++;
                
                $Stock [$ColHeader] = $ColID;
                $Stock [$Col2] = $ColID2;
            }
            else
            {
                $this->DenumberedColIDs [$CurrentColID] = $ColHeader;
                $ColID  = $CurrentColID;
                $CurrentColID++;
                $Stock [$ColHeader] = $ColID;
            }
        }
        
        Log::logVar ( '$ColID', $ColID );
        
        return $ColID;
    }
    
	    /**
     * Méthode protégée pour calculer le DataSet à partir des données brutes, des colonnes de relation et des colonnes d'attribut.
     *
     * @param array $Data Les données brutes à utiliser dans l'espace multidimensionnel.
     * @param array $RelationCols Les colonnes d'identification de relation.
     * @param array $MeasureCols Les colonnes d'identification d'attribut (mesure).
     */
    protected function computeDataSet ( $Data, $RelationCols, $MeasureCols )
    {
        Log::fct_enter ( __METHOD__ );
        $Columns1 = array ();
        $Columns2 = array ();
        $Ignore = array ();
        
//         Log::logVar ( '$MeasureCols', $MeasureCols );

        foreach ( $MeasureCols as $ColID )
        {
            $LastChar = substr ( $ColID, -1 );
            $Col2 = substr_replace ( $ColID, 2, -1 );
//             Log::logVar ( '$ColID', $ColID );
//             Log::logVar ( '$Col2', $Col2 );
//             Log::logVar ( '$LastChar', $LastChar );
            
            if ( ! in_array ( $ColID, $Ignore ) )
            {
                $Columns1 [] = $ColID;
                if ( ( $LastChar == '1' ) && ( in_array ( $Col2, $MeasureCols ) ) )
                {
//                     Log::logVar ( '$ColID 1', $ColID );
                    $Columns2 [] = $Col2;
                    $Ignore [] = $Col2;
//                    $ThisColID = array_search ( $ColHeader, $this->ColIDs );
                }
                else
                {
                    $Columns2 [] = $ColID;
                }
            }
        }
        
        parent::computeDataSet ( $Data, $RelationCols, $MeasureCols );
        
//         Log::logVar ( '$this->ColIDs', $this->ColIDs );
//         Log::logVar ( '$Columns1', $Columns1 );
//         Log::logVar ( '$Columns2', $Columns2 );
        
        $SkyCubeClass = static::SKYCUBE;
//         Log::logVar ( '$SkyCubeClass', $SkyCubeClass );
        
        $this->SkyCube1 = new $SkyCubeClass ( $Data, $RelationCols, $Columns1, $this->MinMax );
        $this->SkyCube2 = new $SkyCubeClass ( $Data, $RelationCols, $Columns2, $this->MinMax );
        
        $this->mergeCuboideLists ();
        Log::fct_exit ( __METHOD__ );
    }
    
	/**
     * Méthode protégée pour fusionner les listes de Cuboides de SkyCube1 et SkyCube2.
     */
    protected function mergeCuboideLists ()
    {
        Log::fct_enter ( __METHOD__ );
        
        // Generer $this->FilteredCuboideIDs et $this->OrderedCuboideIDs (et donc les Cuboides associés)
        
        $this->OrderedCuboideIDs = $this->SkyCube1->getCuboideIDs ( FALSE );

        $CuboideIDs1 = $this->SkyCube1->getCuboideIDs ( TRUE );
        $CuboideIDs2 = $this->SkyCube2->getCuboideIDs ( TRUE );
        
        // Do not use array_merge to preserve int keys
        foreach ( $this->OrderedCuboideIDs as $Level => $DoNotCareAbout )
        {
            $this->FilteredCuboideIDs [$Level] = array_merge ( $CuboideIDs1 [$Level], $CuboideIDs2 [$Level] );
        }
        
//         echo __FILE__ . ":" . __LINE__ . ' - $CuboideIDs1 ' . print_r ( $CuboideIDs1, TRUE ) . "<br>";
//         echo __FILE__ . ":" . __LINE__ . ' - $CuboideIDs2 ' . print_r ( $CuboideIDs2, TRUE ) . "<br>";
//         echo __FILE__ . ":" . __LINE__ . ' - $this->FilteredCuboideIDs ' . print_r ( $this->FilteredCuboideIDs, TRUE ) . "<br>";
        
        Log::fct_exit ( __METHOD__ );
    }
    
	    /**
     * Méthode pour obtenir la liste des Cuboides du SkyCubeEmergent.
     *
     * @return array La liste des Cuboides du SkyCubeEmergent.
     */
    public function getCuboides ()
    {
        Log::fct_enter ( __METHOD__ );
        $Cuboides1 = $this->SkyCube1->getCuboides ();
        $Cuboides2 = $this->SkyCube2->getCuboides ();
        
        $this->Cuboides = array ();
        
        foreach ( $Cuboides1 as $Level => $Cuboides )
        {
            Log::logVar ( '$Level', $Level );
            foreach ( $Cuboides as $ID => $Cuboide )
            {
                Log::logVar ( '$ID', $ID );
                $this->Cuboides [$Level] [$ID] = new CuboideEmergent ( $this->ColIDs, $Cuboide, $Cuboides2 [$Level] [$ID] );
            }
        }
        
        Log::fct_exit ( __METHOD__ );
        return $this->Cuboides;
    }
    
    /**
     * Méthode pour obtenir un Cuboide spécifique en fonction de son ID.
     *
     * @param string $ID L'ID du Cuboide recherché.
     * @return CuboideEmergent|null Le Cuboide correspondant à l'ID ou NULL si non trouvé.
     */
    public function getCuboide ( $ID )
    {
        $Cuboide = NULL;
        $Level = strlen ( $ID );
        if ( isset ( $this->Cuboides [$Level] [$ID] ) )
        {
            $Cuboide = $this->Cuboides [$Level] [$ID];
        }
        else
        {
            $Cuboide1 = $this->SkyCube1->getCuboide ( $ID );
            $Cuboide2 = $this->SkyCube2->getCuboide ( $ID );
            
            $Cuboide = new CuboideEmergent ( $this->ColIDs, $Cuboide1, $Cuboide2 );
            
            $this->Cuboides [$Level] [$ID] = $Cuboide;
        }
        
        return $Cuboide;
    }
    
	/**
     * Méthode pour obtenir les IDs des Cuboides, filtrés ou non, indexés par leur niveau et leur ID.
     *
     * @param bool $Filtered Indicateur pour obtenir les Cuboides filtrés ou non.
     * @return array Les IDs des Cuboides, filtrés ou non, indexés par leur niveau et leur ID.
     */
    public function getCuboideIDs ( $Filtered = TRUE )
    {
        if ( $Filtered && empty ( $this->SetsOfParts ) )
        {
            // Computing Filtered list because it is not initialized
            $this->FilteredCuboideIDs = array ();
            foreach ( $this->OrderedCuboideIDs as $Level => $Cuboides )
            {
                foreach ( $Cuboides as $ID => $CuboideID )
                {
                    $Cuboide = $this->getCuboide ( $ID );
                    $EquivalenceClasses = $Cuboide->getEquivalenceClasses ();
                    
                    if ( ! in_array ( $EquivalenceClasses, $this->SetsOfParts ) )
                    {
                        $this->SetsOfParts [] = $EquivalenceClasses;
                        $this->FilteredCuboideIDs [$Level] [$CuboideID] = $CuboideID;
                    }
                }
            }
            
        }
        
        return ( $Filtered ? $this->FilteredCuboideIDs : $this->OrderedCuboideIDs );
    }
    
	/**
     * Méthode pour obtenir l'espace multidimensionnel généré par le SkyCubeEmergent.
     *
     * @return array L'espace multidimensionnel.
     */
    public function getMultidimensionalSpace ()
    {
        return  $this->SkyCube1->getMultidimensionalSpace ();
    }
    
    /**
     * Méthode pour obtenir l'ensemble des informations d'émergence.
     *
     * @return array L'ensemble des informations d'émergence.
     */
    public function getEmergence ()
    {
        return $this->Emergence;
    }
    
    /**
     * Méthode pour obtenir le nom de colonne encodé à partir du nom complet.
     *
     * @param string $FullName Le nom complet de la colonne encodée.
     * @return string Le nom de colonne encodé.
     */
    public function getCodedColumnName ( $FullName )
    {
        return array_flip ( $this->DenumberedColIDs ) [$FullName];
    }
    
    /**
     * Méthode pour obtenir le nom complet d'une colonne à partir du nom de colonne encodé.
     *
     * @param string $CodedName Le nom de colonne encodé.
     * @return string Le nom complet de la colonne.
     */
    public function getFullColumnName ( $CodedName )
    {
        return $this->DenumberedColIDs [$CodedName];
    }
    
    /**
     * Méthode pour obtenir la liste des identifiants de colonnes dénumérotées.
     *
     * @return array La liste des identifiants de colonnes dénumérotées.
     */
    public function getDenumberedColIDs ()
    {
        return $this->DenumberedColIDs;
    }
    
    /**
     * Méthode pour obtenir le premier SkyCube utilisé dans le SkyCubeEmergent.
     *
     * @return SkyCube Le premier SkyCube utilisé dans le SkyCubeEmergent.
     */
    public function getSkyCube1 ()
    {
        return $this->SkyCube1;
    }
    
     /**
     * Méthode pour obtenir le deuxième SkyCube utilisé dans le SkyCubeEmergent.
     *
     * @return SkyCube Le deuxième SkyCube utilisé dans le SkyCubeEmergent.
     */
    public function getSkyCube2 ()
    {
        return $this->SkyCube2;
    }
    
    /**
     * Méthode pour définir le ratio d'émergence pour un Cuboide spécifique.
     *
     * @param string $CuboideID L'ID du Cuboide pour lequel définir le ratio d'émergence.
     * @param string $MeasureID L'ID de la mesure concernée.
     * @param array $Relation Les identifiants de relation pour le Cuboide.
     * @param float $EmergenceRatio Le ratio d'émergence à définir.
     */
    public function setEmergenceRatio ( $CuboideID, $MeasureID, $Relation, $EmergenceRatio )
    {
        $Key = implode ( ',', array_values ( $Relation ) );
        if ( ! isset ( $this->Emergence [$Key] ) )
        {
            $this->Emergence [$CuboideID][$Key] = $Relation;
            foreach ( $this->DenumberedColIDs as $ColID => $ColName )
            {
                $this->Emergence [$CuboideID] [$Key] [$ColName] = '';
            }
        }
        
        $this->Emergence [$CuboideID] [$Key] [$this->getFullColumnName ($MeasureID)] = $EmergenceRatio;
        
    }
    
    /**
     * Tableau contenant le premier SkyCube.
     *
     * @var SkyCube|null
     */
    protected $SkyCube1;

    /**
     * Tableau contenant le deuxième SkyCube.
     *
     * @var SkyCube|null
     */
    protected $SkyCube2;

    /**
     * Indicateur pour calculer les Cuboides en accord.
     *
     * @var bool
     */
    protected $ComputeAccordCuboides;

    /**
     * Tableau pour stocker les noms de colonnes dénumérotées.
     *
     * @var array
     */
    protected $DenumberedColIDs;

    /**
     * Tableau pour stocker les informations d'émergence.
     *
     * @var array
     */
    protected $Emergence;
}

Log::setDebug ( __FILE__ );

