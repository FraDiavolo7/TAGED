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
