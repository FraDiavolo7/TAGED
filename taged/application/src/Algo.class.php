<?php

class Algo
{
    const FOLDER = '/home/taged/data/aggregates/algo/';
    
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

    }
}


