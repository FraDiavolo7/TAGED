<?php

/**
 * Classe représentant la page d'accueil.
 * 
 * @package TAGED\Pages
 */
class PageAccueil extends TagedPage
{
    /**
     * Constructeur de la classe PageAccueil.
     *
     * @param mixed $InputData Les données d'entrée pour la page.
     */
    public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Accueil';
	
		$Switch = new Switcher ( $InputData );
        
        Log::info ( 'pouet' );
        $this->addStats ();
	}

	/**
	 * Calcule les statistiques pour l'application spécifiée.
	 *
	 * @param array $App Les données de l'application.
	 * @return array Les statistiques calculées.
	 */
	protected function computeStats ( $App )
	{
	    $Stats = array ();
	    if ( $App [STATS_FILE] )
	    {
	        $Stats [STATS_FILE] = $this->computeStatsFiles ( $App );
	    }
	    if ( NULL != $App [STATS_DB] )
	    {
	        $Stats [STATS_DB] = $this->computeStatsDB ( $App );
	    }
	    return $Stats;
	}
	
	/**
	 * Calcule les statistiques à partir de la base de données pour l'application spécifiée.
	 *
	 * @param array $App Les données de l'application.
	 * @return array Les statistiques calculées depuis la base de données.
	 */
	protected function computeStatsDB ( $App )
	{
	    $StatsCols = array ();
	    $StatsData = array ();
	    $StatClass = $App [STATS_DB];
	    
	    Database::close (); // force initialization to switch DB
	    
	    $RawData = $StatClass::getStats ();
	    
	    foreach ( $RawData as $Raw )
	    {
	        $Stat = array (); 
	        foreach ( $Raw as $ColName => $Data )
	        {
	            if ( ! in_array ( $ColName, $StatsCols ) )
	            {
	                $StatsCols [ ] = $ColName;
	            }
	            $Stat [ $ColName ] = $Data;
	        }
	        $StatsData [] = $Stat;
	    }
	    
	    $Stats = array ();
	    $Stats [STATS_COLS] = $StatsCols;
	    $Stats [STATS_DATA] = $StatsData;
	    return $Stats;
	}
	
	
	/**
	 * Calcule les statistiques à partir des fichiers pour l'application spécifiée.
	 *
	 * @param array $App Les données de l'application.
	 * @return array Les statistiques calculées depuis les fichiers.
	 */
	protected function computeStatsFiles ( $App )
    {
        $AppName = $App [APP_NAME];
        $StatsCols = array ();
        $StatsData = array ();
        // Format of the Stats array :
        // [0] => Data for the main part
        // [1] => Data for invalid stats
        // [xxx] => Data for xxx optionnal part

        $StatFile = DATA_HOME . '/stat_' . $AppName;
        if ( file_exists ( $StatFile ) )
        {
            $Text = file_get_contents ( $StatFile );
            $TextLines = explode ( PHP_EOL, $Text );
            $NbLine = 0;

            foreach ( $TextLines as $Line )
            {
                if ( '' != $Line )
                {
                    // Line possible :
                    // DataType MesaureType : Value
                    // DataType_Opt MeasureType : Value
                    
                    // Cut the value from the rest
                    $Data = explode ( STATS_SEPARATOR, $Line );
                    if ( count ( $Data ) > 1 )
                    {
                        // Valuie exists, cut the Label
                        $Value = $Data [1];
                        $Label = $Data [0];
                        $FirstSpace = strpos ( $Label, " ");
                        $DataTypeLabel = substr ( $Label, 0, $FirstSpace );
                        $MeasureType   = substr ( $Label, $FirstSpace + 1 );

                        //echo "$Label => $DataTypeLabel";
                        $FirstUnderscore = strpos ( $DataTypeLabel, "_");
                        if ( FALSE !== $FirstUnderscore )
                        {
                            $DataType     = substr ( $DataTypeLabel, 0, $FirstUnderscore );
                            $DataType_opt = substr ( $DataTypeLabel, $FirstUnderscore + 1 );
                        }
                        else
                        {
                            $DataType = $DataTypeLabel;
                            $DataType_opt = 0;
                        }
                        //echo " => $DataType / $DataType_opt<br>\n";

                        $ColName = $DataType . ' ' . $MeasureType;
                        $StatsData [ $DataType_opt ] [ $ColName ] = $Value;
                        if ( ! in_array ( $ColName, $StatsCols ) )
                        { 
                            $StatsCols [ ] = $ColName;
                        }
                    }
                    else
                    {
                        $StatsData [1][] = $Line;
                    }
                }
            }
        }
        //print_r ( $Stats );

        $Stats = array ();
        $Stats [STATS_COLS] = $StatsCols;
        $Stats [STATS_DATA] = $StatsData;
        return $Stats;
	}

	/**
	 * Calcule toutes les statistiques pour toutes les applications.
	 *
	 * @return array Les statistiques calculées pour toutes les applications.
	 */
	protected function computeAllStats ( )
    {
        $Stats = array ();

        foreach ( $GLOBALS [ APP_LIST ] as $App )
        {
            $Stats [ $App [APP_NAME] ] = $this->computeStats ( $App );
        }

        return $Stats;
    }

    /**
     * Génère la ligne d'en-tête du tableau de statistiques.
     *
     * @param array $Columns Les colonnes du tableau.
     * @return string La ligne d'en-tête du tableau de statistiques.
     */
    protected function doStatHead ( $Columns )
    {
        $LineContent = HTML::th ( 'Part' );
        foreach ( $Columns as $ColName )
        {
            $LineContent .= HTML::th ( $ColName );
        }
        return HTML::tr ( $LineContent );
    }

    /**
     * Génère une ligne de statistiques pour le tableau.
     *
     * @param array $Stats Les statistiques.
     * @param array $Columns Les colonnes du tableau.
     * @param string $Part La partie des statistiques.
     * @return string La ligne de statistiques pour le tableau.
     */
    protected function doStatLine ( $Stats, $Columns, $Part = 'main' )
    {
        $LineContent = HTML::td ( $Part );
        foreach ( $Columns as $ColName )
        {
            $LineContent .= HTML::td ( isset ( $Stats [ $ColName ] ) ?  $Stats [ $ColName ] : '&nbsp;' ); 
        }
        return HTML::tr ( $LineContent );
    }

    /**
     * Génère le tableau de statistiques.
     *
     * @param array $Data Les données de statistiques.
     * @return string Le tableau de statistiques au format HTML.
     */
    protected function doStats ( $Data )
    {
        $StatsData = $Data [STATS_DATA];
        $Columns = $Data [STATS_COLS];
        
        $TableHead = $this->doStatHead ( $Columns );
        $TableContent = '';
        $TableContentFirst = '';
        $TableContentLast = '';
        $NbCols = count ( $Columns ) + 1;

        foreach ( $StatsData as $Index => $Stats )
        {
            if ( $Index === 0 )
            {
                $TableContentFirst = $this->doStatLine ( $Stats, $Columns );
            }
            elseif ( $Index === 1 )
            {
                foreach ( $Stats as $Line )
                {
                    $TableContentLast .= HTML::tr ( HTML::td ( trim ( $Line ), array ( 'colspan' => $NbCols ) ) );
                }
            }
            else
            {
                $TableContent .= $this->doStatLine ( $Stats, $Columns, $Index );
            }
        }
        return HTML::div ( HTML::table ( $TableHead . $TableContentFirst . $TableContent . $TableContentLast, array ( 'class' => 'taged_stats' ) ) );
    }

    /**
     * Ajoute une section de statistiques au contenu de la page.
     *
     * @param array $Data Les données de statistiques.
     * @param string $StatType Le type de statistiques.
     * @return string La section de statistiques au format HTML.
     */
    protected function addStat ( $Data, $StatType )
    {
        $StatTitle = HTML::title ( $StatType, 4 );
        $TableContent = $this->doStats ( $Data );
        return HTML::div ( $StatTitle . $TableContent );
    }

    /**
     * Ajoute toutes les statistiques calculées à la page.
     */
    public function addStats () 
    {
        $Stats = $this->computeAllStats ();

        $Content = '';
        
        foreach ( $Stats as $App => $Data )
        {
            $DivContent = HTML::title ( $App, 3 );
            
            foreach ( $Data as $StatType => $Stat )
            {
                $DivContent .= $this->addStat ( $Stat, $StatType );
            }
            
            $Content .= HTML::div ( $DivContent );
        }

	    $this->add ( HTML::div ( $Content ) );
        TagedDBHnS::stats ();
    }

    /**
     * Les colonnes des statistiques.
     *
     * @var mixed $Cols Les colonnes des statistiques.
     */
    protected $Cols;
} // PageAccueil
