<?php
declare(strict_types=1);

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
     * @param string $file
     * @return void
     */
    public static function process(string $file = null): void {

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
    public static function get(string $file, mixed $data = null): string {

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
                self::process(null);

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
    public static function render(string|array $file, mixed $data = null): void {

        if( is_array($file) ){

            foreach( $file as $item ){
                self::render($item, $data);
            }

            return;
        }

        echo self::get($file, $data);
    }

}