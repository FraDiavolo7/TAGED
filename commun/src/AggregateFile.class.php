<?php

/**
 *
 * @package Commun
 */
class AggregateFile extends Aggregate
{
    protected static $Folder = AGGREGATE_FOLDER_REQUESTS;
    
    protected $File;
    protected $DBTable;
    protected $ClassDB;
    
    public function __construct ( $ClassDB, $File, $Table, $AsNumerics = FALSE )
    {
        $this->File = $File;
        $this->DBTable = $Table;
        $this->ClassDB = $ClassDB;
        
        parent::__construct ( $AsNumerics );
    }
    
    protected function retrieveData ()
    {
        Log::fct_enter ( __METHOD__ );
        
        $DbClass = $this->ClassDB;
        
        $Request = $this->getRequest ( );
        
        $DbClass::execute ( $Request );
         
        $Results = $DbClass::getResults ( );
        
        Log::fct_exit ( __METHOD__ );
        
        return $Results;
    }
    
    public function getRequest ( )
    {
        $Request = "SELECT * FROM " . $this->DBTable . ";";
        
        if ( empty ( $this->File ) )
        {
            Log::debug ( __METHOD__ . " File not provided, using Table name " . $this->DBTable );
        }
        else if ( !file_exists ( $this->File ) )
        {
            Log::error ( __METHOD__ . " File " . $this->File . " is empty, not bothering..." );
        }
        else
        {
            $TmpRequest = file_get_contents ( $this->File );
            
            if ( "" != $TmpRequest ) $Request = $TmpRequest;
        }
        
        return $Request;
    }
    
    public function setRequest ( $NewRequest )
    {
        file_put_contents ( $this->File, $NewRequest );
    }
}

 