<?php

/**
 * Classe représentant la page de collection d'agrégation.
 *
 * @package TAGED\Pages
 */
class PageCollAggregate extends TagedPage
{
    /**
     * Constructeur de la classe PageCollAggregate.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Collection Aggregate';
	
		$Switch = new Switcher ( $InputData );
		
		$Aggregate = new AggCollRequete ( FALSE );
		
		$this->add ( HTML::div ( $Aggregate->show (), array ( 'class' => 'coll_data' ) ) );
	}
} // PageCollAggregate
