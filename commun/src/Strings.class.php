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

//--------------
} // Strings