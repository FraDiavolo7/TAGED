<?php

class CuboideBruteForce extends Cuboide
{
    
    protected function computeCuboide ( )
    {
        if ( $this->IsValid )
        {
            $Transposed = array ();
            foreach ( $this->DataSet as $RowID => $Row )
            {
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
        
        foreach ( $this->DataSet as $RowID => $Row )
        {
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