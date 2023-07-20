<?php

/**
 * Test page for Accords
 * @package TAGED\Pages\Test
 */
class PageTestAccords extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Test Accords';
		$this->FileToParse = NULL;
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}
	
	protected function handle ( $Data )
	{
	    
        $Test = array ();
        $Test [] = array ( 'RowID' => 1, 'Proprio' => 'Dupont',  'Ville' => 'Marseille', 'Prix' => 220, 'Eloignement' => 15, 'Conso' => 275, 'Voisins' => 5 );
        $Test [] = array ( 'RowID' => 2, 'Proprio' => 'Dupond',  'Ville' => 'Paris',     'Prix' => 100, 'Eloignement' => 15, 'Conso' =>  85, 'Voisins' => 1 );
        $Test [] = array ( 'RowID' => 3, 'Proprio' => 'Martin',  'Ville' => 'Marseille', 'Prix' => 220, 'Eloignement' =>  7, 'Conso' => 180, 'Voisins' => 1 );
        $Test [] = array ( 'RowID' => 4, 'Proprio' => 'Sanchez', 'Ville' => 'Aubagne',   'Prix' => 340, 'Eloignement' =>  7, 'Conso' =>  85, 'Voisins' => 3 );
        $Test [] = array ( 'RowID' => 5, 'Proprio' => 'Durand',  'Ville' => 'Paris',     'Prix' => 100, 'Eloignement' =>  7, 'Conso' => 180, 'Voisins' => 1 );
        
        $RelCols = array ();
        $RelCols [] = 'RowID';
        $RelCols [] = 'Proprio';
        $RelCols [] = 'Ville';
        $MesCols = array ();
        $MesCols [] = 'Prix';
        $MesCols [] = 'Eloignement';
        $MesCols [] = 'Conso';
        $MesCols [] = 'Voisins';
	   
        $MesColsPEC = array ();
        $MesColsPEC [] = 'Prix';
        $MesColsPEC [] = 'Eloignement';
        $MesColsPEC [] = 'Conso';
	    
        $MesColsPEV = array ();
        $MesColsPEV [] = 'Prix';
        $MesColsPEV [] = 'Eloignement';
        $MesColsPEV [] = 'Voisins';
        
        $MesColsPCV = array ();
        $MesColsPCV [] = 'Prix';
        $MesColsPCV [] = 'Conso';
        $MesColsPCV [] = 'Voisins';
        
        $MesColsECV = array ();
        $MesColsECV [] = 'Eloignement';
        $MesColsECV [] = 'Conso';
        $MesColsECV [] = 'Voisins';
        
        $this->add ( HTML::startDiv ( array ( 'class' => 'testaccords' ) ) );
	    
// 	    $this->add ( HTML::div ( HTML::tableFull ( $Test, array ( 'border' => '1' ) ) ) );
// 	    $this->add ( HTML::div ( HTML::tableFull ( $RelCols, array ( 'border' => '1' ) ) ) );
// 	    $this->add ( HTML::div ( HTML::tableFull ( $MesCols, array ( 'border' => '1' ) ) ) );
	    
        $SkyCube = new SkyCube ( $Test, $RelCols, $MesCols, Cuboide::TO_MIN );
//        $SkyCube = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesCols, Cuboide::TO_MIN ); 
//        $SkyCubePEC = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsPEC, Cuboide::TO_MIN );
//        $SkyCubePEV = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsPEV, Cuboide::TO_MIN );
//        $SkyCubePCV = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsPCV, Cuboide::TO_MIN );
//        $SkyCubeECV = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsECV, Cuboide::TO_MIN );

// 	    $this->add ( HTML::div ( $SkyCube ) );
// 	    $this->add ( HTML::div ( $SkyCubePEC ) );
// 	    $this->add ( HTML::div ( $SkyCubePEV ) );
// 	    $this->add ( HTML::div ( $SkyCubePCV ) );
//        $this->add ( HTML::div ( SKDisplay::html ( $SkyCubeECV ) ) );
//         $this->add ( HTML::div ( $SkyCubeECV ) );
// 	    $this->add ( HTML::div ( $SkyCubeECV->toLaTeX () ) );
        $this->add ( HTML::div ( SKDisplay::html ( $SkyCube) ) );
        $this->add ( HTML::endDiv ( ) );
	}

	
} // PageCollParse
