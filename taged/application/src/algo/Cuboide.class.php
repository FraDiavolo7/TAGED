<?php

class Cuboide
{
    const TO_MAX = 'max';
    const TO_MIN = 'min';
    
    const TEST_ON = self::TO_MAX;
    
    
    public function __construct ( $CuboideID, $SkyCube )
    {
        $this->ID = $CuboideID;
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->IsValid = FALSE;
        
        $this->computeDataSet ( $SkyCube );
        
        $this->computeCuboide ( );
        
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
        
        $RowsToRemove = array ();
        
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
                $RowsToRemove [] = $RowID;
            }
        }
        
        foreach ( $RowsToRemove as $RowID )
        {
            unset ( $this->DataSet [$RowID] );
        }
        
        $Transposed = array ();
        foreach ( $this->DataSet as $RowID => $Row )
        {
            foreach ( $Row as $ColID => $Value )
            {
                $Transposed[$ColID][$RowID] = $Value;
            }
        }
        
        foreach ( $Transposed as $ColID => $Col )
        {
            $Function = self::TEST_ON;
            $this->MaxCols [$ColID] = $Function ( $Col );
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
        return $this->isInCuboideBruteForce ( $ConsideredRowID );
    }
    
    protected function isInCuboideBruteForce ( $ConsideredRowID )
    {
        // Compares this Row to each other to test if it is better or not (keep the best)
        // If current Row is on 2 factors better than ConsideredRow then it is not in the SkyLine 
        
        $Current = '';
        
        $NbCols = count ( $this->ColIDs );
        $InSkyLine = FALSE;
        if ( $this->ID == $Current ) echo "ConsideredRowID $ConsideredRowID<br>";
        if ( $this->ID == $Current ) echo "NbCols $NbCols<br>";
        
        $NbBetter = array ();
        
        foreach ( $this->DataSet as $RowID => $Row )
        {
            $NbB = 0;
            foreach ( $Row as $ColID => $Value )
            {
                if ( $Value == $this->MaxCols [$ColID] )
                {
                    ++$NbB;
                }
            }
            if ( $this->ID == $Current ) echo "NbBetter $NbB<br>";
            
            $NbBetter[$RowID] = $NbB;
        }
        
        $MaxBetter = max ( $NbBetter );
        
        if ( $this->ID == $Current ) echo "MaxBetter $MaxBetter<br>";
        if ( $this->ID == $Current ) echo "MaxBetter [$ConsideredRowID] " . $NbBetter [$ConsideredRowID] ."<br>";
        
        if ( $NbBetter [$ConsideredRowID] == $MaxBetter ) $InSkyLine = TRUE;
        
        return $InSkyLine;
    }
    
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $IsValid;
    protected $MaxCols;
}