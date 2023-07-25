<?php

/**
 * Classe Date pour gérer les opérations liées à la date.
 *
 * @package Commun
 */
class Date
{
    /**
     * Récupère la date actuelle au format HTML (jj/mm/aa hh:mm:ss).
     *
     * @return string La date au format HTML.
     */
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
	
} // Date