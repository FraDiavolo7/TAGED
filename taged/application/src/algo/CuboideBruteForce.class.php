<?php

/**
 * Class computing a Cuboide using a Brute Force strategy (not meant to be exact or fast)
 * @package TAGED\Algo
 */
class CuboideBruteForce extends Cuboide
{
    /**
     * Computes the Cuboide using the Brute Force strategy.
     */
    public function computeCuboide ( )
    {
        if ( $this->IsValid )
        {
            $Transposed = array ();
            foreach ( $this->RowIDsFiltered as $RowID )
            {
                $Row = $this->DataSet [$RowID];
                foreach ( $Row as $ColID => $Value )
                {
                    $Transposed[$ColID][$RowID] = $Value;
                }
            }
            
            foreach ( $Transposed as $ColID => $Col )
            {
                $Function = self::TEST_ON;
                $this->MaxCols [$ColID] = $Function ( $Col );
            }
            parent::computeCuboide ( );
        }
    }
    
    /**
     * Checks if the given row is in the Cuboide SkyLine based on comparisons with other rows.
     *
     * @param mixed $ConsideredRowID The row ID of the considered row.
     * @return bool Returns true if the row is in the Cuboide SkyLine, otherwise false.
     */
    protected function isInCuboide ( $ConsideredRowID )
    {
        // Compares this Row to each other to test if it is better or not (keep the best)
        // If current Row is on 2 factors better than ConsideredRow then it is not in the SkyLine 
        
        $Current = '';
        
        $NbCols = count ( $this->ColIDs );
        $InSkyLine = FALSE;
        if ( $this->ID == $Current ) echo "ConsideredRowID $ConsideredRowID<br>";
        if ( $this->ID == $Current ) echo "NbCols $NbCols<br>";
        
        $NbBetter = array ();
        
        foreach ( $this->RowIDsFiltered as $RowID )
        {
            $Row = $this->DataSet [$RowID];
            $NbB = 0;
            foreach ( $Row as $ColID => $Value )
            {
                if ( $Value == $this->MaxCols [$ColID] )
                {
                    ++$NbB;
                }
            }
            if ( $this->ID == $Current ) echo "NbBetter $NbB<br>";
            
            $NbBetter[$RowID] = $NbB;
        }
        
        $MaxBetter = max ( $NbBetter );
        
        if ( $this->ID == $Current ) echo "MaxBetter $MaxBetter<br>";
        if ( $this->ID == $Current ) echo "MaxBetter [$ConsideredRowID] " . $NbBetter [$ConsideredRowID] ."<br>";
        
        if ( $NbBetter [$ConsideredRowID] == $MaxBetter ) $InSkyLine = TRUE;
        
        return $InSkyLine;
    }
}