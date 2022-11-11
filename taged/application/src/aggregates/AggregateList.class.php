<?php

class AggregateList
{
    protected static $Folder = AGGREGATE_FOLDER_DESC;
    
    public function __construct ( )
    {
        $this->List = array ();
        $Files = array_diff ( scandir ( static::$Folder ), array ( '.', '..' ) );
        
        foreach ( $Files as $File )
        {
            if ( pathinfo ( $File, PATHINFO_EXTENSION ) == "ini" )
            {
                $FileName = pathinfo ( $File, PATHINFO_FILENAME );
                $this->List [ $FileName ] = Strings::convertCase ( $FileName, Strings::CASE_NATURAL, Strings::CASE_CAMEL_LOWER );
            }
        }
    }
    
    public function getList ()
    {
        return $this->List;
    }

    public function getFileContent ( $Name )
    {
        $FilePath = static::$Folder . $Name . '.ini';
        return parse_ini_file ( $FilePath );
    }
    
    public function getFileField ( $Name, $Field )
    {
        $Data = $this->getFileContent ( $Name );
        return Arrays::getIfSet ( $Data, $Field, NULL );
    }
    

    protected $List;
} // AggregateList
