<?php

class DeepSky
{
    public function __construct ( )
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
    }
    
    protected function interpret ( $Results )
    {
        $DeepSkyResult = array ();
        
        return $DeepSkyResult;
    }
}