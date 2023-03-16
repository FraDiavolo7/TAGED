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
    
    public function __construct ( $Data, $RelationCols, $MeasureCols, $MinMax = Cuboide::TO_MAX )
    {
        Log::fct_enter ( __METHOD__ );
        $this->SkyCube1 = NULL;
        $this->SkyCube2 = NULL;
        $this->MinMax = $MinMax;
        $this->IsValid = FALSE;

        $this->computeDataSet ( $Data, $RelationCols, $MeasureCols );
        Log::fct_exit ( __METHOD__ );
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
                }
                else
                {
                    $Columns2 [] = $ColID;
                }
            }
        }
        
        parent::computeDataSet ( $Data, $RelationCols, $MeasureCols );
        
        $SkyCubeClass = static::SKYCUBE;
        Log::logVar ( '$SkyCubeClass', $SkyCubeClass );
        
        $this->SkyCube1 = new $SkyCubeClass ( $Data, $RelationCols, $Columns1, $this->MinMax );
        $this->SkyCube2 = new $SkyCubeClass ( $Data, $RelationCols, $Columns2, $this->MinMax );
        Log::fct_exit ( __METHOD__ );
    }
    
    public function getCuboides ()
    {
        $Cuboides1 = $this->SkyCube1->getCuboides ();
        $Cuboides2 = $this->SkyCube2->getCuboides ();
        
        $this->Cuboides = array ();
        
        foreach ( $Cuboides1 as $Level => $Cuboides )
        {
            foreach ( $Cuboides as $ID => $Cuboide )
            {
                $this->Cuboides [$Level] [$ID] = new CuboideEmergent ( $this->ColIDs, $Cuboide, $Cuboides2 [$Level] [$ID] );
            }
        }
        
        return $this->Cuboides;
    }
    
    protected $SkyCube1; //** Table indexed by RowID and ColID of Relation measures
    protected $SkyCube2; //** Table indexed by RowID of Relation identifiers
    protected $MinMax;
    protected $IsValid;
}

Log::setDebug ( __FILE__ );

