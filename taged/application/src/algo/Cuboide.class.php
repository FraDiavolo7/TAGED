<?php

class Cuboide
{
    const TO_MAX = 'max';
    const TO_MIN = 'min';
    
    const TEST_ON = self::TO_MAX;
    
    const CURRENT = 'BC';
    
    public function __construct ( $CuboideID, $RawDataSet, $RawRowHeaders, $RawColIDs, $MinMax = self::TO_MAX )
    {
        $this->ID = $CuboideID;
        $this->DataSet = array ();
        $this->RowHeaders = $RawRowHeaders;
        $this->ColIDs = array ();
        $this->MinMax = $MinMax;
        $this->IsValid = FALSE;
        
        $this->computeDataSet ( $RawDataSet, $RawColIDs );
        
        $this->computeCuboide ( );
    }
    
    protected function computeDataSet ( $RawDataSet, $RawColIDs )
    {
        $CuboideCols = str_split ( $this->ID );
        
        foreach ( $CuboideCols as $ColID )
        {
            $this->ColIDs [$ColID] = $RawColIDs [$ColID];
        }
        
        $RowsToRemove = array ();
        
        foreach ( $RawDataSet as $RowID => $Row )
        {
            $Empty = TRUE;
            
            foreach ( $Row as $ColID => $Value )
            {
                if ( isset ( $this->ColIDs [$ColID] ) )
                {
//                    if ( '' !== $Value )
                    if ( ! empty ( $Value ) || ( 0 === $Value ) )
                    {
                        $Empty = FALSE;
                    }
                    $this->DataSet [$RowID] [$ColID] = $Value;
                }
            }
//             if ( $this->ID == self::CURRENT ) echo '$Row ' . print_r ( $Row, TRUE ) . "<br>";
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
    
    protected function computeCuboide ( )
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
    
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $MinMax;
    protected $IsValid;
    protected $MaxCols;
}