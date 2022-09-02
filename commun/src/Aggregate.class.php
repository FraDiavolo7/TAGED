<?php

class Aggregate
{
    protected static $Table = "aggregate";
    protected static $DBClass = "Database";
    
    protected $Data;
    protected $Attributes;
    protected $NbTuples;
    
    /**
     */
    public function __construct ()
    {
        $this->Data = array ();
        $this->Attributes = array ();
        $this->NbTuples = 0;
        $this->fill ();
    }

    /**
     */
    public function __destruct()
    {
        unset ( $this->Data );
        unset ( $this->Attributes );
        unset ( $this->NbTuples );
    }
    
    public function __toString ()
    {
        $String = '';
        
        return $String;
    }
    
    protected function showTableHeader ()
    {
        $Content  = HTML::startTable ();
        $Content .= HTML::startTR ();
        $Content .= HTML::th ( 'Tuple' );
        foreach ( $this->Attributes as $Attr )
        {
            $Content .= HTML::th ( $Attr );
        }
        $Content .= HTML::endTR ();
        return $Content;
    }
    
    protected function showTableFooter ()
    {
        return HTML::endTable ();
    }
    
    protected function showAsTableContent ()
    {
        $Content = '';
        foreach ( $this->Data as $TupleNum => $Data )
        {
            $Content .= HTML::startTR ();
            $Content .= HTML::td ( $TupleNum );
            foreach ( $this->Attributes as $Attr )
            {
                $Value = $Data [$Attr] ?? '&nbsp';
                $Content .= HTML::td ( $Value );
            }
            $Content .= HTML::endTR ();
        }
        return $Content;
    }
    
    public function show ()
    {
        $Content  = HTML::div ( $this->NbTuples . ' tuples', array ( 'class' => 'tuples_nb' ) );
        $Content .= $this->showTableHeader ();
        
        $Content .= $this->showAsTableContent ();

        $Content .= $this->showTableFooter ();
        return $Content;
    }
    
    public function export ( $FilePath, $ExportHeaders = true, $ExportNbTuples = true )
    {
        $TupleFile = $FilePath . '.nbtuple';
        $HeaderFile = $FilePath . '.headers';

        if ( $ExportNbTuples ) file_put_contents ( $TupleFile, $this->NbTuples . PHP_EOL );

        if ( $ExportHeaders ) Arrays::exportAsCSV ( $this->Attributes, ',', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $HeaderFile, array (), array (), ';' );

        Arrays::exportAsCSV ( $this->Data, ',', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $FilePath, array (), array (), ';' );
    }
    
    public function getData ( ) 
    {
        return $this->Data;
    }
    
    public function getNbAttributes ( )
    {
        return count ( $this->Attributes );
    }
    
    public function getAttributes ( )
    {
        return $this->Attributes;
    }
    
    public function getNbTuples ( )
    {
        return $this->NbTuples;
    }
    
    public function getTuple ( $NumTuple ) 
    {
        return $this->Data [$NumTuple] ?? array ();
    }
    
    public function check ( )
    {
        if (    0 == $this->NbTuples     ) throw new Exception ( 'No Tuple'      );
        if ( empty ( $this->Data       ) ) throw new Exception ( 'No Data'       );
        if ( empty ( $this->Attributes ) ) throw new Exception ( 'No Attributes' );
    }
    
    protected function fill ()
    {
        Log::fct_enter ( __METHOD__ );

        $DbClass = static::$DBClass;
        
        $DbClass::execute ( "SELECT * FROM " . static::$Table . ";" );
        
        $Results = $DbClass::getResults ( );
        
        $CurrentTuple = 0;
        
        if ( NULL != $Results )
        {
            foreach ( $Results as $Result )
            {
                foreach ( $Result as $Key => $Value )
                {
                    if ( ! is_numeric ( $Key ) )
                    { 
                        if ( ! in_array ( $Key, $this->Attributes ) ) $this->Attributes [] = $Key;
                         
                        $this->Data [ $CurrentTuple ] [ $Key ] = $Value;
                    }
                }
                $CurrentTuple++;
            }
        }
        
        $this->NbTuples += $CurrentTuple;

        Log::fct_exit ( __METHOD__ );
    }
    
    
}

