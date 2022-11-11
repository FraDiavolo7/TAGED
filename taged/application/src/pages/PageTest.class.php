<?php

class PageTest extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Test';
		$this->FileToParse = NULL;
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}
	
	protected function handle ( $Data )
	{
//	    self::testExportCSV ();

	    self::testStringsConvertCase ();
	}

	protected function compareRes ( $Res, $Attendu )
	{
	    $Content = HTML::span ( 'KO', array ( 'style' => 'background-color: red; color: white') );
	    if ( $Res == $Attendu )
	    {
	        $Content = HTML::span ( 'OK', array ( 'style' => 'background-color: green; color: white') );
	    }
	    return $Content;
	}
	
	protected function testCase ( $Input, $Output, $From, $To )
	{
	    $Result = Strings::convertCase ( $Input, $To, $From );
	    $this->add ( HTML::div (
	        HTML::title ( "From $From to $To '$Output'" , 3 ) .
	        $Input . ' => ' . $Result . ' ' . self::compareRes ( $Result, $Output ) 
	        ));
	}
	
	protected function testStringsConvertCase ()
	{ 
	    $Snake = "texte_qui_sert_a_tester";
	    $SnakeUp = "Texte_Qui_Sert_A_Tester"; 
	    $Camel = "TexteQuiSertATester";
	    $CamelLow = "texteQuiSertATester";
	    $NaturalLow = "texte qui sert a tester";
	    $Natural = "Texte Qui Sert A Tester";
	    
	    self::testCase ( $Snake, $Snake,     Strings::CASE_SNAKE, Strings::CASE_SNAKE         );
	    self::testCase ( $Snake, $SnakeUp,   Strings::CASE_SNAKE, Strings::CASE_SNAKE_UPPER   );
	    self::testCase ( $Snake, $Camel,     Strings::CASE_SNAKE, Strings::CASE_CAMEL         );
	    self::testCase ( $Snake, $CamelLow,  Strings::CASE_SNAKE, Strings::CASE_CAMEL_LOWER   );
	    self::testCase ( $Snake, $Natural,   Strings::CASE_SNAKE, Strings::CASE_NATURAL       );
	    self::testCase ( $Snake, $NaturalLow, Strings::CASE_SNAKE, Strings::CASE_NATURAL_LOWER );
	    
	    self::testCase ( $SnakeUp, $Snake,     Strings::CASE_SNAKE_UPPER, Strings::CASE_SNAKE         );
	    self::testCase ( $SnakeUp, $SnakeUp,   Strings::CASE_SNAKE_UPPER, Strings::CASE_SNAKE_UPPER   );
	    self::testCase ( $SnakeUp, $Camel,     Strings::CASE_SNAKE_UPPER, Strings::CASE_CAMEL         );
	    self::testCase ( $SnakeUp, $CamelLow,  Strings::CASE_SNAKE_UPPER, Strings::CASE_CAMEL_LOWER   );
	    self::testCase ( $SnakeUp, $Natural,   Strings::CASE_SNAKE_UPPER, Strings::CASE_NATURAL       );
	    self::testCase ( $SnakeUp, $NaturalLow, Strings::CASE_SNAKE_UPPER, Strings::CASE_NATURAL_LOWER );
	    
	    self::testCase ( $Camel, $Snake,     Strings::CASE_CAMEL, Strings::CASE_SNAKE         );
	    self::testCase ( $Camel, $SnakeUp,   Strings::CASE_CAMEL, Strings::CASE_SNAKE_UPPER   );
	    self::testCase ( $Camel, $Camel,     Strings::CASE_CAMEL, Strings::CASE_CAMEL         );
	    self::testCase ( $Camel, $CamelLow,  Strings::CASE_CAMEL, Strings::CASE_CAMEL_LOWER   );
	    self::testCase ( $Camel, $Natural,   Strings::CASE_CAMEL, Strings::CASE_NATURAL       );
	    self::testCase ( $Camel, $NaturalLow, Strings::CASE_CAMEL, Strings::CASE_NATURAL_LOWER );
	    
	    self::testCase ( $CamelLow, $Snake,     Strings::CASE_CAMEL_LOWER, Strings::CASE_SNAKE         );
	    self::testCase ( $CamelLow, $SnakeUp,   Strings::CASE_CAMEL_LOWER, Strings::CASE_SNAKE_UPPER   );
	    self::testCase ( $CamelLow, $Camel,     Strings::CASE_CAMEL_LOWER, Strings::CASE_CAMEL         );
	    self::testCase ( $CamelLow, $CamelLow,  Strings::CASE_CAMEL_LOWER, Strings::CASE_CAMEL_LOWER   );
	    self::testCase ( $CamelLow, $Natural,   Strings::CASE_CAMEL_LOWER, Strings::CASE_NATURAL       );
	    self::testCase ( $CamelLow, $NaturalLow, Strings::CASE_CAMEL_LOWER, Strings::CASE_NATURAL_LOWER );
	    
	    self::testCase ( $Natural, $Snake,     Strings::CASE_NATURAL, Strings::CASE_SNAKE         );
	    self::testCase ( $Natural, $SnakeUp,   Strings::CASE_NATURAL, Strings::CASE_SNAKE_UPPER   );
	    self::testCase ( $Natural, $Camel,     Strings::CASE_NATURAL, Strings::CASE_CAMEL         );
	    self::testCase ( $Natural, $CamelLow,  Strings::CASE_NATURAL, Strings::CASE_CAMEL_LOWER   );
	    self::testCase ( $Natural, $Natural,   Strings::CASE_NATURAL, Strings::CASE_NATURAL       );
	    self::testCase ( $Natural, $NaturalLow, Strings::CASE_NATURAL, Strings::CASE_NATURAL_LOWER );
	    
	    self::testCase ( $NaturalLow, $Snake,     Strings::CASE_NATURAL_LOWER, Strings::CASE_SNAKE         );
	    self::testCase ( $NaturalLow, $SnakeUp,   Strings::CASE_NATURAL_LOWER, Strings::CASE_SNAKE_UPPER   );
	    self::testCase ( $NaturalLow, $Camel,     Strings::CASE_NATURAL_LOWER, Strings::CASE_CAMEL         );
	    self::testCase ( $NaturalLow, $CamelLow,  Strings::CASE_NATURAL_LOWER, Strings::CASE_CAMEL_LOWER   );
	    self::testCase ( $NaturalLow, $Natural,   Strings::CASE_NATURAL_LOWER, Strings::CASE_NATURAL       );
	    self::testCase ( $NaturalLow, $NaturalLow, Strings::CASE_NATURAL_LOWER, Strings::CASE_NATURAL_LOWER );
	}
	
	protected function testExportCSV ()
	{
	    
	    $Columns = array ( 'nom', 'num', 'ignore', 'titre', 'aussi', 'truc' );
	    $Rows = array ( 'a', 'b', 'c', 'd' );
	    
	    $ColumnFilter = array ( 'Nom' => 'nom', 'Num' => 'num', 'Titre' => 'titre', 'IGN' => 'ignore', 'Truc' => 'truc' );
	    $ColumnIgnore = array ( 'ignore', 'aussi' );
	    $RowIgnore = array ( 'b', 'c' );
	    $RowFilter = array ( 'a' => 'Ah', 'b' => "Beh", 'c' => "C'est", 'd' => "Dé" );
	    
	    $InputData = array ();
	    
	    foreach ( $Rows as $Row )
	    {
	        foreach ( $Columns as $Column )
	        {
	            $InputData [$Row][$Column] = $Column . '_' . $Row; 
	        }
	    }
	    
	    $this->add ( HTML::title ( 'Par défaut', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData ) ) );
	    
	    $this->add ( HTML::title ( 'Séparateur ;', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ';' ) ) );
	    
	    $this->add ( HTML::title ( 'Colonnes numérotées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_AS_NUM ) ) );
	    
	    $this->add ( HTML::title ( 'Pas de titres de colonnes', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_NO_HEADER ) ) );
	    
	    $this->add ( HTML::title ( 'Colonnes définies', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', $ColumnFilter ) ) );
	    
	    $this->add ( HTML::title ( 'Par défaut, colonnes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, Arrays::EXPORT_ROW_FROM_DATA, '', $ColumnIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'Colonnes numérotées, colonnes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_AS_NUM, Arrays::EXPORT_ROW_FROM_DATA, '', $ColumnIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'Pas de titres de colonnes, colonnes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_FROM_DATA, '', $ColumnIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'Colonnes définies, colonnes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', $ColumnFilter, Arrays::EXPORT_ROW_FROM_DATA, '', $ColumnIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'Lignes numérotées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, Arrays::EXPORT_ROW_ADD_NUM ) ) );
	    
	    $this->add ( HTML::title ( 'Pas de titres de lignes', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, Arrays::EXPORT_ROW_NO_HEADER ) ) );
	    
	    $this->add ( HTML::title ( 'titres de lignes depuis une colonne', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, 'titre' ) ) );
	    
	    $this->add ( HTML::title ( 'Liste de titres de lignes', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, $RowFilter ) ) );
	    
	    $this->add ( HTML::title ( 'Par défaut, lignes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, Arrays::EXPORT_ROW_FROM_DATA, '', array (), $RowIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'Lignes numérotées, lignes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, Arrays::EXPORT_ROW_ADD_NUM, '', array (), $RowIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'Pas de titres de lignes, lignes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, Arrays::EXPORT_ROW_NO_HEADER, '', array (), $RowIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'titres de lignes depuis une colonne, lignes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, 'titre', '', array (), $RowIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'Liste de titres de lignes, lignes ignorées', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, $RowFilter, '', array (), $RowIgnore ) ) );
	    
	    $this->add ( HTML::title ( 'titres de lignes depuis une colonne, lignes ignorées, colonne titre ignorée', 3 ) );
	    $this->add ( HTML::getTag ( 'pre', Arrays::exportAsCSV ( $InputData, ',', Arrays::EXPORT_COLUMN_FROM_DATA, 'titre', '', array ( 'titre' ), $RowIgnore ) ) );
	    
	}
	
} // PageCollParse
