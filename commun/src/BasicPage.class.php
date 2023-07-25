<?php

/**
 * @package Commun
 */
abstract class BasicPage
{
    /**
     * Méthode abstraite qui doit être implémentée dans les classes enfants pour afficher l'en-tête de la page.
     */
	public abstract function showPageHeader ();
	
    /**
     * Méthode abstraite qui doit être implémentée dans les classes enfants pour afficher le pied de page de la page.
     */
	public abstract function showPageFooter ();
	
    /**
     * Constructeur de la classe BasicPage.
     *
	 * DOIT être surchargé.
     * @param mixed $InputData (facultatif) Données d'entrée à passer lors de l'instanciation de la classe.
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

    /**
     * Destructeur de la classe BasicPage.
	 *
     * Affiche la page en fonction des éléments ajoutés, ou effectue une redirection si l'URL de redirection est spécifiée.
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
	
    /**
     * Ajoute des métadonnées à la page.
     *
     * @param array $MetaData Tableau contenant les métadonnées à ajouter.
     */
	public final function addMeta ( $MetaData = array () )
	{
		if ( ! empty ( $MetaData ) )
		{
			$this->MetaList [] = HTML::meta ( $MetaData );
		}
	}
	
    /**
     * Ajoute un fichier JavaScript à inclure dans la page.
     *
     * @param string $JSFile Chemin du fichier JavaScript.
     */
	public final function addJS ( $JSFile )
	{
		if ( '' != $JSFile )
		{
			$this->JSList [] = HTML::jsFile ( $JSFile );
		}
	}
	
    /**
     * Ajoute un fichier CSS à inclure dans la page.
     *
     * @param string $CSSFile Chemin du fichier CSS.
     */
	public final function addCSS ( $CSSFile )
	{
		if ( '' != $CSSFile )
		{
			$this->CSSList [] = HTML::cssFile ( $CSSFile );
		}
	}
	
    /**
     * Ajoute du code JavaScript à inclure dans la page.
     *
     * @param string $Code Code JavaScript à ajouter.
     */
	public final function addJScode ( $Code )
	{
		if ( '' != $Code )
		{
			$this->JSCodes [] = $Code;
		}
	}

    /**
     * Ajoute du code CSS à inclure dans la page.
     *
     * @param string $Code Code CSS à ajouter.
     */
	public final function addCSScode ( $Code )
	{
		if ( '' != $Code )
		{
			$this->CSSCodes [] = $Code;
		}
	}
	
    /**
     * Ajoute du contenu HTML à afficher dans la page.
     *
     * @param string $HTML Contenu HTML à ajouter.
     */
	public final function add ( $HTML )
	{
		if ( '' != $HTML )
		{
			$this->HTMLContent [] = $HTML;
		}
	}
	
    /**
     * Affiche la section <head> de la page HTML, incluant les métadonnées, les fichiers JavaScript et CSS, ainsi que les codes JavaScript et CSS.
     */
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
	
    /**
     * Affiche la fin de la page HTML.
     */
	protected final function showFoot ()
	{
		HTML::showEndHtml ();
	}

    /**
     * Affiche le contenu HTML ajouté à la page à l'aide de la méthode add().
     */
	public final function showPageContent ()
	{
		foreach ( $this->HTMLContent as $Content )
		{
			echo $Content . "\n";
		}
	}
	
    /**
     * Effectue une redirection vers l'URL spécifiée.
     *
     * @param string $URL URL de redirection.
     */
	public final function redirect ( $URL )
	{
	    $this->RedirectURL = $URL;
	}
	
    /**
     * Tableau contenant les métadonnées de la page.
     *
     * @var array
     */
    protected $MetaList;

    /**
     * Tableau contenant les chemins des fichiers CSS à inclure dans la page.
     *
     * @var array
     */
    protected $CSSList;

    /**
     * Tableau contenant les codes CSS à inclure dans la page.
     *
     * @var array
     */
    protected $CSSCodes;

    /**
     * Tableau contenant les chemins des fichiers JavaScript à inclure dans la page.
     *
     * @var array
     */
    protected $JSList;

    /**
     * Tableau contenant les codes JavaScript à inclure dans la page.
     *
     * @var array
     */
    protected $JSCodes;

    /**
     * Tableau contenant le contenu HTML à afficher dans la page.
     *
     * @var array
     */
    protected $HTMLContent;

    /**
     * URL de redirection. Si définie, la page redirigera vers cette URL.
     *
     * @var string
     */
    protected $RedirectURL;

}
