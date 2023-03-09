<?php

class Cuboide
{
    public function __construct ( $CuboideID, $SkyCube )
    {
        $this->ID = $CuboideID;
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->IsValid = FALSE;
        
        $this->computeDataSet ( $SkyCube );
        
//         echo "ID " . $this->ID . " ";
        
//         if ( $this->IsValid )
//         {
//             echo "Valid <br>\n";
//             echo "ColIDs " . HTML::tableFull ( $this->ColIDs, array ( 'border' => '1' ) ) .  "<br>\n";
//             echo "DataSet " . HTML::tableFull ( $this->DataSet, array ( 'border' => '1' ) ) .  "<br>\n";
//             //echo "RowHeaders " . HTML::tableFull ( $this->RowHeaders, array ( 'border' => '1' ) ) .  "<br>\n";
//         }
//         else
//         {
//             echo "Not Valid <br>\n";
//         }
    }
    
    public function __toString ()
    {
        $String  = HTML::div ( $this->ID . ' (' . ( $this->IsValid ? 'V' : 'I' ) . ')' );
        $String .= HTML::div ( HTML::tableFull ( $this->DataSet, array ( 'border' => '1' ) ) );
        
        return HTML::div ( $String, array ( 'class' => 'cuboide' ) );
    }
    
    public function toHTML ()
    {
        $HTML = HTML::div ( HTML::div ( $this->ID ) . HTML::div ( '(' . ( $this->IsValid ? 'V' : 'I' ) . ')' ), array ( 'class' => 'title' ) );
        
        $TableHeaders = '';
        $TableRows = '';
        $FirstRow = TRUE;
        foreach ( $this->DataSet as $RowID => $Row )
        {
            $TableRow = '';
            foreach ( $this->RowHeaders[$RowID] as $RowHeader => $HeaderValue )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= HTML::th ( $RowHeader, array ( 'class' => 'row_header ' . strtolower ( $RowHeader ) ) );
                }
                $TableRow .= HTML::td ( $HeaderValue, array ( 'class' => 'row_header ' . strtolower ( $RowHeader ) ) );
            }
            foreach ( $Row as $ColID => $Value )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= HTML::th ( $ColID, array ( 'class' => 'row_value' ) );
                }
                $TableRow .= HTML::td ( $Value, array ( 'class' => 'row_value' ) );
            }

            $TableRows .= HTML::tr ( $TableRow );
            $FirstRow = FALSE;
        }
        
        $HTML .= HTML::table (
            HTML::tr ( $TableHeaders, array ( 'class' => 'headers' ) ) .
            $TableRows,
            array ( 'class' => 'cuboide' )
            );
        return HTML::div ( $HTML, array ( 'class' => 'cuboide' ) );
    }
    
    protected function computeDataSet ( $SkyCube )
    {
        $RawDataSet = $SkyCube->getDataSet ();
        $this->RowHeaders = $SkyCube->getRowHeaders ();
        $RawColIDs = $SkyCube->getColIDs ();
        $CuboideCols = str_split ( $this->ID );
        
        foreach ( $CuboideCols as $ColID )
        {
            $this->ColIDs [$ColID] = $RawColIDs [$ColID];
        }
        
        $RowToRemove = array ();
        
        foreach ( $RawDataSet as $RowID => $Row )
        {
            $Empty = TRUE;
            foreach ( $Row as $ColID => $Value )
            {
                if ( isset ( $this->ColIDs [$ColID] ) )
                {
                    if ( '' !== $Value )
                    {
                        $Empty = FALSE;
                    }
                    $this->DataSet [$RowID] [$ColID] = $Value;
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
        
        $this->IsValid = TRUE;
    }
    
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $IsValid;
}