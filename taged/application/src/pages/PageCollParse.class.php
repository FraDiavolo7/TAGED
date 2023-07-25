<?php

/**
 * Classe représentant la page de parsing de collection.
 *
 * @package TAGED\Pages
 */
class PageCollParse extends TagedPage
{
    /**
     * Constante représentant le nom du champ de fichier de parsing.
     */
    const PARSER_FILE = 'parser_file';
    
    /**
     * Constante représentant le nom du champ de soumission du formulaire.
     */
    const PARSER_SUBMIT = 'parser_submit';
    
    /**
     * Constructeur de la classe PageCollParse.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Collection Parser';
		$this->FileToParse = NULL;
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

	/**
	 * Gère le traitement des données d'entrée.
	 *
	 * @param mixed $Data Les données d'entrée pour la page.
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
	 * Effectue l'opération de parsing.
	 */
	protected function parse ()
	{
	    $Content = '';
	    
	    try
	    {
	        $TextToParse = file_get_contents ( $this->FileToParse );
	        
	        $Parser = new CollParser ( $TextToParse );
	        
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
	 * Le fichier à parser.
	 *
	 * @var string|null
	 */
	protected $FileToParse;
} // PageCollParse
