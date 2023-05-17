<?php

/**
 * 
 * 
 * Concepts de base :
 * DataSet : Ensemble de données indexées par les RowID d'un côté et les ColID de l'autre
 * RowID : ID chiffré d'une ligne de donnée ( Tuple ) il est directement issu de l'ordre des données d'origine, il remplace l'ensemble des données d'identification des données d'origine
 * ColID : ID alphabétique d'une colonne de donnée ( Attribut ), il remplace les noms de colonnes des données d'origine
 * CuboideID : Combinaison des ColID composant le Cuboide
 */
class SkyCubeEmergent extends SkyCube
{
    const SKYCUBE = 'SkyCubeBlocNestedLoop';
    
    public function __construct ( $Data, $RelationCols, $MeasureCols, $MinMax = Cuboide::TO_MAX, $ComputeAccordCuboides = FALSE )
    {
        Log::fct_enter ( __METHOD__ );
        $this->SkyCube1 = NULL;
        $this->SkyCube2 = NULL;
        $this->MinMax = $MinMax;
        $this->IsValid = FALSE;
        $this->ComputeAccordCuboides = $ComputeAccordCuboides;
        
        $this->computeDataSet ( $Data, $RelationCols, $MeasureCols );
        Log::fct_exit ( __METHOD__ );
    }
    
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
                $ColID  = $CurrentColID . '1';
                $ColID2 = $CurrentColID . '2';
                $CurrentColID++;
                
                $Stock [$ColHeader] = $ColID;
                $Stock [$Col2] = $ColID2;
            }
            else
            {
                $ColID  = $CurrentColID;
                $CurrentColID++;
                $Stock [$ColHeader] = $ColID;
            }
        }
        
        Log::logVar ( '$ColID', $ColID );
        
        return $ColID;
    }
    
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
        
        echo __FILE__ . ":" . __LINE__ . ' - $CuboideIDs1 ' . print_r ( $CuboideIDs1, TRUE ) . "<br>";
        echo __FILE__ . ":" . __LINE__ . ' - $CuboideIDs2 ' . print_r ( $CuboideIDs2, TRUE ) . "<br>";
        echo __FILE__ . ":" . __LINE__ . ' - $this->FilteredCuboideIDs ' . print_r ( $this->FilteredCuboideIDs, TRUE ) . "<br>";
        
        Log::fct_exit ( __METHOD__ );
    }
    
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
    
//     public function getCuboideIDs ( $Filtered = TRUE )
//     {
//         if ( empty ( $this->OrderedCuboideIDs ))
//         {
//             $this->OrderedCuboideIDs = $this->SkyCube1->getCuboideIDs ( FALSE );
//         }
//         if ( empty ( $this->FilteredCuboideIDs ))
//         {
//             $this->FilteredCuboideIDs = $this->SkyCube1->getCuboideIDs ( TRUE );
//         }
//         return ( $Filtered ? $this->FilteredCuboideIDs : $this->OrderedCuboideIDs );
//     }
    
    public function getCuboide ( $ID )
    {
        $Cuboide1 = $this->SkyCube1->getCuboide ( $ID );
        $Cuboide2 = $this->SkyCube2->getCuboide ( $ID );
        
        $Cuboide = new CuboideEmergent ( $this->ColIDs, $Cuboide1, $Cuboide2 );
        
        return $Cuboide;
    }
    
    public function getMultidimensionalSpace ()
    {
        return  $this->SkyCube1->getMultidimensionalSpace ();
    }
    
    protected $SkyCube1; //** Table indexed by RowID and ColID of Relation measures
    protected $SkyCube2; //** Table indexed by RowID of Relation identifiers
    protected $ComputeAccordCuboides;
}

Log::setDebug ( __FILE__ );
