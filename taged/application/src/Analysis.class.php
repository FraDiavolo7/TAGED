<?php

/**
 * @package TAGED
 */
class Analysis 
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
    
	/**
	 * Chemin du fichier de description.
	 */
	const DESC_FILE_PATH = '/chemin/vers/le/fichier_de_description.txt';

	/**
	 * Clé pour la classe de base de données.
	 */
	const DBCLASS = 'DbClass';

	/**
	 * Clé pour le nom de la table de base de données.
	 */
	const TABLE = 'Table';

	/**
	 * Clé pour le nom du fichier de requête.
	 */
	const FILE = 'File';

	/**
	 * Clé pour les colonnes de relation dans les données de test.
	 */
	const REL_COLS = 'RelationCols';

	/**
	 * Clé pour les colonnes à mesurer dans les données de test.
	 */
	const MES_COLS = 'MeasureCols';
    
	/**
	 * Tableau associatif contenant les noms de base de données du jeu.
	 *
	 * Chaque clé représente le nom d'une application spécifique du jeu,
	 * et chaque valeur est le nom de la base de données correspondante.
	 */    const GAME_DB = array (
        APP_NAME_COLLECTION   => 'TagedDBColl',
        APP_NAME_MATCH3       => 'TagedDBMatch3',
        APP_NAME_HACK_N_SLASH => 'TagedDBHnS'
    );
    
    /**
     * Constructeur de l'analyse.
     * @param string $DescFile Chemin vers le fichier de description.
     * @param bool $IsTest Indique si l'analyse est à des fins de test.
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
        $this->Min          = array ();
        $this->Max          = array ();
        $this->IsTest       = $IsTest;
    }
    
    /**
     * Charge les données à partir du fichier de description.
     */
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
    
    /**
     * Explose une chaîne de colonnes en un tableau.
     * @param string $Cols Les colonnes sous forme de chaîne séparée par des virgules.
     * @return array Les colonnes sous forme de tableau associatif.
     */
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
    
    /**
     * Vérifie si l'analyse est prête pour le calcul.
     */
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
    
    /**
     * Prépare l'analyse en chargeant les données et en créant l'instance de SkyCube.
     */
    public function prepare ()
    {
        $this->Name = basename ( $this->DescFile, '.ini' );
        
        if ( $this->IsTest )
        {
            $this->Name .= '_Test';
            $this->DataSet      = $this->getTestData ();
            $this->RelationCols = $this->getTestRelCols ();
            $this->MeasureCols  = $this->getTestMesCols ();
            $this->Min  = $this->getTestMin ();
            $this->Max  = $this->getTestMax ();
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
            
            $Aggregate = $this->getAggregateFile ( FALSE ) ;
            

            $NbTuples = $Aggregate->getNbTuples ();
            $NbAttributes = count ( $RelationCols );
            
            $this->DataSet = $Aggregate->getData ( );
            
        }
        
        $this->check ();
        
        if ( $this->Runnable )
        {
            $this->SkyCube = new SkyCubeEmergent ( $this->DataSet, $this->RelationCols, $this->MeasureCols, Cuboide::TO_MIN );
        }
    }
    
    /**
     * Nettoie les données en remplaçant les caractères spéciaux par des underscores.
     * @param string $Data Les données à nettoyer.
     * @return string Les données nettoyées.
     */
    public function cleanData ( $Data )
    {
        return str_replace ( array (';', ',', ' '), '_', $Data );
    }
    

    /**
     * Convertit une valeur en numérique et conserve une correspondance des valeurs d'attribut.
     * @param mixed $Value La valeur à convertir en numérique.
     * @param string $Attribute Le nom de l'attribut.
     * @return mixed La valeur numérique convertie.
     */
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
    
     /**
     * Calcule l'attribut Cuboide et définit le taux d'émergence dans SkyCube.
     * @param int $CuboideID L'ID du Cuboide.
     * @param string $ColID L'ID de la colonne.
     * @param string $Folder Le chemin du dossier.
     */
    protected function computeCuboideAttribute ( $CuboideID, $ColID, $Folder )
    {
//        echo __FILE__ . ':' . __LINE__ . " $CuboideID $ColID<br>";
        $FileName   = $CuboideID . '-' . $ColID;
        
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
        $Relations = Arrays::getColumns ( $InputRowHeaders, $RelationHeaders );
        
        $AlgoAnalysis = new IDEA ( $FileName, $Folder );
        
        $AlgoAnalysis->setAlgorithm ( $this->Algorithm );
        
        $AlgoAnalysis->setMin ( $this->Min [$ColID] ?? NULL );
        $AlgoAnalysis->setMax ( $this->Max [$ColID] ?? NULL );
        
//         $AlgoAnalysis->setMin ( 35 );
//         $AlgoAnalysis->setMax ( 90 );
        $AlgoAnalysis->setMeasures  ( $Measures  );
        $AlgoAnalysis->setRelations ( $Relations );
        
        $Results = $AlgoAnalysis->run ();
        
        foreach ( $Results as $Result )
        {
            $this->SkyCube->setEmergenceRatio ( $CuboideID, $ColID, $Result ['rel'], $Result ['ER'] );
        }
    }
        
    /**
     * Calcule le Cuboide complet et ses attributs.
     * @param int $CuboideID L'ID du Cuboide.
     * @param string $Folder Le chemin du dossier.
     */
    protected function computeCuboide ( $CuboideID, $Folder )
    {
//        echo __FILE__ . ':' . __LINE__ . " $CuboideID $Folder<br>";
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
    
    /**
     * Génère l'entrée pour l'algorithme en traitant tous les Cuboides.
     */
    protected function generateAlgoInput ()
    {
        // Process all cuboides
        // For each Tuple, insert into InputData with NULL to fill the blanks

        $AlgoInputMeasures = array ();
        $AlgoInputRelations = array ();
        
        // 1 list all columns
        
        // 2 for each Cuboide, fetch each row
        
        // If Row does not existe, create
        
        // If Row exists fill the blanks only
        
        
        $Cuboides = $this->SkyCube->getCuboideIDs ( TRUE );
        foreach ( $Cuboides as $Level => $CuboideIDs )
        {
            $CurrentLevel = '';
            
            foreach ( $CuboideIDs as $CuboideID )
            {
                $Cuboide = $this->SkyCube->getCuboide ( $CuboideID );
                $InputDataSet = $Cuboide->getDataSetFiltered ( );
                $InputRowHeaders = $Cuboide->getRowHeadersFiltered ( );
                
                //$MeasureHeaders  = array ( $ColID . '1', $ColID . '2' );
                $RelationHeaders = array_keys ( $InputRowHeaders [0] );
                
                foreach ( $RelationHeaders as $RowID => $ColumnName )
                {
                    if ( strtolower ( $ColumnName ) == 'rowid' )
                    {
                        unset ( $RelationHeaders [$RowID] );
                    }
                }
                
                //$Measures  = Arrays::getColumns ( $InputDataSet,    $MeasureHeaders  );
                $Relations = Arrays::getColumns ( $InputRowHeaders, $RelationHeaders );
                echo __METHOD__ . ' $InputDataSet ' . print_r ( $InputDataSet, TRUE ) . "<br>";
                
                foreach ( $InputDataSet as $RowID => $Row )
                {
                    foreach ( $Row as $ColID => $Measure )
                    {
                        if ( ! isset ( $AlgoInputMeasures [$RowID][$ColID] ) )
                        {
                            $AlgoInputMeasures [$RowID][$ColID] = $Measure;
                            $AlgoInputRelations [$RowID] = $Relations[$RowID];
                        }
                    }
                }
            }
        }
        
        echo __METHOD__ . ' Algo Input <pre>' . print_r ( $AlgoInputMeasures, TRUE ) . "</pre><br>";
        echo __METHOD__ . ' Algo Input <pre>' . print_r ( $AlgoInputRelations, TRUE ) . "</pre><br>";
    }
    
    /**
     * Effectue l'analyse en utilisant l'algorithme sélectionné.
     */
    public function compute ()
    {
        $Result = FALSE;
        $this->Result = '';
        $this->check ();
        
        if ( $this->Runnable )
        {
            $Message = "Computing analysis on " . $this->DescFile . " ($this->Algorithm)";
            
            Log::info ( $Message );
            
           // echo $Message . "<br>";
            
            $this->Result .= $Message . PHP_EOL;
            
            $TmpFolder  = AGGREGATE_FOLDER_TMP . $this->Name . "/";
            
            if ( is_dir ( $TmpFolder ) )
            {
                shell_exec ( "rm -Rf $TmpFolder" );
            }
            
            mkdir ( $TmpFolder );
            
          //  $this->generateAlgoInput ();
            
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
        
    /**
     * Obtient le résultat de l'analyse.
     * @return string Le résultat de l'analyse.
     */
    public function getResult ()
    {
        return $this->Result;
    }
    
    /**
     * Crée une nouvelle instance d'analyse avec les paramètres spécifiés et l'écrit dans le fichier de description.
     * @param string $Name Le nom de l'analyse.
     * @param string $Game Le jeu pour lequel l'analyse est créée.
     * @param string $RelationCols Les colonnes utilisées pour les relations.
     * @param string $MeasureCols Les colonnes utilisées pour les mesures.
     * @param string|null $File Le chemin du fichier de requête.
     * @param string|null $Table Le nom de la table de base de données.
     */
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
    
    /**
     * Supprime une instance d'analyse existante par son nom.
     * @param string $Name Le nom de l'analyse à supprimer.
     */
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
    
	/**
	 * Récupère les données de test pour l'analyse.
	 *
	 * @return array Les données de test sous forme de tableau.
	 */
    protected function getTestData ()
    {
        $DataSet = array ();
//         $DataSet [] = array ( 'RowId' => 1, 'Format' => 'Gen 1 OU', 'Joueur' => '065;103;065', 'Adversaire' => '065;143;065', 'Rarete' => 5, 'Duree1' => 20, 'Echec1' => 30, 'Duree2' => 22, 'Echec2' => 32 );
//         $DataSet [] = array ( 'RowId' => 2, 'Format' => 'Gen 1 OU', 'Joueur' => '065;113;143', 'Adversaire' => '065;103;065', 'Rarete' => 4, 'Duree1' => 60, 'Echec1' => 50, 'Duree2' => 64, 'Echec2' => 53 );
//         $DataSet [] = array ( 'RowId' => 3, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;121', 'Adversaire' => '065;103;065', 'Rarete' => 5, 'Duree1' => 30, 'Echec1' => 40, 'Duree2' => 33, 'Echec2' => 45 );
//         $DataSet [] = array ( 'RowId' => 4, 'Format' => 'Gen 1 OU', 'Joueur' => '065;143;065', 'Adversaire' => '121;113;143', 'Rarete' => 1, 'Duree1' => 80, 'Echec1' => 40, 'Duree2' => 87, 'Echec2' => 44 );
//         $DataSet [] = array ( 'RowId' => 5, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;128', 'Adversaire' => '121;113;121', 'Rarete' => 5, 'Duree1' => 90, 'Echec1' => 60, 'Duree2' => 98, 'Echec2' => 66 );
//         $DataSet [] = array ( 'RowId' => 6, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;103', 'Adversaire' => '121;113;121', 'Rarete' => 9, 'Duree1' => 30, 'Echec1' => 50, 'Duree2' => 36, 'Echec2' => 54 );
//         $DataSet [] = array ( 'RowId' => 7, 'Format' => 'Gen 1 OU', 'Joueur' => '065;113;143', 'Adversaire' => '065;113;143', 'Rarete' => 7, 'Duree1' => 80, 'Echec1' => 60, 'Duree2' => 81, 'Echec2' => 63 );
//         $DataSet [] = array ( 'RowId' => 8, 'Format' => 'Gen 1 OU', 'Joueur' => '121;113;080', 'Adversaire' => '065;143;065', 'Rarete' => 9, 'Duree1' => 90, 'Echec1' => 70, 'Duree2' => 92, 'Echec2' => 75 );

//         $DataSet [] = array ( 'RowId' =>  1, 'Format' => 'Gen 1 OU', 'Joueur' => 'A', 'Adversaire' => 'D', 'Rarete' => 5, 'Duree1' => 20, 'Echec1' => 30, 'Duree2' => 25, 'Echec2' => 35 );
//         $DataSet [] = array ( 'RowId' =>  2, 'Format' => 'Gen 1 OU', 'Joueur' => 'B', 'Adversaire' => 'E', 'Rarete' => 4, 'Duree1' => 60, 'Echec1' => 50, 'Duree2' => 65, 'Echec2' => 55 );
//         $DataSet [] = array ( 'RowId' =>  3, 'Format' => 'Gen 1 OU', 'Joueur' => 'B', 'Adversaire' => 'F', 'Rarete' => 5, 'Duree1' => 30, 'Echec1' => 40, 'Duree2' => 35, 'Echec2' => 45 );
//         $DataSet [] = array ( 'RowId' =>  4, 'Format' => 'Gen 1 OU', 'Joueur' => 'B', 'Adversaire' => 'A', 'Rarete' => 1, 'Duree1' => 80, 'Echec1' => 40, 'Duree2' => 85, 'Echec2' => 45 );
//         $DataSet [] = array ( 'RowId' =>  5, 'Format' => 'Gen 1 OU', 'Joueur' => 'C', 'Adversaire' => 'A', 'Rarete' => 5, 'Duree1' => 90, 'Echec1' => 60, 'Duree2' => 95, 'Echec2' => 65 );
//         $DataSet [] = array ( 'RowId' =>  6, 'Format' => 'Gen 1 OU', 'Joueur' => 'C', 'Adversaire' => 'B', 'Rarete' => 9, 'Duree1' => 30, 'Echec1' => 50, 'Duree2' => 35, 'Echec2' => 55 );
//         $DataSet [] = array ( 'RowId' =>  7, 'Format' => 'Gen 1 OU', 'Joueur' => 'D', 'Adversaire' => 'B', 'Rarete' => 7, 'Duree1' => 80, 'Echec1' => 60, 'Duree2' => 85, 'Echec2' => 65 );
//         $DataSet [] = array ( 'RowId' =>  8, 'Format' => 'Gen 1 OU', 'Joueur' => 'D', 'Adversaire' => 'C', 'Rarete' => 9, 'Duree1' => 90, 'Echec1' => 70, 'Duree2' => 95, 'Echec2' => 75 );
        
//         $DataSet [] = array ( 'RowId' =>  9, 'Format' => 'Gen 1 OU', 'Joueur' => 'E', 'Adversaire' => 'D', 'Rarete' => 5, 'Duree1' => 20, 'Echec1' => 50, 'Duree2' => 30, 'Echec2' => 55 );
//         $DataSet [] = array ( 'RowId' => 10, 'Format' => 'Gen 1 OU', 'Joueur' => 'E', 'Adversaire' => 'E', 'Rarete' => 4, 'Duree1' => 60, 'Echec1' => 30, 'Duree2' => 65, 'Echec2' => 40 );
// C        065;143;065 1
// B        065;103;065 4
// A        121;113;128 5
// E        065;113;143 7
// D        121;113;080 9
        
// F        121;113;121 5
//         121;113;103 9
        
//         $DataSet [] = array ( 'RowId' =>  1, 'Format' => 'Gen 1 OU', 'Joueur' => 'A', 'Adversaire' => 'D', 'Rarete' => 5, 'Duree1' => 20, 'Echec1' => 30, 'Duree2' => 25, 'Echec2' => 35 );
//         $DataSet [] = array ( 'RowId' =>  2, 'Format' => 'Gen 1 OU', 'Joueur' => 'B', 'Adversaire' => 'E', 'Rarete' => 4, 'Duree1' => 60, 'Echec1' => 50, 'Duree2' => 65, 'Echec2' => 55 );
//         $DataSet [] = array ( 'RowId' =>  3, 'Format' => 'Gen 1 OU', 'Joueur' => 'B', 'Adversaire' => 'F', 'Rarete' => 4, 'Duree1' => 30, 'Echec1' => 40, 'Duree2' => 35, 'Echec2' => 45 );
//         $DataSet [] = array ( 'RowId' =>  4, 'Format' => 'Gen 1 OU', 'Joueur' => 'B', 'Adversaire' => 'A', 'Rarete' => 4, 'Duree1' => 80, 'Echec1' => 40, 'Duree2' => 85, 'Echec2' => 45 );
//         $DataSet [] = array ( 'RowId' =>  5, 'Format' => 'Gen 1 OU', 'Joueur' => 'C', 'Adversaire' => 'A', 'Rarete' => 1, 'Duree1' => 90, 'Echec1' => 60, 'Duree2' => 95, 'Echec2' => 65 );
//         $DataSet [] = array ( 'RowId' =>  6, 'Format' => 'Gen 1 OU', 'Joueur' => 'C', 'Adversaire' => 'B', 'Rarete' => 1, 'Duree1' => 30, 'Echec1' => 50, 'Duree2' => 35, 'Echec2' => 55 );
//         $DataSet [] = array ( 'RowId' =>  7, 'Format' => 'Gen 1 OU', 'Joueur' => 'D', 'Adversaire' => 'B', 'Rarete' => 9, 'Duree1' => 80, 'Echec1' => 60, 'Duree2' => 85, 'Echec2' => 65 );
//         $DataSet [] = array ( 'RowId' =>  8, 'Format' => 'Gen 1 OU', 'Joueur' => 'D', 'Adversaire' => 'C', 'Rarete' => 9, 'Duree1' => 90, 'Echec1' => 70, 'Duree2' => 95, 'Echec2' => 75 );
        
//         $DataSet [] = array ( 'RowId' =>  9, 'Format' => 'Gen 1 OU', 'Joueur' => 'E', 'Adversaire' => 'D', 'Rarete' => 7, 'Duree1' => 20, 'Echec1' => 50, 'Duree2' => 30, 'Echec2' => 55 );
//         $DataSet [] = array ( 'RowId' => 10, 'Format' => 'Gen 1 OU', 'Joueur' => 'E', 'Adversaire' => 'E', 'Rarete' => 7, 'Duree1' => 60, 'Echec1' => 30, 'Duree2' => 65, 'Echec2' => 40 );
// A D 5 => 121, 113, 128
// B E 4 => 065, 103, 065
// B F 4 => 065, 103, 065
// B A 4 => 065, 103, 065
// C A 1 => 121, 113, 080
// C B 1 => 121, 113, 080
// D B 9 => 065, 113, 143
// D C 9 => 065, 113, 143
// E D 7 => 065, 143, 065
// E E 7 => 065, 143, 065
        
        
//         $DataSet [] = array ( 'RowId' =>  1, 'Format' => 'Gen 1 OU', 'Joueur' => '121, 113, 128', 'Adversaire' => '065, 113, 143', 'Rarete' => 5, 'Duree1' => 20, 'Echec1' => 30, 'Duree2' => 25, 'Echec2' => 30 );
//         $DataSet [] = array ( 'RowId' =>  2, 'Format' => 'Gen 1 OU', 'Joueur' => '065, 103, 065', 'Adversaire' => '065, 143, 065', 'Rarete' => 4, 'Duree1' => 60, 'Echec1' => 50, 'Duree2' => 65, 'Echec2' => 45 );
//         $DataSet [] = array ( 'RowId' =>  3, 'Format' => 'Gen 1 OU', 'Joueur' => '065, 103, 065', 'Adversaire' => '121, 113, 121', 'Rarete' => 4, 'Duree1' => 30, 'Echec1' => 40, 'Duree2' => 35, 'Echec2' => 30 );
//         $DataSet [] = array ( 'RowId' =>  4, 'Format' => 'Gen 1 OU', 'Joueur' => '065, 103, 065', 'Adversaire' => '121, 113, 128', 'Rarete' => 4, 'Duree1' => 80, 'Echec1' => 40, 'Duree2' => 85, 'Echec2' => 50 );
//         $DataSet [] = array ( 'RowId' =>  5, 'Format' => 'Gen 1 OU', 'Joueur' => '121, 113, 080', 'Adversaire' => '121, 113, 128', 'Rarete' => 1, 'Duree1' => 90, 'Echec1' => 60, 'Duree2' => 95, 'Echec2' => 70 );
//         $DataSet [] = array ( 'RowId' =>  6, 'Format' => 'Gen 1 OU', 'Joueur' => '121, 113, 080', 'Adversaire' => '065, 103, 065', 'Rarete' => 1, 'Duree1' => 30, 'Echec1' => 50, 'Duree2' => 35, 'Echec2' => 30 );
//         $DataSet [] = array ( 'RowId' =>  7, 'Format' => 'Gen 1 OU', 'Joueur' => '065, 113, 143', 'Adversaire' => '065, 103, 065', 'Rarete' => 9, 'Duree1' => 80, 'Echec1' => 60, 'Duree2' => 85, 'Echec2' => 50 );
//         $DataSet [] = array ( 'RowId' =>  8, 'Format' => 'Gen 1 OU', 'Joueur' => '065, 113, 143', 'Adversaire' => '121, 113, 080', 'Rarete' => 9, 'Duree1' => 80, 'Echec1' => 70, 'Duree2' => 95, 'Echec2' => 70 );
//         $DataSet [] = array ( 'RowId' =>  9, 'Format' => 'Gen 1 OU', 'Joueur' => '065, 143, 065', 'Adversaire' => '065, 113, 143', 'Rarete' => 7, 'Duree1' => 20, 'Echec1' => 50, 'Duree2' => 25, 'Echec2' => 30 );
//         $DataSet [] = array ( 'RowId' => 10, 'Format' => 'Gen 1 OU', 'Joueur' => '065, 143, 065', 'Adversaire' => '065, 143, 065', 'Rarete' => 7, 'Duree1' => 60, 'Echec1' => 30, 'Duree2' => 65, 'Echec2' => 45 );
        $A = 'A'; // '121, 113, 006'
        $B = 'B'; // '065, 103, 065'
        $C = 'C'; // '121, 113, 080'
        $D = 'D'; // '065, 113, 143'
        $E = 'E'; // '065, 040, 065'
        $F = 'F'; // '121, 113, 121'

        
        $DataSet [] = array ( 'RowId' =>  1, 'Format' => 'UU', 'Joueur' => $A, 'Adversaire' => $D, 'Rarete' => 5, 'Duree1' => 25, 'Echec1' => 30, 'Duree2' => 20, 'Echec2' => 30 );
        $DataSet [] = array ( 'RowId' =>  2, 'Format' => 'OU', 'Joueur' => $B, 'Adversaire' => $E, 'Rarete' => 4, 'Duree1' => 65, 'Echec1' => 50, 'Duree2' => 60, 'Echec2' => 45 );
        $DataSet [] = array ( 'RowId' =>  3, 'Format' => 'OU', 'Joueur' => $B, 'Adversaire' => $F, 'Rarete' => 4, 'Duree1' => 35, 'Echec1' => 40, 'Duree2' => 30, 'Echec2' => 30 );
        $DataSet [] = array ( 'RowId' =>  4, 'Format' => 'OU', 'Joueur' => $B, 'Adversaire' => $A, 'Rarete' => 4, 'Duree1' => 85, 'Echec1' => 40, 'Duree2' => 80, 'Echec2' => 50 );
        $DataSet [] = array ( 'RowId' =>  5, 'Format' => 'OU', 'Joueur' => $C, 'Adversaire' => $A, 'Rarete' => 1, 'Duree1' => 95, 'Echec1' => 60, 'Duree2' => 90, 'Echec2' => 70 );
        $DataSet [] = array ( 'RowId' =>  6, 'Format' => 'OU', 'Joueur' => $C, 'Adversaire' => $B, 'Rarete' => 1, 'Duree1' => 35, 'Echec1' => 50, 'Duree2' => 30, 'Echec2' => 30 );
        $DataSet [] = array ( 'RowId' =>  7, 'Format' => 'OU', 'Joueur' => $D, 'Adversaire' => $B, 'Rarete' => 9, 'Duree1' => 85, 'Echec1' => 60, 'Duree2' => 80, 'Echec2' => 50 );
        $DataSet [] = array ( 'RowId' =>  8, 'Format' => 'OU', 'Joueur' => $D, 'Adversaire' => $C, 'Rarete' => 9, 'Duree1' => 85, 'Echec1' => 70, 'Duree2' => 90, 'Echec2' => 70 );
        $DataSet [] = array ( 'RowId' =>  9, 'Format' => 'UU', 'Joueur' => $E, 'Adversaire' => $D, 'Rarete' => 7, 'Duree1' => 25, 'Echec1' => 50, 'Duree2' => 20, 'Echec2' => 30 );
        $DataSet [] = array ( 'RowId' => 10, 'Format' => 'UU', 'Joueur' => $E, 'Adversaire' => $E, 'Rarete' => 7, 'Duree1' => 65, 'Echec1' => 30, 'Duree2' => 60, 'Echec2' => 45 );
        
        return $DataSet;
    }
    
	/**
	 * Récupère les colonnes à mesurer dans les données de test.
	 *
	 * @return array Les colonnes à mesurer.
	 */
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
    
	/**
	 * Récupère les colonnes de relation dans les données de test.
	 *
	 * @return array Les colonnes de relation.
	 */
    protected function getTestRelCols ()
    {
        $RelCols = array ();
        $RelCols [] = 'RowId';
        $RelCols [] = 'Format';
        $RelCols [] = 'Joueur';
        $RelCols [] = 'Adversaire';
        return $RelCols;
    }
    
	/**
	 * Récupère les valeurs minimales pour chaque colonne.
	 *
	 * @return array Les valeurs minimales par colonne.
	 */
    protected function getTestMin ()
    {
        return array ( 'B' => 0, 'C' => 0 );
    }
    
	/**
	 * Récupère les valeurs maximales pour chaque colonne.
	 *
	 * @return array Les valeurs maximales par colonne.
	 */
    protected function getTestMax ()
    {
        return array ( 'B' => 700, 'C' => 500 );
    }
    
   
	/**
	 * Récupère le nom du fichier de requête.
	 *
	 * @return string Le nom du fichier de requête.
	 */
    public function getRequestFile  () { return $this->RequestFile ; }
	/**
	 * Récupère le nom de la table de base de données.
	 *
	 * @return string Le nom de la table de base de données.
	 */
    public function getDBTable      () { return $this->DBTable     ; }
	/**
	 * Récupère le nom de la classe de base de données.
	 *
	 * @return string Le nom de la classe de base de données.
	 */
    public function getDBClass      () { return $this->DBClass     ; }

	/**
	 * Récupère les colonnes de relation pour l'analyse.
	 *
	 * @return array Les colonnes de relation.
	 */
    public function getRelationCols () { return $this->RelationCols; }

	/**
	 * Récupère les colonnes à mesurer pour l'analyse.
	 *
	 * @return array Les colonnes à mesurer.
	 */
    public function getMeasureCols  () { return $this->MeasureCols ; }

	/**
	 * Récupère le cube de données multidimensionnel SkyCube.
	 *
	 * @return SkyCube Le cube de données multidimensionnel.
	 */
    public function getSkyCube      () { return $this->SkyCube ; }
    
	/**
	 * Définit le nom du fichier de requête.
	 *
	 * @param string $NewValue Le nouveau nom du fichier de requête.
	 */
    public function setRequestFile  ( $NewValue ) { $this->RequestFile  = $NewValue; }

	/**
	 * Définit le nom de la table de base de données.
	 *
	 * @param string $NewValue Le nouveau nom de la table de base de données.
	 */
    public function setDBTable      ( $NewValue ) { $this->DBTable      = $NewValue; }

	/**
	 * Définit le nom de la classe de base de données.
	 *
	 * @param string $NewValue Le nouveau nom de la classe de base de données.
	 */
    public function setDBClass      ( $NewValue ) { $this->DBClass      = $NewValue; }

	/**
	 * Définit les colonnes de relation pour l'analyse.
	 *
	 * @param array $NewValue Les nouvelles colonnes de relation.
	 */
    public function setRelationCols ( $NewValue ) { $this->RelationCols = $NewValue; }

	/**
	 * Définit les colonnes à mesurer pour l'analyse.
	 *
	 * @param array $NewValue Les nouvelles colonnes à mesurer.
	 */
    public function setMeasureCols  ( $NewValue ) { $this->MeasureCols  = $NewValue; }
    
	/**
	 * Définit les valeurs minimales pour chaque colonne.
	 *
	 * @param array $NewValue Les nouvelles valeurs minimales par colonne.
	 */
    public function setMin ( $NewValue ) { $this->Min = $NewValue; }

	/**
	 * Définit les valeurs maximales pour chaque colonne.
	 *
	 * @param array $NewValue Les nouvelles valeurs maximales par colonne.
	 */
    public function setMax ( $NewValue ) { $this->Max = $NewValue; }
    
	/**
	 * Définit l'algorithme utilisé pour l'analyse.
	 *
	 * @param string $Algo Le nom de l'algorithme à utiliser.
	 */
    public function setAlgorithm ( $Algo ) { $this->Algorithm = $Algo; }

	/**
	 * Écrit les paramètres d'analyse dans le fichier de description.
	 */    
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
	/**
	 * @var array Tableau associatif contenant les valeurs des attributs pour les tests.
	 */
	protected $AttributeValues;

	/**
	 * @var array Tableau contenant les attributs ignorés lors des tests.
	 */
	protected $AttributeIgnored;
    
    /**
     * @var string|null Chemin vers le fichier de description.
     */
    protected $DescFile;

    /**
     * @var string|null Chemin vers le fichier de requête.
     */
    protected $RequestFile;

    /**
     * @var string|null Nom de la table de base de données.
     */
    protected $DBTable;

    /**
     * @var string|null Nom de la classe de base de données.
     */
    protected $DBClass;

    /**
     * @var array|null Colonnes utilisées pour les relations.
     */
    protected $RelationCols;

    /**
     * @var array|null Colonnes utilisées pour les mesures.
     */
    protected $MeasureCols;

    /**
     * @var bool Indique si l'analyse peut être exécutée.
     */
    protected $Runnable;

    /**
     * @var string Chemin vers le fichier de description.
     */
    protected $DescFilePath;

    /**
     * @var string|null Algorithme utilisé pour l'analyse.
     */
    protected $Algorithm;

    /**
     * @var array|null Jeu de données utilisé pour l'analyse.
     */
    protected $DataSet;

    /**
     * @var SkyCubeEmergent|null Instance de SkyCube utilisée pour l'analyse.
     */
    protected $SkyCube;

    /**
     * @var array Un tableau pour stocker les valeurs minimales de chaque colonne.
     */
    protected $Min;

    /**
     * @var array Un tableau pour stocker les valeurs maximales de chaque colonne.
     */
    protected $Max;

    /**
     * @var bool Indique si l'analyse est à des fins de test.
     */
    protected $IsTest;

}


