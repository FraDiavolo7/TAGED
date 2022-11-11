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
        $Content = '';
        $this->check ();
        
        if ( $this->Runnable )
        {
            $Message = "Executing analysis on " . $this->DescFile;
            
            Log::info ( $Message );
            $Content .= $Message . PHP_EOL;
            
            $Name = basename ( $this->DescFile, '.ini' ); 
            $TmpFolder = AGGREGATE_FOLDER_TMP . $Name . "/";
            
            if ( ! is_dir ( $TmpFolder ) ) mkdir ( $TmpFolder );
            
            $AggregateFile = "$TmpFolder/aggregate";
            
            $Aggregate = $this->getAggregateFile ( TRUE ) ;

            $RelationCols = self::explodeCols ( $this->RelationCols );
            $MeasureCols  = self::explodeCols ( $this->MeasureCols  );
            
            $NbTuples = $Aggregate->getNbTuples ();
            $NbAttributes = count ( $RelationCols );
            
            $Aggregate->export ( $AggregateFile, $RelationCols, $MeasureCols, false );
            
            shell_exec ( "sed -i '1d' $AggregateFile.rel" );
            shell_exec ( "sed -i '1d' $AggregateFile.mes" );
            
            $AlgoOpt = " -m$M -n$N"; 
            
            $Command = "$Algorithm $AggregateFile $NbAttributes $NbTuples $AlgoOpt";
            
            $Content .= shell_exec ( $Command . ' 2>&1' ) . PHP_EOL;

            // rmdir ( $TmpFolder );
        }
        else 
        {
            $Content = 'Analyse non valide' . PHP_EOL;
        }
        
        return $Content;
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

}


