<?php

class PageMaintenance extends BasicPage
{
	const PAGE_SELECTOR = 'sel';
	const PAGE_DEFAULT = 'Maintenance';
	const PAGE_LIST = array (
		'Maintenance' => 'Maintenance'
	);
	
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		
		$this->SiteTitle = 'TAGED';
		$this->addCSS ( 'css/taged.css' );
		
		$Switch = new Switcher ( $InputData ); 
	}

	public function showPageHeader ()
	{
		HTML::showStartBody ();
		HTML::showHeaderDiv ( 
			HTML::div ( $this->SiteTitle ) .
			HTML::div ( 
				HTML::Title ( $this->PageTitle, 1 ) 
			)
		);
	}
	
	public function showPageFooter ()
	{
		HTML::showEndBody ();
	}
}
