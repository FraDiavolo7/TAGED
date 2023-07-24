<?php

/**
 * Classe représentant la page de parsing Hack'n Slash.
 *
 * @package TAGED\Pages
 */
class PageHnSParse extends TagedPage
{
    /**
     * Nom de la propriété représentant le nom du fichier à parser.
     */
    const PARSER_FILE = 'parser_file';
    
    /**
     * Nom de la propriété représentant le bouton de soumission pour le parsing.
     */
    const PARSER_SUBMIT = 'parser_submit';
    
    /**
     * Constructeur de la classe PageHnSParse.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Hack&apos;n Slash Parser';
		$this->FileToParse = NULL;
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	/**
	 * Gère les actions en fonction des données d'entrée.
	 *
	 * @param mixed $Data Les données d'entrée pour le traitement.
	 */
	protected function handle ( $Data )
	{
// 	    $this->add ( HTML::div ( print_r ( $Data, true ) ) );
	    
	    $Submit = Form::getData ( self::PARSER_SUBMIT, '', $Data );
	    
	    if ( $Submit != '' )
	    {
	        $this->FileToParse = Form::getFileName ( self::PARSER_FILE );
// 	        $this->add ( HTML::div ( '$this->FileToParse : ' . $this->FileToParse ) );
// 	        $this->add ( HTML::div ( '$_FILES : ' . print_r ( $_FILES, true ) ) );
	        
	        if ( NULL != $this->FileToParse ) 
	        {
	            // 	        $this->add ( HTML::div ( 'File to parse : ' . $this->FileToParse ) );
	            $this->parse ();
	        }
	    }
	    
	    $this->show ();
	}
	
	/**
	 * Affiche le contenu de la page.
	 */
	protected function show ( )
	{
	    $File = HTML::inputFile ( self::PARSER_FILE );
	    $Submit = HTML::submit( self::PARSER_SUBMIT, "Envoyer" );
	    
	    $this->add ( HTML::form ( $File . $Submit, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}
	
	/**
	 * Parse le contenu du fichier spécifié et affiche le résultat du parsing.
	 */
	protected function parse ()
	{
	    $Content = '';
	    
	    try
	    {
	        $TextToParse = file_get_contents ( $this->FileToParse );
	        
	        $Parser = new HnSParser ( $TextToParse );
	        
	        $Parser->parse ();
	        $Content = 'Parsing succeeded<br>' . $Parser;
	    }
	    
	    catch ( Exception $Exc )
	    {
	        $Content = $Exc->getMessage ();
	    }
	    
	    $this->add ( HTML::div ( $Content ) );
	}
	
	/**
	 * Propriété représentant le nom du fichier à parser.
	 *
	 * @var string|null $FileToParse
	 */
	protected $FileToParse;
} // PageHnSParse
