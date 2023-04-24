<?php

class CuboideEmergent 
{
    const CURRENT = 'ABC';
    public function __construct ( $ColIDs, $Cuboide1, $Cuboide2 )
    {
        $this->Cuboide1 = $Cuboide1;
        $this->Cuboide2 = $Cuboide2;
        
        $this->ID = $Cuboide1->getID ();
        $this->RowHeaders = $Cuboide1->getRowHeaders ();
        $this->ColIDs = $ColIDs;
        
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
        
        foreach ( $RowIDs as $RowID )
        {
            foreach ( $this->ColIDs as $ColID => $ColHeader )
            {
                 if ( $this->ID == self::CURRENT ) echo '$RowID ' . print_r ( $RowID, TRUE ) . "<br>";
                 if ( $this->ID == self::CURRENT ) echo '$ColID ' . print_r ( $ColID, TRUE ) . "<br>";
//                 if ( $this->ID == self::CURRENT ) echo '$this->ColIDsC1 [$ColID] \'' . print_r ( $this->ColIDsC1 [$ColID], TRUE ) . "'<br>";
//                 if ( $this->ID == self::CURRENT ) echo '$this->ColIDsC2 [$ColID] \'' . print_r ( $this->ColIDsC2 [$ColID], TRUE ) . "'<br>";
//                 if ( $this->ID == self::CURRENT ) echo '$DataSet1 [$RowID] [$this->ColIDsC1 [$ColID]] \'' . print_r ( $DataSet1 [$RowID] [$this->ColIDsC1 [$ColID]], TRUE ) . "'<br>";
//                 if ( $this->ID == self::CURRENT ) echo '$DataSet1 [$RowID] [$this->ColIDsC2 [$ColID]] \'' . print_r ( $DataSet1 [$RowID] [$this->ColIDsC2 [$ColID]], TRUE ) . "'<br>";
                if ( $this->ID == self::CURRENT ) echo '$DataSet1 [$RowID]  \'' . print_r ( $DataSet1 [$RowID] , TRUE ) . "'<br>";
                if ( $this->ID == self::CURRENT ) echo '$DataSet2 [$RowID]  \'' . print_r ( $DataSet2 [$RowID] , TRUE ) . "'<br>";
                $ColID1 = $this->ColIDsC1 [$ColID] ?? 123456789;
                $ColID2 = $this->ColIDsC2 [$ColID] ?? 123456789;
                
                $Val1 = $DataSet1 [$RowID] [$ColID1] ?? '';
                $Val2 = $DataSet2 [$RowID] [$ColID2] ?? '';
                
                echo '$Val1 ' . $Val1 . ' = $DataSet1 [$RowID ' . $RowID . '] [$ColID1 ' . $ColID1 . ' ' . $ColID . '] ?? "";' . "<br>" ;
                echo '$Val2 ' . $Val2 . ' = $DataSet2 [$RowID ' . $RowID . '] [$ColID2 ' . $ColID2 . ' ' . $ColID . '] ?? "";' . "<br>" ;
                $TmpDataSet [$RowID] [$ColID] = ( $Val1 != '' ? $Val1 : ( $Val2 != '' ? $Val2 : '' ) );
            }
        }
        
        $this->DataSet = $TmpDataSet;
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
    
    public function getID ( ) { return $this->ID; }
    public function getDataSet ( ) { return $this->DataSet; }
    public function getRowHeaders ( ) { return $this->RowHeaders; }
    public function getColIDs ( ) { return $this->ColIDs; }
    public function isValid ( ) { return $this->IsValid; }
    
    public function getEquivalenceClasses ( $AsArray = FALSE )
    {
        $Result = ( $AsArray ? array () : '' ); 
        return $Result;
    }
    
    protected $Cuboide1;
    protected $Cuboide2;
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $ColIDsC1; //** Table indexed by ColID of Cuboide1 ColIDs
    protected $ColIDsC2; //** Table indexed by ColID of Cuboide2 ColIDs
    protected $IsValid;
}

//Log::setDebug ( __FILE__ );
