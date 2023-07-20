<?php

/**
 * Handling all Cuboide related computation
 * @package TAGED\Algo
 */
class Cuboide extends CuboideBase
{
    const TO_MAX = 'max';
    const TO_MIN = 'min';
    
    const TEST_ON = self::TO_MAX;
    
    const CURRENT = '';
    
    /**
     * Cuboide constructor.
     *
     * @param string $CuboideID The Cuboide ID.
     * @param array $RawDataSet The raw data set.
     * @param array $RawRowHeaders The raw row headers.
     * @param array $RawColIDs The raw column IDs.
     * @param string $MinMax The minimum or maximum value type (TO_MAX or TO_MIN).
     */
    public function __construct ( $CuboideID, $RawDataSet, $RawRowHeaders, $RawColIDs, $MinMax = self::TO_MAX )
    {
        parent::__construct ( $CuboideID );

        $this->RowHeaders = $RawRowHeaders;
        $this->MinMax = $MinMax;
        
        $this->computeDataSet ( $RawDataSet, $RawColIDs );
    }

    /**
     * Prepares the data set
     * 
     * Removes empty lines
     * Computes Accords classes
     * @param array $RawDataSet
     * @param array $RawColIDs
     */
    protected function computeDataSet ( $RawDataSet, $RawColIDs )
    {
        $CuboideCols = str_split ( $this->ID );
        
        foreach ( $CuboideCols as $ColID )
        {
            $this->ColIDs [$ColID] = $RawColIDs [$ColID];
        }
        
        $RowsToRemove = array ();
        
        $FirstRow = TRUE;
        
        foreach ( $RawDataSet as $RowID => $Row )
        {
            $Empty = TRUE;
            
            foreach ( $Row as $ColID => $Value )
            {
                if ( isset ( $this->ColIDs [$ColID] ) )
                {
                    if ( ! empty ( $Value ) || ( 0 === $Value ) )
                    {
                        $Empty = FALSE;
                    }
                    $this->DataSet [$RowID] [$ColID] = $Value;
                    $this->RowIDsInput [$RowID] = $RowID;
                }
            }

            $Equivalence = $this->isEquivalent ( $this->DataSet, $RowID );
            
            if ( $FirstRow || ( $Equivalence === FALSE ) )
            {
                $this->EquivalenceClasses [$RowID] = array ();
            }
            else
            {
                $this->EquivalenceClasses [$Equivalence][] = $RowID;
            }
            $FirstRow = FALSE;
            
            if ( $Empty )
            {
                $RowsToRemove [] = $RowID;
            }
        }
        
        foreach ( $RowsToRemove as $RowID )
        {
            unset ( $this->RowIDsInput [$RowID] );
            unset ( $this->DataSet [$RowID] );
        }

        $this->IsValid = TRUE;
    }
    
    /**
     * Computes the Cuboide.
     */
    public function computeCuboide ( )
    {
        if ( $this->IsValid )
        {
            foreach ( $this->RowIDsInput as $RowID )
            {
                if ( $this->isInCuboide ( $RowID ) )
                {
                    $this->RowIDsFiltered [$RowID] = $RowID;
                }
            }
        }
    }

    /**
     * Checks if a given row is in the Cuboide.
     *
     * @param mixed $ConsideredRowID The row ID to consider.
     * @return bool Returns true if the row is in the Cuboide, otherwise false.
     */
    protected function isInCuboide ( $ConsideredRowID )
    {
        return TRUE;
    }
    
    protected $MinMax;

}