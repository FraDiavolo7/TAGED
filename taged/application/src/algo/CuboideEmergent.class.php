<?php

class CuboideEmergent 
{
    public function __construct ( $ColIDs, $Cuboide1, $Cuboide2 )
    {
        $this->Cuboide1 = $Cuboide1;
        $this->Cuboide2 = $Cuboide2;
        
        $this->ID = $Cuboide1->getID ();
        $this->RowHeaders = $Cuboide1->getRowHeaders ();
        $this->ColIDs = $ColIDs;
        
        $this->mergeColumns ( );
        
        $this->mergeDataSets ( );
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
            
            if ( $C1Found )
            {
                $this->ColIDsC1 [$ColID] = $C1Key;
            }
            
            if ( $C2Found )
            {
                $this->ColIDsC2 [$ColID] = $C2Key;
            }
            
            if ( ! $C1Found && ! $C2Found )
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
                $TmpDataSet [$RowID] [$ColID] = $DataSet1 [$RowID] [$this->ColIDsC1 [$ColID]] ?? $DataSet1 [$RowID] [$this->ColIDsC2 [$ColID]] ?? '';
            }
        }
        
        $this->DataSet = $TmpDataSet;
        Log::fct_exit ( __METHOD__ );
    }
    
    protected function computeCuboide ( )
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
    
    protected $Cuboide1;
    protected $Cuboide2;
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $ColIDsC1; //** Table indexed by ColID of Cuboide1 ColIDs
    protected $ColIDsC2; //** Table indexed by ColID of Cuboide2 ColIDs
}

Log::setDebug ( __FILE__ );
