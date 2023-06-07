<?php

/**
 * 
 * 
 * Concepts de base :
 * DataSet : Ensemble de données indexées par les RowID d'un côté et les ColID de l'autre
 * RowID : ID chiffré d'une ligne de donnée ( Tuple ) il est directement issu de l'ordre des données d'origine, il remplace l'ensemble des données d'identification des données d'origine
 * ColID : ID alphabétique d'une colonne de donnée ( Attribut ), il remplace les noms de colonnes des données d'origine
 * CuboideID : Combinaison des ColID composant le Cuboide
 */
class SKDisplay
{
    
    const ROW_ID = 'RowId';
    
    const NO_FLAG = 0;
    const SHOW_FILTERED      = 0x01;
    const SHOW_REMOVED       = 0x02; // Filtered mais avec les tables supprimées
    const SHOW_EQUIV_CLASS   = 0x04; //** Les classes sont présentées dans les titres
    const SHOW_VALIDITY      = 0x08; //** La validité est présentée dans les titres
    const SHOW_DATA_RAW      = 0x10; // Exclusif avec SHOW_DATA_xxx (RAW préféré si 2 sont présents)
    const SHOW_DATA_FILTERED = 0x20; // Exclusif avec SHOW_DATA_xxx (RAW préféré si 2 sont présents)
    const SHOW_DATA_COMPUTED = 0x40; // Exclusif avec SHOW_DATA_xxx (RAW préféré si 2 sont présents)
    
    public static function text ( $Object )
    {
        $Text = print_r ( $Object, TRUE );
        
        return $Text;
    }
    
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

    public static function htmlEmergence ( $SkyCube )
    {
        $Emergence = $SkyCube->getEmergence ();
        
        $Headers = array_keys ( reset ( $Emergence ) );
        
        $String = HTML::div ( HTML::tableFull ($Headers,  array ( 'border' => '1' ) ) );
        $String .= HTML::div ( HTML::tableFull ($Emergence, array ( 'border' => '1' ) ) );
        return HTML::div ( $String, array ( 'class' => 'emergence_data' ) );
    }
    
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

    public static function htmlCuboideParam ( $Cuboide, $Flags = self::NO_FLAG, $Removed = FALSE )
    {
        $CuboideClass = 'cuboide ' . ( $Removed ? 'removed' : '' );
        $Title  = '';
        $Title .= HTML::div ( $Cuboide->getID (), array ( 'class' => 'cuboide_id' ) );
        
        if ( $Flags & self::SHOW_VALIDITY )
        {
            $Title .= HTML::div ( $Cuboide->isValid () ? 'V' : 'I', array ( 'class' => 'cuboide_validity' ) );
        }
        
        if ( $Flags & self::SHOW_EQUIV_CLASS )
        {
            $Title .= HTML::div ( $Cuboide->getEquivalenceClasses ( FALSE, $Flags & self::SHOW_DATA_FILTERED ), array ( 'class' => 'cuboide_equiv_class' ) );
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
    
    public static function htmlCuboideEmergent ( $CuboideEmergent )
    {
        $Text = __METHOD__;
        
        return $Text;
    }
   
    public static function latex ( $Object )
    {
        
    }
    
    public static function latexSkyCube ( $SkyCube )
    {
        
    }
    
    public static function latexSkyCubeEmergent ( $SkyCubeEmergent )
    {
        
    }
    
    public static function latexCuboide ( $Cuboide )
    {
        
    }
    
    public static function latexCuboideEmergent ( $CuboideEmergent )
    {
        
    }
    
    
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
    
    public function getDataSet ()
    {
        return $this->DataSet;
    }
    
    public function getRowHeaders ()
    {
        return $this->RowHeaders;
    }
    
    public function getColIDs ()
    {
        return $this->ColIDs;
    }
    
    protected $DataSet; //** Table indexed by RowID and ColID of Relation measures
    protected $RowHeaders; //** Table indexed by RowID of Relation identifiers
    protected $ColIDs; //** Table indexed by ColID of Measure identifiers
    protected $Cuboides; //** List of Cuboides indexed by their header placeholder combinaison
    protected $MinMax;
    protected $IsValid;
}


