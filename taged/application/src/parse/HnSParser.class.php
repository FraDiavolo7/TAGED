<?php

/**
 * Parser pour Hack'N Slash (Diablo 3).
 * 
 * Cette classe est responsable de l'analyse des données Hack'N Slash (Diablo 3) fournies en texte brut.
 * Elle extrait les informations pertinentes et les organise pour une utilisation ultérieure.
 * 
 * @package TAGED\Parser\HackNSlash
 */
class HnSParser 
{

	/**
     * Constructeur de la classe HnSParser.
     * 
     * Initialise l'objet en utilisant les données de texte à analyser, l'URL, le serveur et la classe de héros fournis.
     * 
     * @param string $TextToParse Les données de texte à analyser pour Hack'N Slash (Diablo 3).
     * @param string $URL L'URL associée aux données de texte (facultatif).
     * @param string $Srv Le serveur sur lequel le ladder est généré (par défaut : "eu").
     * @param string $HClass La classe des héros répertoriés (par défaut : "barbarian").
     */
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
    
	    /**
     * Destructeur de la classe HnSParser.
     * 
     * Ce destructeur ne contient aucune action spécifique, mais est inclus pour des raisons de cohérence.
     */
    public function __destruct ( )
    {
    }
	
	    /**
     * Méthode magique __toString.
     * 
     * Retourne une chaîne de caractères représentant l'objet lorsqu'il est converti en chaîne.
     * 
     * @return string Une chaîne représentant l'objet HnSParser.
     */
    public function __toString ()
    {
        $String = '';
        
        $String .= "<br>\n";
        return $String;
    }
	
    /**
     * Nettoie la variable membre $this->FullText de tous les caractères indésirables.
     * 
     * Cette méthode utilise la modification de motif PCRE m (PCRE_MULTILINE) pour utiliser les caractères "\n" dans
     * une chaîne de sujet afin de faire correspondre les groupes multilignes (particulièrement utile pour les listes).
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
	
	    /**
     * Applique un modèle PCRE (Perl Compatible Regular Expression) à la variable membre $this->ProcessedText.
     * 
     * Cette méthode est utilisée pour appliquer le modèle PCRE à la variable membre $this->ProcessedText, en utilisant
     * la fonction de rappel (callback) fournie.
     * 
     * @param string $Pattern Le modèle PCRE à appliquer.
     * @param callable $Callback La fonction de rappel (callback) à utiliser pour le remplacement.
     * @param int $Count Le nombre de fois que le modèle PCRE doit être appliqué (par défaut : 1).
     */
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
     * Analyse (avec PCRE) la variable membre $this->ProcessedText.
     * 
     * Cette méthode utilise les expressions régulières PCRE pour analyser la variable membre $this->ProcessedText.
     * Elle effectue une série de remplacements et d'actions pour extraire les informations pertinentes des données.
     * 
     * @throws Exception Si une exception se produit lors de l'analyse.
     */
    public function parse () 
    {
        //Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " . $this->ProcessedText );
		//echo "Processed <table border=1>" . $this->ProcessedText . "</table>\n";
		$Tmp = $this->ProcessedText;
		$Cpt = 0;
        $Tmp = preg_replace_callback ( '/(<tr.*<\/tr>)/sU', array ( $this, 'parseHero' ), $Tmp );
	}
	
	/**
     * Fonction de rappel (callback) pour l'analyse des héros.
     * 
     * Cette méthode est utilisée comme fonction de rappel (callback) par la méthode parse() pour analyser les données
     * d'un héros spécifique à partir du texte analysé.
     * 
     * @param array $Matches Un tableau de correspondances PCRE (résultat de l'analyse).
     * 
     * @return string Retourne une chaîne vide, car elle ne remplace pas le texte analysé.
     */
	protected function parseHero ( $Matches )
	{
        //Log::debug ( __FUNCTION__ . ':' . __LINE__ . " " . print_r ( $Matches, TRUE) );
        preg_replace_callback ( '/[^<]*<td[^>]*>\n([0-9]*)\..*\<a href="([^"]*)".*\<img[^\<\>]*\>\n(.*)\n\<\/a\>.*\<td[^\<\>]*\>\n([^\<\>]*)\n\<\/td\>.*\<td[^\<\>]*\>\n([^\<\>]*)\n\<\/td\>/msU', array ( $this, 'parseHeroData' ), $Matches [0] );
          
		return '';
	}
	
	/**
     * Fonction de rappel (callback) pour l'analyse des données d'un héros.
     * 
     * Cette méthode est utilisée comme fonction de rappel (callback) par la méthode parseHero() pour analyser les
     * données spécifiques d'un héros à partir des correspondances PCRE.
     * 
     * @param array $Matches Un tableau de correspondances PCRE (résultat de l'analyse).
     * 
     * @return string Retourne une chaîne vide, car elle ne remplace pas le texte analysé.
     */
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
	
	    /**
     * @var string Le nom du fichier.
     */
    private $filename;

    /**
     * @var string Le titre.
     */
    private $head;

    /**
     * @var string Le texte brut à analyser.
     */
    private $text;

    /**
     * @var string Le texte brut complet à analyser.
     */
    private $FullText;

    /**
     * @var string Le texte traité après nettoyage.
     */
    private $ProcessedText;

    /**
     * @var mixed L'objet Game associé aux données analysées.
     */
    private $Game;

    /**
     * @var string Le serveur sur lequel le ladder est généré.
     */
    private $Server;

    /**
     * @var string La classe des héros répertoriés.
     */
    private $HeroClass;

    /**
     * @var string L'URL associée aux données de texte (facultatif).
     */
    private $URL;

    /**
     * @var string L'URL de base pour les liens relatifs.
     */
    private $BaseURL;
}
