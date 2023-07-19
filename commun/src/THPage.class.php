<?php

/**
 *
 * @package Commun
 */
class THPage extends BasicPage
{
    use Connection;

    const TAGED_URL = '/';
    const LINK_LIST = array (
		'TAGED' => '/'
    	);
    	
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		Session::start ();
		
		$this->addCSS ( '/css/th.css' );
	}

	protected function links ()
	{
	    $LinksContent = "";
	    
	    foreach ( self::LINK_LIST as $Label => $Link )
	    {
	        $LinksContent .= HTML::link ( $Link, $Label, true );
	    }
	    
	    return HTML::div ( $LinksContent, array ( "class" => "links") );
	}
	
	public function showPageHeader ()
	{
	    Log::fct_enter ( __METHOD__ );
	    HTML::showStartBody ();
	    
	    HTML::showDiv ( 
            $this->links () . 
            self::connectionForm (),
	        array ( "class" => "taged_header" )
	        );
	    Log::fct_exit ( __METHOD__ );
	}
	    
	public function showPageFooter ()
	{
	    HTML::showEndBody ();
	}
} // Page
