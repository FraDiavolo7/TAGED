<?php

class CoSky
{
    public function __construct ( $SkyCube, $k = 2 )
    {
        $this->SkyCube = $SkyCube;
        $this->k = $k;
    }
    
    public function run ()
    {
        $this->prepare ();
        
        $Results = '';
        
        return $this->interpret ( $Results );
    }
    
    protected function prepare ()
    {
        $CuboidesIDs  = $this->SkyCube->getCuboideIDs ( FALSE );
        $CuboideLevel = reset ( $CuboidesIDs  );
        $CuboideID    = reset ( $CuboideLevel );
        
        $Cuboide = $this->SkyCube->getCuboide ( $CuboideID );
        $DataSet = $Cuboide->getDataSetFiltered ();
        $ColIDs = $Cuboide->getColIDs ();
        $HeadersCol = array_values ( $this->SkyCube->getColIDs () );
        
        foreach ( $DataSet as $RowID => $Row )
        {
            foreach ( $Row as $ColID => $Value )
            {
                $this->Data [$ColIDs [$ColID]] [$RowID + 1] = $Value;
            }
            $this->Tuples [] = $RowID + 1;
        }
        
        foreach ( $this->Data as $Attr => $Values )
        {
            $this->Ideal   [$Attr] = 100;
            $this->MinMax  [$Attr] = 'min';
            $this->SumAttr [$Attr] = array_sum ( $Values );
        }
    }
    
    /**
     * Do not read, not yet unpublished
     */
    protected function interpret ( $Results )
    {
        foreach ( $this->Tuples as $Tuple )
        {
            $this->SumPSquare [$Tuple] = 0;
        }
        
        $this->SumGini = 0;
        $this->SumIdealSquare = 0;
        
        foreach ( $this->Data as $Attr => $Data )
        {
            $this->SumNSquare [$Attr] = 0;
            foreach ( $Data as $Tuple => $Value )
            {
                $Ni = $Value / $this->SumAttr [$Attr];
                $this->N [$Attr] [$Tuple]  = $Ni;
                $this->SumNSquare [$Attr] += $Ni ** 2;
            }
            
            $this->Gini [$Attr] = 1 - $this->SumNSquare [$Attr];
            $this->SumGini += $this->Gini [$Attr];
        }
        
        foreach ( $this->Data as $Attr => $Data )
        {
            $Compare = $this->MinMax [$Attr];
            foreach ( $Data as $Tuple => $Value )
            {
                $this->W [$Attr] [$Tuple]  = $this->Gini [$Attr] / $this->SumGini;
                $this->P [$Attr] [$Tuple]  = $this->W [$Attr] [$Tuple] * $this->N [$Attr] [$Tuple];
                $this->SumPSquare [$Tuple] += $this->P [$Attr] [$Tuple] ** 2;
                $this->Ideal [$Attr] = $Compare ( $this->P [$Attr] [$Tuple], $this->Ideal [$Attr] );
            }
        }
        
        foreach ( $this->Ideal as $Attr => $Ideal )
        {
            $this->SumIdealSquare += $Ideal ** 2;
        }
        
        $this->SqrtSumIdealSquare = sqrt ( $this->SumIdealSquare );

        foreach ( $this->Tuples as $Tuple )
        {
            $this->SqrtSumPSquare [$Tuple] = sqrt ( $this->SumPSquare [$Tuple] );
        }
            
        foreach ( $this->Tuples as $Tuple )
        {
            $SumIP = 0;
            foreach ( $this->Data as $Attr => $Data )
            {
                $SumIP += $this->Ideal [$Attr] * $this->P [$Attr] [$Tuple];
            }
            $this->Score [$Tuple] = $SumIP / ( $this->SqrtSumPSquare [$Tuple] * $this->SqrtSumIdealSquare );
        }
    }
    
    protected function sortScores ( $EntryA, $EntryB )
    {
        $ValueA = $EntryA ['Score'] ?? 0;
        $ValueB = $EntryB ['Score'] ?? 0;
        
        return ( $EntryA == $EntryB ? 0 : ( $EntryA < $EntryB ? 1 : -1 ) );
    }
    
    public function getScores ( )
    {
        $Scores = array ();
        
        foreach ( $this->Tuples as $Tuple )
        {
            $Scores [$Tuple] ['RowId'] = $Tuple;
            
            foreach ( $this->Data as $Attr => $Data )
            {
                $Scores [$Tuple] [$Attr] = $Data [$Tuple];
            }
            $Scores [$Tuple] ['Score'] = $this->Score [$Tuple];
        }
        
        usort ( $Scores, array ( $this, 'sortScores' ) );

        return array_slice ( $Scores, 0, $this->k );
    }

    public function getData ( )
    {
        return $this->Data;
    }
    public function getN                  () { return $this->N                 ; }
    public function getGini               () { return $this->Gini              ; }
    public function getW                  () { return $this->W                 ; }
    public function getP                  () { return $this->P                 ; }
    public function getIdeal              () { return $this->Ideal             ; }
    public function getSumAttr            () { return $this->SumAttr           ; }
    public function getSumNSquare         () { return $this->SumNSquare        ; }
    public function getSumPSquare         () { return $this->SumPSquare        ; }
    public function getSumIdealSquare     () { return $this->SumIdealSquare    ; }
    public function getSumGini            () { return $this->SumGini           ; }
    public function getSqrtSumPSquare     () { return $this->SqrtSumPSquare    ; }
    public function getSqrtSumIdealSquare () { return $this->SqrtSumIdealSquare; }
    
    protected $k;
    protected $SkyCube;
    protected $Data;
    protected $Tuples;
    protected $MinMax;
    protected $N;
    protected $Gini;
    protected $W;
    protected $P;
    protected $Ideal;
    protected $Score;

    protected $SumAttr; // by Attribute
    protected $SumNSquare; // by Attribute
    protected $SumPSquare; // by Tuple
    protected $SumIdealSquare; // scalar
    protected $SumGini; // scalar
    
    protected $SqrtSumPSquare; // by Tuple
    protected $SqrtSumIdealSquare; // scalar
    
}