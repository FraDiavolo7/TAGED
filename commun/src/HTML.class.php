<?php

/**
 *
 * @package Commun
 */
class HTML
{
    const PAGE_SELECTOR = 'sel';
    
    public static function showHeader ( $Current = '' )
    {
        $Page = PAGE_DEFAULT;
        if ( ( '' != $Current ) && ( isset ( $GLOBALS [PAGE_LIST] [$Current] ) ) ) $Page = $Current;
        
    ?><!DOCTYPE html>
    <html>
    <head>
        <title><?= $GLOBALS [PAGE_LIST] [$Page] [PAGE_LIST_TITLE] ?></title>
        <link rel="stylesheet" href="noox.css">
        <style>

        </style>
    <?php
        self::tag ( '/head' );
        self::tag ( 'body' );
        self::tag ( 'header' );
        self::tag ( 'h1', $GLOBALS [PAGE_LIST] [$Page] [PAGE_LIST_MENU] );
        self::tag ( '/header' );

        self::showMenu ( $Current );
    }



    public static function showFooter ()
    {
        self::tag ( 'footer', ' ' );
    }

    public static function showMenu ( $Current = '' )
    {
    }

    public static function redirect ( $URL, $Time = 0 )
    {
       self::tag ( '!DOCTYPE html' );
       self::tag ( 'html' );
       self::tag ( 'head' );
       self::tag ( 'meta', '', array ( 'http-equiv' => "refresh", 'content' => $Time . "; URL=" . $URL ) );
       self::tag ( '/head' );
       self::tag ( '/html' );
    }
    
    public static function showRedirect ( $URL, $Time = 0 )
    {
        echo self::redirect ( $URL, $Time );
    }
    
    public static function bool ( $Boolean )
    {
        return ( $Boolean ? "TRUE" : "FALSE" );
    }
    
    public static function getTag ( $Tag = 'br', $Content = '', $Attributes = array () )
    {
        $FullTag = '<' . $Tag;
        foreach ( $Attributes as $Attr => $Val )
        {
            $FullTag .= " $Attr='$Val'";
        }
        $FullTag .= ">";
        
        if ( $Content != '' ) $FullTag .= $Content . '</' . $Tag   .'>';
        
        return $FullTag . "\n";
    }

    public static function tag ( $Tag = 'br', $Content = '', $Attributes = array () )
    {
        echo self::getTag ( $Tag, $Content, $Attributes );
    }

    public static function upgradeInsecureRequests ()
    {
        self::tag ( 'meta', '', array ( 'http-equiv' => "Content-Security-Policy", 'content' => "upgrade-insecure-requests" ) );
    }
    
    
    public static function startHtml ()
    {
        $Text  = self::getTag ( '!DOCTYPE html' ) . "\n";
        $Text .= self::getTag ( 'html' );
        return $Text;
    }
    public static function meta ( $Data )  { return self::getTag ( 'meta', '', $Data );    }
    public static function cssFile ( $File )  { return self::getTag ( 'link', '', array ( 'rel' => 'stylesheet', 'href' => $File ) );    }
    public static function endHtml ()  {   return self::getTag ( '/html' );    }
    public static function endBody () { return self::getTag ( '/body' ); }
    public static function startBody ( $Attributes = array () )  {   return self::getTag ( 'body', '', $Attributes );    }
    public static function startHead ()  {   return self::getTag ( 'head' ); }    
    public static function endHead ()  {   return self::getTag ( '/head' );    }
    public static function jsFile ( $File )  {   return self::getTag ( 'script', ' ', array ( "src" => $File ) );    }
    public static function startCSS ()  {   return self::getTag ( 'style' );    }
    public static function endCSS ()     {   return self::getTag ( '/style' );    }
    public static function table     ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'table',   $Content, $Attributes );    }
    public static function tr        ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'tr',      $Content, $Attributes );    }
    public static function th        ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'th',      $Content, $Attributes );    }
    public static function td        ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'td',      $Content, $Attributes );    }
    public static function form      ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'form',    $Content, $Attributes );    }
    public static function p         ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'p',       $Content, $Attributes );    }
    public static function div       ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'div',     $Content, $Attributes );    }
    public static function pre       ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'pre',     $Content, $Attributes );    }
    public static function span      ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'span',    $Content, $Attributes );    }
    public static function headerDiv ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'header',  $Content, $Attributes );    }
    public static function footer    ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'foorter', $Content, $Attributes );    }
    public static function article   ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'article', $Content, $Attributes );    }
    public static function aside     ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'aside',   $Content, $Attributes );    }
    public static function nav       ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'nav',     $Content, $Attributes );    }
    public static function section   ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'section', $Content, $Attributes );    }
    public static function ul        ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'ul',      $Content, $Attributes );    }
    public static function li        ( $Content = ' ', $Attributes = array () )  { return self::getTag ( 'li',      $Content, $Attributes );    }
    public static function startForm    ( $Attributes = array () )  { return self::getTag ( 'form',    '', $Attributes ); }
    public static function startDiv     ( $Attributes = array () )  { return self::getTag ( 'div',     '', $Attributes ); }
    public static function startSpan    ( $Attributes = array () )  { return self::getTag ( 'span',    '', $Attributes ); }
    public static function startNav     ( $Attributes = array () )  { return self::getTag ( 'nav',     '', $Attributes ); }
    public static function startHeader  ( $Attributes = array () )  { return self::getTag ( 'header',  '', $Attributes ); }
    public static function startFooter  ( $Attributes = array () )  { return self::getTag ( 'footer',  '', $Attributes ); }
    public static function startSection ( $Attributes = array () )  { return self::getTag ( 'section', '', $Attributes ); }
    public static function startArticle ( $Attributes = array () )  { return self::getTag ( 'article', '', $Attributes ); }
    public static function startAside   ( $Attributes = array () )  { return self::getTag ( 'aside',   '', $Attributes ); }
    public static function startUL      ( $Attributes = array () )  { return self::getTag ( 'ul',      '', $Attributes ); }
    public static function startLI      ( $Attributes = array () )  { return self::getTag ( 'li',      '', $Attributes ); }
    public static function startTable   ( $Attributes = array () )  { return self::getTag ( 'table',   '', $Attributes ); }
    public static function startTR      ( $Attributes = array () )  { return self::getTag ( 'tr',      '', $Attributes ); }
    public static function startTH      ( $Attributes = array () )  { return self::getTag ( 'th',      '', $Attributes ); }
    public static function startTD      ( $Attributes = array () )  { return self::getTag ( 'td',      '', $Attributes ); }
    public static function endTable   ( )  { return self::getTag ( '/table'   ); }
    public static function endTR      ( )  { return self::getTag ( '/tr'      ); }
    public static function endTH      ( )  { return self::getTag ( '/th'      ); }
    public static function endTD      ( )  { return self::getTag ( '/td'      ); }
    public static function endUL      ( )  { return self::getTag ( '/ul'      ); }
    public static function endLI      ( )  { return self::getTag ( '/li'      ); }
    public static function endForm    ( )  { return self::getTag ( '/form'    ); }
    public static function endDiv     ( )  { return self::getTag ( '/div'     ); }
    public static function endSpan    ( )  { return self::getTag ( '/span'    ); }
    public static function endNav     ( )  { return self::getTag ( '/nav'     ); }
    public static function endHeader  ( )  { return self::getTag ( '/header'  ); }
    public static function endFooter  ( )  { return self::getTag ( '/footer'  ); }
    public static function endSection ( )  { return self::getTag ( '/section' ); }
    public static function endArticle ( )  { return self::getTag ( '/article' ); }
    public static function endAside   ( )  { return self::getTag ( '/aside'   ); }
    public static function startJS    ( )  { return self::getTag ( 'script'   ); }    
    public static function endJS      ( )  { return self::getTag ( '/script'  ); }    
    public static function headTitle ( $Title )  {   return self::getTag ( 'title', $Title );    }    
    public static function title ( $Title, $Level = 1 )  {   return self::getTag ( 'h'.$Level, $Title );    }    

    public static function link ( $URL, $Label = '', $NewPage = false, $Attributes = array () )  
	{   
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'href' ] = $URL;
        $InnerAttributes [ 'target' ] = ( $NewPage ? '_blank' : '_self' ) ;
		
		$Text = ( $Label == '' ? $URL : $Label );

		return self::getTag ( 'a', $Text, $InnerAttributes );    
	}    

    
	public static function borderDiv ( $Content = ' ', $Attributes = array () )  
	{ 
	    return self::div ( self::div ( $Content, array ( "class" => "border" ) ), $Attributes );    
	}
	
    
    public static function showStartHtml ()  { echo self::startHtml ( ) . "\n";    }
    public static function showEndHtml   () { echo self::endHtml ( ) . "\n";    }
    public static function showStartBody ( $Attributes = array () )  { echo self::startBody ( $Attributes = array () ) . "\n";    }
    public static function showEndBody   ()  { echo self::endBody ( ) . "\n";    }
    public static function showStartHead ()  { echo self::startHead ( ) . "\n";    }
    public static function showEndHead   ()  { echo self::endHead ( ) . "\n";    }
    public static function showCssFile ( $File )  { echo self::cssFile ( $File ) . "\n";    }
    public static function showStartCSS ()  { echo self::startCSS ( ) . "\n";    }
    public static function showEndCSS ()  { echo self::endCSS ( ) . "\n";    }
    public static function showStartJS ()  { echo self::startJS ( ) . "\n";    }
    public static function showEndJS ()  { echo self::endJS ( ) . "\n";    }
    public static function showHeadTitle ( $Title )  { echo self::headTitle ( $Title ) . "\n";    }
    public static function showTitle ( $Title, $Level = 1 )  { echo self::Title ( $Title, $Level ) . "\n";    }
    public static function showStartForm    ( $Attributes = array () )  { echo self::startForm    ( $Attributes ) . "\n";    }
    public static function showStartDiv     ( $Attributes = array () )  { echo self::startDiv     ( $Attributes ) . "\n";    }
    public static function showStartNav     ( $Attributes = array () )  { echo self::startNav     ( $Attributes ) . "\n";    }
    public static function showStartHeader  ( $Attributes = array () )  { echo self::startHeader  ( $Attributes ) . "\n";    }
    public static function showStartFooter  ( $Attributes = array () )  { echo self::startFooter  ( $Attributes ) . "\n";    }
    public static function showStartSection ( $Attributes = array () )  { echo self::startSection ( $Attributes ) . "\n";    }
    public static function showStartArticle ( $Attributes = array () )  { echo self::startActible ( $Attributes ) . "\n";    }
    public static function showStartAside   ( $Attributes = array () )  { echo self::startAside   ( $Attributes ) . "\n";    }
    public static function showStartUL      ( $Attributes = array () )  { echo self::startUL      ( $Attributes ) . "\n";    }
    public static function showStartLI      ( $Attributes = array () )  { echo self::startLI      ( $Attributes ) . "\n";    }
    public static function showStartTable   ( $Attributes = array () )  { echo self::startTable   ( $Attributes ) . "\n";    }
    public static function showStartTR      ( $Attributes = array () )  { echo self::startTR      ( $Attributes ) . "\n";    }
    public static function showStartTH      ( $Attributes = array () )  { echo self::startTH      ( $Attributes ) . "\n";    }
    public static function showStartTD      ( $Attributes = array () )  { echo self::startTD      ( $Attributes ) . "\n";    }
    public static function showEndUL      ( )  { echo self::endUL      ( ) . "\n"; }
    public static function showEndLI      ( )  { echo self::endLI      ( ) . "\n"; }
    public static function showEndForm    ( )  { echo self::endForm    ( ) . "\n"; }
    public static function showEndDiv     ( )  { echo self::endDiv     ( ) . "\n"; }
    public static function showEndNav     ( )  { echo self::endNav     ( ) . "\n"; }
    public static function showEndHeader  ( )  { echo self::endHeader  ( ) . "\n"; }
    public static function showEndFooter  ( )  { echo self::endFooter  ( ) . "\n"; }
    public static function showEndSection ( )  { echo self::endSection ( ) . "\n"; }
    public static function showEndArticle ( )  { echo self::endArticle ( ) . "\n"; }
    public static function showEndAside   ( )  { echo self::endAside   ( ) . "\n"; }
    public static function showEndTable   ( )  { echo self::endTable   ( ) . "\n"; }
    public static function showEndTR      ( )  { echo self::endTR      ( ) . "\n"; }
    public static function showEndTH      ( )  { echo self::endTH      ( ) . "\n"; }
    public static function showEndTD      ( )  { echo self::endTD      ( ) . "\n"; }
    public static function showForm      ( $Content = ' ', $Attributes = array () )  { echo self::form      ( $Content, $Attributes ) . "\n";    }
    public static function showDiv       ( $Content = ' ', $Attributes = array () )  { echo self::div       ( $Content, $Attributes ) . "\n";    }
    public static function showNav       ( $Content = ' ', $Attributes = array () )  { echo self::div       ( $Content, $Attributes ) . "\n";    }
    public static function showHeaderDiv ( $Content = ' ', $Attributes = array () )  { echo self::headerDiv ( $Content, $Attributes ) . "\n";    }
    public static function showFooterDiv ( $Content = ' ', $Attributes = array () )  { echo self::footer    ( $Content, $Attributes ) . "\n";    }
    public static function showSection   ( $Content = ' ', $Attributes = array () )  { echo self::section   ( $Content, $Attributes ) . "\n";    }
    public static function showArticle   ( $Content = ' ', $Attributes = array () )  { echo self::article   ( $Content, $Attributes ) . "\n";    }
    public static function showAside     ( $Content = ' ', $Attributes = array () )  { echo self::aside     ( $Content, $Attributes ) . "\n";    }
    public static function showUL        ( $Content = ' ', $Attributes = array () )  { echo self::ul        ( $Content, $Attributes ) . "\n";    }
    public static function showLI        ( $Content = ' ', $Attributes = array () )  { echo self::li        ( $Content, $Attributes ) . "\n";    }
    public static function showTable     ( $Content = ' ', $Attributes = array () )  { echo self::table     ( $Content, $Attributes ) . "\n";    }
    public static function showTR        ( $Content = ' ', $Attributes = array () )  { echo self::tr        ( $Content, $Attributes ) . "\n";    }
    public static function showTH        ( $Content = ' ', $Attributes = array () )  { echo self::th        ( $Content, $Attributes ) . "\n";    }
    public static function showTD        ( $Content = ' ', $Attributes = array () )  { echo self::td        ( $Content, $Attributes ) . "\n";    }
    public static function showLink ( $URL, $Label = '', $NewPage = false, $Attributes = array () )  { echo self::link ( $URL, $Label, $NewPage, $Attributes ) . "\n";    }

    
    
    public static function image  ( $ImageURL, $Attributes = array () )
    { 
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'src' ] = $ImageURL;
        return self::getTag ( 'img', '', $InnerAttributes ); 
    }
    
    public static function showImage ( $ImageURL, $Attributes = array () )  { echo self::image ( $ImageURL, $Attributes ) . "\n";    }
    
    public static function select ( $Name, $Values = array (), $Selected = '', $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'name' ] = $Name;
        
        $Options = ' ';
        foreach ( $Values as $Value => $Label )
        {
            $OptAttr = array ();
            
            if ( $Value == $Selected ) $OptAttr [ 'selected' ] = 'true';
            
            $OptAttr [ 'value' ] = $Value;
            $Options .= self::getTag ( 'option', $Label, $OptAttr ) . "\n";
        }
        
        return self::getTag ( 'select', $Options, $InnerAttributes );
    }
    
    public static function showSelect ( $Name, $Values, $Selected = '', $Attributes = array () )
    {
        echo self::select ( $Name, $Values, $Selected, $Attributes ) . "\n";
    }

    public static function radios ( $Name, $Values = array (), $Selected = '', $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'type' ] = 'radio';
        if ( ! isset ( $InnerAttributes [ 'id' ] ) ) $InnerAttributes [ 'id' ] = $Name;
        $InnerAttributes [ 'name' ] = $Name;
        
        $Radios = '';
        foreach ( $Values as $Name => $Value )
        {
            $RadioAttr = $InnerAttributes;
            $RadioAttr [ 'value' ] = $Value;
            $RadioAttr [ 'id' ] .= '_' . $Value;
            $Label = $Value;
            
            if ( is_string ( $Name ) ) $Label = $Name;
            if ( $Label == $Selected ) $RadioAttr [ 'checked' ] = 'true';
            
            $Radios .= self::getTag ( 'input', '', $RadioAttr ) . "\n";
            $Radios .= self::getTag ( 'label', $Label, array ( 'for' => $RadioAttr [ 'id' ] ) ) . "\n";
        }
        
        return $Radios;
    }
    
    public static function showRadios ( $Name, $Values, $Selected = '', $Attributes = array () )
    {
        echo self::radios ( $Name, $Values, $Selected, $Attributes ) . "\n";
    }

    public static function checkbox ( $Name, $Value, $Label = '', $Checked = false, $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        if ( ! isset ( $InnerAttributes [ 'id' ] ) ) $InnerAttributes [ 'id' ] = $Name;
        if ( $Checked ) $InnerAttributes [ 'checked' ] = 'true';

        $Text = '';
        $Text .= self::input ( $Name, 'checkbox', $Value, $InnerAttributes ) . "\n";
        if ( $Label != '' ) 
        {
            $Text .= self::getTag ( 'label', $Label, array ( 'for' => $InnerAttributes [ 'id' ] ) ) . "\n";
        }

        return $Text;
    }
    
    public static function showCheckbox ( $Name, $Value, $Label = '', $Checked = false, $Attributes = array () )
    {
        echo self::checkbox ( $Name, $Value, $Checked, $Attributes ) . "\n";
    }

    public static function submit ( $Name, $Label, $Attributes = array () )
    {
        return self::input ( $Name, 'submit', $Label, $Attributes );
    }
    
    public static function showSubmit ( $Name, $Label, $Attributes = array () )
    {
        echo self::submit ( $Name, $Label, $Attributes ) . "\n";
    }

    public static function inputDate ( $Name, $Value, $Placeholder = '', $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'placeholder' ] = $Placeholder;
        
        return self::input ( $Name, 'date', $Value, $InnerAttributes );
    }
    
    public static function showInputDate ( $Name, $Value, $Placeholder = '', $Attributes = array () )
    {
        echo self::inputDate ( $Name, $Value, $Min, $Max, $Placeholder, $Attributes ) . "\n";
    }
    
    public static function inputNum ( $Name, $Value, $Min = 1, $Max = 7, $Placeholder = '', $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'min' ] = $Min;
        $InnerAttributes [ 'max' ] = $Max;
        $InnerAttributes [ 'placeholder' ] = $Placeholder;
        
        return self::input ( $Name, 'number', $Value, $InnerAttributes );
    }
    
    public static function showInputNum ( $Name, $Value, $Min = 1, $Max = 7, $Placeholder = '', $Attributes = array () )
    {
        echo self::inputNum ( $Name, $Value, $Min, $Max, $Placeholder, $Attributes ) . "\n";
    }

    public static function inputText ( $Name, $Value, $Placeholder = '', $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'placeholder' ] = $Placeholder;
        
        return self::input ( $Name, 'text', $Value, $InnerAttributes );
    }
    
    public static function showInputText ( $Name, $Value, $Placeholder = '', $Attributes = array () )
    {
        echo self::inputText ( $Name, $Value, $Placeholder, $Attributes ) . "\n";
    }

    public static function inputFile ( $Name, $Placeholder = '', $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'placeholder' ] = $Placeholder;
        
        return self::input ( $Name, 'file', '', $InnerAttributes );
    }
    
    public static function showInputFile ( $Name, $Placeholder = '', $Attributes = array () )
    {
        echo self::inputFile ( $Name, $Placeholder, $Attributes ) . "\n";
    }
    
    public static function inputPassword ( $Name, $Value, $Placeholder = '', $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'placeholder' ] = $Placeholder;
        
        return self::input ( $Name, 'password', $Value, $InnerAttributes );
    }
    
    public static function showInputPassword ( $Name, $Value, $Placeholder = '', $Attributes = array () )
    {
        echo self::inputText ( $Name, $Value, $Placeholder, $Attributes ) . "\n";
    }
    
    public static function inputHidden ( $Name, $Value, $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        
        return self::input ( $Name, 'hidden', $Value, $InnerAttributes );
    }
    
    public static function showInputHidden ( $Name, $Value, $Attributes = array () )
    {
        echo self::inputHidden ( $Name, $Value, $Attributes ) . "\n";
    }

    public static function input ( $Name, $Type, $Value, $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'name' ] = $Name;
        $InnerAttributes [ 'type' ] = $Type;
        $InnerAttributes [ 'value' ] = $Value;
        
        return self::getTag ( 'input', '', $InnerAttributes );
    }
    
    public static function showInput ( $Name, $Type, $Value, $Attributes = array () )
    {
        echo self::input ( $Name, $Type, $Value, $Attributes ) . "\n";
    }
    
    public static function textarea ( $Name, $Value, $Rows, $Cols, $Attributes = array () )
    {
        $InnerAttributes = $Attributes;
        $InnerAttributes [ 'name' ] = $Name;
        $InnerAttributes [ 'rows' ] = $Rows;
        $InnerAttributes [ 'cols' ] = $Cols;
        
        return self::getTag ( 'textarea', $Value, $InnerAttributes );
    }
    
    public static function showTextarea ( $Name, $Value, $Rows, $Cols, $Attributes = array () )
    {
        echo self::textarea ( $Name, $Value, $Rows, $Cols, $Attributes ) . "\n";
    }
    
    public static function menu ( $Liste, $Selected = '', $Attributes = array (), $Depth = 1 )
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
    
    public static function showVar  ( $Name, $Var )
    {
        if ( is_string ( $Var ) ) self::tag ( 'pre', "$Name = $Var" );
        else  self::tag ( 'pre', "$Name = " . print_r ( $Var, TRUE ) );
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
    
    public static function tableFull ( $Array, $Attributes = array (), $ShowID = FALSE )
    {
        $TableContent = '';
        $TableContent .= self::startTR ();
        
        foreach ( $Array as $RowHeader => $Row )
        {
            if ( $ShowID )
            {
                $TableContent .= self::td ( strval ( $RowHeader ) );
            }
            if ( is_array ( $Row ) )
            {
                $TRContent = '';
                foreach ( $Row as $ColHeader => $Cell )
                {
                    $TRContent .= self::td ( $Cell );
                }
                $TableContent .= $TRContent;
                $TableContent .= self::endTR ();
                $TableContent .= self::startTR ();
            }
            else
            {
                $TableContent .= self::td ( $Row );
            }
        }
        
        $TableContent .= self::endTR ();
        
        
        
        return self::table ( $TableContent, $Attributes );
    }
    
    public static function showTableFull ( $Array, $Attributes = array () )
    {
        echo self::tableFull ( $Array, $Attributes ) . "\n";
    }
        
}
