<?php


/**
 * Cette classe sert à gérer les sessions.
 * @package Commun
 */
class Session
{
	public static function start ( )
	{
	    session_start();
	}
	
	public static function add ( $Label, $Var )
	{
	    if ( isset ( $_SESSION [ $Label ] ) ) 
	    {
	        unset ( $_SESSION [ $Label ] );
	    } 
	    $_SESSION [ $Label ] = $Var;
	}
	
	public static function get ( $Label )
	{
	    $Var = NULL;
	    if ( isset ( $_SESSION [ $Label ] ) )
	    {
	        $Var = $_SESSION [ $Label ];
	    }
	    return $Var;
	}
}
