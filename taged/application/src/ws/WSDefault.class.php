<?php

/**
 * Default WebService (doing nothing)
 * @package TAGED\WebServices
 */
class WSDefault extends TagedWS
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );

	}

} // WSDefault
