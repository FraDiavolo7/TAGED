<?php

class PageParseHnS extends TagedPage
{
    const PARSER_FILE = 'parser_file';
    const PARSER_SUBMIT = 'parser_submit';
    
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Parser';
		$this->FileToParse = NULL;
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}

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
	
	protected function show ( )
	{
	    $File = HTML::inputFile ( self::PARSER_FILE );
	    $Submit = HTML::submit( self::PARSER_SUBMIT, "Envoyer" );
	    
	    $this->add ( HTML::form ( $File . $Submit, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}
	
	protected function parse ()
	{
	    $Content = '';
	    
	    try
	    {
	        $TextToParse = file_get_contents ( $this->FileToParse );
	        
	        $Parser = new ParserHnS ( $TextToParse );
	        
	        $Parser->parse ();
	        $Content = 'Parsing succeeded<br>' . $Parser;
	    }
	    
	    catch ( Exception $Exc )
	    {
	        $Content = $Exc->getMessage ();
	    }
	    
	    $this->add ( HTML::div ( $Content ) );
	}
	
	protected $FileToParse; //!< The file to parse 
} // PageParse
