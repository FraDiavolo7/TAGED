<?php

/**
 * Cette classe représente une page de test de la classe CoSky.
 * 
 * @package TAGED\Pages\Test
 */
class PageTestCoSky extends TagedPage
{
    /**
     * Initialise l'objet PageTestCoSky.
     *
     * @param mixed $InputData Les données d'entrée pour initialiser la page. Si null, les données $_REQUEST seront utilisées.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'TestCoSky';
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}
	
	/**
	 * Gère la soumission du formulaire et traite les données d'entrée.
	 *
	 * @param mixed $Data Les données d'entrée à traiter.
	 */
	protected function handle ( $Data )
	{
	    $CoSky = new CoSky ( NULL );
	    
	    $CoSky->run ();
	    
	    $Data = $CoSky->getData ();
	    
	    $ToShow = array ();
	    
	    foreach ( $Data as $Attr => $Entries )
	    {
	        foreach ( $Entries as $Tuple => $Value )
	        {
	            $ToShow [$Tuple] [$Attr] = $Value;
	        }
	    }
	    
	    foreach ( $ToShow as $Tuple => $Row )
	    {
	        $ToShow [$Tuple] ['Score'] = $CoSky->getScore ($Tuple);
	    }

	    $this->add ( HTML::tableFull ( $ToShow, array ( 'class' => 'cuboide'), TRUE ) );
	}
	
} // PageTestCoSky
