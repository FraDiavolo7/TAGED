<?php

class IDEA
{
    public function __construct ( $Name, $Folder )
    {
        $this->FilePath   = $Folder . $Name;
        $this->Algorithm = '';
        $this->Min = NULL;
        $this->Max = NULL;
        $this->NbAttributes = 0;
        $this->NbTuples = 0;
        
        $this->InputMeasures = array ();
        $this->InputRelations = array ();
        
        $this->WIPMeasures = array ();
        $this->WIPRelations = array ();
        
        $this->PreparedMeasures = array ();
        $this->PreparedRelations = array ();
        
        $this->Attributes = array ();
        $this->AttributeValues = array ();
        $this->AttributeIgnored = array ();
    }
    
    public function run ()
    {
        $this->prepare ();
        
        $AlgoOpt = " -m$this->Min -n$this->Max";
        
        $Command = "$this->Algorithm $this->FilePath $this->NbAttributes $this->NbTuples $AlgoOpt";
        
        echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $Command, TRUE ) . "</pre> <br>";
        
        $ShellResult = shell_exec ( $Command . ' 2>&1' ) . PHP_EOL;
        
        $this->interpret ( $ShellResult );
    }
    
    protected function prepare ()
    {
        $this->prepareRelations ();
        $this->prepareMeasures  ();
        $this->prepareTupleFile ();
        $this->exportMeasures   ();
        $this->exportRelations  ();
        
        $this->computeMinMax ();
    }
    
    protected function prepareRelations ()
    {
        $this->WIPRelations = $this->InputRelations;

        $this->anonymize ();
    }

    
    protected function anonymize ()
    {
        $TmpRelations = array ();
        foreach ( $this->WIPRelations as $RowID => $Row )
        {
            foreach ( $Row as $Attr => $Value )
            {
                if ( ! is_numeric ( $Attr ) )
                {
                    if ( ! in_array ( $Attr, $this->Attributes ) ) $this->Attributes [] = $Attr;
                    
                    $TmpRelations [ $RowID ] [ $Attr ] = $this->convertToNumerics ( $Value, $Attr );
                }
            }
        }
        $this->WIPRelations = $TmpRelations;
    }
    
    protected function prepareMeasures ()
    {
        $this->WIPMeasures = $this->InputMeasures;
        
        foreach ( $this->WIPMeasures as $RowID => $Row )
        {
            $NbAttr = 0;
            $NbZeros = 0;
            foreach ( $Row as $MeasureID => $Value )
            {
                ++$NbAttr;
                if ( $Value === 0 )
                {
                    ++$NbZeros;
                }
            }
            
            $NbCopies = $NbAttr - $NbZeros;
            
            if ( $NbCopies > 1 )
            {
                $TmpMes = array ();
                $TmpRel = array ();
                $Current = 0;
                foreach ( $Row as $MeasureID => $Value )
                {
                    for ( $i = 0 ; $i < $NbCopies ; ++$i )
                    {
                        $NewValue = 0;
                        if ( ( $Value != 0 ) && ( $Current == $i ) )
                        {
                            $NewValue = $Value;
                        }
                        $TmpMes [$i][$MeasureID] = $NewValue;
                    }
                    ++$Current;
                }
                
                foreach ( $TmpMes as $NewRow )
                {
                    $this->PreparedMeasures [] = $NewRow;
                    $this->PreparedRelations [] = $this->WIPRelations [$RowID];
                }
            }
            else
            {
                $this->PreparedMeasures [] = $Row;
                $this->PreparedRelations [] = $this->WIPRelations [$RowID];
            }
        }
    }
    
    protected function prepareTupleFile ()
    {
        $TupleFile  = $this->FilePath . '.nbtuple';
        
        $this->NbTuples = count ( $this->PreparedMeasures );
        
        file_put_contents ( $TupleFile, $this->NbTuples . PHP_EOL );
    }
    
    protected function exportMeasures ()
    {
        $MesPath    = $this->FilePath . '.mes';
        echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $this->PreparedMeasures, TRUE ) . "</pre> <br>";
        
        Arrays::exportAsCSV ( $this->PreparedMeasures,  ' ', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $MesPath, array (), array (), ';' );
    }
    
    protected function exportRelations ()
    {
        $RelPath    = $this->FilePath . '.rel';
        
        echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $this->PreparedRelations, TRUE ) . "</pre> <br>";
        Arrays::exportAsCSV ( $this->PreparedRelations, ' ', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $RelPath, array (), array (), ';' );
    }
    
    protected function computeMinMax ()
    {
        if ( NULL === $this->Min ) $this->Min = min ( min ( $this->PreparedMeasures ) );
        if ( NULL === $this->Max ) $this->Max = max ( max ( $this->PreparedMeasures ) );
    }
    
    protected function interpret ( $Results )
    {
        echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $Results, TRUE ) . "</pre> <br>";
    }
    

    protected function convertToNumerics ( $Value, $Attribute )
    {
        $Result = $Value;
        
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
        
        return $Result;
    }
    
    public function setAlgorithm ( $NewValue ) { $this->Algorithm      = $NewValue; }
    public function setMeasures  ( $NewValue ) { $this->InputMeasures  = $NewValue; }
    public function setRelations ( $NewValue ) { $this->InputRelations = $NewValue; }
    public function setMin ( $NewValue ) { $this->Min = $NewValue; }
    public function setMax ( $NewValue ) { $this->Max = $NewValue; }
    
    protected $Algorithm;
    protected $FilePath;
    protected $Min;
    protected $Max;
    protected $NbAttributes; 
    protected $NbTuples; 

    protected $InputMeasures;
    protected $InputRelations;

    protected $WIPMeasures;
    protected $WIPRelations;
    
    protected $PreparedMeasures;
    protected $PreparedRelations;

    protected $Attributes;
    protected $AttributeValues;
    protected $AttributeIgnored;
}