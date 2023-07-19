<?php

/**
 * Class handling an Emergent SkyCube
 * 
 * Composed of 2 SkyCubes, presenting the initial state and the final state of the data
 * @package TAGED\Algo
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
        $this->Emergence = array ();
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
        
//         echo __FILE__ . ":" . __LINE__ . ' - $CuboideIDs1 ' . print_r ( $CuboideIDs1, TRUE ) . "<br>";
//         echo __FILE__ . ":" . __LINE__ . ' - $CuboideIDs2 ' . print_r ( $CuboideIDs2, TRUE ) . "<br>";
//         echo __FILE__ . ":" . __LINE__ . ' - $this->FilteredCuboideIDs ' . print_r ( $this->FilteredCuboideIDs, TRUE ) . "<br>";
        
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
    
    
    public function getMultidimensionalSpace ()
    {
        return  $this->SkyCube1->getMultidimensionalSpace ();
    }
    
    public function getEmergence ()
    {
        return $this->Emergence;
    }
    
    public function getCodedColumnName ( $FullName )
    {
        return array_flip ( $this->DenumberedColIDs ) [$FullName];
    }
    
    public function getFullColumnName ( $CodedName )
    {
        return $this->DenumberedColIDs [$CodedName];
    }
    
    public function getDenumberedColIDs ()
    {
        return $this->DenumberedColIDs;
    }
    
    public function getSkyCube1 ()
    {
        return $this->SkyCube1;
    }
    
    public function getSkyCube2 ()
    {
        return $this->SkyCube2;
    }
    
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
    
    protected $SkyCube1; //** Table indexed by RowID and ColID of Relation measures
    protected $SkyCube2; //** Table indexed by RowID of Relation identifiers
    protected $ComputeAccordCuboides;
    
    protected $DenumberedColIDs;
    
    protected $Emergence;
}

Log::setDebug ( __FILE__ );

