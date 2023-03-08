<?php

/**
 * Basic features of a Page on this framework
 */
abstract class BasicPage
{
	/*
	 * SHALL be overloaded
	 */
	public abstract function showPageHeader ();
	
	/*
	 * SHALL be overloaded
	 */
	public abstract function showPageFooter ();
	
	/*
	 * SHALL be called
	 */
	public function __construct ( $InputData = NULL )
	{
		$this->MetaList = array ();
		$this->CSSList = array ();
		$this->CSSCodes = array ();
		$this->JSList = array ();
		$this->JSCodes = array ();
		$this->HTMLContent = array ();
		$this->RedirectURL = '';
	}

	/*
	 * SHALL be called
	 */
    public function __destruct ()
	{
	    if ( '' != $this->RedirectURL )
	    {
	        HTML::showRedirect ( $this->RedirectURL );
	    }
	    else 
	    {
	        $this->showHead ();
	        $this->showPageHeader ();
	        
	        $this->showPageContent ();
	        
	        $this->showPageFooter ();
	        $this->showFoot ();
	    }
	}
	
	public final function addMeta ( $MetaData = array () )
	{
		if ( ! empty ( $MetaData ) )
		{
			$this->MetaList [] = HTML::meta ( $MetaData );
		}
	}
	
	public final function addJS ( $JSFile )
	{
		if ( '' != $JSFile )
		{
			$this->JSList [] = HTML::jsFile ( $JSFile );
		}
	}
	
	public final function addCSS ( $CSSFile )
	{
		if ( '' != $CSSFile )
		{
			$this->CSSList [] = HTML::cssFile ( $CSSFile );
		}
	}
	
	public final function addJScode ( $Code )
	{
		if ( '' != $Code )
		{
			$this->JSCodes [] = $Code;
		}
	}

	public final function addCSScode ( $Code )
	{
		if ( '' != $Code )
		{
			$this->CSSCodes [] = $Code;
		}
	}
	
	public final function add ( $HTML )
	{
		if ( '' != $HTML )
		{
			$this->HTMLContent [] = $HTML;
		}
	}
	
	public final function showHead ()
	{
		HTML::showStartHtml ();
		HTML::showStartHead ();
		HTML::showHeadTitle ( $this->SiteTitle . '-' . $this->PageTitle );

		HTML::upgradeInsecureRequests ();
		
		if ( ! empty ( $this->MetaList ) )
		{
			foreach ( $this->MetaList as $Meta )
			{
				echo $Meta . "\n";
			}
		}
		
		if ( ! empty ( $this->CSSList ) )
		{
			foreach ( $this->CSSList as $CSSFile )
			{
				echo $CSSFile . "\n";
			}
		}
		
		if ( ! empty ( $this->JSList ) )
		{
			foreach ( $this->JSList as $JSFile )
			{
				echo $JSFile . "\n";
			}
		}

		if ( ! empty ( $this->CSSCodes ) )
		{
			HTML::showStartCSS ();
			foreach ( $this->CSSCodes as $CSSCode )
			{
				echo $CSSFile . "\n";
			}
			HTML::showEndCSS ();
		}
		
		if ( ! empty ( $this->JSCodes ) )
		{
			HTML::showStartJS ();
			foreach ( $this->JSCodes as $JSCode )
			{
			    echo $JSCode . "\n";
			}
			HTML::showEndJS ();
		}
		HTML::showEndHead ();
	}
	
	protected final function showFoot ()
	{
		HTML::showEndHtml ();
	}

	public final function showPageContent ()
	{
		foreach ( $this->HTMLContent as $Content )
		{
			echo $Content . "\n";
		}
	}
	
	public final function redirect ( $URL )
	{
	    $this->RedirectURL = $URL;
	}
	
	protected $SiteTitle;
	protected $PageTitle;
	protected $CSSList;
	protected $CSSCodes;
	protected $JSList;
	protected $JSCodes;
	protected $HTMLContent;
	protected $RedirectURL;
}
