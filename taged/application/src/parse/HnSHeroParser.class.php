<?php

class HnSHeroParser {

    private $filename;
    private $head;
    private $text;
    
    private $FullText;
    private $ProcessedText;
    private $Game;

    private $Server; ///!< Server on which the ladder is coming
    private $HeroClass; ///!< Class of the Heroes listed
    
    public function __construct ( $TextToParse, $Parameters = array () ) 
    {
        echo __CLASS__ . ' ' . print_r ( $Parameters, TRUE ) . "\n";
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
        $this->ProcessedText  = preg_replace('/^.*\<tbody\>(.*)\<\/tbody\>.*$/sU', '$1', $tmp);
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
		//echo "Processed <table border=1>" . $this->ProcessedText . "</table>\n";
		$Tmp = $this->ProcessedText;
		$Cpt = 0;
//        $Tmp = preg_replace_callback ( '/^(.*)\<\/tr\>.*$/msU', array ( $this, 'parseHero' ), $Tmp );
        $Tmp = preg_replace_callback ( '/(<tr.*<\/tr>)/sU', array ( $this, 'parseHero' ), $Tmp );
	}
	
	protected function parseHero ( $Matches )
	{
        preg_replace_callback ( '/[^<]*<td[^>]*>\n([0-9]*)\..*\<a href="([^"]*)".*\<img[^\<\>]*\>\n(.*)\n\<\/a\>.*\<td[^\<\>]*\>\n([^\<\>]*)\n\<\/td\>.*\<td[^\<\>]*\>\n([^\<\>]*)\n\<\/td\>/msU', array ( $this, 'parseHeroData' ), $Matches [0] );
          
		return '';
	}

}
