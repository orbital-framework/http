<?php
declare(strict_types=1);

namespace Orbital\Http;

use \Exception;
use \Orbital\Framework\App;
use \Orbital\Framework\Request;

abstract class Router {

    /**
     * Path for routers
     * @var string
     */
    public static $path = '/';

    /**
     * Router URL
     * @var string
     */
    public static $url = null;

    /**
     * Router Query
     * @var string
     */
    public static $query = null;

    /**
     * Active route
     * @var array
     */
    public static $route = array();

    /**
     * "Routers" for errors - 404, 401...
     * @var array
     */
    public static $errors = array();

    /**
     * Requests routers
     * HTTP / WebDAV methods
     * @var array
     */
    private static $routers = array(
        # HEAD === GET
        'GET' => array(),
        'POST' => array(),
        'PUT' => array(),
        'DELETE' => array(),
        'CONNECT' => array(),
        'OPTIONS' => array(),
        'TRACE' => array(),
        'COPY' => array(),
        'LOCK' => array(),
        'MKCOL' => array(),
        'MOVE' => array(),
        'PROPFIND' => array(),
        'PROPPATCH' => array(),
        'UNLOCK' => array(),
        'REPORT' => array(),
        'MKACTIVITY' => array(),
        'CHECKOUT' => array(),
        'MERGE' => array()
    );

    /**
     * Retrieve router active URL
     * @return string
     */
    public static function getActiveUrl(): string {

        if( is_null(self::$url) ){
            self::processUrl();
        }

        return self::$url;
    }

    /**
     * Retrieve router active query
     * @return string
     */
    public static function getActiveQuery(): string {

        if( is_null(self::$query) ){
            self::processUrl();
        }

        return self::$query;
    }

    /**
     * Retrieve active route
     * @return array
     */
    public static function getActiveRoute(): array {

        if( !self::$route ){

            $method = Request::method();
            $query = self::getActiveQuery();
            $route = self::getRoute($query, $method);

            self::$route = $route;

        }

        return self::$route;
    }

    /**
     * Retrieve route from $uri and $method
     * @param string $uri
     * @param string $method
     * @return array
     */
    public static function getRoute(string $uri, string $method): array {

        $route = array();

        if( !array_key_exists($method, self::$routers) ){
            return $route;
        }

        $routers = self::$routers[ $method ];

        foreach( $routers as $router ){

            $pattern = $router['rule'];
            $pattern = preg_replace('/\(:([a-zA-Z0-9]+)\)/', '([a-z0-9-_]+)', $pattern);
            $pattern = '/^'. str_replace('/', '\/', $pattern). '$/i';

            if( preg_match($pattern, $uri, $matches)
                OR $router['rule'] === $uri ){

                $rule = $router['rule'];
                $callback = $router['callback'];
                $parameters = array();
                $options = array();

                if( count($matches) > 1 ){
                    foreach( $matches as $key => $value ){
                        if( $key === 0 ){
                            continue;
                        }
                        $parameters[] = $value;
                    }
                }

                if( !$parameters ){
                    $parameters = array();
                }

                if( is_array($router['options']) ){
                    $options = $router['options'];
                }

                $route = array(
                    'method' => $method,
                    'rule' => $rule,
                    'callback' => $callback,
                    'parameters' => $parameters,
                    'options' => $options
                );

                break;
            }

        }

        return $route;
    }

    /**
     * Process request and run callback
     * @return void
     */
    public static function runRequest(): void {

        $route = self::getActiveRoute();

        if( !$route ){
            self::runError(404, null, $route);
            return;
        }

        $options = $route['options'];

        if( $options
            AND isset($options['contentType'])
            AND !is_null($options['contentType']) ){
            Header::contentType($options['contentType']);
        }

        if( $options
            AND isset($options['status'])
            AND !is_null($options['status']) ){
            Header::status($options['status']);
        }

        Header::send();

        try{
            App::runMethod($route['callback'], $route['parameters']);
        } catch( Exception $e ) {
            self::runError(500, $e, $route);
        }

    }

    /**
     * Force error on request
     * @param int $number
     * @param Exception $exception
     * @param array $last
     * @return void
     */
    public static function runError(int $number = 404, Exception $exception = null, array $last = null): void {

        if( !isset(self::$errors[$number])
            OR ($last AND $last['rule'] === $number) ){

            if( $exception instanceof Exception ){
                throw $exception;
            }else{
                die('Router error '. $number);
            }

        }

        // Set new route and try again
        $method = ($last) ? $last['method'] : Request::method();
        $callback = self::$errors[ $number ]['callback'];

        self::$route = array(
            'method' => $method,
            'rule' => $number,
            'callback' => $callback,
            'parameters' => array($exception),
            'options' => array('status' => $number)
        );

        self::runRequest();

    }

    /**
     * Process request URL
     * @return void
     */
    private static function processUrl(): void {

        $url = str_replace('/index.php', '', $_SERVER['PHP_SELF']);

        if( isset($_SERVER['REQUEST_URI']) ){
            $url = str_replace($url, '', $_SERVER['REQUEST_URI']);
        }

        $query = explode('?', $url);
        $query = $query[0];
        $query = ($query !== '/') ? rtrim($query,'/') : $query;

        self::$url = $url;
        self::$query = strtolower($query);

    }

    /**
     * Set routers to APP
     * @param string|array $httpMethod
     * @param string $rule
     * @param string $callback
     * @param array $options
     * @return void
     */
    public static function set(string|array $httpMethod, string $rule, string $callback, array $options = array()): void {

        if( is_array($httpMethod) ){

            foreach( $httpMethod as $new ){
                self::set(
                    $new,
                    $rule,
                    $callback,
                    $options
                );
            }

            return;
        }

        $path = self::getPath(). trim($rule, '/');
        $path = str_replace('//', '/', $path);

        if( $path != '/' ){
            $path = rtrim($path, '/');
        }

        $router = array(
            'rule' => $path,
            'callback' => $callback,
            'options' => $options
        );

        self::$routers[ $httpMethod ][ $path ] = $router;

    }

    /**
     * Set error callback when router goes wrong
     * @param string $number
     * @param string $callback
     * @return void
     */
    public static function setError(string $number, string $callback): void {

        self::$errors[$number] = array(
            'callback' => $callback
        );

    }

    /**
     * Set path prefix to routers
     * @param string $path
     * @return void
     */
    public static function setPath(string $path): void {

        $path = '/'. trim($path, '/'). '/';
        $path = str_replace('//', '/', $path);

        self::$path = $path;
    }

    /**
     * Retrieve path prefix to routers
     * @return string
     */
    public static function getPath(): string {
        return self::$path;
    }

    /**
     * Create valid URI
     * @param string $string
     * @return string
     */
    public static function createUri(string $string): string {

        $string = strtolower($string);

        $accents = array(
            'á', 'à', 'â', 'ã',
            'é', 'è', 'ê',
            'í', 'ì', 'î',
            'ó', 'ò', 'ô', 'õ',
            'ú', 'ù', 'û', 'ç'
        );

        $nonAccents = array(
            'a', 'a', 'a', 'a',
            'e', 'e', 'e',
            'i', 'i', 'i',
            'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'c'
        );

        $string = str_replace($accents, $nonAccents, $string);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);
        $string = trim($string, '-');

        return $string;
    }

    /**
     * Create and format URL
     * @param string $url
     * @param string $location
     * @param string $query
     * @return string
     */
    public static function createUrl(string $url, string $location = '', string $query = null): string {

        $url = trim($url, '/');

        if( !empty($location) ){
            $url .= '/'. $location;
        }

        if( !empty($query) ){
            $url .= '/?'. str_replace('?', '', $query);
        }

        $url = preg_replace('/((?<!:)\/{2,4}\/?)/', '/', $url);

        return $url;
    }

    /**
     * Retrieve URL
     * @param string $location
     * @param string $query
     * @param boolean $ignorePath
     * @return string
     */
    public static function getUrl(string $location = '', string $query = null, bool $ignorePath = true): string {

        $url = App::get('url');

        if( !$ignorePath
            AND self::getPath() ){
            $url .= '/'. trim(self::getPath(), '/');
        }

        return self::createUrl($url, $location, $query);
    }

    /**
     * Print URL
     * @param string $location
     * @param string $query
     * @param boolean $ignorePath
     * @return void
     */
    public static function url(string $location = '', string $query = null, bool $ignorePath = true): void {
        echo self::getUrl($location, $query, $ignorePath);
    }

    /**
     * Retrieve Path URL
     * @param string $location
     * @param string $query
     * @return string
     */
    public static function getPathUrl(string $location = '', string $query = null): string {
        return self::getUrl($location, $query, false);
    }

    /**
     * Print Path URL
     * @param string $location
     * @param string $query
     * @return void
     */
    public static function pathUrl(string $location = '', string $query = null): void {
        echo self::getPathUrl($location, $query, false);
    }

    /**
     * Retrieve current URL
     * @param boolean $useQuery
     * @return string
     */
    public static function getCurrentUrl(bool $useQuery = false): string {

        $query = null;
        $location = ( $useQuery )
            ? self::getActiveQuery() : self::getActiveUrl();

        return self::getUrl($location, $query, true);
    }

    /**
     * Print Current URL
     * @param boolean $useQuery
     * @return void
     */
    public static function currentUrl(bool $useQuery = false): void {
        echo self::getCurrentUrl($useQuery);
    }

}