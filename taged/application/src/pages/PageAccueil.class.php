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
        $Stats = array ();

        $Folder = DATA_HOME . '/' . $AppName;
        $Cmd = STATS_GET_SCRIPT . ' ' . $Folder;

        $Text = shell_exec ( $Cmd );
        $TextLines = explode ( PHP_EOL, $Text );

        foreach ( $TextLines as $Line )
        {
            if ( '' != $Line )
            {
                $Data = explode ( STATS_SEPARATOR, $Line );
                if ( count ( $Data ) > 1 )
                {
                    $Stats [ $Data [0] ] = $Data [ 1 ];
                }
                else
                {
                    $Stats [] = $Line;
                }
            }
        }

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

    public function addStats ()
    {
        $Stats = $this->computeAllStats ();

        $Content = '';

        foreach ( $Stats as $App => $Data )
        {
            $AppContent = HTML::title ( $App, 3 ); 
            $TableContent = '';

            foreach ( $Data as $Index => $Value )
            {
                $Label = ( is_numeric ( $Index ) ? '' : $Index );
                $TableContent .= HTML::tr ( HTML::td ( trim ( $Label ) ) . HTML::td ( trim ( $Value ) ) );
            }
            $Content .= HTML::div ( $AppContent . HTML::div ( HTML::table ( $TableContent ) ) );
        }

	    $this->add ( HTML::div ( $Content ) );
    }

} // PageAccueil
