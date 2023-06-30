<?php

class CoSky
{
    public function __construct ( $SkyCube )
    {
    }
    
    public function run ()
    {
        $this->prepare ();
        
        $Results = '';
        
        return $this->interpret ( $Results );
    }
    
    protected function prepare ()
    {
        for ( $Attr = 0; $Attr < 5 ; ++$Attr )
        {
            $this->MinMax [$Attr] = ( $Attr % 2 ? 'min' : 'max' );
            $this->Ideal [$Attr] = ( $Attr % 2 ? 1000 : 0 );
            
            for ( $Tuple = 0; $Tuple < 10 ; ++$Tuple )
            {
                if ( ! isset ( $this->Tuples [$Tuple] ) ) $this->Tuples [$Tuple] = $Tuple;
                $Value = rand ( 5, 10 );
                $this->Data [$Attr] [$Tuple] = $Value;
                $this->SumAttr [$Attr] += $Value;
            }
        }
    }
    
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
    
    public function getScore ( $Tuple )
    {
        return $this->Score  [$Tuple];
    }
    
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