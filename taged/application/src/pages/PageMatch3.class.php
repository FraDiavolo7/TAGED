<?php

class PageMatch3 extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Match 3';
	
		$Switch = new Switcher ( $InputData );

		$this->addJS ( "http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" );
		$this->addJS ( "wade_1.5.js" );
        $this->addJS ( "wade.ifx_1.0.js" );
    	$this->addJS ( "wade.particles_1.0.1.js" );
		
		$this->addJScode ( "$(document).ready(function()
        {
           wade.init('app.js');
        });" );

		$this->add ( HTML::div ( '', array ( 'id' => 'wade_main_div', 'width' => "800", 'height' => "600", 'tabindex' => "1" ) ) );
		$this->add ( HTML::div ( '.', array ( 'id' => 'chromeFix' ) ) );
		$this->add ( HTML::div ( '.', array ( 'id' => 'chromeFix2' ) ) );

	}
} // PageCollParse
