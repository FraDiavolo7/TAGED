<?php

trait Connection
{
    protected static $RES_NO_CONNEC = 0;
    protected static $RES_SUCCESS = 1;
    protected static $RES_ERROR = 2;
    
    protected static $BUTTON_SUBMIT_NAME   = 'signin';
    protected static $BUTTON_SUBMIT_LABEL  = 'Connexion';
    protected static $BUTTON_SIGN_UP_NAME  = 'signup';
    protected static $BUTTON_SIGN_UP_LABEL = 'Inscription';
    protected static $INPUT_LOGIN_NAME     = 'login';
    protected static $INPUT_LOGIN_LABEL    = 'Identifiant';
    protected static $INPUT_PASSW_NAME     = 'passw';
    protected static $INPUT_PASSW_LABEL    = 'Mot de Passe';
    
    protected function connectionForm ( $Data = array (), $Attributes = array () )
    {
        $InputData= $Data;
        if ( empty ( $InputData ) )
        {
            $InputData = $_REQUEST;
        }
        
        $FormAttributes = $Attributes;
        if ( ! isset ( $FormAttributes [ 'method' ] ) ) $FormAttributes [ 'method' ] = 'POST';
        if ( ! isset ( $FormAttributes [ 'id' ] ) ) $FormAttributes [ 'id' ] = 'connection_form';

        $Login    = Form::getData ( self::$INPUT_LOGIN_NAME,    '', $InputData );
        $Password = Form::getData ( self::$INPUT_PASSW_NAME,    '', $InputData );
        
        $Form = '';
        
        $Form .= HTML::div (
            HTML::submit ( self::$BUTTON_SUBMIT_NAME,  self::$BUTTON_SUBMIT_LABEL  ) .
            HTML::submit ( self::$BUTTON_SIGN_UP_NAME, self::$BUTTON_SIGN_UP_LABEL ),
            array ( 'class' => 'connection_buttons' )
            );
        
        $Form .= HTML::div (
            HTML::inputText     ( self::$INPUT_LOGIN_NAME, $Login, self::$INPUT_LOGIN_LABEL  ) .
            HTML::inputPassword ( self::$INPUT_PASSW_NAME, $Password, self::$INPUT_PASSW_LABEL ),
            array ( 'class' => 'connection_buttons' )
            );
        
        return HTML::form ( $Form, $FormAttributes );
    }
    
    protected function connectionHandle ( $Data = array () )
    {
        $InputData= $Data;
        if ( empty ( $InputData ) )
        {
            $InputData = $_REQUEST;
        }
        
        $Result = self::$RES_NO_CONNEC;
        
        $Submit   = Form::getData ( self::$BUTTON_SUBMIT_NAME,  '', $InputData );
        $SignUp   = Form::getData ( self::$BUTTON_SIGN_UP_NAME, '', $InputData );
        $Login    = Form::getData ( self::$INPUT_LOGIN_NAME,    '', $InputData );
        $Password = Form::getData ( self::$INPUT_PASSW_NAME,    '', $InputData );
        
        if ( $Submit != '' )
        {
            if ( $this->Feteur != NULL )
            {
                unset ( $this->Feteur );
                $this->Feteur = NULL;
            }
            $this->Feteur = new Feteur ( );
            $ConnectionSuccess = $this->Feteur->loadLogin ( $Login, $Password );
            $Result = ( $ConnectionSuccess ? self::$RES_SUCCESS : self::$RES_ERROR);
        }
        else if ( $SignUp != '' )
        {
            
        }
            
        return $Result;
    }
    
    protected function connectionSuccess ( $Message )
    {
        return HTML::div ( $Message, array ( 'class' => 'connection_success' ) );
    }
    
    protected function connectionError ( $Error, $Message )
    {
        return HTML::div ( "$Error - $Message", array ( 'class' => 'connection_error' ) );
    }
    
    protected $Feteur = NULL;
} // Connection
