<?php

class AnalysisTest extends Analysis
{
    
    /*
     * Utilisation attendue :
     * $Truc = new Analysis ( $FilePath, $IsTest )
     * $Truc->setAlgorithm ( $Algo );
     * $Truc->prepare (); // Generate the SkyCube to for computation
     * $Truc->compute (); // TAGED run on Each attribute of each Cuboide => Sub functions...
     *
     * $Truc->getDataSet ();
     * $Truc->getSkyCube ();
     * 
     */
    
    public function __construct ( $DescFile, $IsTest = FALSE )
    {
        $this->DescFile = $DescFile;
        $this->RequestFile  = NULL; 
        $this->DBTable      = NULL;
        $this->DBClass      = NULL;
        $this->RelationCols = NULL;
        $this->MeasureCols  = NULL;
        $this->Runnable     = FALSE;
        $this->DescFilePath = AGGREGATE_FOLDER_DESC . $this->DescFile;
        $this->Algorithm    = NULL;
        $this->DataSet      = NULL;
        $this->SkyCube      = NULL;
        $this->IsTest       = $IsTest;
    }
    
    protected function load ()
    {
        
        if ( file_exists ( $this->DescFilePath ) )
        {
            $AnalysisData = parse_ini_file ( $this->DescFilePath );
            
            $this->RequestFile  = Arrays::getIfSet ( $AnalysisData, self::FILE,     NULL );
            $this->DBTable      = Arrays::getIfSet ( $AnalysisData, self::TABLE,    NULL );
            $this->DBClass      = Arrays::getIfSet ( $AnalysisData, self::DBCLASS,  NULL );
            $RelationCols = Arrays::getIfSet ( $AnalysisData, self::REL_COLS, Arrays::EXPORT_COLUMN_NO_HEADER );
            $MeasureCols  = Arrays::getIfSet ( $AnalysisData, self::MES_COLS, Arrays::EXPORT_COLUMN_NO_HEADER );
            $this->RelationCols = self::explodeCols ( strtolower ( $RelationCols ) );
            $this->MeasureCols  = self::explodeCols ( strtolower ( $MeasureCols  ) );
            
        }
    }
    
    protected function check ()
    {
        $this->Runnable = FALSE;
        
        if ( ( NULL == $this->DBTable ) && ( NULL == $this->RequestFile ) )
        {
            Log::error ( "No Table or Request File defined for " . $this->DescFile . " cannot run analysis." );
        }
        
        else if ( NULL == $this->DataSet )
        {
            Log::error ( "No DataSet defined for " . $this->DescFile . " cannot run analysis." );
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
    
    public function prepare ()
    {
        $this->Name = basename ( $this->DescFile, '.ini' );
        
        if ( $this->IsTest )
        {
            $this->Name .= '_Test';
            $this->DataSet      = $this->getTestData ();
            $this->RelationCols = $this->getTestRelCols ();
            $this->MeasureCols  = $this->getTestMesCols ();
            $this->DBTable = 'osef'; // To pass check
        }
        else
        {
            $this->load ();
            
            $TmpFolder = AGGREGATE_FOLDER_TMP . $Name . "/";
            $ResultFolder = AGGREGATE_FOLDER_RESULTS . $Name . "/";
            
            if ( is_dir ( $TmpFolder ) )
            {
                shell_exec ( "rm -Rf $TmpFolder" );
            }
            
            mkdir ( $TmpFolder );
            
            $AggregateFile = "$TmpFolder/aggregate";
            
            $Aggregate = $this->getAggregateFile ( TRUE ) ;
            

            $NbTuples = $Aggregate->getNbTuples ();
            $NbAttributes = count ( $RelationCols );
            
            $this->DataSet = $Aggregate->getData ( );
        }
        
        $this->check ();
        
        if ( $this->Runnable )
        {
            $this->SkyCube = new SkyCubeEmergent ( $this->DataSet, $this->RelationCols, $this->MeasureCols, $MinMax );
        }
    }
    
    public function cleanData ( $Data )
    {
        return str_replace ( array (';', ',', ' '), '_', $Data );
    }
    
    protected $AttributeValues;
    protected $AttributeIgnored;
    
    protected function convertToNumerics ( $Value, $Attribute )
    {
        $Result = $Value;
        
        $Attr = rtrim ( $Attribute, "0123456789" );
        if ( ! isset ( $this->AttributeValues [$Attr] ) )
        {
            if ( is_numeric ( $Value ) )
            {
                $this->AttributeIgnored [$Attr] = TRUE;
            }
            else
            {
                $this->AttributeValues [$Attr] = array ();
                $Result = 1;
            }
        }
        elseif ( ! isset ( $this->AttributeIgnored [$Attr] ) )
        {
            $Result = array_search ( $Value, $this->AttributeValues [$Attr] );
            if ( FALSE === $Result )
            {
                $Result = count ( $this->AttributeValues [$Attr] ) + 1;
            }
        }
        
        $this->AttributeValues [$Attr] [$Result] = $Value;
        
        return $Result;
    }
    
    protected function computeCuboideAttribute ( $CuboideID, $ColID, $Folder )
    {
        echo __FILE__ . ':' . __LINE__ . " $CuboideID $ColID<br>";
        $FileName   = $CuboideID . '-' . $ColID;
        $FilePath   = $Folder . $FileName;
        $TupleFile  = $FilePath . '.nbtuple';
        $AttrValues = $FilePath . '.attr.';
        $RelPath    = $FilePath . '.rel';
        $MesPath    = $FilePath . '.mes';
        
        $Cuboide = $this->SkyCube->getCuboide ( $CuboideID );
        $InputDataSet = $Cuboide->getDataSetFiltered ( );
        $InputRowHeaders = $Cuboide->getRowHeadersFiltered ( );
        
        $MeasureHeaders  = array ( $ColID . '1', $ColID . '2' );
        $RelationHeaders = array_keys ( $InputRowHeaders [0] );
        
        foreach ( $RelationHeaders as $RowID => $ColumnName )
        {
            if ( strtolower ( $ColumnName ) == 'rowid' )
            {
                unset ( $RelationHeaders [$RowID] );
            }
        }
        
        $Measures  = Arrays::getColumns ( $InputDataSet,    $MeasureHeaders  );
        //$Relations = Arrays::getColumns ( $InputRowHeaders, $RelationHeaders );
        /**/
        $RelationsRaw = Arrays::getColumns ( $InputRowHeaders, $RelationHeaders );
        $Relations = array ();
         
        // Anonymisation
        foreach ( $RelationsRaw as $RowID => $Row )
        {
            foreach ( $Row as $Attr => $Value )
            {
                if ( ! is_numeric ( $Attr ) )
                {
                    if ( ! in_array ( $Attr, $this->Attributes ) ) $this->Attributes [] = $Attr;
                    
                    $Relations [ $RowID ] [ $Attr ] = $this->convertToNumerics ( $Value, $Attr );
                }
            }
        }
//        echo __FILE__ . ':' . __LINE__ . " <pre>" . print_r ( $this->AttributeValues, TRUE ) . "</pre> <br>";
        
        // Export Anonymisation
        foreach ( $RelationHeaders as $ColumnName )
        {
            $AttrValuesFile = $AttrValues . $ColumnName;
            foreach ( $this->AttributeValues [$ColumnName] as $Key => $Val )
            {
                file_put_contents ( $AttrValuesFile, "$Key=$Val" . PHP_EOL, FILE_APPEND );
            }
        }
        /**/
        
        // Conversion de 
        // A B => X Y
        // en 
        // A B => X 0
        // A B => 0 Y
        
        $TAGEDMeasures = array ();
        $TAGEDRelations = array ();
        
        foreach ( $Measures as $RowID => $Row )
        {
            $NbAttr = 0;
            $NbZeros = 0;
            foreach ( $Row as $MeasureID => $Value )
            {
                ++$NbAttr;
                if ( $Value === 0 )
                {
                    ++$NbZeros;
                }
            }
            
            $NbCopies = $NbAttr - $NbZeros;
            
            if ( $NbCopies > 1 )
            {
                $TmpMes = array ();
                $TmpRel = array ();
                $Current = 0;
                foreach ( $Row as $MeasureID => $Value )
                {
                    for ( $i = 0 ; $i < $NbCopies ; ++$i )
                    {
                        $NewValue = 0;
                        if ( ( $Value != 0 ) && ( $Current == $i ) )
                        {
                            $NewValue = $Value;
                        }
                        $TmpMes [$i][$MeasureID] = $NewValue;
                    }
                    ++$Current;
                }
                
                foreach ( $TmpMes as $NewRow )
                {
                    $TAGEDMeasures [] = $NewRow;
                    $TAGEDRelations [] = $Relations [$RowID];
                }
            }
            else
            {
                $TAGEDMeasures [] = $Row;
                $TAGEDRelations [] = $Relations [$RowID];
            }
        }
        
        
//        $Relations = array_map ( array ( $this, 'cleanData' ), $RelationsRaw );
//        echo __FILE__ . ':' . __LINE__ . " <pre>" . print_r ( $TAGEDMeasures, TRUE ) . "</pre> <br>";
        
        $NbTuples = count ( $TAGEDMeasures );
        
        Arrays::exportAsCSV ( $TAGEDRelations, ' ', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $RelPath, array (), array (), ';' );
        Arrays::exportAsCSV ( $TAGEDMeasures,  ' ', Arrays::EXPORT_COLUMN_NO_HEADER, Arrays::EXPORT_ROW_NO_HEADER, $MesPath, array (), array (), ';' );
        
        file_put_contents ( $TupleFile, $NbTuples . PHP_EOL );

        $NbAttributes = count ( $RelationHeaders );
        
        $M = min ( min ( $Measures ) );
        $N = max ( max ( $Measures ) );
        
        $AlgoOpt = " -m$M -n$N";
        
        $Command = "$this->Algorithm $FilePath $NbAttributes $NbTuples $AlgoOpt";
        
        echo __FILE__ . ':' . __LINE__ . " <pre>" . print_r ( $Command, TRUE ) . "</pre> <br>";
        
        $ShellResult = shell_exec ( $Command . ' 2>&1' ) . PHP_EOL;
        
        echo __FILE__ . ':' . __LINE__ . " <pre>" . print_r ( $ShellResult, TRUE ) . "</pre> <br>";
    }
    
    protected function computeCuboide ( $CuboideID, $Folder )
    {
        echo __FILE__ . ':' . __LINE__ . " $CuboideID $Folder<br>";
        $Cuboide = $this->SkyCube->getCuboide ( $CuboideID );
        $ColIDs = $Cuboide->getColIDs ();
        
        $AnalysisColIDs = array ();
        $Ignore = array ();
        
        //         Log::logVar ( '$MeasureCols', $MeasureCols );
        
        foreach ( $ColIDs as $ColID => $Osef )
        {
            $LastChar = substr ( $ColID, -1 );
            $BaseCol = substr ( $ColID, 0, -1 );
            $Col2 = substr_replace ( $ColID, 2, -1 );
            
            if ( ! in_array ( $ColID, $Ignore ) )
            {
                if ( ( $LastChar == '1' ) && ( isset ( $ColIDs [$Col2] ) ) )
                {
                    $AnalysisColIDs [] = $BaseCol;
                    $Ignore [] = $Col2;
                }
            }
        }
        
        foreach ( $AnalysisColIDs as $ColID )
        {
            $this->computeCuboideAttribute ( $CuboideID, $ColID, $Folder );
        }
    }
    
    public function compute ()
    {
        $Result = FALSE;
        $this->Result = '';
        $this->check ();
        
        if ( $this->Runnable )
        {
            $Message = "Computing analysis on " . $this->DescFile . " ($this->Algorithm)";
            
            Log::info ( $Message );
            
            echo $Message . "<br>";
            
            $this->Result .= $Message . PHP_EOL;
            
            $TmpFolder  = AGGREGATE_FOLDER_TMP . $this->Name . "/";
            
            if ( is_dir ( $TmpFolder ) )
            {
                shell_exec ( "rm -Rf $TmpFolder" );
            }
            
            mkdir ( $TmpFolder );
            
            $Cuboides = $this->SkyCube->getCuboideIDs ( TRUE );
            foreach ( $Cuboides as $Level => $CuboideIDs )
            {
                $CurrentLevel = '';
                
                foreach ( $CuboideIDs as $CuboideID )
                {
                    $this->computeCuboide ( $CuboideID, $TmpFolder );
                }
            }
        }
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
        $DataSet [] = array ( 'RowId' => 1, 'Format' => 'Gen 1 OU', 'Joueur' => '065;103;065', 'Adversaire' => '065;143;065', 'Rarete' => 5, 'Duree1' => 20, 'Echec1' => 30, 'Duree2' => 25, 'Echec2' => 35 );
        $DataSet [] = array ( 'RowId' => 2, 'Format' => 'Gen 1 OU', 'Joueur' => '065;113;143', 'Adversaire' => '065;103;065', 'Rarete' => 4, 'Duree1' => 60, 'Echec1' => 50, 'Duree2' => 65, 'Echec2' => 55 );
        $DataSet [] = array ( 'RowId' => 3, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;121', 'Adversaire' => '065;103;065', 'Rarete' => 5, 'Duree1' => 30, 'Echec1' => 40, 'Duree2' => 35, 'Echec2' => 45 );
        $DataSet [] = array ( 'RowId' => 4, 'Format' => 'Gen 1 OU', 'Joueur' => '065;143;065', 'Adversaire' => '121;113;143', 'Rarete' => 1, 'Duree1' => 80, 'Echec1' => 40, 'Duree2' => 85, 'Echec2' => 40 );
        $DataSet [] = array ( 'RowId' => 5, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;128', 'Adversaire' => '121;113;121', 'Rarete' => 5, 'Duree1' => 90, 'Echec1' => 60, 'Duree2' => 95, 'Echec2' => 60 );
        $DataSet [] = array ( 'RowId' => 6, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;103', 'Adversaire' => '121;113;121', 'Rarete' => 9, 'Duree1' => 30, 'Echec1' => 50, 'Duree2' => 35, 'Echec2' => 50 );
        $DataSet [] = array ( 'RowId' => 7, 'Format' => 'Gen 1 OU', 'Joueur' => '065;113;143', 'Adversaire' => '065;113;143', 'Rarete' => 7, 'Duree1' => 80, 'Echec1' => 60, 'Duree2' => 85, 'Echec2' => 60 );
        $DataSet [] = array ( 'RowId' => 8, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;080', 'Adversaire' => '065;143;065', 'Rarete' => 9, 'Duree1' => 90, 'Echec1' => 70, 'Duree2' => 95, 'Echec2' => 70 );
        return $DataSet;
    }
    
    protected function getTestMesCols ()
    {
        $MesCols = array ();
        $MesCols [] = 'Rarete';
        $MesCols [] = 'Duree1';
        $MesCols [] = 'Echec1';
        $MesCols [] = 'Duree2';
        $MesCols [] = 'Echec2';
        return $MesCols;
    }
    
    protected function getTestRelCols ()
    {
        $RelCols = array ();
        $RelCols [] = 'RowId';
        $RelCols [] = 'Format';
        $RelCols [] = 'Joueur';
        $RelCols [] = 'Adversaire';
        $RelCols [] = 'Adversaire';
        return $RelCols;
    }
    
   
    public function getRequestFile  () { return $this->RequestFile ; }
    public function getDBTable      () { return $this->DBTable     ; }
    public function getDBClass      () { return $this->DBClass     ; }
    public function getRelationCols () { return $this->RelationCols; }
    public function getMeasureCols  () { return $this->MeasureCols ; }
    public function getSkyCube      () { return $this->SkyCube ; }
    
    public function setRequestFile  ( $NewValue ) { $this->RequestFile  = $NewValue; }
    public function setDBTable      ( $NewValue ) { $this->DBTable      = $NewValue; }
    public function setDBClass      ( $NewValue ) { $this->DBClass      = $NewValue; }
    public function setRelationCols ( $NewValue ) { $this->RelationCols = $NewValue; }
    public function setMeasureCols  ( $NewValue ) { $this->MeasureCols  = $NewValue; }
    
    public function setAlgorithm ( $Algo ) { $this->Algorithm = $Algo; }
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
    protected $Algorithm;
    protected $SkyCube;
    protected $IsTest;
}


