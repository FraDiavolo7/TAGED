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
    
    
    
    private static function extractColumns ( $InputData, $ColumnHeader = self::EXPORT_COLUMN_FROM_DATA, $IgnoreColumns = array () )
    {
        $Columns = array ();
        $UnfilteredCols = array ();
//         HTML::showVar ( '$InputData', $InputData );
//          HTML::showVar ( '$ColumnHeader', $ColumnHeader );
//         HTML::showVar ( '$IgnoreColumns', $IgnoreColumns );
        
        if ( ( $ColumnHeader == self::EXPORT_COLUMN_FROM_DATA ) ||
            ( $ColumnHeader == self::EXPORT_COLUMN_AS_NUM ) ||
            ( $ColumnHeader == self::EXPORT_COLUMN_NO_HEADER ) )
        {
            // Using first item's columns
            $TrashCols = array_keys ( reset ( $InputData ) );
//             HTML::showVar ( '$TrashCols', $TrashCols );
            
            if ( $ColumnHeader == self::EXPORT_COLUMN_FROM_DATA )
            {
                foreach ( $TrashCols as $Key )
                {
                    $UnfilteredCols [ $Key ] = $Key;
                }
            }
            else
            {
                $UnfilteredCols = $TrashCols;
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
        
//         HTML::showVar ( '$UnfilteredCols', $UnfilteredCols );
        
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
//          HTML::showVar ( '$Columns', $Columns );
        
        return $Columns;
    }
    
    private static function replaceIfNeeded ( $String, $Needle, $ReplaceBy = '' )
    {
        $Result = $String;
        if ( '' != $ReplaceBy )
        {
            $Result = str_replace ( $Needle, $ReplaceBy, $String );
            //echo "$Result = str_replace ( $Needle, $ReplaceBy, $String )" . PHP_EOL;
        }
        return $Result;
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
    public static function exportAsCSV ( $InputData, $Separator = ',', $ColumnHeader = self::EXPORT_COLUMN_FROM_DATA, $RowHeader = self::EXPORT_ROW_FROM_DATA, $FilePath = '', $IgnoreColumns = array (), $IgnoreRows = array (), $ReplaceSep = '' )
    {
//         echo $FilePath . print_r ( $InputData, true ) . PHP_EOL;
        $Result = '';
        $Continue = true;

        // Array of input column name indexed by the output label
        $Columns = self::extractColumns ( $InputData, $ColumnHeader, $IgnoreColumns );
                
//         echo $FilePath . print_r ( $Columns, true ) . PHP_EOL;
        if ( is_array ( $Columns ) )
        {
            if ( $ColumnHeader != self::EXPORT_COLUMN_NO_HEADER ) 
            {
                $Line = '';
                if ( $RowHeader != self::EXPORT_ROW_NO_HEADER )
                {
                    $Line .= 'index' . $Separator;
                }
                foreach ( $Columns as $Label => $Key )
                {
                    $Line .= self::replaceIfNeeded ( $Label, $Separator, $ReplaceSep );
                    $Line .= $Separator;
                }
                
                $Line .= PHP_EOL;
                
                if ( '' != $FilePath )
                {
                    file_put_contents ( $FilePath, $Line, FILE_APPEND );
                }
                else
                {
                    $Result .= $Line;
                }
            }
                
            $FalseIndex = 0;
            foreach ( $InputData as $Index => $Row )
            {
                $Line = '';
                
                if ( ! in_array ( $Index, $IgnoreRows ) )
                {
                            
                    if ( $RowHeader == self::EXPORT_ROW_FROM_DATA )
                    {
                        $Line .= self::replaceIfNeeded ( $Index, $Separator, $ReplaceSep ) . $Separator;
                    }
                    
                    else if ( $RowHeader == self::EXPORT_ROW_ADD_NUM )
                    {
                        $Line .= self::replaceIfNeeded ( $FalseIndex, $Separator, $ReplaceSep ) . $Separator;
                        ++$FalseIndex;
                    }
                    
                    else if ( is_array ( $RowHeader ) )
                    {
                        $Line .= self::replaceIfNeeded ( $RowHeader [ $Index ], $Separator, $ReplaceSep ) . $Separator;
                    }
                    
                    else if ( is_string ( $RowHeader ) && ( $RowHeader != self::EXPORT_ROW_NO_HEADER ) )
                    {
                        $Line .= self::replaceIfNeeded ( $Row [ $RowHeader ], $Separator, $ReplaceSep ) . $Separator;
                    }
                    
                    foreach ( $Columns as $Label => $Key )
                    {
                        if ( isset ( $Row [ $Key ] ) )
                        {
                            $Line .= self::replaceIfNeeded ( $Row [ $Key ], $Separator, $ReplaceSep );
                        }
                        $Line .= $Separator;
                    }
                    $Line .= PHP_EOL;
                 
                    if ( '' != $FilePath )
                    {
                        file_put_contents ( $FilePath, $Line, FILE_APPEND );
                    }
                    else
                    {
                        $Result .= $Line;
                    }
                }
            }
        }
                
        return $Result;
    }
    
    public static function getCSVLine ( $CSVFile, $SearchValue, $Column = 0, $MaxLength = 1000, $Separator = ',', $IgnoreTitles = TRUE )
    {
        $Row = array ();
        
        if ( ( $File = fopen ( $CSVFile, "r" ) ) !== FALSE )
        {
            while ( ( $Line = fgetcsv ( $File, $MaxLength, $Separator) ) !== FALSE )
            {
                if ( count ( $Line ) > $Column )
                {
                    if ( $Line [$Column] == $SearchValue )
                    {
                        $Row = $Line;
                        break;
                    }
                }
//                 else
//                 {
//                     echo 'pas assez de donnees ' . print_r ( $Line, FALSE ) . PHP_EOL;
//                 }
            }
            
            fclose ( $File );
        }
//         else
//         {
//             echo 'file not found';
//         }
        
        return $Row;
    }
    
    /**
     * Same behavior as array_merge_recursive, but numeric keys are considered as identical
     * @return Array
     */
    public static function arrayMergeRecursive () 
    {
        $Arrays = func_get_args();
        $Base = array_shift ( $Arrays );
        
        foreach ( $Arrays as $Array ) 
        {
            reset ( $Base ); //important
            while ( list ( $Key, $Value ) = @each ( $Array ) ) 
            {
                if ( is_array ( $Value ) && @is_array ( $Base [$Key] ) ) 
                {
                    $Base [$Key] = static::arrayMergeRecursive ( $Base [$Key], $Value );
                } 
                else 
                {
                    $Base [$Key] = $Value;
                }
            }
        }
        
        return $Base;
    }
}

