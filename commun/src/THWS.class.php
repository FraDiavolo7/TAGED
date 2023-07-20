<?php

/**
 *
 * @package Commun
 */
class THWS extends BasicWS
{
    use Connection;

	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
	}

} // THWS
