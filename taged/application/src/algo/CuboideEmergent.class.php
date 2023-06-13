<?php

class CuboideEmergent extends CuboideBase
{
    const CURRENT = '';
    public function __construct ( $ColIDs, $Cuboide1, $Cuboide2 )
    {
        $this->Cuboide1 = $Cuboide1;
        $this->Cuboide2 = $Cuboide2;
        
        $this->ID = $Cuboide1->getID ();
        $this->RowHeaders = $Cuboide1->getRowHeaders ();
        $this->ColIDs = $ColIDs;
        $this->EquivalenceClasses = array ();
        
        $this->RowIDsInput = array ();
        $this->RowIDsFiltered = array ();
        $this->RowIDsComputed = array ();
        $this->mergeColumns ( );
        
        $this->mergeDataSets ( );
        $this->IsValid = TRUE;
    }
    
    protected function mergeColumns ( )
    {
        Log::fct_enter ( __METHOD__ );
        $Cols1 = $this->Cuboide1->getColIDs ();
        $Cols2 = $this->Cuboide2->getColIDs ();
        
        $ColsToRemove = array ();
        
        foreach ( $this->ColIDs as $ColID => $ColHeader )
        {
            $C1Key = array_search ( $ColHeader, $Cols1 );
            $C2Key = array_search ( $ColHeader, $Cols2 );
            
            $C1Found = ( FALSE !== $C1Key );
            $C2Found = ( FALSE !== $C2Key );
            
            if ( $C1Found && $C2Found )
            {
                $this->ColIDsC1 [$ColID] = $C1Key;
                $this->ColIDsC2 [$ColID] = $C2Key;
            }
            elseif ( $C1Found )
            {
                $this->ColIDsC1 [$ColID] = $C1Key;
            }
            elseif ( $C2Found )
            {
                $this->ColIDsC2 [$ColID] = $C2Key;
            }
            else 
            {
                $ColsToRemove [] = $ColID;
            }
        }

        foreach ( $ColsToRemove as $ColID )
        {
            unset ( $this->ColIDs [$ColID] );
        }
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function mergeDataSets ()
    {
        Log::fct_enter ( __METHOD__ );
        $DataSet1 = $this->Cuboide1->getDataSet ();
        $DataSet2 = $this->Cuboide2->getDataSet ();
        
        $RowIDs = array_merge ( array_keys ( $DataSet1 ), array_keys ( $DataSet2 ) ); 
        
        $TmpDataSet = array ();
        $FilteredDataSet = array ();
        
        $RowIDsFiltered1 = $this->Cuboide1->getFilteredIDs ();
        $RowIDsFiltered2 = $this->Cuboide2->getFilteredIDs ();
        
        foreach ( $RowIDs as $RowID )
        {
            $InF1 = in_array ( $RowID, $RowIDsFiltered1 );
            $InF2 = in_array ( $RowID, $RowIDsFiltered2 );
            
            if ( $InF1 || $InF2 ) $this->RowIDsFiltered [] = $RowID;
            
            foreach ( $this->ColIDs as $ColID => $ColHeader )
            {
//                 $this->logVar ( $RowID, '$RowID', TRUE, __FILE__, __LINE__ );
//                 $this->logVar ( $ColID, '$ColID', TRUE, __FILE__, __LINE__ );
//                 $this->logVar ( $this->ColIDsC1 [$ColID], '$this->ColIDsC1 [$ColID]', TRUE, __FILE__, __LINE__ );
//                 $this->logVar ( $this->ColIDsC2 [$ColID], '$this->ColIDsC2 [$ColID]', TRUE, __FILE__, __LINE__ );
//                 $this->logVar ( $DataSet1 [$RowID] [$this->ColIDsC1 [$ColID]], '$DataSet1 [$RowID] [$this->ColIDsC1 [$ColID]]', TRUE, __FILE__, __LINE__ );
//                 $this->logVar ( $DataSet2 [$RowID] [$this->ColIDsC2 [$ColID]], '$DataSet2 [$RowID] [$this->ColIDsC2 [$ColID]]', TRUE, __FILE__, __LINE__ );
//                 $this->logVar ( $DataSet1 [$RowID], '$DataSet1 [$RowID]', TRUE, __FILE__, __LINE__ );
//                 $this->logVar ( $DataSet2 [$RowID], '$DataSet2 [$RowID]', TRUE, __FILE__, __LINE__ );

                $ColID1 = $this->ColIDsC1 [$ColID] ?? 123456789;
                $ColID2 = $this->ColIDsC2 [$ColID] ?? 123456789;
                
                $Val1 = $DataSet1 [$RowID] [$ColID1] ?? '';
                $Val2 = $DataSet2 [$RowID] [$ColID2] ?? '';
                
//                 echo __METHOD__ . ' ' . $this->ID . " $RowID $ColID '$Val1' '$Val2'<br>";
                
//                 $this->log ( '$Val1 ' . $Val1 . ' = $DataSet1 [$RowID ' . $RowID . '] [$ColID1 ' . $ColID1 . ' ' . $ColID . '] ?? "";', TRUE, __FILE__, __LINE__ );
//                 $this->log ( '$Val2 ' . $Val2 . ' = $DataSet2 [$RowID ' . $RowID . '] [$ColID2 ' . $ColID2 . ' ' . $ColID . '] ?? "";', TRUE, __FILE__, __LINE__ );

                $TmpDataSet [$RowID] [$ColID] = ( $Val1 != '' ? $Val1 : ( $Val2 != '' ? $Val2 : '' ) );
                
                if ( $InF1 || $InF2 ) $FilteredDataSet [$RowID] [$ColID] = ( $InF1 && $Val1 != '' ? $Val1 : ( $InF2 && $Val2 != '' ? $Val2 : '' ) );
            }
        }
        
        $this->DataSet = $TmpDataSet;
        $this->FilteredDataSet = $FilteredDataSet;
        
        //$this->RowIDsFiltered = array_unique ( array_merge ( $RowIDsFiltered1, $RowIDsFiltered2 ), SORT_NUMERIC );
        
        $FirstRow = TRUE;
        
        foreach ( $this->DataSet as $RowID => $Row )
        {
            $Equivalence = $this->isEquivalent ( $this->DataSet, $RowID );
            if ( $FirstRow || ( $Equivalence === FALSE ) )
            {
                $this->EquivalenceClasses [$RowID] = array ();
            }
            else
            {
                $this->EquivalenceClasses [$Equivalence][] = $RowID;

                if ( FALSE !== ( $Key = array_search  ( $RowID, $this->RowIDsFiltered ) ) ) unset ( $this->RowIDsFiltered [$Key] );
            }
            $FirstRow = FALSE;
        }
        
//         $this->logVar ( $this->getEquivalenceClasses ( FALSE ), 'EquivalenceClasses ', TRUE, __FILE__, __LINE__ );
//         $this->logVar ( $this->Cuboide1->getEquivalenceClasses ( FALSE ), 'EquivalenceClasses1', TRUE, __FILE__, __LINE__ );
//         $this->logVar ( $this->Cuboide2->getEquivalenceClasses ( FALSE ), 'EquivalenceClasses2', TRUE, __FILE__, __LINE__ );
        
        Log::fct_exit ( __METHOD__ );
    }
    
    public function computeCuboide ( )
    {
        Log::fct_enter ( __METHOD__ );
        // Merging
        // ColIDs => remove columns neither in Cuboide1 nor Cuboide2
        // Data Set => add all data from both Cuboides
        
        Log::fct_exit ( __METHOD__ );
    }
    
    
    public function getDataSetFiltered ( )
    {
        return $this->FilteredDataSet;
    }
    
//     public function getID ( ) { return $this->ID; }
//     public function getDataSet ( ) { return $this->DataSet; }
//     public function getRowHeaders ( ) { return $this->RowHeaders; }
//     public function getColIDs ( ) { return $this->ColIDs; }
//     public function isValid ( ) { return $this->IsValid; }
    
//     public function getEquivalenceClasses ( $AsArray = FALSE )
//     {
//         $Result = ( $AsArray ? array () : '' ); 
//         return $Result;
//     }
    
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
    
    protected $FilteredDataSet;

    protected $Cuboide1;
    protected $Cuboide2;
//     protected $ID; //** Cuboide ID is the combinaison of ColIDs
//     protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
//     protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
//     protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $ColIDsC1; //** Table indexed by ColID of Cuboide1 ColIDs
    protected $ColIDsC2; //** Table indexed by ColID of Cuboide2 ColIDs
//     protected $IsValid;
    
//     protected $RowIDsInput;    // Data as they were entered
//     protected $RowIDsFiltered; // Data after SkyLine
//     protected $RowIDsComputed; // Data after Taged
    
//     protected $EquivalenceClasses; 
}

//Log::setDebug ( __FILE__ );
