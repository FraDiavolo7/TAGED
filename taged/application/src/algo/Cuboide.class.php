<?php

/**
 * Handling all Cuboide related computation
 * @package TAGED\Algo
 */
class Cuboide extends CuboideBase
{
    const TO_MAX = 'max';
    const TO_MIN = 'min';
    
    const TEST_ON = self::TO_MAX;
    
    const CURRENT = '';
    
    public function __construct ( $CuboideID, $RawDataSet, $RawRowHeaders, $RawColIDs, $MinMax = self::TO_MAX )
    {
        parent::__construct ( $CuboideID );

        $this->RowHeaders = $RawRowHeaders;
        $this->MinMax = $MinMax;
        
        $this->computeDataSet ( $RawDataSet, $RawColIDs );
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
                    $this->RowIDsInput [$RowID] = $RowID;
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
            unset ( $this->RowIDsInput [$RowID] );
            unset ( $this->DataSet [$RowID] );
        }

        $this->IsValid = TRUE;
    }
    
    public function computeCuboide ( )
    {
        if ( $this->IsValid )
        {
            foreach ( $this->RowIDsInput as $RowID )
            {
                if ( $this->isInCuboide ( $RowID ) )
                {
                    $this->RowIDsFiltered [$RowID] = $RowID;
                }
            }
        }
    }

    protected function isInCuboide ( $ConsideredRowID )
    {
        return TRUE;
    }
    
//     public function getID ( ) { return $this->ID; }
//     public function getDataSet ( ) { return $this->DataSet; }
//     public function getRowHeaders ( ) { return $this->RowHeaders; }
//     public function getColIDs ( ) { return $this->ColIDs; }
//     public function isValid ( ) { return $this->IsValid; }

//     public function getFilteredIDs ( ) { return $this->RowIDsFiltered; }
//     public function getComputedIDs ( ) { return $this->RowIDsComputed; }
    
//     public function getDataSetFiltered ( ) 
//     { 
//         $Result = array ();
//         foreach ( $this->RowIDsFiltered as $RowID )
//         {
//             $Result [$RowID] = $this->DataSet [$RowID];
//         }
//         return $Result; 
//     }
    
//     public function getDataSetComputed ( ) 
//     { 
//         $Result = array ();
//         foreach ( $this->RowIDsComputed as $RowID )
//         {
//             $Result [$RowID] = $this->DataSet [$RowID];
//         }
//         return $Result; 
//     }
    
//     public function getEquivalenceClasses ( $AsArray = FALSE )
//     {
//         $Result = ( $AsArray ? array () : '' );
        
//         if ( $AsArray )
//         {
//             $Result = $this->EquivalenceClasses;
//         }
//         else
//         {
//             $Sep = '{';
//             foreach ( $this->EquivalenceClasses as $RowIndex => $Parts )
//             {
//                 $Result .= $Sep . $RowIndex . implode ( '', $Parts );
//                 $Sep = ',';
//             }
//             $Result .= '}';
//         }
//         return $Result;
//     }
    
//     protected $ID; //** Cuboide ID is the combinaison of ColIDs
//     protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
//     protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
//     protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $MinMax;
//     protected $IsValid;
    
//     protected $RowIDsInput;    // Data as they were entered
//     protected $RowIDsFiltered; // Data after SkyLine
//     protected $RowIDsComputed; // Data after Taged
    
//     protected $EquivalenceClasses; 
}