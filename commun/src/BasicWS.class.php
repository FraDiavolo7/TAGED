<?php

/**
 *
 * @package Commun
 */
abstract class BasicWS
{
	/*
	 * SHALL be called
	 */
	public function __construct ( $InputData = NULL )
	{
	}

	/*
	 * SHALL be called
	 */
    public function __destruct ()
	{
	}
	
    /*
     * MAIN ws function
     */
    public function serve ()
    {
        return 'Unknown WS';
    }

    /*
     * Function used by children as default
     */
    protected function nothing ()
    {
        return '';
    }
    
}
