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
class SkyCube
{
    const MAX_CUBOIDE = 128;
    const MIN_COLID = 'A';
    
    const CUBOIDE = 'Cuboide';
    
    public function __construct ( $Data, $RelationCols, $MeasureCols, $MinMax = Cuboide::TO_MAX )
    {
        Log::fct_enter ( __METHOD__ );
        Log::logVar ( '$MeasureCols', $MeasureCols );
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->Cuboides = array ();
        $this->MinMax = $MinMax;
        $this->IsValid = TRUE;
        $this->CurrentColID = self::MIN_COLID;
        $this->SetsOfParts = array ();
        $this->computeDataSet ( $Data, $RelationCols, $MeasureCols );
        
        $this->generateCuboideList ();

        Log::fct_exit ( __METHOD__ );
    }

    protected function generateCuboideListLvl ( $Level, $ColIDs, $Current = '' )
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
            
            $EquivalenceClasses = $Cuboide->getEquivalenceClasses ();
            if ( ! in_array ( $EquivalenceClasses, $this->SetsOfParts ) )
            {
                $this->SetsOfParts [] = $EquivalenceClasses;
                $Cuboide->computeCuboide ();
                $this->Cuboides [$Level] [$Current] = $Cuboide;
            }
        }
        else if ( $Left >= $Needed )
        {
            while ( ! empty ( $ColIDs ) )
            {
                $Key = array_shift ( $ColIDs ); // Retrieves the first item while removing it from the list 
                $this->generateCuboideListLvl ( $Level, $ColIDs, $Current . $Key );
            }
        }
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function generateCuboideList ()
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
                     $this->generateCuboideListLvl ( $i, array_keys ( $this->ColIDs ) );
                }
            }
            else 
            {
                $this->IsValid = FALSE;
            }
        }
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function getColID ( $ColHeader, $MeasureCols )
    {
        $ColID = $this->CurrentColID++;
        
        Log::logVar ( '$ColID', $ColID );
        
        return $ColID;
    }
    
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
    
    public function getDataSet ()
    {
        return $this->DataSet;
    }
    
    public function getRowHeaders ()
    {
        return $this->RowHeaders;
    }
    
    public function getColIDs ()
    {
        return $this->ColIDs;
    }
    
    public function getCuboides ()
    {
        return $this->Cuboides;
    }
    
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $Cuboides; //** List of Cuboides indexed by their header placeholder combinaison
    protected $MinMax;
    protected $IsValid;
    protected $CurrentColID; // used only during construction phase
    
    protected $SetsOfParts;
}

Log::setDebug ( __FILE__ );

