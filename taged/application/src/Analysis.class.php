<?php

class Analysis
{
    const FILE     = 'AggregateFile';
    const TABLE    = 'AggregateTable';
    const DBCLASS  = 'DBClass';
    const REL_COLS = 'RelationCols';
    const MES_COLS = 'MeasureCols';
    
    const GAME_DB = array ( 
        APP_NAME_COLLECTION   => 'TagedDBColl', 
        APP_NAME_MATCH3       => 'TagedDBMatch3',
        APP_NAME_HACK_N_SLASH => 'TagedDBHnS'
    );
    
    public function __construct ( $DescFile )
    {
        $this->DescFile = $DescFile;
        $this->RequestFile  = NULL; 
        $this->DBTable      = NULL;
        $this->DBClass      = NULL;
        $this->RelationCols = NULL;
        $this->MeasureCols  = NULL;
        $this->Runnable     = FALSE;
        $this->DescFilePath = AGGREGATE_FOLDER_DESC . $this->DescFile;
        
        $this->load ();
    }
    
    protected function load ()
    {
        
        if ( file_exists ( $this->DescFilePath ) )
        {
            $AnalysisData = parse_ini_file ( $this->DescFilePath );
            
            $this->RequestFile  = Arrays::getIfSet ( $AnalysisData, self::FILE,     NULL );
            $this->DBTable      = Arrays::getIfSet ( $AnalysisData, self::TABLE,    NULL );
            $this->DBClass      = Arrays::getIfSet ( $AnalysisData, self::DBCLASS,  NULL );
            $this->RelationCols = Arrays::getIfSet ( $AnalysisData, self::REL_COLS, Arrays::EXPORT_COLUMN_NO_HEADER );
            $this->MeasureCols  = Arrays::getIfSet ( $AnalysisData, self::MES_COLS, Arrays::EXPORT_COLUMN_NO_HEADER );
            
        }
    }
    
    protected function check ()
    {
        $this->Runnable = FALSE;
        
        if ( NULL == $this->DBClass )
        {
            Log::error ( "No DB defined for " . $this->DescFile . " cannot run analysis." );
        }
        
        else if ( ( NULL == $this->DBTable ) && ( NULL == $this->RequestFile ) ) 
        {
            Log::error ( "No Table or Request File defined for " . $this->DescFile . " cannot run analysis." );
        }
        
        else if ( '' == $this->RelationCols )
        {
            Log::error ( "No Columns for relation defined for " . $this->DescFile . " cannot run analysis." );
        }
        
        else if ( '' == $this->MeasureCols )
        {
            Log::error ( "No Columns for measures defined for " . $this->DescFile . " cannot run analysis." );
        }
        
        else
        {
            $this->Runnable = TRUE;
        }
    }
    
    protected static function explodeCols ( $Cols )
    {
        $Result = $Cols;
        
        if ( Arrays::EXPORT_COLUMN_NO_HEADER != $Cols )
        {
            $Tmp = explode ( ',', $Cols );
            $Result = array ();
            
            foreach ( $Tmp as $Value )
            {
                $Result [ $Value ] = $Value;
            }
        }
        
        return $Result;
    }
    
    public function run ( $Algorithm, $M, $N )
    {
        $Result = FALSE;
        $this->Result = '';
        $this->check ();
        
        if ( $this->Runnable )
        {
            $Message = "Executing analysis on " . $this->DescFile . " ($Algorithm)";
            
            Log::info ( $Message );
            $this->Result .= $Message . PHP_EOL;
            
            $Name = basename ( $this->DescFile, '.ini' ); 
            $TmpFolder = AGGREGATE_FOLDER_TMP . $Name . "/";
            $ResultFolder = AGGREGATE_FOLDER_RESULTS . $Name . "/";
            
            if ( is_dir ( $TmpFolder ) ) 
            {
                shell_exec ( "rm -Rf $TmpFolder" );
            }
            
            mkdir ( $TmpFolder );
            
            $AggregateFile = "$TmpFolder/aggregate";
            
            $Aggregate = $this->getAggregateFile ( TRUE ) ;

            $RelationCols = self::explodeCols ( strtolower ( $this->RelationCols ) );
            $MeasureCols  = self::explodeCols ( strtolower ( $this->MeasureCols  ) );
            
            $NbTuples = $Aggregate->getNbTuples ();
            $NbAttributes = count ( $RelationCols );
            
            $Aggregate->export ( $AggregateFile, $RelationCols, $MeasureCols, false );
            
            shell_exec ( "sed -i '1d' $AggregateFile.rel" );
            shell_exec ( "sed -i '1d' $AggregateFile.mes" );
            
            $AlgoOpt = " -m$M -n$N"; 
            
            $Command = "$Algorithm $AggregateFile $NbAttributes $NbTuples $AlgoOpt";
            
            $this->Result .= shell_exec ( $Command . ' 2>&1' ) . PHP_EOL;

            shell_exec ( "rm -Rf $ResultFolder" );
            shell_exec ( "mv $TmpFolder $ResultFolder" );
            
            $Result = TRUE;
        }
        else 
        {
            $this->Result = 'Analyse non valide' . PHP_EOL;
            $Result = FALSE;
        }
        
        return $Result;
    }
    
    public function formatResult ()
    {
        $Name = basename ( $this->DescFile, '.ini' );
        $ResultFolder = AGGREGATE_FOLDER_RESULTS . $Name . "/";
        
        $FileBase = 'aggregate';
        
        $AggregateFile = "$ResultFolder/$FileBase";
        
        $CubeFile = "$AggregateFile.cube.emergent";
        
        clearstatcache (); // to prevent cache of file size
        if ( file_exists ( $CubeFile ) && filesize ( $CubeFile ) )  // if file is not empty
        {
            // Load Field Translation
            $Files = scandir ( $ResultFolder );
            $TradFields = array ();
            
            foreach ( $Files as $File )
            {
                $Attr = "$FileBase.attr.";
                if ( Strings::compareSameSize ( $File, $Attr ) )
                {
                    $FieldName = substr ( $File, strlen ( $Attr ) );
                    $TradFields [$FieldName] = parse_ini_file ( "$ResultFolder/$File" );
                }
            }
            
            //         echo '$TradFields ' . print_r ( $TradFields, TRUE ) . "<br>";
            
            // Get Fields
            $RelFields = explode ( ',', $this->RelationCols );
            $MesFields = explode ( ',', $this->MeasureCols  );
            
            $FieldNames = array_merge ( $RelFields, $MesFields );
            
            //         echo '$FieldNames ' . print_r ( $FieldNames, TRUE ) . "<br>";
            
            $this->Result  = HTML::startTable ( array ( 'class' => 'taged_stats' ));
            $this->Result .= HTML::startTR ();
            
            // Compute an array of translation indexed by ordered fields
            $Fields = array ();
            foreach ( $FieldNames as $FieldName )
            {
                $Array = array ();
                
                $this->Result .= HTML::th ( $FieldName );
                
                $FieldName = rtrim ( $FieldName, "0123456789" );
                
                if ( isset ( $TradFields [$FieldName] ) ) $Array = $TradFields [$FieldName];
                
                $Fields [] = $Array;
            }
            $this->Result .= HTML::endTR ();
            
            //         echo '$Fields ' . print_r ( $Fields, TRUE ) . "<br>";
            
            // For each line of Cube, convert relevant fields
            $CubeFile = fopen ( "$AggregateFile.cube.emergent", "r" );
            if ( $CubeFile )
            {
                while ( ( $Line = fgets ( $CubeFile ) ) !== false )
                {
                    $Entries = explode ( ' ', $Line );
                    $this->Result .= HTML::startTR ();
                    
                    foreach ( $Entries as $Num => $Entry )
                    {
                        $OutEntry = $Entry;
                        if ( isset ( $Fields [$Num] [$Entry] ) ) $OutEntry = $Fields [$Num] [$Entry];
                        
                        if ( ':' != $OutEntry ) $this->Result .= HTML::td ( $OutEntry );
                    }
                    $this->Result .= HTML::endTR ();
                }
                
                fclose ( $CubeFile );
            }
            
            $this->Result .= HTML::endTable ();
        }
        
        return $this->Result;
    }
 
    protected function runCuboide ( $Cuboide, $Algorithm, $M, $N )
    {
        $DataSet    = $Cuboide->getDataSet ();
        $RowHeaders = $Cuboide->getRowHeaders ();
        $ColIDs     = array_flip ( $Cuboide->getColIDs () );
        
        $DataToProcess = array ();
        $ColumnList = $this->getSkyCubeColumns ( );
        
        foreach ( $DataSet as $RowID => $Row )
        {
            foreach ( $ColumnList as $ColName => $ColIDList )
            {
                $Init = TRUE;
                foreach ( $ColIDList as $ColHeader )
                {
                    if ( isset ( $ColIDs [ $ColHeader ] ) )
                    {
                        $ColID = $ColIDs [ $ColHeader ];
                        if ( $Init )
                        {
                            $DataToProcess [$ColName] [$RowID] = $RowHeaders [$RowID];
                            $Init = FALSE;
                        }
                        $DataToProcess [$ColName] [$RowID] [$ColID] = $DataSet [$RowID] [$ColID];
                    }
                }
            }
        }
        
        foreach ( $DataToProcess as $ColName => $Data )
        {
            echo "Data to Process : " . $Cuboide->getID ( ) . " " . $ColName . "<br>";
            echo HTML::tableFull ( $Data,  array ( 'border' => '1' ) );
        }
    }
    
    public function runSkyCube ( $Algorithm, $M, $N, $MinMax = Cuboide::TO_MAX )
    {
        $SkyCube = $this->getSkyCube ( TRUE, $MinMax );
        
        if ( NULL != $SkyCube )
        {
            $SkCuboides = $SkyCube->getCuboides ();
            foreach ( $SkCuboides as $Level => $Cuboides )
            {
                foreach ( $Cuboides as $Cuboide )
                {
                    $this->runCuboide ( $Cuboide, $Algorithm, $M, $N );
                }
            }
        }
    }
    
    public function getSkyCubeColumns ( )
    {
        $Names = array ();
        $MesCols = explode ( ',', $this->getMeasureCols () );
        $Ignore = array ();
        foreach ( $MesCols as $ColHeader )
        {
            if ( ! in_array ( $ColHeader, $Ignore ) )
            {
                $LastChar = substr ( $ColHeader, -1 );
                $Col2 = substr_replace ( $ColHeader, 2, -1 );
                
                if ( ( $LastChar == '1' ) && ( in_array ( $Col2, $MesCols ) ) )
                {
                    $Names  [substr ( $ColHeader, 0, -1 )] = array ( $ColHeader, $Col2 );
                    $Ignore [] = $Col2;
                }
                else
                {
                    $Names  [$ColHeader] = array ( $ColHeader );
                }
            }
        }
        return $Names;
    }
    
    
    public function getResult ()
    {
        return $this->Result;
    }
    
    public static function create ( $Name, $Game, $RelationCols, $MeasureCols, $File = NULL, $Table = NULL )
    {
        $DescFile = new Analysis ( $Name . '.ini' );
        
        $DescFile->setDBClass      ( self::GAME_DB [ $Game ] );
        $DescFile->setDBTable      ( $Table        );
        $DescFile->setMeasureCols  ( $MeasureCols  );
        $DescFile->setRelationCols ( $RelationCols );
        $DescFile->setRequestFile  ( $File         );
        $DescFile->write ();
        
        if ( NULL != $File ) 
        {
            file_put_contents ( AGGREGATE_FOLDER_REQUESTS . $File, '' );
        }
        
    }
    
    public static function delete ( $Name )
    {
        $FilePath = AGGREGATE_FOLDER_DESC . $Name . '.ini';
        
        if ( file_exists ( $FilePath ) )
        {
            $DescFile = new Analysis ( $FilePath );
            $RequestFile = $DescFile->getRequestFile ();

            unset ($DescFile);
            
            if ( NULL != $RequestFile ) unlink ( $RequestFile );
            
            unlink ( $FilePath );
        }
    }
    
    public function getAggregateFile ( $AsNumerics = FALSE )
    {
        return new AggregateFile ( $this->DBClass, AGGREGATE_FOLDER_REQUESTS . $this->RequestFile, $this->DBTable, $AsNumerics );
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
    
    public function getSkyCube ( $AsNumerics = FALSE, $MinMax = Cuboide::TO_MAX, $Bidon = FALSE )
    {
        $SkyCube = NULL;
        
        $Aggregate = $this->getAggregateFile ( $AsNumerics ) ;
        
        if ( $Bidon )
        {
            $DataSet = $this->getTestData ();
            $RelCols = $this->getTestRelCols ();
            $MesCols = $this->getTestMesCols ();
        }
        else
        {
            $DataSet = $Aggregate->getData ();
            $RelCols = explode ( ',', $this->getRelationCols () );
            $MesCols = explode ( ',', $this->getMeasureCols () );
        }
        
        $SkyCube = new SkyCubeEmergent ( $DataSet, $RelCols, $MesCols, $MinMax );
        
        return $SkyCube;
    }
    
    public function getRequestFile  () { return $this->RequestFile ; }
    public function getDBTable      () { return $this->DBTable     ; }
    public function getDBClass      () { return $this->DBClass     ; }
    public function getRelationCols () { return $this->RelationCols; }
    public function getMeasureCols  () { return $this->MeasureCols ; }
    
    public function setRequestFile  ( $NewValue ) { $this->RequestFile  = $NewValue; }
    public function setDBTable      ( $NewValue ) { $this->DBTable      = $NewValue; }
    public function setDBClass      ( $NewValue ) { $this->DBClass      = $NewValue; }
    public function setRelationCols ( $NewValue ) { $this->RelationCols = $NewValue; }
    public function setMeasureCols  ( $NewValue ) { $this->MeasureCols  = $NewValue; }
    
    public function write ( )
    {
        $Content = '';
        
        if ( NULL != $this->DBClass      ) $Content .= self::DBCLASS  . "=" . $this->DBClass      . PHP_EOL;
        if ( NULL != $this->DBTable      ) $Content .= self::TABLE    . "=" . $this->DBTable      . PHP_EOL;
        if ( NULL != $this->RequestFile  ) $Content .= self::FILE     . "=" . $this->RequestFile  . PHP_EOL;
        if ( NULL != $this->RelationCols ) $Content .= self::REL_COLS . "=" . $this->RelationCols . PHP_EOL;
        if ( NULL != $this->MeasureCols  ) $Content .= self::MES_COLS . "=" . $this->MeasureCols  . PHP_EOL;
        
        file_put_contents ( $this->DescFilePath, $Content );
    }
    
    protected $DescFile;
    protected $DescFilePath;
    protected $RequestFile;
    protected $DBTable;
    protected $DBClass;
    protected $RelationCols;
    protected $MeasureCols;
    protected $Runnable;
    protected $Result;
}


