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
}

