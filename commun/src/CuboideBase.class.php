<?php

abstract class CuboideBase
{
    const CURRENT = '';
    
    public function __construct ( $CuboideID  )
    {
        $this->ID = $CuboideID;
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->IsValid = FALSE;
        $this->EquivalenceClasses = array ();
        
        $this->RowIDsInput = array ();
        $this->RowIDsFiltered = array ();
        $this->RowIDsComputed = array ();
    }

    abstract public function computeCuboide ( );

    protected function logVar ( $Var, $Label, $Show = FALSE, $File = __FILE__, $Line = __LINE__ )
    {
        $this->log ( $Label . ' : \'' . print_r ( $Var, TRUE ) . '\'', $Show, $File, $Line );
    }
    
    protected function log ( $Text, $Show = FALSE, $File = __FILE__, $Line = __LINE__ )
    {
        if ( $this->ID == static::CURRENT ) 
        {
            if ( $Show )
            {
                echo "$File:$Line - $Text<br>";
            }
            else
            {
                Log::log ( $Text );
            }
        }
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
    
    public function getID ( ) { return $this->ID; }
    public function getDataSet ( ) { return $this->DataSet; }
    public function getRowHeaders ( ) { return $this->RowHeaders; }
    public function getColIDs ( ) { return $this->ColIDs; }
    public function isValid ( ) { return $this->IsValid; }

    public function getFilteredIDs ( ) { return $this->RowIDsFiltered; }
    public function getComputedIDs ( ) { return $this->RowIDsComputed; }
    
    public function getRowHeadersFiltered ( ) 
    { 
        $Result = array ();
        foreach ( $this->RowIDsFiltered as $RowID )
        {
            $Result [$RowID] = $this->RowHeaders [$RowID];
        }
        return $Result; 
    }
    
    public function getDataSetFiltered ( )
    {
        $Result = array ();
        foreach ( $this->RowIDsFiltered as $RowID )
        {
            $Result [$RowID] = $this->DataSet [$RowID];
        }
        return $Result;
    }
    
    public function getRowHeadersComputed ( )
    {
        $Result = array ();
        foreach ( $this->RowIDsComputed as $RowID )
        {
            $Result [$RowID] = $this->RowHeaders [$RowID];
        }
        return $Result;
    }
    
    public function getDataSetComputed ( ) 
    { 
        $Result = array ();
        foreach ( $this->RowIDsComputed as $RowID )
        {
            $Result [$RowID] = $this->DataSet [$RowID];
        }
        return $Result; 
    }
    
    public function getEquivalenceClasses ( $AsArray = FALSE, $OnlyFiltered = FALSE )
    {
        $Result = ( $AsArray ? array () : '' );
        
        if ( $AsArray )
        {
            $Result = $this->EquivalenceClasses;
        }
        else
        {
            $Sep = '{';
            foreach ( $this->EquivalenceClasses as $RowIndex => $Parts )
            {
                if ( ! $OnlyFiltered || in_array ( $RowIndex, $this->RowIDsFiltered ) )
                {
                    $Result .= $Sep . $RowIndex . implode ( '', $Parts );
                    $Sep = ',';
                }
            }
            $Result .= '}';
        }
        return $Result;
    }
    
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $IsValid;
    
    protected $RowIDsInput;    // Data as they were entered
    protected $RowIDsFiltered; // Data after SkyLine
    protected $RowIDsComputed; // Data after Taged
    
    protected $EquivalenceClasses; 
}