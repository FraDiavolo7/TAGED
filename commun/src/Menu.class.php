<?php

class Menu
{
    const PAGE_SELECTOR = 'sel';
    
    public function __construct ( )
    {
        
    }
    
    public function __destruct ( )
    {
        
    }
    
    public function addEntry ( $Path, $Name, $URL, $Rights )
    {
        
    }
    
    public static function nav ( $Liste, $Selected = '', $Attributes = array (), $Depth = 1 )
    {
        $Result = '';
        $Content = '';
        $Class = '';
        // Liste est un tableau à multiple dimensions
        if ( is_array ( $Liste ) )
        {
            foreach ( $Liste as $Name => $Item )
            {
                if ( $Name == $Selected ) $Class = 'selected';
                else $Class = '';
             
                $ItemContent = '';
                if ( is_array ( $Item ) )
                {
                    $ItemContent  = HTML::span ( $Name, array ( 'class' => $Class ) );
                    $ItemContent .= HTML::menu ( $Item, $Selected, array (), $Depth + 1 );
                }
                else
                {
                    $ItemContent = HTML::link ( self::buildGenLink ( $Item ), $Name, false, array ( 'class' => $Class ) );
                }
                $Content .= HTML::li ( $ItemContent );
            }
        }
        
        $Result = HTML::ul ( $Content, array ( "class" => "menu_$Depth") );
        
        if ( $Depth == 1 )
        {
            $Result = HTML::nav ( $Result, $Attributes );
        }
        
        return $Result;
    }
    
    
    public static function buildGenLink ( $Page, $Parameters = array () )
    {
        $Args = "?" . self::PAGE_SELECTOR . "=" . $Page;
        if ( is_array ( $Parameters ) )
        {
            foreach ( $Parameters as $ArgName => $ArgValue )
            {
                $Args .= "&$ArgName=$ArgValue";
            }
        }
        return $Args;
    }
}