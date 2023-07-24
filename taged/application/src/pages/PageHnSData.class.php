<?php

/**
 * Classe représentant la page de données Hack'n Slash.
 *
 * @package TAGED\Pages
 */
class PageHnSData extends TagedPage
{
    /**
     * Constructeur de la classe PageHnSData.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
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
