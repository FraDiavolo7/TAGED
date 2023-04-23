<?php

class Cuboide
{
    const TO_MAX = 'max';
    const TO_MIN = 'min';
    
    const TEST_ON = self::TO_MAX;
    
    const CURRENT = '';
    
    public function __construct ( $CuboideID, $RawDataSet, $RawRowHeaders, $RawColIDs, $MinMax = self::TO_MAX )
    {
        $this->ID = $CuboideID;
        $this->DataSet = array ();
        $this->RowHeaders = $RawRowHeaders;
        $this->ColIDs = array ();
        $this->MinMax = $MinMax;
        $this->IsValid = FALSE;
        $this->EquivalenceClasses = array ();
        
        $this->computeDataSet ( $RawDataSet, $RawColIDs );
        
        //$this->computeCuboide ( );
    }

    protected function isEquivalent ( $RawDataSet, $RowID )
    {
        foreach ( $this->EquivalenceClasses as $RowEq => $Parts )
        {
            if ( empty ( array_diff ( $RawDataSet [$RowEq], $RawDataSet [$RowID] ) ) )
            {
                return $RowEq;
            }
        }
        return FALSE;
    }
    
    /**
     * Prepares the data set
     * Removes empty lines
     * Computes Accords classes
     * @param array $RawDataSet
     * @param array $RawColIDs
     */
    protected function computeDataSet ( $RawDataSet, $RawColIDs )
    {
        $CuboideCols = str_split ( $this->ID );
        
        foreach ( $CuboideCols as $ColID )
        {
            $this->ColIDs [$ColID] = $RawColIDs [$ColID];
        }
        
        $RowsToRemove = array ();
        
        $FirstRow = TRUE;
        
        foreach ( $RawDataSet as $RowID => $Row )
        {
            $Empty = TRUE;
            
            foreach ( $Row as $ColID => $Value )
            {
                if ( isset ( $this->ColIDs [$ColID] ) )
                {
                    if ( ! empty ( $Value ) || ( 0 === $Value ) )
                    {
                        $Empty = FALSE;
                    }
                    $this->DataSet [$RowID] [$ColID] = $Value;
                }
            }

            $Equivalence = $this->isEquivalent ( $this->DataSet, $RowID );
            
            if ( $FirstRow || ( $Equivalence === FALSE ) )
            {
                $this->EquivalenceClasses [$RowID] = array ();
            }
            else
            {
                $this->EquivalenceClasses [$Equivalence][] = $RowID;
            }
            $FirstRow = FALSE;
            
            if ( $Empty )
            {
                $RowsToRemove [] = $RowID;
            }
        }
        
        foreach ( $RowsToRemove as $RowID )
        {
            unset ( $this->DataSet [$RowID] );
        }

        $this->IsValid = TRUE;
    }
    
    public function computeCuboide ( )
    {
        if ( $this->IsValid )
        {
            $RowsToRemove = array ();
            foreach ( $this->DataSet as $RowID => $Row )
            {
                if ( ! $this->isInCuboide ( $RowID ) )
                {
                    $RowsToRemove [] = $RowID;
                }
            }
            
            foreach ( $RowsToRemove as $RowID )
            {
                unset ( $this->DataSet [$RowID] );
            }
        }
    }

    protected function isInCuboide ( $ConsideredRowID )
    {
        return TRUE;
    }
    
    public function getID ( ) { return $this->ID; }
    public function getDataSet ( ) { return $this->DataSet; }
    public function getRowHeaders ( ) { return $this->RowHeaders; }
    public function getColIDs ( ) { return $this->ColIDs; }
    public function isValid ( ) { return $this->IsValid; }
    
    public function getEquivalenceClasses ( $AsArray = FALSE )
    {
        $Result = '';
        
        if ( $AsArray )
        {
            $Result = $this->EquivalenceClasses;
        }
        else
        {
            $Sep = '{';
            foreach ( $this->EquivalenceClasses as $RowIndex => $Parts )
            {
                $Result .= $Sep . $RowIndex . implode ( '', $Parts );
                $Sep = ',';
            }
            $Result .= '}';
        }
        return $Result;
    }
    
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $MinMax;
    protected $IsValid;
    protected $MaxCols;
    
    protected $EquivalenceClasses; 
}