<?php

/** 
 * 
 */
abstract class Arrays  
{
    public static function echoArr ( $Array, $Label = '', $LineLabel = '', $LineEnd = '' )
    {
        echo self::arrToString ( $Array, $Label, $LineLabel, $LineEnd );
    }
    
    public static function arrToString ( $Array, $Label = '', $LineLabel = '', $LineEnd = '' )
    {
        $Result = '';
        $Empty = '';
        if ( empty ( $Array ) ) $Empty = ' is empty';
            
        $Result .= $Label . $Empty . $LineEnd;
        foreach ( $Array as $Key => $Value )
        {
            $Result .= $LineLabel . ' ' . $Key . ' = ' . $Value . $LineEnd;
        }
        return $Result;
    }
    
    public static function getIfSet ( $Array, $Label, $Default = "" )
    {
        $Result = $Default;
        
        if ( isset ( $Array [ $Label ] ) )
        {
            $Result = $Array [ $Label ];
        }

        return $Result;
    }
    
    public static function getOrCrash ( $Array, $Label, $ExceptionText )
    {
        $Result = NULL;
        
        if ( isset ( $Array [ $Label ] ) )
        {
            $Result = $Array [ $Label ];
        }
        else 
        {
            throw new Exception ( $ExceptionText );
        }
        
        return $Result;
        
    }
    
    const EXPORT_COLUMN_FROM_DATA = 'export_col_data';
    const EXPORT_COLUMN_AS_NUM = 'export_col_num';
    const EXPORT_COLUMN_NO_HEADER = 'export_col_not';

    const EXPORT_ROW_FROM_DATA = 'export_row_data';
    const EXPORT_ROW_ADD_NUM = 'export_row_add';
    const EXPORT_ROW_NO_HEADER = 'export_row_not';
    
    
    
    private static function extractColumns ( $InputData, $ColumnHeader = EXPORT_COLUMN_FROM_DATA, $IgnoreColumns = array () )
    {
        $Columns = array ();
        $UnfilteredCols = array ();

        if ( ( $ColumnHeader == EXPORT_COLUMN_FROM_DATA ) ||
            ( $ColumnHeader == EXPORT_COLUMN_AS_NUM ) ||
            ( $ColumnHeader == EXPORT_COLUMN_NO_HEADER ) )
        {
            // Using first item's columns
            $ColumnsTmp = array_keys ( reset ( $InputData ) );
            
            if ( $ColumnHeader == EXPORT_COLUMN_FROM_DATA )
            {
                foreach ( $TrashCols as $Key )
                {
                    $UnfilteredCols [ $Key ] = $Key;
                }
            }
            else
            {
                $UnfilteredCols = $ColumnsTmp;
            }
            
        }
        
        else if ( is_array ( $ColumnHeader ) )
        {
            $UnfilteredCols = $ColumnHeader;
        }
        
        else
        {
            Log::error ( 'Invalid ColumnHeader parameter' );
            $Columns = false;
        }
        
        if ( is_array ( $Columns ) )
        {
            foreach ( $UnfilteredCols as $Label => $Key )
            {
                if ( ! in_array ( $Key, $IgnoreColumns ) )
                {
                    $Columns [ $Label ] = $Key;
                }
            }
        }
        
        return $Columns;
    }
    
    /**
     * @brief Exports the given $InputData as a CSV text
     * Row Header can be either :
     *  - EXPORT_ROW_FROM_DATA : Using the data index as row header 
     *  - EXPORT_ROW_ADD_NUM : A number will be added as row header
     *  - EXPORT_ROW_NO_HEADER : Nothing is presented as row header
     *  - an array : A list of indexes to use (index shall match Input Data)
     *  - a string : The name of the row to present as row header
     * Column Header can be either :
     *  - EXPORT_COLUMN_FROM_DATA : Using the data index as column header 
     *  - EXPORT_COLUMN_AS_NUM : Using a number as column header
     *  - EXPORT_COLUMN_NO_HEADER : Nothing is presented as column header 
     *  - an array : A list of column names indexed by output label (column names shall match Input Data)
     * @param array $InputData The data to export
     * @param string $Separator The field separator
     * @param string|array $ColumnHeader How the column header should be handled
     * @param string|array $RowHeader How the column header should be handled
     * @param sring $FilePath The path of the file to export to (if any )
     * @param array $IgnoreColums List of columns to ignore in presentation
     * @param array $IgnoreRows List of rows to ignore in presentation
     * @return Error message ('' if no problem)
     */
    public static function exportAsCSV ( $InputData, $Separator = ',', $ColumnHeader = EXPORT_COLUMN_FROM_DATA, $RowHeader = EXPORT_ROW_FROM_DATA, $FilePath = '', $IgnoreColumns = array (), $IgnoreRows = array () )
    {
        $Result = '';
        $Continue = true;

        // Array of input column name indexed by the output label
        $Columns = self::extractColumns ( $InputData, $ColumnHeader, $IgnoreColumns );
                
        if ( is_array ( $Columns ) )
        {
            $FalseIndex = 0;
            foreach ( $InputData as $Index => $Row )
            {
                $Line = '';
                
                if ( $RowHeader == EXPORT_ROW_FROM_DATA )
                {
                    $Line .= $Index . $Separator;
                }
                
                else if ( $RowHeader == EXPORT_ROW_ADD_NUM )
                {
                    $Line .= $FalseIndex . $Separator;
                    ++$FalseIndex;
                }
                
                else if ( is_array ( $RowHeader ) )
                {
                    $Line .= $RowHeader [ $Index ] . $Separator;
                }
                
                else if ( is_string ( $RowHeader ) && ( $RowHeader != EXPORT_ROW_NO_HEADER ) )
                {
                    $Line .= $Row [ $RowHeader ] . $Separator;
                }
                
                foreach ( $Columns as $Label => $Key )
                {
                    if ( isset ( $Row [ $Key ] ) )
                    {
                        $Line .= $Row [ $Key ];
                    }
                    $Line .= $Separator;
                }
                $Line .= '\n';
             
                file_put_contents ( $FilePath, $Line, FILE_APPEND );
                
            }
        }
                
        return $Result;
    }
}

