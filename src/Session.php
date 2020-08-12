<?php

namespace Orbital\Http;

abstract class Session {

    /**
     * Session id
     * @var string
     */
    public static $id = NULL;

    /**
     * Session data
     * @var array
     */
    private static $data = array();

    /**
     * Init
     * @param boolean $overload
     * @return void
     */
    public static function init($overload = TRUE){

        if( !session_id() ){
            session_start();
        }

        if( $overload ){
            self::$data =& $_SESSION;
        }

        self::id();

    }

    /**
     * Return session ID
     * @return string
     */
    public static function id(){

        if( !self::$id ){
            self::$id = session_id();
        }

        return self::$id;
    }

    /**
     * Regenerate session id
     * @return string
     */
    public static function regenerate(){

        session_regenerate_id(FALSE);
        self::$id = NULL;

        return self::id();
    }

    /**
     * Set session data
     * @param string|array $key
     * @param string|NULL $value
     * @return void
     */
    public static function set($key, $value = NULL){

        if( is_array($key) AND is_null($value) ){
            self::$data = array_merge(self::$data, $key);
        }else{
            self::$data[ $key ] = $value;
        }

    }

    /**
     * Retrieve session data
     * @param mixed $key
     * @return mixed
     */
    public static function get($key = NULL){

        if( $key ){
            return ( array_key_exists($key, self::$data) )
                ? self::$data[ $key ] : NULL;
        }

        return self::$data;
    }

    /**
     * Remove session data
     * @param string $key
     * @return void
     */
    public static function delete($key){

        if( isset(self::$data[ $key ]) ){
            unset(self::$data[ $key ]);
        }

    }

    /**
     * Destroy session
     * @return boolean
     */
    public static function destroy(){
        self::$id = NULL;
        return session_destroy();
    }

}