<?php

class Aggregate
{
    protected static $Table = "aggregate";
    protected static $DBClass = "Database";
    
    protected $Data;
    protected $Attributes;
    protected $NbTuples;
    protected $AttributeValues;
    protected $AttributeIgnored;
    protected $AsNumerics;
    
    /**
     */
    public function __construct ( $AsNumerics = FALSE )
    {
        $this->Data = array ();
        $this->Attributes = array ();
        $this->NbTuples = 0;
        $this->AttributeValues = array ();
        $this->AttributeIgnored = array ();
        $this->AsNumerics = $AsNumerics;
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
    
    public function export ( $FilePath, $RelationCols, $MeasureCols, $ExportNbTuples = true )
    {
        $TupleFile = $FilePath . '.nbtuple';
        $AttrValues = $FilePath . '.attr.';
        $RelPath = $FilePath . '.rel';
        $MesPath = $FilePath . '.mes';
        
        if ( $this->AsNumerics )
        {
            foreach ( $this->AttributeValues as $Attr => $Values )
            {
                $AttrValuesFile = $AttrValues . $Attr;
                
                if ( file_exists ( $AttrValuesFile ) )
                {
                    unlink ( $AttrValuesFile );
                }

                if ( ! isset ( $this->AttributeIgnored [$Attr] ) )
                {
                    foreach ( $Values as $Key => $Val )
                    {
                        file_put_contents ( $AttrValuesFile, "$Key=$Val" . PHP_EOL, FILE_APPEND );
                    }
                }
            }
        }
        
        

        if ( $ExportNbTuples ) file_put_contents ( $TupleFile, $this->NbTuples . PHP_EOL );
       
        Arrays::exportAsCSV ( $this->Data, ' ', $RelationCols, Arrays::EXPORT_ROW_NO_HEADER, $RelPath, array (), array (), ';' );

        Arrays::exportAsCSV ( $this->Data, ' ', $MeasureCols, Arrays::EXPORT_ROW_NO_HEADER, $MesPath, array (), array (), ';' );
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
    
    public function getAttributeValues ( )
    {
        return $this->AttributeValues;
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
    
    protected function convertToNumerics ( $Value, $Attribute )
    {
        $Result = $Value;
        
        if ( $this->AsNumerics )
        {
            $Attr = rtrim ( $Attribute, "0123456789" );
            if ( ! isset ( $this->AttributeValues [$Attr] ) ) 
            {
                if ( is_numeric ( $Value ) )
                {
                    $this->AttributeIgnored [$Attr] = TRUE;
                }
                else
                {
                    $this->AttributeValues [$Attr] = array ();
                    $Result = 1;
                }
            }
            elseif ( ! isset ( $this->AttributeIgnored [$Attr] ) )
            {
                $Result = array_search ( $Value, $this->AttributeValues [$Attr] );
                if ( FALSE === $Result )
                {
                    $Result = count ( $this->AttributeValues [$Attr] ) + 1;
                }
            }
            
            $this->AttributeValues [$Attr] [$Result] = $Value; 
        }
        
        return $Result;
    }
    
    protected function retrieveData ()
    {
        Log::fct_enter ( __METHOD__ );
        $DbClass = static::$DBClass;
        
        $DbClass::execute ( "SELECT * FROM " . static::$Table . ";" );
        
        $Results = $DbClass::getResults ( );
        
        Log::fct_exit ( __METHOD__ );
        
        return $Results;
    }
    
    protected function fill ()
    {
        Log::fct_enter ( __METHOD__ );

        $Results = $this->retrieveData ( );
        
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
                         
                        $this->Data [ $CurrentTuple ] [ $Key ] = $this->convertToNumerics ( $Value, $Key );
                    }
                }
                $CurrentTuple++;
            }
        }
        
//        print_r ( $this->AttributeValues );
        
        $this->NbTuples += $CurrentTuple;

        Log::fct_exit ( __METHOD__ );
    }
    
    
}

