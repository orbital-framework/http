<?php
declare(strict_types=1);

namespace Orbital\Http;

abstract class Header {

    /**
     * HTTP Status
     * @var array
     */
    protected static $status = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        511 => 'Network Authentication Required'
    );

    /**
     * HTTP Content-Types
     * @var array
     */
    protected static $types = array(
        'hqx'   => 'application/mac-binhex40',
        'cpt'   => 'application/mac-compactpro',
        'csv'   => 'text/x-comma-separated-values',
        'bin'   => 'application/macbinary',
        'dms'   => 'application/octet-stream',
        'lha'   => 'application/octet-stream',
        'lzh'   => 'application/octet-stream',
        'exe'   => 'application/x-msdownload',
        'class' => 'application/octet-stream',
        'psd'   => 'application/x-photoshop',
        'so'    => 'application/octet-stream',
        'sea'   => 'application/octet-stream',
        'dll'   => 'application/octet-stream',
        'oda'   => 'application/oda',
        'pdf'   => 'application/pdf',
        'ai'    => 'application/postscript',
        'eps'   => 'application/postscript',
        'ps'    => 'application/postscript',
        'smi'   => 'application/smil',
        'smil'  => 'application/smil',
        'mif'   => 'application/vnd.mif',
        'xls'   => 'application/excel',
        'ppt'   => 'application/powerpoint',
        'wbxml' => 'application/wbxml',
        'wmlc'  => 'application/wmlc',
        'dcr'   => 'application/x-director',
        'dir'   => 'application/x-director',
        'dxr'   => 'application/x-director',
        'dvi'   => 'application/x-dvi',
        'gtar'  => 'application/x-gtar',
        'gz'    => 'application/x-gzip',
        'php'   => 'application/x-httpd-php',
        'php4'  => 'application/x-httpd-php',
        'php3'  => 'application/x-httpd-php',
        'phtml' => 'application/x-httpd-php',
        'phps'  => 'application/x-httpd-php-source',
        'js'    => 'application/x-javascript',
        'swf'   => 'application/x-shockwave-flash',
        'sit'   => 'application/x-stuffit',
        'tar'   => 'application/x-tar',
        'tgz'   => 'application/x-tar',
        'xhtml' => 'application/xhtml+xml',
        'xht'   => 'application/xhtml+xml',
        'zip'   => 'application/x-zip',
        'mid'   => 'audio/midi',
        'midi'  => 'audio/midi',
        'mpga'  => 'audio/mpeg',
        'mp2'   => 'audio/mpeg',
        'mp3'   => 'audio/mpeg',
        'aif'   => 'audio/x-aiff',
        'aiff'  => 'audio/x-aiff',
        'aifc'  => 'audio/x-aiff',
        'ram'   => 'audio/x-pn-realaudio',
        'rm'    => 'audio/x-pn-realaudio',
        'rpm'   => 'audio/x-pn-realaudio-plugin',
        'ra'    => 'audio/x-realaudio',
        'rv'    => 'video/vnd.rn-realvideo',
        'wav'   => 'audio/x-wav',
        'bmp'   => 'image/bmp',
        'gif'   => 'image/gif',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'jpe'   => 'image/jpeg',
        'png'   => 'image/png',
        'tiff'  => 'image/tiff',
        'tif'   => 'image/tiff',
        'css'   => 'text/css',
        'html'  => 'text/html',
        'htm'   => 'text/html',
        'shtml' => 'text/html',
        'txt'   => 'text/plain',
        'text'  => 'text/plain',
        'log'   => 'text/plain',
        'rtx'   => 'text/richtext',
        'rtf'   => 'text/rtf',
        'xml'   => 'text/xml',
        'xsl'   => 'text/xml',
        'mpeg'  => 'video/mpeg',
        'mpg'   => 'video/mpeg',
        'mpe'   => 'video/mpeg',
        'qt'    => 'video/quicktime',
        'mov'   => 'video/quicktime',
        'avi'   => 'video/x-msvideo',
        'movie' => 'video/x-sgi-movie',
        'doc'   => 'application/msword',
        'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'word'  => 'application/msword',
        'xl'    => 'application/excel',
        'eml'   => 'message/rfc822',
        'json'  => 'application/json'
    );

    /**
     * HTTP Headers
     * @var array
     */
    private static $headers = array();

    /**
     * Register if Headers are already sent
     * @var boolean
     */
    private static $sent = false;

    /**
     * Send HTTP Headers
     * @param boolean $clean
     * @return void
     */
    public static function send(bool $clean = true): void {

        if( !headers_sent() ){

            foreach( self::$headers as $header ){

                if( is_array($header) ){
                    call_user_func_array('header', $header);
                }else{
                    header($header);
                }

            }

            if( $clean ){
                self::$headers = array();
            }

        }

        self::$sent = true;
    }

    /**
     * Retrieve if HTTP Headers are sent
     * @return boolean
     */
    public static function sent(): bool {
        return self::$sent;
    }

    /**
     * List HTTP Headers
     * @return array
     */
    public static function listHeaders(): array {
        return ( headers_sent() ) ? headers_list() : self::$headers;
    }

    /**
     * Set a new HTTP Header Data
     * @param string $header
     * @return void
     */
    public static function set(string $header): void {
        self::$headers[] = $header;
    }

    /**
     * Set HTTP Content-Type
     * @param string $type
     * @param string $charset
     * @return void
     */
    public static function contentType(string $type, string $charset = 'utf-8'): void {
        $type = isset( self::$types[$type] ) ? self::$types[$type] : $type;
        self::set('Content-Type: '. $type. '; charset='. $charset);
    }

    /**
     * Set HTTP Status
     * @param int $code
     * @return void
     */
    public static function status(int $code = 200): void {
        self::set('HTTP/1.1 '. $code. ' '. self::$status[$code]);
    }

    /**
     * Set and redirect HTTP Header Location
     * @see Header::set()
     * @see Header::status()
     * @param string $url
     * @param int $code
     * @return void
     */
    public static function location(string $url, int $code = 301): void {

        self::set('Location: '. $url);
        self::status($code);

        return self::send();
    }

}