<?php

/**
 *
 * @package Commun
 */
class System {

    public static function isDirEmpty ( $Dir ) 
    {
        $Result = FALSE;
        if ( is_readable ( $Dir ) )
        {
            $Result = ( count ( scandir ( $Dir ) ) == 2 );
        }
        return $Result;
    }


//--------------
} // System
