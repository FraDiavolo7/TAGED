<?php

class PageAccueil extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
		$this->PageTitle = 'Accueil';
	
		$Switch = new Switcher ( $InputData );
        
        $this->addStats ();
	}

    protected function computeStats ( $AppName )
    {
        $this->Cols = array ();
        $Stats = array ();
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
                        $Stats [ $DataType_opt ] [ $ColName ] = $Value;
                        if ( ! in_array ( $ColName, $this->Cols ) )
                        { 
                            $this->Cols [ ] = $ColName;
                        }
                    }
                    else
                    {
                        $Stats [1][] = $Line;
                    }
                }
            }
        }
        //print_r ( $Stats );

        return $Stats;
    }

    protected function computeAllStats ( )
    {
        $Stats = array ();

        foreach ( $GLOBALS [ APP_LIST ] as $App )
        {
            $Stats [ $App ] = $this->computeStats ( $App );
        }

        return $Stats;
    }

    protected function doStatHead ( )
    {
        $LineContent = HTML::th ( 'Part' );
        foreach ( $this->Cols as $ColName )
        {
            $LineContent .= HTML::th ( $ColName );
        }
        return HTML::tr ( $LineContent );
    }

    protected function doStatLine ( $Stats, $Part = 'main' )
    {
        $LineContent = HTML::td ( $Part );
        foreach ( $this->Cols as $ColName )
        {
            $LineContent .= HTML::td ( isset ( $Stats [ $ColName ] ) ?  $Stats [ $ColName ] : '&nbsp;' ); 
        }
        return HTML::tr ( $LineContent );
    }


    protected function doStats ( $Data )
    {
        $TableHead = $this->doStatHead ();
        $TableContent = '';
        $TableContentFirst = '';
        $TableContentLast = '';
        $NbCols = count ( $this->Cols ) + 1;

        foreach ( $Data as $Index => $Stats )
        {
            if ( $Index === 0 )
            {
                $TableContentFirst = $this->doStatLine ( $Stats );
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
                $TableContent .= $this->doStatLine ( $Stats, $Index );
            }
        }
        return HTML::div ( HTML::table ( $TableHead . $TableContentFirst . $TableContent . $TableContentLast, array ( 'class' => 'taged_stats' ) ) );
    }


    public function addStats ()
    {
        $Stats = $this->computeAllStats ();

        $Content = '';

        foreach ( $Stats as $App => $Data )
        {
            $AppContent = HTML::title ( $App, 3 ); 
            $TableContent = $this->doStats ( $Data );
            $Content .= HTML::div ( $AppContent . $TableContent );
        }

	    $this->add ( HTML::div ( $Content ) );
    }

    protected $Cols;
} // PageAccueil
