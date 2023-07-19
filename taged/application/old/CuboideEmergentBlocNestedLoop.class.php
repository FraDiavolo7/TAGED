<?php

/**
 * @deprecated
 * @package Deprecated
 */
class CuboideEmergent extends Cuboide
{
    const CUBOIDE = 'CuboideBlocNestedLoop';
    
    protected function computeDataSet ( $RawDataSet, $RawColIDs )
    {
        $this->ColIDs = $RawColIDs;
        $Columns1 = array ();
        $Columns2 = array ();
        $Ignore = array ();
        
        foreach ( $RawColIDs as $ColID )
        {
            $LastChar = substr ( $ColID, -1 );
            $Col2 = substr_replace ( $ColID, '2', -1 );
            
            if ( ! in_array ( $ColID, $Ignore ) )
            {
                $Columns1 [] = $ColID;
                if ( ( $LastChar == '1' ) && ( isset ( $RawColIDs [$Col2] ) ) )
                {
                    $Columns2 [] = $Col2;
                    $Ignore [] = $Col2;
                }
                else
                {
                    $Columns2 [] = $ColID;
                }
            }
        }
        
        $this->Cuboide1 = new Cuboide ( $this->ID, $RawDataSet, $this->RowHeaders, $Columns1, $this->MinMax );
        $this->Cuboide2 = new Cuboide ( $this->ID, $RawDataSet, $this->RowHeaders, $Columns2, $this->MinMax );
        
        $this->IsValid = TRUE;
    }

    protected function mergeColumns ( )
    {
        $Cols1 = $this->Cuboide1->getColIDs ();
        $Cols2 = $this->Cuboide2->getColIDs ();
        
        $ColsToRemove = array ();
        
        foreach ( $this->ColIDs as $ColID => $ColHeader )
        {
            if ( ! isset ( $Cols1 [$ColID] ) && ! isset ( $Cols1 [$ColID] ) )
            {
                $ColsToRemove [] = $ColID;
            }
        }
        
        foreach ( $ColsToRemove as $ColID )
        {
            unset ( $this->ColIDs [$ColID] );
        }
    }
    
    protected function mergeDataSets ()
    {
        $DataSet1 = $this->Cuboide1->getDataSet ();
        $DataSet2 = $this->Cuboide2->getDataSet ();
        
        $TmpDataSet = array ();
        
        foreach ( $this->DataSet as $RowID => $Row )
        {
            foreach ( $this->ColIDs as $ColID => $ColHeader )
            {
                $TmpDataSet [$RowID] [$ColID] = $DataSet1 [$RowID] [$ColID] ?? $DataSet1 [$RowID] [$ColID] ?? '';
            }
        }
        
        $this->DataSet = $TmpDataSet;
    }
    
    public function computeCuboide ( )
    {
        // Merging
        // ColIDs => remove columns neither in Cuboide1 nor Cuboide2
        // Data Set => add all data from both Cuboides
        
        $this->mergeColumns ( );
        
        $this->mergeDataSets ( );
    }
    
    public function getID ( ) { return $this->ID; }
    
    public function getDataSet ( ) 
    { 
        return $this->DataSet; 
    }
    
    public function getRowHeaders ( ) { return $this->RowHeaders; }
    public function getColIDs ( ) { return $this->ColIDs; }
    public function isValid ( ) { return $this->IsValid; }

    protected $Cuboide1;
    protected $Cuboide2;
}