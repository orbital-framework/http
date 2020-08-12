<?php

namespace Orbital\Http;

abstract class View {

    /**
     * View data
     * @var array
     */
    private static $data = array();

    /**
     * View level data
     * @var integer
     */
    private static $level = -1;

    /**
     * Define global variable ($view) from view level data
     * Requires view file if need
     * @return void
     */
    public static function process($file = NULL){

        $exists = isset( self::$data[ self::$level ] );

        if( $exists ){

            global $view;
            $view = self::$data[ self::$level ];

        }

        if( $file ){
            require $file;
        }

    }

    /**
     * Retrieve view
     * @param string $file
     * @param mixed $data
     * @return string
     */
    public static function get($file, $data = NULL){

        if( $data ){
            self::$level = self::$level + 1;
            self::$data[ self::$level ] = $data;
        }

        $file = SRC. "{$file}.php";

        // Parse view
        ob_start();

            self::process($file);

            if( $data ){

                unset(self::$data[ self::$level ]);
                self::$level = self::$level - 1;
                self::process(NULL);

            }

            $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }

    /**
     * Render view
     * @param string|array $file
     * @param mixed $data
     * @return void
     */
    public static function render($file, $data = NULL){

        if( is_array($file) ){

            foreach( $file as $item ){
                self::render($item, $data);
            }

            return;
        }

        echo self::get($file, $data);
    }

}