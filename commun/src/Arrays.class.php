<?php

/**
 * Classe utilitaire pour les opérations sur les tableaux.
 * 
 * Cette classe contient des méthodes pour effectuer différentes opérations sur les tableaux.
 *
 * @package Commun
 */
abstract class Arrays  
{
    /**
     * @var string EXPORT_COLUMN_FROM_DATA Utilisé pour indiquer que les libellés des colonnes doivent être extraits du tableau de données.
     */
    const EXPORT_COLUMN_FROM_DATA = 'export_col_data';
    
    /**
     * @var string EXPORT_COLUMN_AS_NUM Utilisé pour indiquer que les libellés des colonnes doivent être des nombres séquentiels.
     */
    const EXPORT_COLUMN_AS_NUM = 'export_col_num';
    
    /**
     * @var string EXPORT_COLUMN_NO_HEADER Utilisé pour indiquer qu'aucun libellé de colonne ne doit être présenté dans l'export CSV.
     */
    const EXPORT_COLUMN_NO_HEADER = 'export_col_not';
    
    /**
     * @var string EXPORT_ROW_FROM_DATA Utilisé pour indiquer que les libellés des lignes doivent être extraits du tableau de données.
     */
    const EXPORT_ROW_FROM_DATA = 'export_row_data';
    
    /**
     * @var string EXPORT_ROW_ADD_NUM Utilisé pour indiquer que des nombres séquentiels doivent être ajoutés comme libellés de lignes.
     */
    const EXPORT_ROW_ADD_NUM = 'export_row_add';
    
    /**
     * @var string EXPORT_ROW_NO_HEADER Utilisé pour indiquer qu'aucun libellé de ligne ne doit être présenté dans l'export CSV.
     */
    const EXPORT_ROW_NO_HEADER = 'export_row_not';
    
    
    
    /**
     * Affiche un tableau associatif sous forme de chaîne de caractères formatée.
     *
     * @param array $Array Le tableau associatif à afficher.
     * @param string $Label Le libellé à afficher avant le tableau (facultatif).
     * @param string $LineLabel Le libellé à afficher avant chaque ligne (facultatif).
     * @param string $LineEnd La chaîne à utiliser à la fin de chaque ligne (facultatif).
     */
    public static function echoArr ( $Array, $Label = '', $LineLabel = '', $LineEnd = '' )
    {
        echo self::arrToString ( $Array, $Label, $LineLabel, $LineEnd );
    }
    
    /**
     * Convertit un tableau associatif en une chaîne de caractères formatée.
     *
     * @param array $Array Le tableau associatif à convertir.
     * @param string $Label Le libellé à afficher avant le tableau (facultatif).
     * @param string $LineLabel Le libellé à afficher avant chaque ligne (facultatif).
     * @param string $LineEnd La chaîne à utiliser à la fin de chaque ligne (facultatif).
     * @return string La chaîne de caractères formatée.
     */
    public static function arrToString ( $Array, $Label = '', $LineLabel = '', $LineEnd = '' )
    {
        $Result = '';
        $Empty = '';
        if ( empty ( $Array ) ) $Empty = ' is empty';
            
        $Result .= $Label . $Empty . $LineEnd;
        foreach ( $Array as $Key => $Value )
        {
            $Result .= $LineLabel . ' ' . $Key . ' = ' . $Value . $LineEnd;
        }
        return $Result;
    }
    
    /**
     * Récupère la valeur d'une clé dans un tableau associatif, ou une valeur par défaut si la clé n'existe pas.
     *
     * @param array $Array Le tableau associatif.
     * @param string $Label La clé à récupérer.
     * @param mixed $Default La valeur par défaut à retourner si la clé n'existe pas (facultatif, valeur par défaut : "").
     * @return mixed La valeur de la clé ou la valeur par défaut si la clé n'existe pas.
     */
    public static function getIfSet ( $Array, $Label, $Default = "" )
    {
        $Result = $Default;
        
        if ( isset ( $Array [ $Label ] ) )
        {
            $Result = $Array [ $Label ];
        }

        return $Result;
    }
    
    /**
     * Récupère la valeur d'une clé dans un tableau associatif ou génère une exception si la clé n'existe pas.
     *
     * @param array $Array Le tableau associatif.
     * @param string $Label La clé à récupérer.
     * @param string $ExceptionText Le texte de l'exception à générer si la clé n'existe pas.
     * @return mixed La valeur de la clé si elle existe.
     * @throws Exception Si la clé n'existe pas dans le tableau.
     */
    public static function getOrCrash ( $Array, $Label, $ExceptionText )
    {
        $Result = NULL;
        
        if ( isset ( $Array [ $Label ] ) )
        {
            $Result = $Array [ $Label ];
        }
        else 
        {
            throw new Exception ( $ExceptionText );
        }
        
        return $Result;
        
    }

    
    /**
     * Extrait les colonnes du tableau d'entrée en fonction des paramètres fournis.
     *
     * Cette méthode extrait les colonnes du tableau d'entrée en fonction du paramètre $ColumnHeader.
     * Les colonnes à ignorer peuvent être spécifiées avec le paramètre $IgnoreColumns.
     *
     * @param array $InputData Le tableau d'entrée à partir duquel extraire les colonnes.
     * @param string|array $ColumnHeader Comment le libellé des colonnes doit être géré.
     * @param array $IgnoreColumns Liste des colonnes à ignorer dans la présentation (facultatif, valeur par défaut : array()).
     * @return array Les colonnes extraites du tableau d'entrée.
     */
    private static function extractColumns ( $InputData, $ColumnHeader = self::EXPORT_COLUMN_FROM_DATA, $IgnoreColumns = array () )
    {
        $Columns = array ();
        $UnfilteredCols = array ();
//         HTML::showVar ( '$InputData', $InputData );
//          HTML::showVar ( '$ColumnHeader', $ColumnHeader );
//         HTML::showVar ( '$IgnoreColumns', $IgnoreColumns );
        
        if ( ( $ColumnHeader == self::EXPORT_COLUMN_FROM_DATA ) ||
            ( $ColumnHeader == self::EXPORT_COLUMN_AS_NUM ) ||
            ( $ColumnHeader == self::EXPORT_COLUMN_NO_HEADER ) )
        {
            // Using first item's columns
            $TrashCols = array_keys ( reset ( $InputData ) );
//             HTML::showVar ( '$TrashCols', $TrashCols );
            
            if ( $ColumnHeader == self::EXPORT_COLUMN_FROM_DATA )
            {
                foreach ( $TrashCols as $Key )
                {
                    $UnfilteredCols [ $Key ] = $Key;
                }
            }
            else
            {
                $UnfilteredCols = $TrashCols;
            }
            
        } 
        
        else if ( is_array ( $ColumnHeader ) )
        {
            $UnfilteredCols = $ColumnHeader;
        }
        
        else
        {
            Log::error ( 'Invalid ColumnHeader parameter' );
            $Columns = false;
        }
        
//         HTML::showVar ( '$UnfilteredCols', $UnfilteredCols );
        
        if ( is_array ( $Columns ) )
        {
            foreach ( $UnfilteredCols as $Label => $Key )
            {
                if ( ! in_array ( $Key, $IgnoreColumns ) )
                {
                    $Columns [ $Label ] = $Key;
                }
            }
        }
//          HTML::showVar ( '$Columns', $Columns );
        
        return $Columns;
    }
    
    /**
     * Remplace une sous-chaîne dans une chaîne si nécessaire.
     *
     * Cette méthode remplace la sous-chaîne $Needle par la chaîne $ReplaceBy dans la chaîne $String,
     * si $ReplaceBy n'est pas une chaîne vide. Sinon, elle retourne la chaîne $String inchangée.
     *
     * @param string $String La chaîne dans laquelle effectuer le remplacement.
     * @param string $Needle La sous-chaîne à remplacer.
     * @param string $ReplaceBy La chaîne de remplacement (facultatif, valeur par défaut : '').
     * @return string La chaîne résultante après le remplacement (ou inchangée si $ReplaceBy est vide).
     */
    private static function replaceIfNeeded ( $String, $Needle, $ReplaceBy = '' )
    {
        $Result = $String;
        if ( '' != $ReplaceBy )
        {
            $Result = str_replace ( $Needle, $ReplaceBy, $String );
            //echo "$Result = str_replace ( $Needle, $ReplaceBy, $String )" . PHP_EOL;
        }
        return $Result;
    }
    
    /**
     * Exporte les données fournies au format CSV.
     *
     * Cette méthode exporte les données fournies au format CSV avec les options spécifiées.
     * Le résultat peut être enregistré dans un fichier si le paramètre $FilePath est fourni.
     * Les libellés des colonnes et des lignes peuvent être gérés avec les paramètres $ColumnHeader et $RowHeader.
     * Les colonnes ou lignes à ignorer dans la présentation peuvent être spécifiées avec $IgnoreColumns et $IgnoreRows.
     * Row Header peut être:
     *  - EXPORT_ROW_FROM_DATA : Utilise l'index des données d'entrée 
     *  - EXPORT_ROW_ADD_NUM : Un nombre est ajouté artificiellement
     *  - EXPORT_ROW_NO_HEADER : Rien n'est présenté comme entête
     *  - un array : Une liste d'index à utiliser (autant d'index que de lignes de données)
     *  - un string : Le nom de la colonne à présenter comme entête
     * Column Header peut être:
     *  - EXPORT_COLUMN_FROM_DATA : Utilise l'index des données d'entrée 
     *  - EXPORT_COLUMN_AS_NUM : Utilise un numéro artificiel comme entête
     *  - EXPORT_COLUMN_NO_HEADER : Rien n'est présenté comme entête
     *  - an array : Une liste d'index à utiliser (autant d'index que de colonnes de données)

     * @param array $InputData Les données à exporter.
     * @param string $Separator Le séparateur de champ (facultatif, valeur par défaut : ',').
     * @param string|array $ColumnHeader Comment le libellé des colonnes doit être géré.
     * @param string|array $RowHeader Comment le libellé des lignes doit être géré.
     * @param sring $FilePath Le chemin du fichier dans lequel enregistrer les données CSV (facultatif, valeur par défaut : '').
     * @param array $IgnoreColumns Liste des colonnes à ignorer dans la présentation (facultatif, valeur par défaut : array()).
     * @param array $IgnoreRows Liste des lignes à ignorer dans la présentation (facultatif, valeur par défaut : array()).
     * @param string $ReplaceSep La chaîne de remplacement pour le séparateur (facultatif, valeur par défaut : '').
     * @return string Le message d'erreur s'il y a un problème lors de l'exportation (ou chaîne vide si tout va bien).
     */
    public static function exportAsCSV ( $InputData, $Separator = ',', $ColumnHeader = self::EXPORT_COLUMN_FROM_DATA, $RowHeader = self::EXPORT_ROW_FROM_DATA, $FilePath = '', $IgnoreColumns = array (), $IgnoreRows = array (), $ReplaceSep = '' )
    {
//         echo $FilePath . print_r ( $InputData, true ) . PHP_EOL;
        $Result = '';
        $Continue = true;

        // Array of input column name indexed by the output label
        $Columns = self::extractColumns ( $InputData, $ColumnHeader, $IgnoreColumns );
                
//         echo $FilePath . print_r ( $Columns, true ) . PHP_EOL;
        if ( is_array ( $Columns ) )
        {
            if ( $ColumnHeader != self::EXPORT_COLUMN_NO_HEADER ) 
            {
                $Line = '';
                if ( $RowHeader != self::EXPORT_ROW_NO_HEADER )
                {
                    $Line .= 'index' . $Separator;
                }
                foreach ( $Columns as $Label => $Key )
                {
                    $Line .= self::replaceIfNeeded ( $Label, $Separator, $ReplaceSep );
                    $Line .= $Separator;
                }
                
                $Line .= PHP_EOL;
                
                if ( '' != $FilePath )
                {
                    file_put_contents ( $FilePath, $Line, FILE_APPEND );
                }
                else
                {
                    $Result .= $Line;
                }
            }
                
            $FalseIndex = 0;
            foreach ( $InputData as $Index => $Row )
            {
                $Line = '';
                
                if ( ! in_array ( $Index, $IgnoreRows ) )
                {
                            
                    if ( $RowHeader == self::EXPORT_ROW_FROM_DATA )
                    {
                        $Line .= self::replaceIfNeeded ( $Index, $Separator, $ReplaceSep ) . $Separator;
                    }
                    
                    else if ( $RowHeader == self::EXPORT_ROW_ADD_NUM )
                    {
                        $Line .= self::replaceIfNeeded ( $FalseIndex, $Separator, $ReplaceSep ) . $Separator;
                        ++$FalseIndex;
                    }
                    
                    else if ( is_array ( $RowHeader ) )
                    {
                        $Line .= self::replaceIfNeeded ( $RowHeader [ $Index ], $Separator, $ReplaceSep ) . $Separator;
                    }
                    
                    else if ( is_string ( $RowHeader ) && ( $RowHeader != self::EXPORT_ROW_NO_HEADER ) )
                    {
                        $Line .= self::replaceIfNeeded ( $Row [ $RowHeader ], $Separator, $ReplaceSep ) . $Separator;
                    }
                    
                    foreach ( $Columns as $Label => $Key )
                    {
                        if ( isset ( $Row [ $Key ] ) )
                        {
                            $Line .= self::replaceIfNeeded ( $Row [ $Key ], $Separator, $ReplaceSep );
                        }
                        $Line .= $Separator;
                    }
                    $Line .= PHP_EOL;
                 
                    if ( '' != $FilePath )
                    {
                        file_put_contents ( $FilePath, $Line, FILE_APPEND );
                    }
                    else
                    {
                        $Result .= $Line;
                    }
                }
            }
        }
                
        return $Result;
    }
    
    /**
     * Récupère la ligne correspondant à la valeur recherchée dans un fichier CSV.
     *
     * @param string $CSVFile Le chemin complet du fichier CSV.
     * @param mixed $SearchValue La valeur recherchée.
     * @param int $Column Le numéro de colonne à rechercher (facultatif, valeur par défaut : 0).
     * @param int $MaxLength La longueur maximale d'une ligne du fichier CSV (facultatif, valeur par défaut : 1000).
     * @param string $Separator Le séparateur de champ du fichier CSV (facultatif, valeur par défaut : ',').
     * @param bool $IgnoreTitles Indique si les titres de colonnes doivent être ignorés (facultatif, valeur par défaut : true).
     * @return array La ligne correspondant à la valeur recherchée, ou un tableau vide s'il n'y a pas de correspondance.
     */
    public static function getCSVLine ( $CSVFile, $SearchValue, $Column = 0, $MaxLength = 1000, $Separator = ',', $IgnoreTitles = TRUE )
    {
        $Row = array ();
        
        if ( ( $File = fopen ( $CSVFile, "r" ) ) !== FALSE )
        {
            while ( ( $Line = fgetcsv ( $File, $MaxLength, $Separator) ) !== FALSE )
            {
                if ( count ( $Line ) > $Column )
                {
                    if ( $Line [$Column] == $SearchValue )
                    {
                        $Row = $Line;
                        break;
                    }
                }
//                 else
//                 {
//                     echo 'pas assez de donnees ' . print_r ( $Line, FALSE ) . PHP_EOL;
//                 }
            }
            
            fclose ( $File );
        }
//         else
//         {
//             echo 'file not found';
//         }
        
        return $Row;
    }
    
    /**
     * Fusionne deux tableaux de manière récursive, en considérant les clés numériques comme identiques.
     *
     * @return array Le tableau résultant de la fusion.
     */
    public static function arrayMergeRecursive () 
    {
        $Arrays = func_get_args();
        $Base = array_shift ( $Arrays );
        
        foreach ( $Arrays as $Array ) 
        {
            reset ( $Base ); //important
            while ( list ( $Key, $Value ) = @each ( $Array ) ) 
            {
                if ( is_array ( $Value ) && @is_array ( $Base [$Key] ) ) 
                {
                    $Base [$Key] = static::arrayMergeRecursive ( $Base [$Key], $Value );
                } 
                else 
                {
                    $Base [$Key] = $Value;
                }
            }
        }
        
        return $Base;
    }
    
    /**
     * Récupère les colonnes spécifiées d'un tableau associatif, en les renommant si nécessaire.
     *
     * @param array $Array Un tableau multidimensionnel ou un tableau d'objets à partir duquel extraire les colonnes.
     * @param int|string|null $Columns Les colonnes de valeurs à renvoyer.
     * @param int|string|null $IndexKey La colonne à utiliser comme index/clés pour le tableau résultant (facultatif).
     * @return array Le tableau résultant contenant les colonnes spécifiées.
     */
    public static function getColumns ( $Array, $Columns, $IndexKey = NULL )
    {
        $ResultArray = array ();
        if ( is_string ( $Columns ) || is_numeric ( $Columns ) )
        {
            $Columns = array ( $Columns );
        }
        
        if ( is_array ( $Columns ) )
        {
            foreach ( $Array as $RowID => $Row )
            {
                $NewRow = array ();
                foreach ( $Columns as $Column )
                {
                    $NewRow [$Column] = $Row [$Column];
                }
                if ( !is_null ( $IndexKey ) )
                {
                    $NewIndex = $Row [$Column];
                    $ResultArray [$NewIndex] = $NewRow;
                }
                else 
                {
                    $ResultArray [$RowID] = $NewRow;
                }
            }
        }
        elseif ( is_null ( $Columns ) )
        {
            $ResultArray = array_column ( $Array, NULL, $IndexKey );
        }
        
        return $ResultArray;
    }
}

