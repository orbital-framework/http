<?php
declare(strict_types=1);

namespace Orbital\Http;

abstract class Session {

    /**
     * Session id
     * @var string
     */
    public static $id = null;

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
    public static function init(bool $overload = true): void {

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
    public static function id(): string {

        if( !self::$id ){
            self::$id = session_id();
        }

        return self::$id;
    }

    /**
     * Regenerate session id
     * @return string
     */
    public static function regenerate(): string {

        session_regenerate_id(false);
        self::$id = null;

        return self::id();
    }

    /**
     * Set session data
     * @param string|array $key
     * @param string $value
     * @return void
     */
    public static function set(string|array $key, string $value = null): void {

        if( is_array($key) AND is_null($value) ){
            self::$data = array_merge(self::$data, $key);
        }else{
            self::$data[ $key ] = $value;
        }

    }

    /**
     * Retrieve session data
     * @param string $key
     * @return mixed
     */
    public static function get(string $key = null): mixed {

        if( $key ){
            return ( array_key_exists($key, self::$data) )
                ? self::$data[ $key ] : null;
        }

        return self::$data;
    }

    /**
     * Remove session data
     * @param string $key
     * @return void
     */
    public static function delete(string $key): void {

        if( isset(self::$data[ $key ]) ){
            unset(self::$data[ $key ]);
        }

    }

    /**
     * Destroy session
     * @return boolean
     */
    public static function destroy(): bool {
        self::$id = null;
        return session_destroy();
    }

}