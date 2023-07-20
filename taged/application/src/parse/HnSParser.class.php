<?php

/**
 * Parser for Hack'N Slash  ( Diablo 3 )
 * @package TAGED\Parser\HackNSlash
 */
class HnSParser {

    private $filename;
    private $head;
    private $text;
    
    private $FullText;
    private $ProcessedText;
    private $Game;

    private $Server; ///!< Server on which the ladder is coming
    private $HeroClass; ///!< Class of the Heroes listed
    
    private $URL;
    private $BaseURL;
    
    public function __construct ( $TextToParse, $URL = '', $Srv = "eu", $HClass = "barbarian" ) 
    {
        $this->Server = $Srv;
        $this->HeroClass = $HClass;
        $this->URL = $URL;
        $URLinfo =  parse_url ( $URL );
        $this->BaseURL = $URLinfo ['scheme'] . '://' . $URLinfo ['host'];
        Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " . $URL );
           
        $this->FullText = $TextToParse;
        $this->clean ();
    }
    
    public function __destruct ( )
    {
    }
	
    public function __toString ()
    {
        $String = '';
        
        $String .= "<br>\n";
        return $String;
    }
	
    /**
     * @brief Clean the $this->FullText member variable of all undesirables characters.
     * Use because PCRE pattern modifier m (PCRE_MULTILINE) have to use "\n" characters in a subject string to match
     * multiline group (particularly useful for lists).
     */
    private function clean () 
    {
        $tmp = preg_replace('/\R/u', "\n", $this->FullText);
        //Log::logVar ( 'tmp',  $tmp );
        $TmpItems = explode ( 'tbody', $tmp );
        //Log::logVar ( 'Test',  count ( $TmpItems ) );
        $this->ProcessedText = $TmpItems [1];
        //$this->ProcessedText  = preg_replace('/^.*\<tbody\>(.*)\<\/tbody\>.*$/sU', '$1', $tmp);
        //Log::logVar ( '$this->ProcessedText',  $this->ProcessedText );

    }
    protected function applyPattern ( $Pattern, $Callback, $Count = 1 )
    {
        // TOTO @CDE Tester utilité.
        // Si Player est appelé 1 fois, pourquoi rule doit être appelé un nombre de fois défini?
        for ( $i = 0 ; $i < $Count ; ++$i )
        {
            $this->ProcessedText = preg_replace_callback ( $Pattern, array ( $this, $Callback ), $this->ProcessedText );
        }
    }

    /**
     *  @brief Parse (with PCRE) the $this->text member variable.
     *  @throws Exception
     */
    public function parse () 
    {
        //Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " . $this->ProcessedText );
		//echo "Processed <table border=1>" . $this->ProcessedText . "</table>\n";
		$Tmp = $this->ProcessedText;
		$Cpt = 0;
//        $Tmp = preg_replace_callback ( '/^(.*)\<\/tr\>.*$/msU', array ( $this, 'parseHero' ), $Tmp );
        $Tmp = preg_replace_callback ( '/(<tr.*<\/tr>)/sU', array ( $this, 'parseHero' ), $Tmp );
	}
	
	protected function parseHero ( $Matches )
	{
        //Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " . print_r ( $Matches, TRUE) );
        preg_replace_callback ( '/[^<]*<td[^>]*>\n([0-9]*)\..*\<a href="([^"]*)".*\<img[^\<\>]*\>\n(.*)\n\<\/a\>.*\<td[^\<\>]*\>\n([^\<\>]*)\n\<\/td\>.*\<td[^\<\>]*\>\n([^\<\>]*)\n\<\/td\>/msU', array ( $this, 'parseHeroData' ), $Matches [0] );
          
		return '';
	}
	
	protected function parseHeroData ( $Matches )
	{
        /*
        $Hero = Hero::create ( $Matches );
        $this->Heroes [] = $Hero;

        echo $Hero . "<br>\n";
         */
        //Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " . print_r ( $Matches, TRUE) );
        Hero::mark4DL ( $Matches, $this->BaseURL, $this->Server, $this->HeroClass );
		
		return '';
	}
}
