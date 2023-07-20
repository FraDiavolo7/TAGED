<?php

/**
 * Class handling all displays of SkyCubes and associates 
 * @package TAGED\Algo
 */
class SKDisplay
{

    /**
     * The column name representing the Row ID in the data set.
     */
    const ROW_ID = 'RowId';
    
    /**
     * Constant flag value to indicate no special behavior.
     */
    const NO_FLAG = 0;
    
    /**
     * Flag to show filtered data only (without removed data) in the presentation.
     */
    const SHOW_FILTERED = 0x01;
    
    /**
     * Flag to show filtered data along with removed data in the presentation.
     */
    const SHOW_REMOVED = 0x02;
    
    /**
     * Flag to show equivalence classes in the presentation titles.
     */
    const SHOW_EQUIV_CLASS = 0x04;
    
    /**
     * Flag to show validity information in the presentation titles.
     */
    const SHOW_VALIDITY = 0x08;
    
    /**
     * Flag to show raw data in the presentation (exclusive with SHOW_DATA_FILTERED and SHOW_DATA_COMPUTED).
     */
    const SHOW_DATA_RAW = 0x10;
    
    /**
     * Flag to show filtered data in the presentation (exclusive with SHOW_DATA_RAW and SHOW_DATA_COMPUTED).
     */
    const SHOW_DATA_FILTERED = 0x20;
    
    /**
     * Flag to show computed data in the presentation (exclusive with SHOW_DATA_RAW and SHOW_DATA_FILTERED).
     */
    const SHOW_DATA_COMPUTED = 0x40;
    
    /**
     * Flag to show equivalence classes in the presentation titles for filtered data.
     */
    const SHOW_EQUIV_CLASS_FILTERED = 0x80;
    
    /**
     * Convertit l'objet en texte en utilisant la fonction print_r.
     *
     * @param mixed $Object L'objet à convertir en texte.
     * @return string Le texte représentant l'objet.
     */
    public static function text ( $Object )
    {
        $Text = print_r ( $Object, TRUE );
        
        return $Text;
    }
    
    /**
     * Convertit l'objet en format HTML en utilisant une fonction spécifique en fonction du type d'objet.
     * 
     * Si le type d'objet n'est pas reconnu, la fonction text() est utilisée.
     * @param mixed $Object L'objet à convertir en format HTML.
     * @return string Le contenu HTML représentant l'objet.
     */
    public static function html ( $Object )
    {
        $Function = 'text';
        
        if ( is_object ( $Object ) )
        {
            $Class = get_class ( $Object );
            switch ( $Class )
            {
                case 'SkyCubeBruteForce':
                case 'SkyCubeBlocNestedLoop':
                case 'SkyCube':         $Function = 'htmlSkyCube';         break; 
                case 'SkyCubeEmergent': $Function = 'htmlSkyCube'; break;
                case 'CuboideBruteForce': 
                case 'CuboideBlocNestedLoop':
                case 'Cuboide':         $Function = 'htmlCuboide';         break;
                default :               $Function = 'text';                break;
            }
        }
        
        return static::$Function ( $Object );
    }
    
    /**
     * Convertit le SkyCube en format HTML représentant les données d'entrée.
     *
     * @param SkyCube $SkyCube Le SkyCube contenant les données à convertir.
     * @return string Le contenu HTML représentant les données d'entrée.
     */
    public static function htmlInputData ( $SkyCube )
    {
        $RowHeaders = $SkyCube->getRowHeaders ();
        $RowID = array ();
        $ShowRowID = FALSE;
        if ( ! isset ( $RowHeaders [0] [self::ROW_ID] ) )
        {
            $ShowRowID = TRUE;
            $RowID [] = self::ROW_ID;
        }
        $HeadersRow = array_merge ( $RowID, array_keys ( $RowHeaders [0] ) );
        $HeadersCol = array_values ( $SkyCube->getColIDs () );
        $Headers = array_merge ( $HeadersRow, $HeadersCol );
        $InitData = Arrays::arrayMergeRecursive ( $RowHeaders, $SkyCube->getDataSet    ());
        
        $String = HTML::div ( HTML::tableFull ($Headers,  array ( 'border' => '1' ) ) );
        $String .= HTML::div ( HTML::tableFull ($InitData, array ( 'border' => '1' ), $ShowRowID ) );
        return HTML::div ( $String, array ( 'class' => 'input_data' ) );
    }

    /**
     * Convertit le CoSky en format HTML.
     *
     * @param CoSky $CoSky Le CoSky contenant les scores à convertir.
     * @return string Le contenu HTML représentant le CoSky.
     */
    public static function htmlCoSky ( $CoSky )
    {
        $Scores = $CoSky->getScores ();
        
        $TableContent = '';
        $TableHeaders = '';
        $FirstLine = TRUE;
        
        foreach ( $Scores as $Row )
        {
            $TableRow = '';
            foreach ( $Row as $Attr => $Value )
            {
                $Class = 'row_value';
                if ( $Attr == 'RowId' ) $Class = 'row_header';
                if ( $FirstLine ) $TableHeaders .= HTML::th ( $Attr, array ( 'class' => 'row_header ' . strtolower ( $Attr ) ) );
                $TableRow .= HTML::td ( $Value, array ( 'class' => $Class . ' ' . strtolower ( $Attr ) ) );
            }
            $TableContent .= HTML::tr ( $TableRow );
            $FirstLine = FALSE;
        }
        
        $String = HTML::table (
            HTML::tr ( $TableHeaders, array ( 'class' => 'headers' ) ) .
            $TableContent, array ( 'class' => 'co_sky cuboide' ) );
        return HTML::div ( $String, array ( 'class' => 'cosky_data' ) );
    }
    
    /**
     * Convertit le SkyCube en format HTML représentant les émergences.
     *
     * @param SkyCube $SkyCube Le SkyCube contenant les émergences à convertir.
     * @return string Le contenu HTML représentant les émergences.
     */
    public static function htmlEmergence ( $SkyCube )
    {
        $Emergences = $SkyCube->getEmergence ();
        $Emergence = array ();
        $CubeHeaders = $SkyCube->getRowHeaders ();
        $FilteredHeaders = array_keys ( reset ( $CubeHeaders ) );
        $KeyHeader = 'Tuple &eacute;mergent';
        $First = TRUE;
        $String = '';
        
        $TableContent = '';
        $TableHeaders = '';
        $EmptyRow = '';
        $TableHeaders .= HTML::th ( $KeyHeader, array ( 'class' => 'row_header ' . strtolower ( $KeyHeader ) ) );
        $EmptyRowContent .= HTML::td ( '', array ( 'class' => 'row_header ' . strtolower ( $KeyHeader ) ) );
        
        foreach ( $Emergences as $CuboideID => $ERCuboide )
        {
            foreach ( $ERCuboide as $Key => $ERValues )
            {
                $TableRow = '';
                $TableRow .= HTML::th ( '(' . $Key . ')', array ( 'class' => 'row_header ' . strtolower ( $KeyHeader ) ) );
                foreach ( $ERValues as $K => $V )
                {
                    if ( ! in_array ( $K, $FilteredHeaders ) )
                    {
                        if ( $First )
                        {
                            $Label = 'ER<sub>' . $K . '</sub>';
                            $EmptyRowContent .= HTML::td ( '', array ( 'class' => 'row_value ' . strtolower ( $K ) ) );
                            $TableHeaders .= HTML::th ( $Label, array ( 'class' => 'row_value ' . strtolower ( $K ) ) );
                        }

                        $Value = ( is_numeric ( $V ) ? round ( $V, 2 ) : $V );
                        $TableRow .= HTML::td ( $Value, array ( 'class' => 'row_value ' . strtolower ( $K ) ) );
                    }
                }
                if ( $First )
                {
                    $First = FALSE;
                    $EmptyRow = HTML::tr ( $EmptyRowContent, array ( 'class' => 'empty' ) );
                }
                $TableContent .= HTML::tr ( $TableRow );
            }
            $TableContent .= $EmptyRow;
        }
        $String .= HTML::table (
            HTML::tr ( $TableHeaders, array ( 'class' => 'headers' ) ) .
            $TableContent, array ( 'class' => 'emergence_data cuboide' ) );
        
        return HTML::div ( $String, array ( 'class' => 'emergence_data' ) );
    }
    
    /**
     * Convertit le SkyCube en format HTML représentant l'espace multidimensionnel.
     *
     * @param SkyCube $SkyCube Le SkyCube contenant l'espace multidimensionnel à convertir.
     * @return string Le contenu HTML représentant l'espace multidimensionnel.
     */
    public static function htmlMultidimensionalSpace ( $SkyCube )
    {
        $HTML = '';
        
        $MultidimensionalSpace = $SkyCube->getMultidimensionalSpace ();
        $FirstRow = TRUE;
        $TableRows = '';
        $TableHeaders = '';
        $ShowID = FALSE;
        
        if ( ! isset ( $MultidimensionalSpace [0][self::ROW_ID] ) )
        {
            $ShowID = TRUE;
        }
        
        foreach ( $MultidimensionalSpace as $RowID => $Row )
        {
            $FirstCol = TRUE;
            $TableRow = '';
            
            foreach ( $Row as $ColID => $Value )
            {
                if ( $FirstRow )
                {
                    if ( ( $FirstCol ) && ( $ShowID ) )
                    {
                        $TableHeaders .= HTML::th ( self::ROW_ID, array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
                    }
                    $TableHeaders .= HTML::th ( $ColID, array ( 'class' => 'row_value' ) );
                }
                
                if ( ( $FirstCol ) && ( $ShowID ) )
                {
                    $TableRow .= HTML::td ( strval ( $RowID + 1 ), array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
                }
                $TableRow .= HTML::td ( $Value, array ( 'class' => 'row_value' ) );
                $FirstCol = FALSE;
            }
            
            $TableRows .= HTML::tr ( $TableRow );
            $FirstRow = FALSE;
        }
        
        
        $HTML .= HTML::table (
            HTML::tr ( $TableHeaders, array ( 'class' => 'headers' ) ) .
            $TableRows,
            array ( 'class' => 'cuboide' )
            );
        
        return HTML::div ( $HTML, array ( 'class' => 'multidimensional_space' ) );
    }
    
    /**
     * Convertit le SkyCube en format HTML représentant la fusion des Cuboides (Relation fusionnée).
     *
     * @param SkyCube $SkyCube Le SkyCube contenant les Cuboides à fusionner.
     * @param int $Flags Les drapeaux pour contrôler l'affichage des Cuboides fusionnées.
     * @return string Le contenu HTML représentant la fusion des Cuboides.
     */
    public static function htmlSkyCubeFusion ( $SkyCube, $Flags = self::NO_FLAG )
    {
        $RowHeaders = $SkyCube->getRowHeaders ();
        $String = '';
        
        $RowID = array ();
        $ShowRowID = FALSE;
        if ( ! isset ( $RowHeaders [0] [self::ROW_ID] ) )
        {
            $ShowRowID = TRUE;
            $RowID [] = self::ROW_ID;
        }
        $HeadersRow = array_merge ( $RowID, array_keys ( $RowHeaders [0] ) );
        $HeadersCol = array_values ( $SkyCube->getColIDs () );
        $Headers = array_merge ( $HeadersRow, $HeadersCol );
        $InitData = Arrays::arrayMergeRecursive ( $RowHeaders, $SkyCube->getDataSet    ());
        
//                  $String .= HTML::div ( HTML::tableFull ( $SkyCube->getRowHeaders (), array ( 'border' => '1' ) ) );
//                  $String .= HTML::div ( HTML::tableFull ( $SkyCube->getColIDs     (), array ( 'border' => '1' ) ) );
//                  $String .= HTML::div ( HTML::tableFull ( $SkyCube->getDataSet    (), array ( 'border' => '1' ) ) );
//         $String .= HTML::div ( HTML::tableFull ( $HeadersRow, array ( 'border' => '1' ) ) );
//         $String .= HTML::div ( HTML::tableFull ( $HeadersCol, array ( 'border' => '1' ) ) );
//         $String .= HTML::div ( HTML::tableFull ( $Headers, array ( 'border' => '1' ) ) );
        
        $CuboidesContent = '';
        //foreach (  $this->Cuboides as $Level => $Cuboides )
        $OrderedCuboideIDs  = $SkyCube->getCuboideIDs ( FALSE );
        $FilteredCuboideIDs = $SkyCube->getCuboideIDs ( TRUE  );
        
        $BaseList = $OrderedCuboideIDs;
        
        $ShowFiltered = $Flags & self::SHOW_FILTERED; // Relation Fusionnee Abregee
        
        $TableHeaders = '';
        $TableContent = '';
        $EmptyRow = '';

        $Ignore = array ();
        $MustMatch = array ();
        
        if ( $ShowFiltered )
        {
            // Establish list of checked columns
            foreach ( $HeadersCol as $Header )
            {
                $LastChar = substr ( $Header, -1 );
                $Col1 = substr_replace ( $Header, 1, -1 );
                if ( (  $LastChar != 1 ) && (  $LastChar != 2 ) )
                {
                    // Remove invariant columns
                    $Ignore [$Header] = $Header;
                }
                elseif (  $LastChar == 2 )
                {
                    if ( in_array ( $Col1, $HeadersCol ) )
                    {
                        $MustMatch [$Header] = $Header;
                    }
                }
            }
        }
        
        if ( $ShowRowID )
        {
            $TableHeaders .= HTML::th ( self::ROW_ID, array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
            $EmptyRow .= HTML::td ( '', array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
        }
        foreach ( $HeadersRow as $Header )
        {
            $TableHeaders .= HTML::th ( $Header, array ( 'class' => 'row_header ' . strtolower ( $Header ) ) );
            $EmptyRow .= HTML::td ( '', array ( 'class' => 'row_header ' . strtolower ( $Header ) ) );
        }
        foreach ( $HeadersCol as $Header )
        {
            if ( $ShowFiltered && in_array ( $Header, $Ignore ) ) continue;
            $TableHeaders .= HTML::th ( $Header, array ( 'class' => 'row_value ' . strtolower ( $Header ) ) );
            $EmptyRow .= HTML::td ( '', array ( 'class' => 'row_value ' . strtolower ( $Header ) ) );
        }
        
        $NbMustMatch = count ( $MustMatch );
        
        foreach ( $OrderedCuboideIDs as $Level => $CuboideIDs )
        {
            $CurrentLevel = '';
            
            foreach ( $CuboideIDs as $CuboideID )
            {
                $Cuboide = $SkyCube->getCuboide ( $CuboideID );
                $ColIDs = array_flip ( $Cuboide->getColIDs () );
                $Matches = ( $NbMustMatch == 0 );
                $CuboideContent = '';
                
                $RowHeaders = $Cuboide->getRowHeaders ();
                $DataSet = $Cuboide->getDataSetFiltered ();
                
                foreach ( $DataSet as $RowID => $Row )
                {
                    $TableRow = '';
                    $NbMatches = 0;
    
                    if ( $ShowRowID )
                    {
                        $TableRow .= HTML::td ( strval ( $RowID ), array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
                    }
                    
                    foreach ( $RowHeaders [$RowID] as $RowHeader => $HeaderValue )
                    {
                        $TableRow .= HTML::td ( $HeaderValue, array ( 'class' => 'row_header ' . strtolower ( $RowHeader ) ) );
                    }
                    
                    foreach ( $HeadersCol as $Header )
                    {
                        if ( $ShowFiltered )
                        {
                            if ( in_array ( $Header, $Ignore ) ) continue;
                            
                            if ( in_array ( $Header, $MustMatch ) )
                            {
                                if ( ( isset ( $Row [$ColIDs [$Header]] ) ) && ( '' != $Row [$ColIDs [$Header]] ) )
                                {
                                    $NbMatches += 1;
                                }
                            }
                        }
                        $Value = ( isset ( $ColIDs [$Header] ) ? $Row [$ColIDs [$Header]] : 'ALL' );
                        $TableRow .= HTML::td ( ( '' == $Value ? 'ALL' : $Value ), array ( 'class' => 'row_value ' . strtolower ( $ColID ) ) );
                    }
                    
                    if ( $NbMatches == $NbMustMatch ) $Matches = TRUE;
                    $CuboideContent .= HTML::tr ( $TableRow );
                }
                if ( $Matches ) 
                {
                    $TableContent .= $CuboideContent;
                    $TableContent .= HTML::tr ( $EmptyRow, array ( 'class' => 'empty' ) );
                }
            }
        }
        
        $String .= HTML::table ( 
            HTML::tr ( $TableHeaders, array ( 'class' => 'headers' ) ) .
            $TableContent, array ( 'class' => 'relation_fusionnee cuboide' ) );
        
        return HTML::div ( $String, array ( 'class' => 'skycube fusion' ) );
    }
    
    /**
     * Convertit le SkyCube en format HTML représentant les Cuboides avec des paramètres spécifiques.
     *
     * @param SkyCube $SkyCube Le SkyCube contenant les Cuboides à afficher.
     * @param int $Flags Les drapeaux pour contrôler l'affichage des Cuboides.
     * @return string Le contenu HTML représentant les Cuboides avec les paramètres spécifiques.
     */
    public static function htmlSkyCubeParam ( $SkyCube, $Flags = self::NO_FLAG )
    {
        $RowHeaders = $SkyCube->getRowHeaders ();
        $String = '';
            
        $RowID = array ();
        $ShowRowID = FALSE;
        if ( ! isset ( $RowHeaders [0] [self::ROW_ID] ) )
        {
            $ShowRowID = TRUE;
            $RowID [] = self::ROW_ID;
        }
        $HeadersRow = array_merge ( $RowID, array_keys ( $RowHeaders [0] ) );
        $HeadersCol = array_values ( $SkyCube->getColIDs () );
        $Headers = array_merge ( $HeadersRow, $HeadersCol );
        $InitData = Arrays::arrayMergeRecursive ( $RowHeaders, $SkyCube->getDataSet    ());
        
        //         $String .= HTML::div ( HTML::tableFull ( $SkyCube->getRowHeaders (), array ( 'border' => '1' ) ) );
        //         $String .= HTML::div ( HTML::tableFull ( $SkyCube->getColIDs     (), array ( 'border' => '1' ) ) );
        //         $String .= HTML::div ( HTML::tableFull ( $SkyCube->getDataSet    (), array ( 'border' => '1' ) ) );
        
        $CuboidesContent = '';
        //foreach (  $this->Cuboides as $Level => $Cuboides )
        $OrderedCuboideIDs  = $SkyCube->getCuboideIDs ( FALSE );
        $FilteredCuboideIDs = $SkyCube->getCuboideIDs ( TRUE  );
        
        $BaseList = $OrderedCuboideIDs;
        
        $ShowFiltered = $Flags & self::SHOW_FILTERED;
        $ShowFilteredWithRemoved = ( $Flags & self::SHOW_FILTERED ) && ( $Flags & self::SHOW_REMOVED ); 
        
        foreach ( $OrderedCuboideIDs as $Level => $CuboideIDs )
        {
            $CurrentLevel = '';
            
            foreach ( $CuboideIDs as $CuboideID )
            {
                $Removed = ! isset ( $FilteredCuboideIDs [$Level] [$CuboideID] );
                if (
                     ( ! $ShowFiltered )
                  || ( ( $ShowFiltered ) && ( ! $Removed ) )
                  || ( ( $ShowFilteredWithRemoved ) )
                    )
                {
                    $Cuboide = $SkyCube->getCuboide ( $CuboideID );
                    $CurrentLevel .= static::htmlCuboideParam ( $Cuboide, $Flags, $Removed );
                }
            }
            
            $CuboidesContent .= HTML::div (
                HTML::div ( $Level, array ( 'class' => 'title' ) ) .
                HTML::div ( $CurrentLevel),
                array ( 'class' => 'cuboides_lvl lvl_' . $Level ) );
        }
        
        $String .= HTML::div ( 
            HTML::div ( 'Cuboides', array ( 'class' => 'title' ) ) .
            HTML::div ( $CuboidesContent ),
            array ( 'class' => 'cuboides' ) );
        
        return HTML::div ( $String, array ( 'class' => 'skycube' ) );
    }
    
    /**
     * Convertit le SkyCube en format HTML représentant les classes d'équivalence.
     *
     * @param SkyCube $SkyCube Le SkyCube contenant les classes d'équivalence à afficher.
     * @param bool $Filtered Indique si les classes d'équivalence doivent être filtrées (par défaut TRUE).
     * @return string Le contenu HTML représentant les classes d'équivalence.
     */
    public static function htmlEquivalenceClasses ( $SkyCube, $Filtered = TRUE )
    {
        $String = '';
        
        $CuboidesContent = '';
        //foreach (  $this->Cuboides as $Level => $Cuboides )
        foreach (  $SkyCube->getCuboideIDs ($Filtered ) as $Level => $CuboideIDs )
        {
            $CurrentLevel = '';
            
            foreach ( $Cuboides as $Cuboide )
            {
                $HTML = '(' . $Cuboide->getID () . ', ' .  $Cuboide->getEquivalenceClasses ( FALSE ) . ')';
                $CurrentLevel .= HTML::div ( $HTML, array ( 'class' => 'cuboide' ) );
            }
            $CuboidesContent .= HTML::div (
                HTML::div ( $Level, array ( 'class' => 'title' ) ) .
                HTML::div ( $CurrentLevel ),
                array ( 'class' => 'cuboides_lvl lvl_' . $Level ) );
        }
        
        $String .= HTML::div (
            HTML::div ( 'Cuboides', array ( 'class' => 'title' ) ) .
            HTML::div ( $CuboidesContent ),
            array ( 'class' => 'cuboides' ) );
        return HTML::div ( $String, array ( 'class' => 'skycube equivalence_classes' ) );
    }
    
    /**
     * Convertit le SkyCube en format HTML représentant les Cuboides.
     *
     * @param SkyCube $SkyCube Le SkyCube contenant les Cuboides à afficher.
     * @param bool $Filtered Indique si les Cuboides doivent être filtrés (par défaut TRUE).
     * @return string Le contenu HTML représentant les Cuboides.
     */
    public static function htmlSkyCube ( $SkyCube, $Filtered = TRUE )
    {
        $RowHeaders = $SkyCube->getRowHeaders ();
        $RowID = array ();
        $ShowRowID = FALSE;
        if ( ! isset ( $RowHeaders [0] [self::ROW_ID] ) )
        {
            $ShowRowID = TRUE;
            $RowID [] = self::ROW_ID;
        }
        $HeadersRow = array_merge ( $RowID, array_keys ( $RowHeaders [0] ) );
        $HeadersCol = array_values ( $SkyCube->getColIDs () );
        $Headers = array_merge ( $HeadersRow, $HeadersCol );
        $InitData = Arrays::arrayMergeRecursive ( $RowHeaders, $SkyCube->getDataSet    ());

//         $String .= HTML::div ( HTML::tableFull ( $SkyCube->getRowHeaders (), array ( 'border' => '1' ) ) );
//         $String .= HTML::div ( HTML::tableFull ( $SkyCube->getColIDs     (), array ( 'border' => '1' ) ) );
//         $String .= HTML::div ( HTML::tableFull ( $SkyCube->getDataSet    (), array ( 'border' => '1' ) ) );
        
        $CuboidesContent = '';
        //foreach (  $this->Cuboides as $Level => $Cuboides )
        foreach (  $SkyCube->getCuboideIDs ($Filtered ) as $Level => $CuboideIDs )
        {
            $CurrentLevel = '';
            
            foreach ( $CuboideIDs as $CuboideID )
            {
                $Cuboide = $SkyCube->getCuboide ( $CuboideID );
                $CurrentLevel .= static::htmlCuboide ( $Cuboide );
            }
            $CuboidesContent .= HTML::div (
                HTML::div ( $Level, array ( 'class' => 'title' ) ) .
                HTML::div ( $CurrentLevel),
                array ( 'class' => 'cuboides_lvl lvl_' . $Level ) );
        }
        
        $String .= HTML::div (
            HTML::div ( 'Cuboides', array ( 'class' => 'title' ) ) .
            HTML::div ( $CuboidesContent ),
            array ( 'class' => 'cuboides' ) );
        
        return HTML::div ( $String, array ( 'class' => 'skycube' ) );
    }

    /**
     * Convertit le SkyCubeEmergent en format HTML représentant les Cuboides émergents.
     *
     * @param SkyCubeEmergent $SkyCubeEmergent Le SkyCubeEmergent contenant les Cuboides émergents à afficher.
     * @return string Le contenu HTML représentant les Cuboides émergents.
     */
    public static function htmlSkyCubeEmergent ( $SkyCubeEmergent )
    {
        $InitData = array_merge ( $SkyCubeEmergent->getRowHeaders (), $SkyCubeEmergent->getDataSet    ());
        $String  = HTML::div ( HTML::tableFull ($InitData, array ( 'border' => '1' ), TRUE ) );
        
        $String .= HTML::div ( HTML::tableFull ( $SkyCubeEmergent->getRowHeaders (), array ( 'border' => '1' ) ) );
        $String .= HTML::div ( HTML::tableFull ( $SkyCubeEmergent->getColIDs     (), array ( 'border' => '1' ) ) );
        $String .= HTML::div ( HTML::tableFull ( $SkyCubeEmergent->getDataSet    (), array ( 'border' => '1' ) ) );
        
        $CuboidesContent = '';
        //foreach (  $this->Cuboides as $Level => $Cuboides )
        foreach ( array_reverse ( $SkyCubeEmergent->getCuboides (), TRUE ) as $Level => $Cuboides )
        {
            $CurrentLevel = '';
            
            foreach ( $Cuboides as $Cuboide )
            {
                $CurrentLevel .= static::htmlCuboide ( $Cuboide );
            }
            $CuboidesContent .= HTML::div (
                HTML::div ( $Level, array ( 'class' => 'title' ) ) .
                HTML::div ( $CurrentLevel),
                array ( 'class' => 'cuboides_lvl lvl_' . $Level ) );
        }
        
        $String .= HTML::div (
            HTML::div ( 'Cuboides', array ( 'class' => 'title' ) ) .
            HTML::div ( $CuboidesContent ),
            array ( 'class' => 'cuboides' ) );

        return HTML::div ( $String, array ( 'class' => 'skycube' ) );
    }
    
    /**
     * Convertit le Cuboide en format HTML.
     *
     * @param Cuboide $Cuboide Le Cuboide à afficher.
     * @return string Le contenu HTML représentant le Cuboide.
     */
    public static function htmlCuboide ( $Cuboide )
    {
        $HTML = HTML::div ( HTML::div ( $Cuboide->getID () ) . HTML::div ( '(' . ( $Cuboide->isValid () ? 'V' : 'I' ) . ')' ), array ( 'class' => 'title' ) );
        
        $TableHeaders = '';
        $TableRows = '';
        $FirstRow = TRUE;
        $RowHeaders = $Cuboide->getRowHeaders ();
        $ShowID = FALSE;
        
        if ( ! isset ( $RowHeaders [self::ROW_ID] ) )
        {
            $ShowID = TRUE;
        }
        
        foreach ( $Cuboide->getDataSet () as $RowID => $Row )
        {
            $TableRow = '';
            if ( $ShowID )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= HTML::th ( self::ROW_ID, array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
                }
                $TableRow .= HTML::td ( strval ( $RowID ), array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
            }
            foreach ( $RowHeaders [$RowID] as $RowHeader => $HeaderValue )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= HTML::th ( $RowHeader, array ( 'class' => 'row_header ' . strtolower ( $RowHeader ) ) );
                }
                $TableRow .= HTML::td ( $HeaderValue, array ( 'class' => 'row_header ' . strtolower ( $RowHeader ) ) );
            }
            foreach ( $Row as $ColID => $Value )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= HTML::th ( $ColID, array ( 'class' => 'row_value' ) );
                }
                $TableRow .= HTML::td ( $Value, array ( 'class' => 'row_value' ) );
            }
            
            $TableRows .= HTML::tr ( $TableRow );
            $FirstRow = FALSE;
        }
        
        $HTML .= HTML::table (
            HTML::tr ( $TableHeaders, array ( 'class' => 'headers' ) ) .
            $TableRows,
            array ( 'class' => 'cuboide' )
            );
        
        return HTML::div ( $HTML, array ( 'class' => 'cuboide' ) );
        
    }

    /**
     * Convertit le Cuboide en format HTML avec des paramètres personnalisables.
     *
     * @param Cuboide $Cuboide Le Cuboide à afficher.
     * @param int $Flags Les indicateurs pour afficher différentes parties du Cuboide (par défaut self::NO_FLAG).
     * @param bool $Removed Indique si le Cuboide a été supprimé (par défaut FALSE).
     * @return string Le contenu HTML représentant le Cuboide.
     */
    public static function htmlCuboideParam ( $Cuboide, $Flags = self::NO_FLAG, $Removed = FALSE )
    {
        $CuboideClass = 'cuboide ' . ( $Removed ? 'removed' : '' );
        $Title  = '';
        $Title .= HTML::div ( $Cuboide->getID (), array ( 'class' => 'cuboide_id' ) );
        
        if ( $Flags & self::SHOW_VALIDITY )
        {
            $Title .= HTML::div ( $Cuboide->isValid () ? 'V' : 'I', array ( 'class' => 'cuboide_validity' ) );
        }
        
        if ( $Flags & self::SHOW_EQUIV_CLASS || $Flags & self::SHOW_EQUIV_CLASS_FILTERED )
        {
            $Title .= HTML::div ( $Cuboide->getEquivalenceClasses ( FALSE, $Flags & self::SHOW_EQUIV_CLASS_FILTERED ), array ( 'class' => 'cuboide_equiv_class' ) );
        }
        
        $HTML = HTML::div ( $Title , array ( 'class' => 'title' ) );
        
        $TableHeaders = '';
        $TableRows = '';
        $FirstRow = TRUE;
        $RowHeaders = $Cuboide->getRowHeaders ();
        $ColHeaders = $Cuboide->getColIDs ();
        $ShowID = FALSE;
        
        if ( ! isset ( $RowHeaders [0][self::ROW_ID] ) )
        {
            $ShowID = TRUE;
        }
        
        $DataSet = array ();
        
        if ( $Flags & self::SHOW_DATA_RAW )
        {
            $DataSet = $Cuboide->getDataSet ();
        }
        elseif ( $Flags & self::SHOW_DATA_FILTERED )
        {
            $DataSet = $Cuboide->getDataSetFiltered ();
        }
        elseif ( $Flags & self::SHOW_DATA_COMPUTED )
        {
            $DataSet = $Cuboide->getDataSetComputed ();
        }
        
        foreach ( $DataSet as $RowID => $Row )
        {
            $TableRow = '';
            if ( $ShowID )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= HTML::th ( self::ROW_ID, array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
                }
                $TableRow .= HTML::td ( strval ( $RowID ), array ( 'class' => 'row_header ' . strtolower ( self::ROW_ID ) ) );
            }
            foreach ( $RowHeaders [$RowID] as $RowHeader => $HeaderValue )
            {
                if ( $FirstRow )
                {
                    $TableHeaders .= HTML::th ( $RowHeader, array ( 'class' => 'row_header ' . strtolower ( $RowHeader ) ) );
                }
                $TableRow .= HTML::td ( $HeaderValue, array ( 'class' => 'row_header ' . strtolower ( $RowHeader ) ) );
            }
            foreach ( $Row as $ColID => $Value )
            {
                if ( $FirstRow )
                {
                    $Header = $ColHeaders [$ColID] . " ($ColID)";
                    
                    $TableHeaders .= HTML::th ( $Header, array ( 'class' => 'row_value' ) );
                }
                $TableRow .= HTML::td ( $Value, array ( 'class' => 'row_value' ) );
            }
            
            $TableRows .= HTML::tr ( $TableRow );
            $FirstRow = FALSE;
        }
        
        $HTML .= HTML::table (
            HTML::tr ( $TableHeaders, array ( 'class' => 'headers' ) ) .
            $TableRows,
            array ( 'class' => 'cuboide' )
            );
        
        return HTML::div ( $HTML, array ( 'class' => $CuboideClass ) );
        
    }
    
    /**
     * Convertit le CuboideEmergent en format HTML.
     *
     * @param CuboideEmergent $CuboideEmergent Le CuboideEmergent à afficher.
     * @return string Le contenu HTML représentant le CuboideEmergent.
     */
    public static function htmlCuboideEmergent ( $CuboideEmergent )
    {
        $Text = __METHOD__;
        
        return $Text;
    }
    
    /**
     * Génère la représentation LaTeX pour l'objet fourni.
     *
     * @param mixed $Object L'objet pour lequel générer la représentation LaTeX.
     * @return string La représentation LaTeX.
     */
    public static function latex ( $Object )
    {
        
    }
    
    /**
     * Génère la représentation LaTeX pour le SkyCube fourni.
     *
     * @param SkyCube $SkyCube Le SkyCube pour lequel générer la représentation LaTeX.
     * @return string La représentation LaTeX du SkyCube.
     */
    public static function latexSkyCube ( $SkyCube )
    {
        
    }
    
    /**
     * Génère la représentation LaTeX pour le SkyCubeEmergent fourni.
     *
     * @param SkyCubeEmergent $SkyCubeEmergent Le SkyCubeEmergent pour lequel générer la représentation LaTeX.
     * @return string La représentation LaTeX du SkyCubeEmergent.
     */
    public static function latexSkyCubeEmergent ( $SkyCubeEmergent )
    {
        
    }
    
    /**
     * Génère la représentation LaTeX pour le Cuboide fourni.
     *
     * @param Cuboide $Cuboide Le Cuboide pour lequel générer la représentation LaTeX.
     * @return string La représentation LaTeX du Cuboide.
     */
    public static function latexCuboide ( $Cuboide )
    {
        
    }
    
    /**
     * Génère la représentation LaTeX pour le CuboideEmergent fourni.
     *
     * @param CuboideEmergent $CuboideEmergent Le CuboideEmergent pour lequel générer la représentation LaTeX.
     * @return string La représentation LaTeX du CuboideEmergent.
     */
    public static function latexCuboideEmergent ( $CuboideEmergent )
    {
        
    }
    
    /**
     * Génère la représentation LaTeX pour le SkyCube actuel.
     *
     * @return string La représentation LaTeX du SkyCube.
     */
    public function toLaTex ()
    {
        $CuboideLaTeX = '\begin{figure}[htbp]
 \centering
 \tiny
 \resizebox{1\textwidth}{!}{
  \begin{tikzpicture}[
   line join=bevel
   ]';
        $Arrows = '';
        $NbLevels = count ( $this->Cuboides );
        
        $SizeMax = ( $NbLevels - 1 ) * 100 ;
        
        $Yinc = 75;
        $Y = $SizeMax;
        
        $ArrowArray = array ();
        
        foreach ( array_reverse ( $this->Cuboides, TRUE ) as $Level => $Cuboides )
        {
            $CurrentLevel = '';
            $NbCuboidesLvl = count ( $Cuboides );
            $i = 1;
            $Xinc = 300 / $NbCuboidesLvl;
            $X = $Xinc / 2;
            
            $Arrows .= '   \draw [stealth-] (n' . $NbLevels . $Level . '.north) -- (top);' . PHP_EOL ;
            $Arrows .= '   \draw [stealth-] (bottom) -- (n1' . $Level . '.south);' . PHP_EOL ;
            
            foreach ( $Cuboides as $Cuboide )
            {
                if ( $Level == $NbLevels )
                {
                    $Node = 'top';
                    $X = 150;
                }
                else
                {
                    $Node = 'n' . $Level . $i++;
                }
                
                if ( $Level > 1 && $Level < $NbLevels )
                {
                    
                }
                
                $CuboideLaTeX .= $Cuboide->toLaTeX ( $Node, $X, $Y );
                
                $X += $Xinc;
            }
            $Y -= $Yinc;
        }
        
        $CuboideLaTeX .= '   \node (bottom) at (150pt, 0pt) {$\emptyset$};' . PHP_EOL;
        
        $CuboideLaTeX .= '   %% Draws' . PHP_EOL;
        
        $CuboideLaTeX .= $Arrows;
        
        $CuboideLaTeX .= '  \end{tikzpicture}
 }
 \caption{Représentation du treillis du Skycube de la relation \texttt{Mettre un Nom}}\label{fig:skycube_treillis_complet_3}
\end{figure}';
        
        
        return HTML::pre ( $CuboideLaTeX,
            array ( 'class' => 'cuboides_latex' ) );
        
    }
    
    /**
     * Génère une représentation sous forme de chaîne de caractères pour le SkyCube actuel.
     *
     * @return string La représentation du SkyCube sous forme de chaîne de caractères.
     */
    public function __toString ()
    {
        $String  = HTML::div ( HTML::tableFull ( $this->RowHeaders, array ( 'border' => '1' ) ) );
        $String .= HTML::div ( HTML::tableFull ( $this->ColIDs, array ( 'border' => '1' ) ) );
        $String .= HTML::div ( HTML::tableFull ( $this->DataSet, array ( 'border' => '1' ) ) );
        
        $CuboidesContent = '';
        //foreach (  $this->Cuboides as $Level => $Cuboides )
        foreach ( array_reverse ( $this->Cuboides, TRUE ) as $Level => $Cuboides )
        {
            $CurrentLevel = '';
            
            foreach ( $Cuboides as $Cuboide )
            {
                $CurrentLevel .= $Cuboide->toHTML ();
            }
            $CuboidesContent .= HTML::div ( 
                HTML::div ( $Level, array ( 'class' => 'title' ) ) . 
                HTML::div ( $CurrentLevel), 
                array ( 'class' => 'cuboides_lvl lvl_' . $Level ) );
        }
        
        $String .= HTML::div ( 
            HTML::div ( 'Cuboides', array ( 'class' => 'title' ) ) . 
            HTML::div ( $CuboidesContent ), 
            array ( 'class' => 'cuboides' ) );
        
        $String .= HTML::pre ( $CuboideLaTeX, 
            array ( 'class' => 'cuboides' ) );
        
        return HTML::div ( $String, array ( 'class' => 'skycube' ) );
    }
    
    /**
     * Génère une liste de Cuboides pour le niveau donné en utilisant la récursivité.
     *
     * @param int $Level Le niveau pour lequel générer les Cuboides.
     * @param array $ColIDs La liste des identifiants de colonnes.
     * @param string $Current Le préfixe actuel pour les Cuboides.
     */
    protected function generateCuboideListLvl ( $Level, $ColIDs, $Current = '' )
    {
//         echo "Level $Level <br>\n";
//         echo "ColIDs " . print_r ( $ColIDs, TRUE ) .  "<br>\n";
//         echo "Current $Current <br>\n";
        $Left = count ( $ColIDs );
        $Length = strlen ( $Current );
        $Needed = $Level - $Length;
//         echo "Left $Left <br>\n";
//         echo "Length $Length <br>\n";
//         echo "Needed $Needed <br>\n";
        
        if ( $Length == $Level )
        {
            $CuboideClass = static::CUBOIDE;
            //echo "Cuboide : $Level $Current <br>\n";
            $this->Cuboides [$Level] [$Current] = new $CuboideClass ( $Current, $this, $this->MinMax );
        }
        else if ( $Left >= $Needed )
        {
            while ( ! empty ( $ColIDs ) )
            {
                $Key = array_shift ( $ColIDs ); // Retrieves the first item while removing it from the list 
                $this->generateCuboideListLvl ( $Level, $ColIDs, $Current . $Key );
            }
        }
    }
    
    /**
     * Génère la liste complète des Cuboides.
     */
    protected function generateCuboideList ()
    {
        if ( $this->IsValid )
        {
            $IDCount = count ( $this->ColIDs );
            $NbCuboide = pow ( 2, $IDCount ) - 1;
//             echo "IDCount $IDCount <br>\n";
//             echo "NbCuboide $NbCuboide <br>\n";
            
            if ( $NbCuboide <= self::MAX_CUBOIDE )
            {
                for ( $i = 1 ; $i <= $IDCount ; ++$i )
                {
                     $this->generateCuboideListLvl ( $i, array_keys ( $this->ColIDs ) );
                }
            }
            else 
            {
                $this->IsValid = FALSE;
            }
        }
    }
    
    /**
     * Calcule le DataSet à partir des données fournies en utilisant les colonnes de relation et de mesure.
     *
     * @param array $Data Les données brutes pour le DataSet.
     * @param array $RelationCols Les colonnes de relation.
     * @param array $MeasureCols Les colonnes de mesure.
     */
    protected function computeDataSet ( $Data, $RelationCols, $MeasureCols )
    {
        if ( is_array ( $Data ) )
        {
            $ColHeaders = array ();
            $NextColID = self::MIN_COLID;
            foreach ( $Data as $RowID => $Row )
            {
                foreach ( $Row as $ColHeader => $Value )
                {
                    if ( in_array ( $ColHeader, $RelationCols ) )
                    {
                        $this->RowHeaders [$RowID] [$ColHeader] = $Value;
                    }
                    else if ( in_array ( $ColHeader, $MeasureCols ) )
                    {
                        if ( ! isset ( $ColHeaders [$ColHeader] ) )
                        {
                            $ColHeaders [$ColHeader] = $NextColID;
                            $this->ColIDs [$NextColID] = $ColHeader;
                            $NextColID++;
                        }
                        $ColID = $ColHeaders [$ColHeader];
                        $this->DataSet [$RowID] [$ColID] = $Value;
                    }
                }
            }
            
            $RowToRemove = array ();
            
            foreach ( $this->DataSet as $RowID => $Row )
            {
                $Empty = TRUE;
                foreach ( $Row as $ColID => $Value )
                {
                    if ( '' !== $Value )
                    {
                        $Empty = FALSE;
                        break;
                    }
                }
                if ( $Empty )
                {
                    $RowToRemove [] = $RowID;
                }
            }
            
            foreach ( $RowToRemove as $RowID )
            {
                unset ( $this->DataSet [$RowID] );
            }
        }
        else
        {
            $this->IsValid = FALSE;
        }
    }
    
    /**
     * Récupère le DataSet actuel du SkyCube.
     *
     * @return array Le DataSet actuel.
     */
    public function getDataSet ()
    {
        return $this->DataSet;
    }
    
    /**
     * Récupère les en-têtes de ligne actuels du SkyCube.
     *
     * @return array Les en-têtes de ligne actuels.
     */
    public function getRowHeaders ()
    {
        return $this->RowHeaders;
    }
    
    /**
     * Récupère les identifiants de colonnes actuels du SkyCube.
     *
     * @return array Les identifiants de colonnes actuels.
     */
    public function getColIDs ()
    {
        return $this->ColIDs;
    }
    
    /**
     * Tableau associatif indexé par RowID et ColID représentant les mesures de la relation.
     *
     * @var array
     */
    protected $DataSet;
    
    /**
     * Tableau associatif indexé par RowID représentant les identifiants de la relation.
     *
     * @var array
     */
    protected $RowHeaders;
    
    /**
     * Tableau associatif indexé par ColID représentant les identifiants de mesure.
     *
     * @var array
     */
    protected $ColIDs;
    
    /**
     * Liste des Cuboides indexés par leur combinaison d'en-tête de placeholder.
     *
     * @var array
     */
    protected $Cuboides;
    
    /**
     * Tableau contenant les valeurs minimales et maximales pour le calcul.
     *
     * @var array
     */
    protected $MinMax;
    
    /**
     * Indique si les données de la relation sont valides.
     *
     * @var bool
     */
    protected $IsValid;
    
}


