<?php

class PageAccueil extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Accueil';
	
		$Switch = new Switcher ( $InputData );
	}
} // PageAccueil
