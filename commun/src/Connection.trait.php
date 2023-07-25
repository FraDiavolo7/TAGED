<?php

/**
 * Trait représentant des fonctionnalités de connexion.
 *
 * @package Commun
 */
trait Connection
{
    /** @var int Constante représentant l'état de non connexion. */
    protected static $RES_NO_CONNEC = 0;

    /** @var int Constante représentant l'état de connexion réussie. */
    protected static $RES_SUCCESS = 1;

    /** @var int Constante représentant l'état d'erreur de connexion. */
    protected static $RES_ERROR = 2;

    /** @var string Nom du bouton de soumission du formulaire de connexion. */
    protected static $BUTTON_SUBMIT_NAME = 'signin';

    /** @var string Libellé du bouton de soumission du formulaire de connexion. */
    protected static $BUTTON_SUBMIT_LABEL = 'Connexion';

    /** @var string Nom du bouton d'inscription du formulaire de connexion. */
    protected static $BUTTON_SIGN_UP_NAME = 'signup';

    /** @var string Libellé du bouton d'inscription du formulaire de connexion. */
    protected static $BUTTON_SIGN_UP_LABEL = 'Inscription';

    /** @var string Nom du champ de saisie de l'identifiant dans le formulaire de connexion. */
    protected static $INPUT_LOGIN_NAME = 'login';

    /** @var string Libellé du champ de saisie de l'identifiant dans le formulaire de connexion. */
    protected static $INPUT_LOGIN_LABEL = 'Identifiant';

    /** @var string Nom du champ de saisie du mot de passe dans le formulaire de connexion. */
    protected static $INPUT_PASSW_NAME = 'passw';

    /** @var string Libellé du champ de saisie du mot de passe dans le formulaire de connexion. */
    protected static $INPUT_PASSW_LABEL = 'Mot de Passe';
    
    /**
     * Affiche le formulaire de connexion.
     *
     * @param array $Data (facultatif) Données pour pré-remplir le formulaire.
     * @param array $Attributes (facultatif) Attributs HTML supplémentaires pour le formulaire.
     * @return string Code HTML représentant le formulaire de connexion.
     */
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
        
        return HTML::div ( 
            HTML::div ( ' ', array ( 'class' => 'show_connection' ) ) .
            HTML::form ( $Form, $FormAttributes ),
            array ( 'class' => 'connection_area' )
            );
    }
    
    /**
     * Gère la soumission du formulaire de connexion.
     *
     * @param array $Data (facultatif) Données soumises par le formulaire.
     * @return int Résultat de la connexion (RES_SUCCESS en cas de réussite, RES_ERROR en cas d'erreur, RES_NO_CONNEC par défaut).
     */
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
    
    /**
     * Affiche un message de succès de connexion.
     *
     * @param string $Message Message à afficher pour la connexion réussie.
     * @return string Code HTML représentant le message de succès de connexion.
     */
    protected function connectionSuccess ( $Message )
    {
        return HTML::div ( $Message, array ( 'class' => 'connection_success' ) );
    }
    
    /**
     * Affiche un message d'erreur de connexion.
     *
     * @param string $Error Code d'erreur de connexion.
     * @param string $Message Message d'erreur à afficher.
     * @return string Code HTML représentant le message d'erreur de connexion.
     */
    protected function connectionError ( $Error, $Message )
    {
        return HTML::div ( "$Error - $Message", array ( 'class' => 'connection_error' ) );
    }
    
    /**
     * @var object|null Feteur utilisé pour la connexion.
     */
   protected $Feteur = NULL;
} // Connection
