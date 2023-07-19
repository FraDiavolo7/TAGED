<?php

/**
 * Class handling the list of available algorithms 
 * @package TAGED
 */
class Algo
{

    const FOLDER = '/home/taged/data/aggregates/algo/'; // Folder full path to the algorithms executables
    
    /**
     * @brief Generates the list of available algorithms
     * @return unknown[]
     */
    public static function generateList ( )
    {
        $List = array ();
        $Files = scandir ( self::FOLDER );
        
        foreach ( $Files as $File )
        {
            if ( ! Strings::compareSameSize ( $File, '.' ) )
            {
                $List [self::FOLDER . $File] = $File;
            }
        }

        return $List;
    } // generateList ( )
} // Algo


