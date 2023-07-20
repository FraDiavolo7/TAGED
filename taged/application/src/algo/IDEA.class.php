<?php

/**
 * Handles the IDEA program running and results
 * @package TAGED\Algo
 */
class IDEA
{
    /**
     * The constructor of the IDEA class.
     *
     * @param string $Name The name of the IDEA file.
     * @param string $Folder The folder where the IDEA file is located.
     */
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
    
    /**
     * Runs the IDEA program and interprets the results.
     *
     * @return array An array representing the interpreted results.
     */
    public function run ()
    {
        $this->prepare ();
        
        $AlgoOpt = " -m" . $this->Max . " -n" . $this->Min;
        
        $Command = "$this->Algorithm $this->FilePath $this->NbAttributes $this->NbTuples $AlgoOpt";
        
        //echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $Command, TRUE ) . "</pre> <br>";
        //echo " <pre>" . print_r ( $Command, TRUE ) . "</pre> <br>";
        
        $ShellResult = shell_exec ( $Command . ' 2>&1' ) . PHP_EOL;
        
        return $this->interpret ( $ShellResult );
    }
    
    /**
     * Prepares the data for IDEA execution by calling various internal methods.
     */
    protected function prepare ()
    {
        $this->prepareRelations ();
        $this->prepareMeasures  ();
        $this->prepareTupleFile ();
        $this->exportMeasures   ();
        $this->exportRelations  ();
        
        $this->computeMinMax ();
    }
    
    /**
     * Prepares the relations data for IDEA execution.
     * Handles anonymization, attribute counting, and value conversion.
     */
    protected function prepareRelations ()
    {
        $this->WIPRelations = $this->InputRelations;

        $this->anonymize ();
        
        $this->countAttributes ();
    }
    
    /**
     * Counts the number of attributes (columns) in the data set.
     */
    protected function countAttributes ()
    {
        $this->NbAttributes = count ( reset ( $this->WIPRelations ) );
    }
    
    /**
     * Anonymizes the attribute values in the relations data.
     */
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
    
    /**
     * Prepares the measures data for IDEA execution.
     * Handles duplicating rows for multiple measures and value conversions.
     */
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
                $Row [$MeasureID] = 100 - intval ( $Value );
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
    
    /**
     * Prepares the tuple file required for IDEA execution.
     */
    protected function prepareTupleFile ()
    {
        $TupleFile  = $this->FilePath . '.nbtuple';
        
        $this->NbTuples = count ( $this->PreparedMeasures );
        
        file_put_contents ( $TupleFile, $this->NbTuples . PHP_EOL );
    }
    
    /**
     * Exports the prepared measures data to a CSV file.
     */
    protected function exportMeasures ()
    {
        $MesPath    = $this->FilePath . '.mes';
        //echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $this->PreparedMeasures, TRUE ) . "</pre> <br>";
        
        Arrays::exportAsCSV ( $this->PreparedMeasures,  ' ', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $MesPath, array (), array (), ';' );
    }
    
    /**
     * Exports the prepared relations data to a CSV file.
     */
    protected function exportRelations ()
    {
        $RelPath    = $this->FilePath . '.rel';
        
        //echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $this->PreparedRelations, TRUE ) . "</pre> <br>";
        Arrays::exportAsCSV ( $this->PreparedRelations, ' ', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $RelPath, array (), array (), ';' );
    }
    
    /**
     * Computes the minimum and maximum values for IDEA computation.
     * If Min and Max values are not set, computes them from the input measures.
     */
    protected function computeMinMax ()
    {
        if ( NULL === $this->Min ) $this->Min = min ( min ( $this->InputMeasures ) );
        if ( NULL === $this->Max ) $this->Max = max ( max ( $this->InputMeasures ) );
    }
    
    /**
     * Interprets the IDEA results from the generated output file.
     *
     * @param string $Results The raw shell output from IDEA execution.
     * @return array An array representing the interpreted results.
     */
    protected function interpret ( $Results )
    {
        $TagedResult = array ();
        $ResultPath    = $this->FilePath . '.cube.emergent';
        
        $Cube = file ( $ResultPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        
        foreach ( $Cube as $CubeEntry )
        {
            $Result = array ();
            $Data = explode ( ' : ', $CubeEntry );
            $CodedRelations = explode ( ' ', $Data [0] );
            $Emergence = explode ( ' ', $Data [1] );
            $EmergenceRatio = 'zero';
            
            if ( $Emergence [0] == 0 )
            {
                if ( $Emergence [1] != 0 )
                {
                    $EmergenceRatio = '&infin;';
                }
            }
            else
            {
                $EmergenceRatio = $Emergence [1] / $Emergence [0];
            }
            
            $Relations = array ();
            $i = 0;
            foreach ( $this->AttributeValues as $Attr => $Corresp )
            {
                $CodedValue = $CodedRelations [$i++];
                $Value = ( $CodedValue == 'ALL' ? 'ALL' : $Corresp [$CodedValue] );
                $Relations [$Attr] = $Value;
            }
            //echo __FILE__ . ':' . __LINE__ . ' - ' . __METHOD__ . " <pre>" . print_r ( $CubeEntry, TRUE ) . "</pre> <br>";
//             echo " <pre>" . print_r ( $CubeEntry, TRUE ) . "</pre> <br>";
//             echo " <pre>" . print_r ( $Relations, TRUE ) . "</pre> <br>";
            
            $Result ['rel'] = $Relations;
            $Result ['ER'] = $EmergenceRatio;
            
            if ( ! empty ( $Result ) )
            {
                $TagedResult [] = $Result;
            }
        }
        
        return $TagedResult;
    }
    
    /**
     * Converts the given attribute value to a numeric representation.
     *
     * @param mixed $Value The attribute value to be converted.
     * @param string $Attribute The name of the attribute.
     * @return mixed The numeric representation of the attribute value.
     */
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
    
    /**
     * Sets the algorithm used by IDEA.
     *
     * @param string $NewValue The name of the algorithm.
     */
    public function setAlgorithm ( $NewValue ) { $this->Algorithm      = $NewValue; }
    
    /**
     * Sets the input measures data for IDEA execution.
     *
     * @param array $NewValue An associative array representing input measures.
     */
    public function setMeasures  ( $NewValue ) { $this->InputMeasures  = $NewValue; }
    
    /**
     * Sets the input relations data for IDEA execution.
     *
     * @param array $NewValue An associative array representing input relations.
     */
    public function setRelations ( $NewValue ) { $this->InputRelations = $NewValue; }
        
    /**
     * Sets the minimum value for IDEA computation.
     *
     * @param int|null $NewValue The minimum value for IDEA computation.
     */
    public function setMin ( $NewValue ) { $this->Min = $NewValue; }
    
    /**
     * Sets the maximum value for IDEA computation.
     *
     * @param int|null $NewValue The maximum value for IDEA computation.
     */
    public function setMax ( $NewValue ) { $this->Max = $NewValue; }
    
    /**
     * @var string The name of the algorithm used by IDEA.
     */
    protected $Algorithm;
    
    /**
     * @var string The full file path of the IDEA file.
     */
    protected $FilePath;
    
    /**
     * @var int|null The minimum value for IDEA computation.
     */
    protected $Min;
    
    /**
     * @var int|null The maximum value for IDEA computation.
     */
    protected $Max;
    
    /**
     * @var int The number of attributes (columns) in the data set.
     */
    protected $NbAttributes;
    
    /**
     * @var int The number of tuples (rows) in the data set.
     */
    protected $NbTuples;
    
    /**
     * @var array The input measures for IDEA.
     */
    protected $InputMeasures;
    
    /**
     * @var array The input relations for IDEA.
     */
    protected $InputRelations;
    
    /**
     * @var array The working in-progress measures for IDEA.
     */
    protected $WIPMeasures;
    
    /**
     * @var array The working in-progress relations for IDEA.
     */
    protected $WIPRelations;
    
    /**
     * @var array The prepared measures for export.
     */
    protected $PreparedMeasures;
    
    /**
     * @var array The prepared relations for export.
     */
    protected $PreparedRelations;
    
    /**
     * @var array The attribute names.
     */
    protected $Attributes;
    
    /**
     * @var array The attribute values with their numeric correspondence.
     */
    protected $AttributeValues;
    
    /**
     * @var array The ignored attribute names.
     */
    protected $AttributeIgnored;
}