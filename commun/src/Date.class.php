<?php

/**
 *
 * @package Commun
 */
class Date
{
    public static function getDateHTML ( )
	{
		$Result = '01/01/70 00:00:00';

		$DateInfo = getdate ();
		
		$Result = sprintf ( '%02d/%02d/%02d %02d:%02d:%02d', 
		    $DateInfo ['mday'], 
		    $DateInfo ['mon'], 
		    substr ( $DateInfo ['year'], 2, 2), 
		    $DateInfo ['hours'], 
		    $DateInfo ['minutes'], 
		    $DateInfo ['seconds'] );
		
		return $Result;
	}
	
}