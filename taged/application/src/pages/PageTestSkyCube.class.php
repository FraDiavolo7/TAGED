<?php

class PageTestSkyCube extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Test SkyCube';
		$this->FileToParse = NULL;
	
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data );
	}
	
	protected function handle ( $Data )
	{
// 	    $Test = array ();
// 	    $RelCols = array ();
// 	    $MesCols = array ();
	    
// 	    $Rows = 3;
// 	    $Rels = 3;
// 	    $Mess = 4;
// 	    for ( $Row = 1; $Row <= $Rows ; ++$Row )
// 	    {
// 	        $RowData = array ();
// 	        for ( $Rel = 1; $Rel <= $Rels ; ++$Rel )
// 	        {
// 	            $Name = 'r' . $Rel;
// 	            $RowData [$Name] = 'R' . $Rel . $Row;
// 	            if ( $Row == 1 ) $RelCols [] = $Name;
// 	        }
// 	        for ( $Mes = 1; $Mes <= $Mess ; ++$Mes )
// 	        {
// 	            $Name = 'm' . $Mes;
// 	            $RowData [$Name] = $Mes . $Row;
// 	            if ( $Row == 1 ) $MesCols [] = $Name;
// 	        }
// 	        $Test [$Row] = $RowData;
// 	    }

// 	    $Test = array ();
// 	    $Test [] = array ( 'RowID' => 1, 'Type' => 'Feticheur', 'Propriete' => 'Vitesse', 'Parangon' => 600, 'Rarete' => 5,  'Gemmes' => 30, 'Succes' => 3 );
// 	    $Test [] = array ( 'RowID' => 2, 'Type' => 'Feticheur', 'Propriete' => 'Vitesse', 'Parangon' => 900, 'Rarete' => 5,  'Gemmes' => 85, 'Succes' => 7 );
// 	    $Test [] = array ( 'RowID' => 3, 'Type' => 'Sorciere',  'Propriete' => 'Chance',  'Parangon' => 600, 'Rarete' => 10, 'Gemmes' => 50, 'Succes' => 7 );
// 	    $Test [] = array ( 'RowID' => 4, 'Type' => 'Sorciere',  'Propriete' => 'Vitesse', 'Parangon' => 100, 'Rarete' => 10, 'Gemmes' => 85, 'Succes' => 5 );
// 	    $Test [] = array ( 'RowID' => 5, 'Type' => 'Croise',    'Propriete' => 'Chance',  'Parangon' => 900, 'Rarete' => 10, 'Gemmes' => 50, 'Succes' => 7 );
// 	    $RelCols = array ();
// 	    $RelCols [] = 'RowID';
// 	    $RelCols [] = 'Type';
// 	    $RelCols [] = 'Propriete';
// 	    $MesCols = array ();
// 	    $MesCols [] = 'Parangon';
// 	    $MesCols [] = 'Rarete';
// 	    $MesCols [] = 'Gemmes';
// 	    $MesCols [] = 'Succes';
	    
	    
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
        
        $this->add ( HTML::startDiv ( array ( 'class' => 'testskycube' ) ) );
	    
// 	    $this->add ( HTML::div ( HTML::tableFull ( $Test, array ( 'border' => '1' ) ) ) );
// 	    $this->add ( HTML::div ( HTML::tableFull ( $RelCols, array ( 'border' => '1' ) ) ) );
// 	    $this->add ( HTML::div ( HTML::tableFull ( $MesCols, array ( 'border' => '1' ) ) ) );
	    
//	    $SkyCube = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesCols, Cuboide::TO_MIN ); 
        $SkyCubePEC = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsPEC, Cuboide::TO_MIN );
        $SkyCubePEV = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsPEV, Cuboide::TO_MIN );
        $SkyCubePCV = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsPCV, Cuboide::TO_MIN );
        $SkyCubeECV = new SkyCubeBlocNestedLoop ( $Test, $RelCols, $MesColsECV, Cuboide::TO_MIN );

// 	    $this->add ( HTML::div ( $SkyCube ) );
// 	    $this->add ( HTML::div ( $SkyCubePEC ) );
// 	    $this->add ( HTML::div ( $SkyCubePEV ) );
// 	    $this->add ( HTML::div ( $SkyCubePCV ) );
        $this->add ( HTML::div ( SKDisplay::html ( $SkyCubeECV ) ) );
//         $this->add ( HTML::div ( $SkyCubeECV ) );
// 	    $this->add ( HTML::div ( $SkyCubeECV->toLaTeX () ) );
	    $this->add ( HTML::endDiv ( ) );
	}

	
} // PageCollParse
