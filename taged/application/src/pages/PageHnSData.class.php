<?php

/**
 *
 * @package TAGED\Pages
 */
class PageHnSData extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Hack&apos;n Slash Data';
	
		$Switch = new Switcher ( $InputData );
		
		$Table = new HnSTable ();
		
		$this->add ( HTML::div ( $Table->show (), array ( 'class' => 'hns_data' ) ) );
	}
} // PageHnSData
