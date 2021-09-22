<?php

class TagedPage extends THPage
{
	const PAGE_SELECTOR = 'sel';
	const PAGE_DEFAULT = 'Accueil';
	const PAGE_LIST = array (
		'Accueil' => 'Accueil',
		'Replay Parser' => 'Parse'
	);
    	
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		
		$this->SiteTitle = 'TAGED';
		$this->addCSS ( 'css/taged.css' );
	}

	public function showPageHeader ()
	{
	    Log::fct_enter ( __METHOD__ );
	    parent::showPageHeader ();
	    
	    HTML::showHeaderDiv ( 
	            HTML::div ( $this->SiteTitle, array ( "class" => "sitetitle" ) ) .
	            HTML::div (
	                    HTML::div ( HTML::Title ( $this->PageTitle, 1 ), array ( "class" => "pagetitle" ) ) .
	                    Menu::nav ( self::PAGE_LIST )
	                    )
	            );
	    Log::fct_exit ( __METHOD__ );
	}
	    
	public function showPageFooter ()
	{
	    HTML::showEndBody ();
	}
} // FetePage