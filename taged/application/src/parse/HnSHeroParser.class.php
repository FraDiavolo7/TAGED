<?php

class HnSHeroParser {

    private $FullText;
    private $ProcessedText;
    private $Game;

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
        
        $Server    = Arrays::getIfSet ( $Parameters, 0, 'pq'       );
        $HeroClass = Arrays::getIfSet ( $Parameters, 1, 'class'    );
        $Rank      = Arrays::getIfSet ( $Parameters, 2, 9999       );
        $Rift      = Arrays::getIfSet ( $Parameters, 3, 0          );
        $Time      = Arrays::getIfSet ( $Parameters, 4, '00-00-00' );
        $URL       = Arrays::getIfSet ( $Parameters, 5, 'http://'  );
        
        $this->Hero = new Hero ( );
        $this->Hero->setServer ( $Server    );
        $this->Hero->setClass  ( $HeroClass );
        $this->Hero->setRank   ( $Rank      );
        $this->Hero->setRift   ( $Rift      );
        $this->Hero->setTime   ( $Time      );
        $this->Hero->setURL    ( $URL       );
        
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
        $Tmp = preg_replace_callback ( '#<div class="profile-head".*<h2 class="header-2".*>(.*)</div>#sU', array ( $this, 'parseUser' ), $Tmp );
        $Tmp = preg_replace_callback ( '#<div class="profile-sheet".*<strong>(.*)<span.*>.(.*).</span>.*<h2.*>(.*)</h2>#sU', array ( $this, 'parseHero' ), $Tmp );
		$Tmp = preg_replace_callback ( '#<ul class="gear-labels"(.*)</ul>#sU', array ( $this, 'parseItems' ), $Tmp );
		$Tmp = preg_replace_callback ( '#<ul class="active-skills clear-after">(.*)</ul>#sU', array ( $this, 'parseActive' ), $Tmp );
		$Tmp = preg_replace_callback ( '#<ul class="passive-skills clear-after">(.*)</ul>#sU', array ( $this, 'parsePassive' ), $Tmp );
		$Tmp = preg_replace_callback ( '#<div class="page-section attributes".*<ul class="attributes-core">(.*)</ul>.*<ul class="attributes-core secondary">(.*)</ul>.*<ul class="resources">(.*)</ul>#sU', array ( $this, 'parseAttributes' ), $Tmp );
//		echo $this->Hero . "\n";
        $this->Hero->save ();
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
	    $URL      = trim ( Arrays::getOrCrash ( $Matches, 1, 'Invalid URL' ) );
	    $UserName = trim ( Arrays::getOrCrash ( $Matches, 2, 'Invalid User name' ) );
	    $UserTag  = trim ( Arrays::getOrCrash ( $Matches, 3, 'Invalid User Tag'  ) );
	    $Clan     = trim ( Arrays::getIfSet   ( $Matches, 4, '' ) );
	    $this->Hero->setURL      ( $URL      );
	    $this->Hero->getPlayer ()->setUserName ( $UserName );
	    $this->Hero->getPlayer ()->setTag      ( $UserTag );
	    $this->Hero->getPlayer ()->setClan     ( $Clan );
	    return '';
	}
	
	protected function parseHero ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    $Level = $Matches [1];
	    $Level    = trim ( Arrays::getOrCrash ( $Matches, 1, 'Invalid Level' ) );
	    $Parangon = trim ( Arrays::getOrCrash ( $Matches, 2, 'Invalid Parangon' ) );
	    $HeroName = trim ( Arrays::getOrCrash ( $Matches, 3, 'Invalid Hero name' ) );
	    $this->Hero->setLevel    ( $Level    );
	    $this->Hero->setParangon ( $Parangon );
	    $this->Hero->setHeroname ( $HeroName );
	    return '';
	}
	
	protected function parseItems ( $Matches )
	{
	    $Tmp = preg_replace_callback ( '#<li class="(.*)".*>.*<a href="(.*)".*<span class="item-name">(.*)</span>(.*)</a>.*</li>#sU', array ( $this, 'parseItem' ), $Matches [0] );
	    
		return '';
	}
	
	protected function parseItem ( $Matches )
	{
	    $Item = new HnSItem ();
	    $Item->setPosition ( Strings::replace ( 'gear-label slot-', '', $Matches [1] ) );
	    $Item->setImage ( $Matches [2] );
	    $Item->setName ( $Matches [3] );

	    $this->Hero->addItem ( $Item );
	    
	    $Tmp = preg_replace_callback ( '#.*class="bonus-value (.*)".*>(.*)</span>#sU', array ( $this, 'parseParams' ), $Matches [4] );

	  //  echo $this->Hero->getCurItem () . "\n";
	    
	    return '';
	}
	
	protected function parseParams ( $Matches ) 
	{
	    $this->Hero->addAffix ( trim ($Matches [2]) );
	    return '';
	}
	

	protected function parseActive ( $Matches )
	{
	    $Tmp = preg_replace_callback ( '#<li>.*<span class="skill-name">(.*)<span.*>(.*)<.*</li>#sU', array ( $this, 'parseAct' ), $Matches [0] );
	    
	    return '';
	}
	
	protected function parseAct ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    static $Count = 1;
	    $Comp = new HnSComp ( trim ( $Matches [1] ), $Count++, HnSComp::TYPE_ACTIVE, trim ( $Matches [2] ) );
	    
	    $this->Hero->addComp ( $Comp );
	    
	    return '';
	}
	
	
	protected function parsePassive ( $Matches )
	{
	    $Tmp = preg_replace_callback ( '#<li>.*<span class="skill-name">(.*)</span>#sU', array ( $this, 'parsePass' ), $Matches [0] );
	    
	    return '';
	}

	protected function parsePass ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    static $Count = 1;
	    $Comp = new HnSComp ( trim ( $Matches [1] ), $Count++ );
	    
	    $this->Hero->addComp ( $Comp );
	    return '';
	}
	
	
	protected function parseAttributes ( $Matches )
	{
	    $Tmp = preg_replace_callback ( '#<li>.*<span.*>(.*)</span>.*<span.*>(.*)</span>.*</li>#sU', array ( $this, 'parseAttrP' ), $Matches [1] );
	    $Tmp = preg_replace_callback ( '#<li>.*<span.*>(.*)</span>.*<span.*>(.*)</span>.*</li>#sU', array ( $this, 'parseAttrS' ), $Matches [2] );
	    $Tmp = preg_replace_callback ( '#<li.*<span class="value">(.*)</span>.*<span class="label">(.*)</span>.*</li>#sU', array ( $this, 'parseAttrR' ), $Matches [3] );
	    
	    return '';
	}
	
	protected function parseAttrP ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    $this->Hero->setAttr ( trim ( $Matches [1] ), trim ( $Matches [2] ) );
	    return '';
	}
	
	protected function parseAttrS ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    $this->Hero->setAttr ( trim ( $Matches [1] ), trim ( $Matches [2] ) );
	    return '';
	}
	
	protected function parseAttrR ( $Matches )
	{
	    Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " );
	    $this->Hero->setAttr ( trim ( $Matches [2] ), trim ( $Matches [1] ) );
	    return '';
	}
	
}
