<?php

/**
 * Classe représentant la page de données de collection.
 *
 * @package TAGED\Pages
 */
class PageCollData extends TagedPage
{
    /**
     * Constructeur de la classe PageCollData.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Collection Data';
	
		$Switch = new Switcher ( $InputData );
		
		$Table = new CollTable ();
		
		$this->add ( HTML::div ( $Table->show (), array ( 'class' => 'coll_data' ) ) );
	}
} // PageCollParse
