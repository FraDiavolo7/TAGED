<?php

/**
 * Test page
 * @package TAGED\Pages\Test
 */
class PageTestSkyCubeEmergent extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Test SkyCube Emergent';
		$this->FileToParse = NULL;
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data ); 
	} 
	
	protected function handle ( $Data )
	{
	    $Test = array ();
	    $Test [] = array ( 'RowID' => 1, 'Type' => 'Feticheur', 'Propriete' => 'Vitesse', 'Parangon1' => 600, 'Rarete1' => 5,  'Gemmes1' => 30, 'Succes1' => 3,  'Parangon2' => 650, 'Rarete2' => 6,  'Gemmes2' => 40, 'Succes2' => 2 );
	    $Test [] = array ( 'RowID' => 2, 'Type' => 'Feticheur', 'Propriete' => 'Vitesse', 'Parangon1' => 900, 'Rarete1' => 5,  'Gemmes1' => 85, 'Succes1' => 7,  'Parangon2' => 950, 'Rarete2' => 6,  'Gemmes2' => 95, 'Succes2' => 6 );
	    $Test [] = array ( 'RowID' => 3, 'Type' => 'Sorciere',  'Propriete' => 'Chance',  'Parangon1' => 600, 'Rarete1' => 10, 'Gemmes1' => 50, 'Succes1' => 7,  'Parangon2' => 650, 'Rarete2' => 11, 'Gemmes2' => 60, 'Succes2' => 6 );
	    $Test [] = array ( 'RowID' => 4, 'Type' => 'Sorciere',  'Propriete' => 'Vitesse', 'Parangon1' => 100, 'Rarete1' => 10, 'Gemmes1' => 85, 'Succes1' => 5,  'Parangon2' => 150, 'Rarete2' => 11, 'Gemmes2' => 95, 'Succes2' => 4 );
	    $Test [] = array ( 'RowID' => 5, 'Type' => 'Croise',    'Propriete' => 'Chance',  'Parangon1' => 900, 'Rarete1' => 10, 'Gemmes1' => 50, 'Succes1' => 7,  'Parangon2' => 950, 'Rarete2' => 11, 'Gemmes2' => 60, 'Succes2' => 6 );
	    $RelCols = array ();
	    $RelCols [] = 'RowID';
	    $RelCols [] = 'Type';
	    $RelCols [] = 'Propriete';
	    $MesCols = array ();
	    $MesCols [] = 'Parangon1';
	    $MesCols [] = 'Rarete1';
	    $MesCols [] = 'Gemmes1';
	    $MesCols [] = 'Succes1';
	    $MesCols [] = 'Parangon2';
	    $MesCols [] = 'Rarete2';
	    $MesCols [] = 'Gemmes2';
	    $MesCols [] = 'Succes2';
	    
        $this->add ( HTML::startDiv ( array ( 'class' => 'testskycube' ) ) );
	    
        $SkyCubeEmergent = new SkyCubeEmergent ( $Test, $RelCols, $MesCols, Cuboide::TO_MIN ); 

        $this->add ( HTML::div ( SKDisplay::html ( $SkyCubeEmergent ) ) );
	    $this->add ( HTML::endDiv ( ) );
	}

	
} // PageCollParse
