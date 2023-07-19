<?php

/**
 * @package Deprecated
 */
 class DisplayCuboide
{
    const TO_MAX = 'max';
    const TO_MIN = 'min';
    
    const TEST_ON = self::TO_MAX;
    
    public function __construct ( $CuboideID, $SkyCube, $MinMax = self::TO_MAX )
    {
        $this->ID = $CuboideID;
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->MinMax = $MinMax;
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
    
    protected function getLaTeXHeader ( $ColHeader )
    {
            $LaTeXHeader = strtoupper ( substr ( $ColHeader, 0, 1 ) );
           
        return $LaTeXHeader;
    }
    
    public function toLaTeX ( $Node, $X, $Y )
    {
        
        $TableHeaders = '';
        $TableRows = '';
        $FirstRow = TRUE;
        $TableHeaders = '\texttt{Id} ';
        $Cols = '';
        foreach ( $this->DataSet as $RowID => $Row )
        {
            $TableRow = '';
            $Sep = '& ';
            $ID = $Row ['RowID'] ?? $RowID;
            $TableRow .= '$' .  $ID . '$ ';
            foreach ( $Row as $ColID => $Value )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= $Sep . '\texttt{' . strtoupper ( substr ( $this->ColIDs [$ColID], 0, 1 ) )  . '} ';
                    $Cols .= 'c';
                    
                }
                $TableRow .= $Sep . '$' . $Value . '$ ';
            }
            
            $TableRows .= '    ' . $TableRow . '\\\\' . PHP_EOL;
            $FirstRow = FALSE;
        }
        
        $Result = '';
        $Result .= '\node (' . $Node . ') at (' . $X . 'pt, ' . $Y . 'pt)' . PHP_EOL;
        $Result .= '{' . PHP_EOL;
        $Result .= '    \begin{tabular}{c|'. $Cols . '}' . PHP_EOL;
        $Result .= '    \toprule' . PHP_EOL;
//        $Result .= '    \texttt{Id} & \texttt{R} & \texttt{G} & \texttt{S} \\' . PHP_EOL;
        $Result .= '    ' . $TableHeaders . '\\\\' . PHP_EOL;
        $Result .= '    \midrule' . PHP_EOL;
        $Result .= $TableRows;
//         $Result .= '    $2$ & $5$  & $85$ & $7$ \\' . PHP_EOL;
//         $Result .= '    $3$ & $10$ & $50$ & $7$ \\' . PHP_EOL;
//         $Result .= '    $4$ & $10$ & $85$ & $5$ \\' . PHP_EOL;
//         $Result .= '    $5$ & $10$ & $50$ & $7$ \\' . PHP_EOL;
        $Result .= '    \bottomrule' . PHP_EOL;
        $Result .= '    \end{tabular}' . PHP_EOL;
        $Result .= '};' . PHP_EOL;
        
        return $Result;
    }
    
    
    protected function isInCuboide ( $ConsideredRowID )
    {
        return TRUE;
    }
    
    protected $ID; //** Cuboide ID is the combinaison of ColIDs
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $MinMax;
    protected $IsValid;
    protected $MaxCols;
}