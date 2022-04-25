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
    
    protected $CurrentItem;
    protected $ItemList;
    
    const ITEM_NAME  = 'itemname';
    const ITEM_POS   = 'itempos';
    const ITEM_IMG   = 'itemimg';
    const ITEM_AFFIX = 'itemaffix';
    
    public function __construct ( $TextToParse, $Parameters = array () ) 
    {
        echo __CLASS__ . ' ' . print_r ( $Parameters, TRUE ) . "\n";
        $this->FullText = $TextToParse;
        $this->CurrentItem = 0;
        $this->ItemList = array ();
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
        $this->ProcessedText = preg_replace('/\R/u', "\n", $this->FullText);
    }
    

    /**
     *  @brief Parse (with PCRE) the $this->text member variable.
     *  @throws Exception
     */
    public function parse () 
    {
        Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
        $Tmp = $this->ProcessedText;
		/** NOT WORKING **/
        Log::debug ( __FUNCTION__ . ':' . __LINE__ . " CDE : parseUser n'est pas appelé!!!" );
        $Tmp = preg_replace_callback ( '#<div class="profile-head".*<h2 class="header-2">(.*)</div>#sU', array ( $this, 'parseUser' ), $Tmp );
		$Tmp = preg_replace_callback ( '#<ul class="gear-labels"(.*)</ul>#sU', array ( $this, 'parseItems' ), $Tmp );
	}

	protected function parseUser ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    
	    $Tmp = preg_replace_callback ( '#<a href="(.*)">.*>(.*)<span.*>(.*)</span>.*<span.*>(.*)</span>#sU', array ( $this, 'parseUserDetails' ), $Matches [0] );
	    
	    return '';
	}

	protected function parseUserDetails ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    $Link = $Matches [1];
	    $UserName = $Matches [2];
	    $UserTag = $Matches [3];
	    $Clan = $Matches [4];
	    echo 'User ' . $UserName . ' ' . $UserTag . ' from ' . $Clan . "\n";
	    return '';
	}
	
	
	protected function parseItems ( $Matches )
	{
	    $Tmp = preg_replace_callback ( '#<li class="(.*)".*>.*<a href="(.*)".*<span class="item-name">(.*)</span>(.*)</a>.*</li>#sU', array ( $this, 'parseItem' ), $Matches [0] );
	    
		return '';
	}
	
	protected function parseItem ( $Matches )
	{
	    $Item = array ();
	    $Item [self::ITEM_POS ] = Strings::replace ( 'gear-label slot-', '', $Matches [1] );
	    $Item [self::ITEM_IMG ] = $Matches [2];
	    $Item [self::ITEM_NAME] = $Matches [3];
	    $Item [self::ITEM_AFFIX] = array ();
	    
	    $this->ItemList [$this->CurrentItem] = $Item;
	    
	    $Tmp = preg_replace_callback ( '#.*class="bonus-value (.*)".*>(.*)</span>#sU', array ( $this, 'parseParams' ), $Matches [4] );
	    $this->showItem ();
	    
	    $this->CurrentItem += 1;
	    return '';
	}
	
	protected function parseParams ( $Matches )
	{
	    $this->ItemList [$this->CurrentItem][self::ITEM_AFFIX][] = trim ($Matches [2]);
	    return '';
	}
	
	protected function showItem ( $ItemNum = -1 )
	{
	    if ( $ItemNum == -1 ) $ItemNum = $this->CurrentItem;
	    
	    if ( isset ( $this->ItemList [$ItemNum] ) )
	    {
	        $Name  = Arrays::getIfSet ( $this->ItemList [$ItemNum], self::ITEM_NAME, "" );
	        $Pos   = Arrays::getIfSet ( $this->ItemList [$ItemNum], self::ITEM_POS,  "" );
	        $Image = Arrays::getIfSet ( $this->ItemList [$ItemNum], self::ITEM_IMG,  "" );
	        $Affix = Arrays::getIfSet ( $this->ItemList [$ItemNum], self::ITEM_AFFIX, array () );
	        echo 'Item ' . $ItemNum . ' ' . $Name . ' on ' . $Pos . "\n";
	        foreach ( $Affix as $Aff )
	        {
	            echo $Aff . "\n";
	        }
	    }
	}

}
