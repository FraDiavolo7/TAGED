<?php

class PageShowSkyCube extends TagedPage
{
    const SHOW_AGGREGATE = 'sskc_aggregate';
    const SHOW_PASSWORD = 'sskc_password';
    const SHOW_SUBMIT = 'sskc_submit';
    
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Affichier Aggregation en SkyCube Emergent';
		$this->Password = 'EmrakulUnMouton';
		$this->Aggregate = '';
		$this->AggregateObj = NULL;
		$this->SkyCube = NULL;
		
		$this->AggregateListObj = new AggregateList ();
		$this->AggregateList = array_merge ( array ( "" => 'Choisis' ), $this->AggregateListObj->getList () );
		
		$Switch = new Switcher ( $InputData );
		
		$this->handle ( $Data ); 
	} 

	protected function getTestData ()
	{
        $DataSet = array ();
        $DataSet [] = array ( 'RowID' => 1, 'Type' => 'Feticheur', 'Propriete' => 'Vitesse', 'Parangon1' => 600, 'Rarete1' => 5,  'Gemmes1' => 30, 'Succes1' => 3,  'Parangon2' => 650, 'Rarete2' => 6,  'Gemmes2' => 40, 'Succes2' => 2 );
        $DataSet [] = array ( 'RowID' => 2, 'Type' => 'Feticheur', 'Propriete' => 'Vitesse', 'Parangon1' => 900, 'Rarete1' => 5,  'Gemmes1' => 85, 'Succes1' => 7,  'Parangon2' => 950, 'Rarete2' => 6,  'Gemmes2' => 95, 'Succes2' => 6 );
        $DataSet [] = array ( 'RowID' => 3, 'Type' => 'Sorciere',  'Propriete' => 'Chance',  'Parangon1' => 600, 'Rarete1' => 10, 'Gemmes1' => 50, 'Succes1' => 7,  'Parangon2' => 650, 'Rarete2' => 11, 'Gemmes2' => 60, 'Succes2' => 6 );
        $DataSet [] = array ( 'RowID' => 4, 'Type' => 'Sorciere',  'Propriete' => 'Vitesse', 'Parangon1' => 100, 'Rarete1' => 10, 'Gemmes1' => 85, 'Succes1' => 5,  'Parangon2' => 150, 'Rarete2' => 11, 'Gemmes2' => 95, 'Succes2' => 4 );
        $DataSet [] = array ( 'RowID' => 5, 'Type' => 'Croise',    'Propriete' => 'Chance',  'Parangon1' => 900, 'Rarete1' => 10, 'Gemmes1' => 50, 'Succes1' => 7,  'Parangon2' => 950, 'Rarete2' => 11, 'Gemmes2' => 60, 'Succes2' => 6 );
	    return $DataSet;
	}
	
	protected function getTestMesCols ()
	{
        $MesCols = array ();
        $MesCols [] = 'Parangon1';
        $MesCols [] = 'Rarete1';
        $MesCols [] = 'Gemmes1';
        $MesCols [] = 'Succes1';
        $MesCols [] = 'Parangon2';
        $MesCols [] = 'Rarete2';
        $MesCols [] = 'Gemmes2';
        $MesCols [] = 'Succes2';
        return $MesCols;
	}
	
	protected function getTestRelCols ()
	{
        $RelCols = array ();
        $RelCols [] = 'RowID';
        $RelCols [] = 'Type';
	    $RelCols [] = 'Propriete';
	    return $RelCols;
	}
	
	protected function handle ( $Data )
	{
	    $Submit = Form::getData ( self::SHOW_SUBMIT, '', $Data );
	    $this->Aggregate = Form::getData ( self::SHOW_AGGREGATE, '', $Data );
	    $AggregateFile = NULL;
	    
	    if ( '' != $this->Aggregate )
	    {
	        $this->AggregateObj = new Analysis ( $this->Aggregate . '.ini' );
	    }
	    if ( $Submit != '' )
	    {
	        $Password = Form::getData ( self::SHOW_PASSWORD, '', $Data );
	        
	        if ( $Password == $this->Password )
	        {
	            
	            $Aggregate = $this->AggregateObj->getAggregateFile ( TRUE ) ;
	            
	            
	            $DataSet = $Aggregate->getData ();
	            $RelCols = explode ( ',', $this->AggregateObj->getRelationCols () );
	            $MesCols = explode ( ',', $this->AggregateObj->getMeasureCols () );
	            
// 	            echo '$RelCols ' . print_r ( $RelCols, TRUE ) . "<br>";
// 	            echo '$RelCols ' . print_r ( $this->getTestRelCols (), TRUE ) . "<br>";
// 	            echo '$MesCols ' . print_r ( $MesCols, TRUE ) . "<br>";
// 	            echo '$MesCols ' . print_r ( $this->getTestMesCols (), TRUE ) . "<br>";
// 	            echo '$DataSet ' . print_r ( $this->getTestData (), TRUE ) . "<br>";
// 	            echo '$DataSet ' . print_r ( $DataSet, TRUE ) . "<br>";
	            
	            $this->SkyCube = new SkyCubeEmergent ( $DataSet, $RelCols, $MesCols, Cuboide::TO_MIN );
                
	        }
	    }
	    
	    $this->show ();
	}

	protected function show ( )
	{
	    $Password = HTML::div ( HTML::inputPassword ( self::SHOW_PASSWORD, '' ), array ( 'class' => 'passwd' ) );
	    $Aggregate = HTML::div ( HTML::select ( self::SHOW_AGGREGATE, $this->AggregateList, $this->Aggregate, array ( 'onchange' => 'this.form.submit()' ) ), array ( 'class' => 'aggregate' ) );
	    $Submit = HTML::div ( HTML::submit( self::SHOW_SUBMIT, "Envoyer" ), array ( 'class' => 'submit' ) );
	    
	    $Result = '';
	    
	    if ( NULL != $this->SkyCube )
	    {
	        $Result = HTML::div ( SKDisplay::html ( $this->SkyCube ) );
	    }
	    
	    $Content = $Aggregate;
	    
	    if ( '' != $this->Aggregate )
	    {
	        $Content .= $Password;
	        $Content .= $Submit;
	        $Content .= $Result;
	    }
	    
	    $this->add ( HTML::form ( $Content, array ( 'method' => 'POST',  'enctype' => 'multipart/form-data' ) ) );
	}
	
	protected $SkyCube;
	protected $Password;
	protected $Aggregate;
	protected $AggregateObj;
	
	protected $AggregateListObj;
	protected $AggregateList;
} // PageShowSkyCube
