<?php

class CuboideBlocNestedLoop extends Cuboide
{
    const CURRENT = '';
    
    protected function countDifferences ( $RowID1, $RowID2, &$NbBetter, &$NbWorse )
    {
        $NbBetter = 0;
        $NbWorse = 0;
        
        foreach ( $this->ColIDs as $ColID => $Header )
        {
//             if ( $this->ID == self::CURRENT ) echo "ColID $ColID<br>";
//             if ( $this->ID == self::CURRENT ) echo "[$RowID1]" . $this->DataSet [$RowID1] [$ColID] ."<br>";
//             if ( $this->ID == self::CURRENT ) echo "[$RowID2]" . $this->DataSet [$RowID2] [$ColID] ."<br>";
//             if ( ! isset ( $this->DataSet [$RowID1] ) )          echo __LINE__ . ' ' . $this->ID . ' $this->DataSet [' . $RowID1 . ']' . 'not set' ."<br>";
//             if ( ! isset ( $this->DataSet [$RowID1] [$ColID] ) ) echo __LINE__ . ' ' . $this->ID . ' $this->DataSet [' . $RowID1 . '] [' . $ColID . ']' . 'not set' ."<br>";
//             if ( ! isset ( $this->DataSet [$RowID2] ) )          echo __LINE__ . ' ' . $this->ID . ' $this->DataSet [' . $RowID2 . ']' . 'not set' ."<br>";
//             if ( ! isset ( $this->DataSet [$RowID2] [$ColID] ) ) echo __LINE__ . ' ' . $this->ID . ' $this->DataSet [' . $RowID2 . '] [' . $ColID . ']' . 'not set' ."<br>";
            
            if ( $this->MinMax == self::TO_MAX )
            {
                if ( ! isset ( $this->DataSet [$RowID1] [$ColID] ) )
                {
                    $NbWorse += 1;
                }
                elseif ( ! isset ( $this->DataSet [$RowID2] [$ColID] ) )
                {
                    $NbBetter += 1;
                }
                else 
                {
                    $NbBetter += ( $this->DataSet [$RowID1] [$ColID] > $this->DataSet [$RowID2] [$ColID] ? 1 : 0 );
                    $NbWorse  += ( $this->DataSet [$RowID1] [$ColID] < $this->DataSet [$RowID2] [$ColID] ? 1 : 0 );
                }
            }
            else
            {
                if ( ! isset ( $this->DataSet [$RowID1] [$ColID] ) )
                {
                    $NbBetter += 1;
                }
                elseif ( ! isset ( $this->DataSet [$RowID2] [$ColID] ) )
                {
                    $NbWorse += 1;
                }
                else
                {
                    $NbBetter += ( $this->DataSet [$RowID1] [$ColID] < $this->DataSet [$RowID2] [$ColID] ? 1 : 0 );
                    $NbWorse  += ( $this->DataSet [$RowID1] [$ColID] > $this->DataSet [$RowID2] [$ColID] ? 1 : 0 );
                }
            }
        }
    }
    
    public function computeCuboide ( )
    {
        $FirstID = array_key_first ( $this->RowIDsInput );
        $Skyline = array ( $FirstID => $FirstID );
        
        if ( $this->ID == self::CURRENT ) echo "Skyline " . __LINE__  . " " . print_r ( $Skyline, TRUE ) . "<br>";
        
        foreach ( $this->RowIDsInput as $RowID )
        {
            $Row = $this->DataSet [$RowID];
            if ( $RowID == $FirstID ) continue;
//             if ( $this->ID == self::CURRENT ) echo "RowID $RowID<br>";
            
            $RowsToRemove = array ();
            
            $IsWorse = FALSE;
            
            foreach ( $Skyline as $SLRowID )
            {
                $NbBetter = 0;
                $NbWorse = 0;
                $this->countDifferences ( $RowID, $SLRowID, $NbBetter, $NbWorse );
//                 if ( $this->ID == self::CURRENT ) echo "SLRowID $SLRowID<br>";
//                 if ( $this->ID == self::CURRENT ) echo "NbBetter $NbBetter<br>";
//                 if ( $this->ID == self::CURRENT ) echo "NbWorse $NbWorse<br>";
                
                if ( ( $NbWorse > 0 ) && ( $NbBetter == 0 ) )
                {
                    $IsWorse = TRUE;
                    break;
                }
                
                if ( ( $NbBetter > 0 ) && ( $NbWorse == 0 ) )
                {
                    $RowsToRemove [] = $SLRowID;
                }
            }
            
            if ( $IsWorse ) 
            {
                if ( $this->ID == self::CURRENT ) echo "Skyline " . __LINE__  . " row $RowID is worse, skipping " . print_r ( $Row, TRUE ) . "<br>";
                continue;
            }
            
            foreach ( $RowsToRemove as $RowToRemove )
            {
                if ( $this->ID == self::CURRENT ) echo "Skyline " . __LINE__  . " removing " . $RowToRemove . " " . print_r ( $this->DataSet [$RowToRemove], TRUE ). "<br>";
                unset ( $Skyline [$RowToRemove] );
            }
            
            $Skyline [$RowID] = $RowID;
            if ( $this->ID == self::CURRENT ) echo "Skyline " . __LINE__  . " " . print_r ( $Skyline, TRUE ) . "<br>";
        }

        if ( $this->ID == self::CURRENT ) echo "Skyline " . __LINE__  . " " . print_r ( $Skyline, TRUE ) . "<br>";
        
        foreach ( $this->DataSet as $RowID => $Row )
        {
            if ( isset ( $Skyline [$RowID] ) )
            {
                $this->RowIDsFiltered [$RowID] = $RowID;
            }
        }
        if ( $this->ID == self::CURRENT ) echo "Skyline " . __LINE__  . " end " . print_r ( $this->RowIDsFiltered, TRUE ) . "<br>";
    }
}