<?php

class User 
{
    private $Username;
    private $Tag;
    private $Clan;
    
    /**
     * @brief Hero constructor.
     * @param int $Hero The position of the player
     * @param string $Username The name of the user playing
     * @param int $Avatar The avatar of the user
     * @param int $Rating The rating of the user
     */
    public function __construct ( $Username = '', $Tag = '', $Clan = '' ) 
    {
        $this->Username = $Username;
        $this->Tag = $Tag;
        $this->Clan = $Clan;
    }

    /**
     *  @brief Creates a player from an array of data arrange as :
     *  Array [1] => Hero position
     *  Array [2] => User name
     *  Array [3] => Avatar
     *  Array [4] => Rating
     *  @param array $Array The array to use for filling the Palyer
     *  @return A new Hero object
     */
    public static function create ( $Array )
    {
        $Rank     = Arrays::getOrCrash ( $Array, 1, 'Invalid Hero position' );
        $Username = Arrays::getOrCrash ( $Array, 3, 'Invalid Player name'     );
        $URL      = Arrays::getOrCrash ( $Array, 2, 'Invalid hero URL'     );
        $Rift     = Arrays::getIfSet   ( $Array, 4, ''  );
        $Time     = Arrays::getIfSet   ( $Array, 5, ''  );
        
        return new Hero ( '', $Username, $URL, $Rank, $Rift, $Time );
    }
    
    public static function mark4DL ( $Array, $BaseURL, $Server, $HeroClass )
    {
        $Rank     = Arrays::getOrCrash ( $Array, 1, 'Invalid Hero position' );
        $Username = Arrays::getOrCrash ( $Array, 3, 'Invalid Player name'     );
        $URL      = $BaseURL . Arrays::getOrCrash ( $Array, 2, 'Invalid hero URL'     );
        $Rift     = Arrays::getIfSet   ( $Array, 4, ''  );
        $Time     = Arrays::getIfSet   ( $Array, 5, ''  );
        $TimeArr  = explode ( 'min ', $Time );
        $TimeMin  = $TimeArr [0];
        $TimeArrS = explode ( '.', $TimeArr [1] ); 
        $TimeS    = $TimeArrS [0];
        $TimeMS   = $TimeArrS [1];
        $TimeStr  = sprintf ( "%02d-%02d-%03d", $TimeMin, $TimeS, $TimeMS );
        $Folder   = sprintf ( '%s%03d/', DATA_TMP_HNS_ADDR, $Rank % 100 );
        $FileName = sprintf ( '%s_%s_%04d_%03d_%s', $Server, $HeroClass, $Rank, $Rift, $TimeStr );
        echo "marking $FileName $URL<br>\n";
        $FilePath = $Folder . $FileName;

        if ( ! is_dir ( $Folder ) ) mkdir ( $Folder, 0777, true );

        file_put_contents ( $FilePath, $URL );
    }

    public function __toString ( )
    {
        return 'Hero ' . $this->Heroname . ' is ' . $this->Username . ' #' . $this->Rank . ' Rift ' . $this->Rift . " in " . $this->Time;
    }

    public function setHeroname ( $NewValue ) { $this->Heroname = $NewValue; }
    public function setUsername ( $NewValue ) { $this->Username = $NewValue; }
    public function setURL      ( $NewValue ) { $this->URL      = $NewValue; }
    public function setServer   ( $NewValue ) { $this->Server   = $NewValue; }
    public function setClass    ( $NewValue ) { $this->Class    = $NewValue; }
    public function setRank     ( $NewValue ) { $this->Rank     = $NewValue; }
    public function setRift     ( $NewValue ) { $this->Rift     = $NewValue; }
    public function setTime     ( $NewValue ) { $this->Time     = $NewValue; }
    public function setTime     ( $NewValue ) { $this->Time     = $NewValue; }
    
    public function getHeroname ( ) { return $this->Heroname; }
    public function getUsername ( ) { return $this->Username; }
    public function getURL      ( ) { return $this->URL     ; }
    public function getServer   ( ) { return $this->Server  ; }
    public function getClass    ( ) { return $this->Class   ; }
    public function getRank     ( ) { return $this->Rank    ; }
    public function getRift     ( ) { return $this->Rift    ; }
    public function getTime     ( ) { return $this->Time    ; }
    
    
}
