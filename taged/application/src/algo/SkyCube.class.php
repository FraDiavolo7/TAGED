<?php

/**
 * Class handling a Skycube
 *  
 * Concepts de base :
 * DataSet : Ensemble de données indexées par les RowID d'un côté et les ColID de l'autre
 * RowID : ID chiffré d'une ligne de donnée ( Tuple ) il est directement issu de l'ordre des données d'origine, il remplace l'ensemble des données d'identification des données d'origine
 * ColID : ID alphabétique d'une colonne de donnée ( Attribut ), il remplace les noms de colonnes des données d'origine
 * CuboideID : Combinaison des ColID composant le Cuboide
 * @package TAGED\Algo
 */
class SkyCube
{
    /**
     * Le nombre maximal de Cuboides autorisé.
     */
    const MAX_CUBOIDE = 128;
    
    /**
     * L'ID de la première colonne de données.
     */
    const MIN_COLID = 'A';
    
    /**
     * Le nom de la classe Cuboide utilisée pour la construction des Cuboides.
     */
    const CUBOIDE = 'Cuboide';
    
    /**
     * Constructeur de la classe SkyCube.
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
        Log::logVar ( '$MeasureCols', $MeasureCols );
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->Cuboides = array ();
        $this->OrderedCuboideIDs = array ();
        $this->FilteredCuboideIDs = array ();
        $this->MinMax = $MinMax;
        $this->IsValid = TRUE;
        $this->CurrentColID = self::MIN_COLID;
        $this->SetsOfParts = array ();
        $this->computeDataSet ( $Data, $RelationCols, $MeasureCols );
        
        $this->generateCuboideList ( $ComputeAccordCuboides );

        Log::fct_exit ( __METHOD__ );
    }
    
    /**
     * Méthode protégée pour générer la liste des Cuboides pour un niveau donné.
     *
     * @param int $Level Le niveau du Cuboide.
     * @param array $ColIDs Les IDs des colonnes à utiliser pour générer les Cuboides.
     * @param bool $ComputeAccordCuboides Indicateur pour calculer les Cuboides en accord.
     * @param string $Current La combinaison d'en-têtes actuelle pour le Cuboide.
     */
    protected function generateCuboideListLvl ( $Level, $ColIDs, $ComputeAccordCuboides = FALSE, $Current = '' )
    {
        Log::fct_enter ( __METHOD__ );
        Log::logVar ( '$Level', $Level );
        Log::logVar ( '$ColIDs', $ColIDs );
        Log::logVar ( '$Current', $Current );
        $Left = count ( $ColIDs );
        $Length = strlen ( $Current );
        $Needed = $Level - $Length;
        
        if ( $Length == $Level )
        {
            $CuboideClass = static::CUBOIDE;
            Log::logVar ( '$CuboideClass', $CuboideClass );
            $Cuboide = new $CuboideClass ( $Current, $this->DataSet, $this->RowHeaders, $this->ColIDs, $this->MinMax );

            $this->Cuboides [$Current] = $Cuboide;
            $this->OrderedCuboideIDs [$Level] [$Current] = $Current;
            
            $EquivalenceClasses = $Cuboide->getEquivalenceClasses ();
            if ( ! in_array ( $EquivalenceClasses, $this->SetsOfParts ) )
            {
                $this->SetsOfParts [] = $EquivalenceClasses;
                $Cuboide->computeCuboide ();
                $this->FilteredCuboideIDs [$Level] [$Current] = $Current;
            }
            elseif ( $ComputeAccordCuboides )
            {
                $Cuboide->computeCuboide ();
            }
        }
        else if ( $Left >= $Needed )
        {
            while ( ! empty ( $ColIDs ) )
            {
                $Key = array_shift ( $ColIDs ); // Retrieves the first item while removing it from the list 
                $this->generateCuboideListLvl ( $Level, $ColIDs, $ComputeAccordCuboides, $Current . $Key );
            }
        }
        Log::fct_exit ( __METHOD__ );
    }
    
    /**
     * Méthode protégée pour générer la liste des Cuboides pour un niveau donné.
     *
     * @param int $Level Le niveau du Cuboide.
     * @param array $ColIDs Les IDs des colonnes à utiliser pour générer les Cuboides.
     * @param bool $ComputeAccordCuboides Indicateur pour calculer les Cuboides en accord.
     * @param string $Current La combinaison d'en-têtes actuelle pour le Cuboide.
     */
    protected function generateCuboideList ( $ComputeAccordCuboides = FALSE )
    {
        Log::fct_enter ( __METHOD__ );
        if ( $this->IsValid )
        {
            $IDCount = count ( $this->ColIDs );
            $NbCuboide = pow ( 2, $IDCount ) - 1;
            Log::logVar ( '$NbCuboide', $NbCuboide );
            
            if ( $NbCuboide <= self::MAX_CUBOIDE )
            {
                for ( $i = $IDCount ; $i ; --$i )
                {
                    $this->generateCuboideListLvl ( $i, array_keys ( $this->ColIDs ), $ComputeAccordCuboides );
                }
            }
            else 
            {
                $this->IsValid = FALSE;
            }
        }
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
        $ColID = $this->CurrentColID++;
        
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
        Log::logVar ( '$RelationCols', $RelationCols );
        Log::logVar ( '$MeasureCols', $MeasureCols );
        if ( is_array ( $Data ) )
        {
            $ColHeaders = array ();
            $NextColID = self::MIN_COLID;
            foreach ( $Data as $RowID => $Row )
            {
                foreach ( $Row as $ColHeader => $Value )
                {
                    if ( in_array ( $ColHeader, $RelationCols ) )
                    {
                        $this->RowHeaders [$RowID] [$ColHeader] = $Value;
                    }
                    else if ( in_array ( $ColHeader, $MeasureCols ) )
                    {
                        if ( ! isset ( $ColHeaders [$ColHeader] ) )
                        {
                            $NextColID = $this->getColID ( $ColHeader, $MeasureCols );
                            $ColHeaders [$ColHeader] = $NextColID;
                            $this->ColIDs [$NextColID] = $ColHeader;
                            $NextColID++;
                        }
                        $ColID = $ColHeaders [$ColHeader];
                        $this->DataSet [$RowID] [$ColID] = $Value;
                    }
                }
            }
            Log::logVar ( '$this->ColIDs', $this->ColIDs );
            
            $RowToRemove = array ();
            
            foreach ( $this->DataSet as $RowID => $Row )
            {
                $Empty = TRUE;
                foreach ( $Row as $ColID => $Value )
                {
                    if ( '' !== $Value )
                    {
                        $Empty = FALSE;
                        break;
                    }
                }
                if ( $Empty )
                {
                    $RowToRemove [] = $RowID;
                }
            }
            
            foreach ( $RowToRemove as $RowID )
            {
                unset ( $this->DataSet [$RowID] );
            }
        }
        else
        {
            $this->IsValid = FALSE;
        }
        Log::fct_exit ( __METHOD__ );
    }
    
    /**
     * Méthode protégée pour générer des combinaisons de valeurs.
     *
     * @param array $List Le tableau de valeurs à combiner.
     * @param array $Begin La combinaison actuelle en cours de construction.
     * @return array Les combinaisons générées.
     */
    protected function generateCombinatorial ( $List, $Begin = array () )
    {
//        echo 'generateCombinatorial ' . print_r ( $List, TRUE ) . ' ' . print_r ( $Begin, TRUE ) . "<br>\n";
        $Combinatorial = array ();
        if ( empty ( $List ) )
        {
            $Combinatorial[] = $Begin;
        }
        else
        {
            $Keys = array_keys ( $List );
            $CurrentKey = array_shift ( $Keys );
            $Current = array_shift ( $List );
            foreach ( $Current as $Value )
            {
                $Begin [$CurrentKey] = $Value;
                $Combinatorial = array_merge ( $Combinatorial, $this->generateCombinatorial ( $List, $Begin ) );;
//                 $Result = $this->generateCombinatorial ( $List, $Begin );
//                 $Tmp = array_merge ( $Combinatorial, $Result );
//                 $Combinatorial = $Tmp;
            }
        }
        return $Combinatorial;
    }
    
    
    /**
     * Méthode de test pour générer des combinaisons et afficher le résultat.
     * Utilisée uniquement à des fins de test.
     */
    public function testCombinatorial ()
    {
        $Test = array ();
        $Test ['toto'] = array ( 1, 3 ); 
        $Test ['tata'] = array ( 4, 2 );
        
        $Combi = $this->generateCombinatorial ( $Test );
        
        echo 'Combi ' . print_r ( $Combi, TRUE ) . "<br>\n";
    }
    
    /**
     * Méthode pour obtenir l'espace multidimensionnel généré par le Skycube.
     *
     * @return array L'espace multidimensionnel.
     */
    public function getMultidimensionalSpace ()
    {
        $MultidimensionalSpace = array ();
        $ALL = 'ALL';
        
        // Init valeurs possibles
        $ColumnValues = array ();
        
        foreach ( $this->RowHeaders as $Headers )
        {
            foreach ( $Headers as $ColName => $Value )
            {
                if ( strtolower ( $ColName ) != 'rowid' )
                {
                    $ColumnValues [$ColName] [$Value] = $Value;
                }
            }
        }
        
        $NbColumns = count ( $ColumnValues );
        $Columns = array_keys ( $ColumnValues );
        
        foreach ( $ColumnValues as $ColName => $ColValues )
        {
            $ColumnValues [$ColName] [$ALL] = $ALL;
        }
        
        $MultidimensionalSpaceUnsorted = $this->generateCombinatorial ( $ColumnValues );
        $Sorter = array ();

        foreach ( $MultidimensionalSpaceUnsorted as $Entry )
        {
            $Values = array_count_values ( $Entry );
            $NbAll = $Values [$ALL] ?? 0;
            $Sorter [$NbAll][] = $Entry;
        }
        
        foreach ( $Sorter as $Entries )
        {
            $MultidimensionalSpace = array_merge ( $MultidimensionalSpace, $Entries );
        }

        $Empty = array ();
        foreach ( $Columns as $ColName )
        {
            $Empty [$ColName] = '&oslash;';
        }
        
        $MultidimensionalSpace [] = $Empty;
        
        // Ajout vide
        
        return $MultidimensionalSpace;
    }
    
    /**
     * Méthode pour obtenir le DataSet du Skycube.
     *
     * @return array Le DataSet du Skycube.
     */
    public function getDataSet ()
    {
        return $this->DataSet;
    }
    
    /**
     * Méthode pour obtenir les en-têtes de lignes du Skycube.
     *
     * @return array Les en-têtes de lignes du Skycube.
     */
    public function getRowHeaders ()
    {
        return $this->RowHeaders;
    }
    
    /**
     * Méthode pour obtenir les en-têtes de colonnes (ColIDs) du Skycube.
     *
     * @return array Les en-têtes de colonnes (ColIDs) du Skycube.
     */
    public function getColIDs ()
    {
        return $this->ColIDs;
    }
    
    /**
     * Méthode pour obtenir la liste des Cuboides du Skycube.
     *
     * @return array La liste des Cuboides du Skycube.
     */
    public function getCuboides ()
    {
        return $this->Cuboides;
    }
    
    /**
     * Méthode pour obtenir les IDs des Cuboides, filtrés ou non, indexés par leur niveau et leur ID.
     *
     * @param bool $Filtered Indicateur pour obtenir les Cuboides filtrés ou non.
     * @return array Les IDs des Cuboides, filtrés ou non, indexés par leur niveau et leur ID.
     */
    public function getCuboideIDs ( $Filtered = TRUE )
    {
        return ( $Filtered ? $this->FilteredCuboideIDs : $this->OrderedCuboideIDs );
    }
    
    /**
     * Méthode pour obtenir un Cuboide spécifique en fonction de son ID.
     *
     * @param string $ID L'ID du Cuboide recherché.
     * @return Cuboide|null Le Cuboide correspondant à l'ID ou NULL si non trouvé.
     */
    public function getCuboide ( $ID )
    {
        return $this->Cuboides [$ID] ?? NULL;
    }
    
    /**
     * Tableau des données brutes indexées par RowID et ColID de Relation measures.
     *
     * @var array
     */
    protected $DataSet;
    
    /**
     * Tableau des en-têtes de lignes indexé par RowID de Relation identifiers.
     *
     * @var array
     */
    protected $RowHeaders;
    
    /**
     * Tableau des en-têtes de colonnes indexé par ColID de Measure identifiers.
     *
     * @var array
     */
    protected $ColIDs;
    
    /**
     * Indicateur pour la valeur maximale ou minimale lors de la construction des Cuboides.
     *
     * @var int
     */
    protected $MinMax;
    
    /**
     * Indicateur pour déterminer si l'instance de SkyCube est valide.
     *
     * @var bool
     */
    protected $IsValid;
    
    /**
     * ID de colonne actuel utilisé uniquement pendant la phase de construction.
     *
     * @var string
     */
    protected $CurrentColID;
    
    /**
     * Tableau des ensembles de parties utilisé pour le calcul des Cuboides.
     *
     * @var array
     */
    protected $SetsOfParts;
    
    /**
     * Tableau des Cuboides indexé par leur combinaison d'en-têtes.
     *
     * @var array
     */
    protected $Cuboides;
    
    /**
     * Tableau des Cuboide IDs indexé par leur niveau et leur ID.
     *
     * @var array
     */
    protected $OrderedCuboideIDs;
    
    /**
     * Tableau des Cuboide IDs filtrés indexé par leur niveau et leur ID.
     *
     * @var array
     */
    protected $FilteredCuboideIDs;
}

Log::setDebug ( __FILE__ );

