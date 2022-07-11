<?php

class WSMatch3 extends TagedWS
{
    const WS_M3_DATA = 'data';
    const WS_M3_SELECTOR = 'm3';
    const WS_M3_DEFAULT = 'nothing';
    const WS_M3_LIST = array (
        'fin' => 'gameOver',
        'step' => 'intermediate',
        'new' => 'newGame'
    );
    
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$this->InputData = ( NULL == $InputData ? $_REQUEST : $InputData );
	}

	/*
	 * MAIN ws function
	 */
	public function serve ()
	{
	    $Action = $this->getM3Selector ();
	    
	    return $this->$Action ();
	}
	
	protected function store ()
	{
	    Log::fct_enter ( __METHOD__ );
	    $RawData = Arrays::getIfSet ( $this->InputData, self::WS_M3_DATA, '{}' );
	    $Data = json_decode ( $RawData, true );
	    
// 	    Log::debug ( __METHOD__ . ' ' . print_r ( $Data, TRUE ) );
	    
	    $Game = M3Game::build ( $Data );
	    
// 	    Log::debug ( __METHOD__ . ' ' . $Game );
	    
	    if ( NULL != $Game ) $Game->save ();
	    Log::fct_exit ( __METHOD__ );
	}

	protected function intermediate ()
	{
	    Log::fct_enter ( __METHOD__ );
	    
	    $this->store ();
	    
	    Log::fct_exit ( __METHOD__ );
	    return '';
	}
	
	protected function gameOver ()
	{
	    Log::fct_enter ( __METHOD__ );
	    
	    $this->store ();
	    
	    Log::fct_exit ( __METHOD__ );
	    return '';
	}
	
	protected function newGame ()
	{
	    Log::fct_enter ( __METHOD__ );

// 	    Log::debug ( __METHOD__ . ' ' . self::WS_M3_DATA . ' ' . print_r ( $this->InputData, TRUE ) );
	    
	    $RawData = Arrays::getIfSet ( $this->InputData, self::WS_M3_DATA, '{}' );
	    $Data = json_decode ( $RawData, true );
	    
// 	    Log::debug ( __METHOD__ . ' ' . print_r ( $Data, TRUE ) );
	    
	    Log::fct_exit ( __METHOD__ );
	    return '';
	}
	
	protected function getM3Selector ()
	{
	    $Selector = Arrays::getIfSet ( $this->InputData, self::WS_M3_SELECTOR, self::WS_M3_DEFAULT );
	    
	    $Action = Arrays::getIfSet ( self::WS_M3_LIST, $Selector, self::WS_M3_DEFAULT );
	    
	    return $Action;
	}
	
	protected $InputData;
} // WSMatch3
