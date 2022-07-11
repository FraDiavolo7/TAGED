<?php

class TagedWS extends THWS
{
	const WS_SELECTOR = 'sel';
	const WS_DEFAULT = 'none';
	const WS_LIST = array (
	    'none' => 'Default',
	    'm3' => 'Match3'
	);
    	
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
	}

	public static function getSelector ()
	{
	    $Selector = Form::getData ( self::WS_SELECTOR, self::WS_DEFAULT );
	    
	    $Action = Arrays::getIfSet ( self::WS_LIST, $Selector, self::WS_DEFAULT );
	    
	    return $Action;
	}
} // TagedWS
