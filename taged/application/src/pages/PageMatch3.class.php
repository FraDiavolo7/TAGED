<?php

/**
 * Classe représentant une redirection vers le jeu Match 3.
 * 
 * @package TAGED\Pages
 */
class PageMatch3 extends TagedPage
{
    /**
     * Constructeur de la classe PageMatch3.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$this->redirect ( '/match3' );

	}
} // PageMatch3
