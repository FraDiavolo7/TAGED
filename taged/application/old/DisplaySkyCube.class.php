<?php

/**
 * @deprecated
 * @package Deprecated
 */
class DisplaySkyCube
{
    const MAX_CUBOIDE = 128;
    const MIN_COLID = 'A';
    
    const CUBOIDE = 'Cuboide';
    
    public function __construct ( $Data, $RelationCols, $MeasureCols, $MinMax = Cuboide::TO_MAX )
    {
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->Cuboides = array ();
        $this->MinMax = $MinMax;
        $this->IsValid = TRUE;
        $this->computeDataSet ( $Data, $RelationCols, $MeasureCols );
        
//         if ( $this->IsValid )
//         {
//             echo "Valid <br>\n";
//             echo "DataSet " . HTML::tableFull ( $this->DataSet, array ( 'border' => '1' ) ) .  "<br>\n";
//             echo "RowHeaders " . HTML::tableFull ( $this->RowHeaders, array ( 'border' => '1' ) ) .  "<br>\n";
//             echo "ColIDs " . HTML::tableFull ( $this->ColIDs, array ( 'border' => '1' ) ) .  "<br>\n";
//         }
//         else 
//         {
//             echo "Not Valid <br>\n";
//         }
        
        $this->generateCuboideList ();
        
//         if ( $this->IsValid )
//         {
//             echo "Valid <br>\n";
//             echo "Cuboides " . Arrays::arrToString ( $this->Cuboides ) .  "<br>\n";
//         }
//         else
//         {
//             echo "Not Valid <br>\n";
//         }
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


