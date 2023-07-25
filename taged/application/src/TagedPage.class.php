<?php

/**
 * Classe de base pour les pages Taged.
 * 
 * @package TAGED
 */
class TagedPage extends THPage
{
    /**
     * Clé pour le sélecteur de page.
     * @var string
     */
    const PAGE_SELECTOR = 'sel';
    
    /**
     * Nom de la page par défaut.
     * @var string
     */
    const PAGE_DEFAULT = 'Accueil';
    
    /**
     * Liste des pages disponibles avec leurs noms affichés.
     * @var array
     */
    const PAGE_LIST = array (
		'Accueil' => 'Accueil',
	    'Match 3' => 'Match3',
	    'Collection Data' => 'CollData',
	    'Hack&apos;n Slash Data' => 'HnSData',
	    'Gestion Aggregations' => 'GestAggregate',
// 	    'Aggregations' => 'ChangeAggregate',
// 	    'Show Aggregations' => 'ShowAggregate'
//	    'Test' => 'Test',
//        'TestCoSky' => 'TestCoSky',
// 	    'Test Accords' => 'TestAccords',
//  	    'Test SkyCube' => 'TestSkyCube',
// 	    'Test SkyCube Emergent' => 'TestSkyCubeEmergent',
//	    'Collection Aggregate' => 'CollAggregate',
	);
    	
    /**
     * Constructeur de la classe TagedPage.
     * @param mixed $InputData Données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		
		$this->SiteTitle = 'TAGED';
		$this->addCSS ( '/css/taged.css' );
	}

	/**
	 * Affiche l'en-tête de la page.
	 */
	public function showPageHeader ()
	{
	    Log::fct_enter ( __METHOD__ );
	    parent::showPageHeader ();
	    
	    HTML::showHeaderDiv ( 
	            HTML::div ( 
	                HTML::div ( $this->SiteTitle, array ( "class" => "sitetitle" ) ) . 
	                HTML::div ( Date::getDateHTML (), array ( "class" => "sitedate" ) )
	                ) .
	            HTML::div (
	                    HTML::div ( HTML::Title ( $this->PageTitle, 1 ), array ( "class" => "pagetitle" ) ) .
	                    Menu::nav ( self::PAGE_LIST )
	                    )
	            );
	    Log::fct_exit ( __METHOD__ );
	}
	    
	/**
	 * Affiche le pied de page de la page.
	 */
	public function showPageFooter ()
	{
	    HTML::showEndBody ();
	}
} // TagedPage
