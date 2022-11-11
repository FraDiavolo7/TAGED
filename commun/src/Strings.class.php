<?php
// ========================================================================== //
//                              S T R I N G S                                 //
// ========================================================================== //
/**
 * Class Strings manages common handlings of string variables to oncrease language capabilities
 */
class Strings {

    public static function bool ( $Boolean )
    {
        return ( $Boolean ? "true" : "false" );
    }
    
    public static function limitSize ( $String, $Length, $Placeholder = '...' )
    {
        $Result = $String;
        if ( strlen ( $String ) > $Length )
        {
            $Result = substr ( 0, strlen ( $String ) - strlen ( $Placeholder ) ) . $Placeholder;
        }
        return $Result;
    }
    
    /**
     * Explodes the provided String into pieces separated by spaces or tabulation
     * @param string $String The String to explode
     * @return array
     */
    public static function explodeSpaces ( $String )
    {
        $StringToExplode = str_replace ( "\t", " ", $String );
       
        return explode ( " ", $StringToExplode );
    }

    /**
     * Explodes the provided String into pieces separated by spaces or tabulation
     * @param string $String The String to explode
     * @return array
     */
    public static function explodeLines ( $String )
    {
        $StringToExplode = str_replace ( "\r\n", "\n", $String );
        $StringToExplode = str_replace ( "\r", "\n", $StringToExplode );
       
        return explode ( "\n", $StringToExplode );
    }
   
    /**
     * Compares both Strings together but only for the length of the shorter of the two
     * @param string $String1 The first string to compare
     * @param string $String2 The second string to compare
     * @return bool
     */
    public static function compareSameSize ( $String1, $String2 )
    {
        $SmallString = $String1;
        $LongString = $String2;
        if ( strlen ( $String1 ) > strlen ( $String2 ) )
        {
            $SmallString = $String2;
            $LongString = $String1;
        }
        return strtolower ( $SmallString ) == strtolower ( substr ( $LongString, 0, strlen ( $SmallString ) ) );
    }

    /**
     * Gets a formated version of the time
     * Time format : YYYYMMJJ_HHmmss  
     * @return string
     */
    public static function getTime ()
    {
        return date ( 'Ymd_His', time () );
    } // getTime ()
   
    /**
     * Gets a formated version of the date
     * Date format : YYYYMMJJ
     * @return string
     */
    public static function getDate ()
    {
        return date ( 'Ymd', time () );
    } // getDate ()


/**
  * Does myString contains x ?
  * Ex: strContains ("toto", "t") or strContains ("toto", array ("t", "hio"))
  * @param string $in The string to search in
  * @param string|array $that The string to search or an array on string to search
  * @return bollean
*/
public static function contains ($in, $that)
{
   if (is_array ($that))
   {
      foreach ($that as $word)
      {
         if (strpos ($in, $word) !== FALSE)
         {
            return TRUE;
         }
      } # foreach element of array
      return FALSE;
   }
   # else scalar
   else
   {
      return (strpos ($in, $that) !== FALSE);
   }
} # contains


/**
  * Advanced (str)replace function
  * replace $it by $by in $in string starting at the $nth occurence for $nb occurences
  * if $nth = 0, replace all occurences
  * Principle: we explode string into an array using that as separator
  *            then we rebuild result depending on position
  * @param string $it The searched string
  * @param string $by The new string
  * @param string $in The original string
  * @param integer $nth Starting at the nth occurence of it, if 0 replace all occurences
  * @param integer $nb Number of occurences of it to change
  * @return string
*/
public static function replace ($it, $by, $in, $nth=0, $nb=1)
{
  if ($nth == 0) return str_replace ($it, $by, $in);

  $words = explode ($it, $in);
  return implode ($it, array_slice ($words, 0, $nth)).$by.   # keep as is
         implode ($by, array_slice ($words, $nth, $nb)).     # replace from nth for nb
         (count ($words)> $nth+$nb ? $it.implode ($it, array_slice ($words, $nth+$nb)):""); #  finally keep the rest as is
} # replace

/**
  * Generate a random string of a given length (excluding prefix)
  * @param integer|10 $length Size of string to generate
  * @param string|"" $prefix Optional prefix of generated strings
  * @return string
*/
public static function generateRandom($length=10, $prefix="")
{
    return $prefix.substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
} # generateRandom


/**
  * Enclose a string with enclosure
  * @param string $that String to process
  * @param string|'"' $before The string to add before
  * @param string|NULL $after The string to add after. If not given then after=before
  * @return string
*/
public static function enclose ($that, $before='"', $after=NULL)
{
   is_null ($after) && $after=$before;
   return $before.$that.$after;
}

/**
 * Convets a text to pure ASCII characters
 * @param string $String The String to convert
 * @return string
 * @note Converts only Cyrilic for now
 */
public static function convertToAscii ( $String )
{
    $Result = $String;
    $TmpStr = $String;

    $normalizeChars = array(
    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
    'Ł' => 'L', 'ч' => 'y',
    'α' => 'a', 'β' => 'b', 'Γ' => 'G', 'γ' => 'g', 'Δ' => 'D', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z',
    'η' => 'h', 'Θ' => 'T', 'θ' => 't', 'ι' => 'i', 'κ' => 'k', 'Λ' => 'L', 'λ' => 'l', 'μ' => 'm',
    'ν' => 'n', 'Ξ' => 'X', 'ξ' => 'x', 'Π' => 'P', 'π' => 'p', 'ρ' => 'r', 'Σ' => 'S', 'σ' => 's',
    'τ' => 't', 'υ' => 'u', 'Φ' => 'F', 'φ' => 'f', 'χ' => 'x', 'Ψ' => 'P', 'ψ' => 'p', 'Ω' => 'O',
    'ω' => 'o',
    'ł' => 'l', 'Ħ' => 'H', 'Ŕ' => 'R', 'Č' => 'C', 'č' => 'c', 'Ď' => 'D', 'ď' => 'c', 'Ě' => 'E',
    'ě' => 'e', 'Ľ' => 'L', 'ľ' => 'l', 'Ň' => 'N', 'ň' => 'n', 'Ř' => 'R', 'ř' => 'r', 'Š' => 'S',
    'š' => 's', 'Ť' => 'T', 'ť' => 't', 'Ž' => 'Z', 'ž' => 'z', 'Ǎ' => 'A', 'ǎ' => 'a', 'Ǐ' => 'I',
    'ǐ' => 'i', 'Ǒ' => 'O', 'ǒ' => 'o', 'Ǔ' => 'U', 'ǔ' => 'u', 'Ǚ' => 'U', 'ǚ' => 'u', 'Ǧ' => 'G',
    'ǧ' => 'g', 'Ǩ' => 'K', 'ǩ' => 'k', 'Ǯ' => 'Z', 'ǯ' => 'z', 'ǰ' => 'j', 'Ȟ' => 'J', 'ȟ' => 'j',


    'š' => 's', 'ž' => 'z', 'Ŏ' => 'O', 'š' => 's',


    );

    $TmpStr = strtr ( $TmpStr, $normalizeChars );

    $cyr = ['Љ', 'Њ', 'Џ', 'џ', 'ш', 'ђ', 'ч', 'ћ', 'ж', 'љ', 'њ', 'Ш', 'Ђ', 'Ч', 'Ћ', 'Ж','Ц','ц', 'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п', 'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я', 'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П', 'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];
    $lat = ['Lj', 'Nj', 'Dž', 'dž', 'š', 'đ', 'č', 'ć', 'ž', 'lj', 'nj', 'Š', 'Đ', 'Č', 'Ć', 'Ž','C','c', 'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p', 'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya', 'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P', 'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
        ];

    $TmpStr = str_replace ( $cyr, $lat, $TmpStr );

    $Result = $TmpStr;

    return $Result;
}

const CASE_SNAKE = 'snake_case';
const CASE_SNAKE_UPPER = 'Snake_Case';
const CASE_CAMEL = 'CamelCase';
const CASE_CAMEL_LOWER = 'camelCase';
const CASE_NATURAL = 'Natural Case';
const CASE_NATURAL_LOWER = 'natural case';

public static function convertCase ( $Text, $To = self::CASE_NATURAL, $From = self::CASE_CAMEL )
{
    $Text = trim ( $Text );
    $Result = $Text;
    
    if ( $To != $From )
    {
        $Intermediate = array ();
        switch ( $From )
        {
            case self::CASE_SNAKE_UPPER :
                $Text = strtolower ( $Text );
                
            case self::CASE_SNAKE :
                $Intermediate = explode ( '_', $Text );
                break;
                
            case self::CASE_CAMEL : 
            case self::CASE_CAMEL_LOWER : 
                $StringToExplode = str_replace ( 
                    array ( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' ), 
                    array ( ' A', ' B', ' C', ' D', ' E', ' F', ' G', ' H', ' I', ' J', ' K', ' L', ' M', ' N', ' O', ' P', ' Q', ' R', ' S', ' T', ' U', ' V', ' W', ' X', ' Y', ' Z' ),
                    $Text );
                
                $Text = trim ( $StringToExplode );
            
            case self::CASE_NATURAL : 
                $Text = strtolower ( $Text );
            
            case self::CASE_NATURAL_LOWER : 
                $Intermediate = explode ( ' ', $Text );
                break;
                
            default: break;
        }
        
        switch ( $To ) 
        {
            case self::CASE_SNAKE_UPPER :
                $Intermediate = array_map ( 'ucfirst', $Intermediate );
            
            case self::CASE_SNAKE :
                
                $Result = implode ( '_', $Intermediate );
                break;
            
            case self::CASE_CAMEL : 
            case self::CASE_CAMEL_LOWER :
                $Intermediate = array_map ( 'ucfirst', $Intermediate );
                
                $Result = implode ( '', $Intermediate );
                
                if ( $To == self::CASE_CAMEL_LOWER ) $Result = lcfirst ( $Result );
                
                break;
                
            case self::CASE_NATURAL :  
                $Intermediate = array_map ( 'ucfirst', $Intermediate );
                
            case self::CASE_NATURAL_LOWER : 
                
                $Result = implode ( ' ', $Intermediate );
                break;
                
            default: break;
        }
    }
    
    return $Result;
}

//--------------
} // Strings
