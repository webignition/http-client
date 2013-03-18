<?php

namespace webignition\Http\Client;

class CurlException extends \Exception {
    
    const MALFORMED_URL_CODE = 3;
    const COULDNT_RESOLVE_HOST_CODE = 6;
    const OPERATION_TIMEDOUT = 28;    
    
    /**
     * Collection of HTTP-relevant cURL codes
     * 
     * Most commonly-occurring codes in error cases:
     *  3: invalid URL format
     *  6: could not resolve host
     *  28: timeout, either in connecting or waiting for the complete response
     * 
     * @var array
     */
    private $curlCodes = array(
        1 => "The URL you passed to libcurl used a protocol that this libcurl does not support. The support might be a compile-time option that you didn't use, it can be a misspelled protocol string or just a protocol libcurl has no code for.",
        2 => "Very early initialization code failed. This is likely to be an internal error or problem, or a resource problem where something fundamental couldn't get done at init time.",
        3 => "The URL was not properly formatted.",
        4 => "A requested feature, protocol or option was not found built-in in this libcurl due to a build-time decision. This means that a feature or option was not enabled or explicitly disabled when libcurl was built and in order to get it to function you have to get a rebuilt libcurl.",
        5 => "Couldn't resolve proxy. The given proxy host could not be resolve",
        6 => "Couldn't resolve host. The given remote host was not resolved.",
        7 => "Failed to connect() to host or proxy.",
        9 => "We were denied access to the resource given in the URL. For FTP, this occurs while trying to change to the remote directory.",
        22 => "This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is >= 400.",
        23 => "An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback.",
        26 => "There was a problem reading a local file or an error returned by the read callback.",
        27 => "A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs.",
        28 => "Operation timeout. The specified time-out period was reached according to the conditions.",
        33 => "The server does not support or accept range requests.",
        34 => "CURLE_HTTP_POST_ERROR: This is an odd error that mainly occurs due to internal confusion.",
        35 => "CURLE_SSL_CONNECT_ERROR: A problem occurred somewhere in the SSL/TLS handshake. You really want the error buffer and read the message there as it pinpoints the problem slightly more. Could be certificates (file formats, paths, permissions), passwords, and others.",
        36 => "The download could not be resumed because the specified offset was out of the file boundary.",
        37 => "A file given with FILE:// couldn't be opened. Most likely because the file path doesn't identify an existing file. Did you check file permissions?",
        47 => "Too many redirects. When following redirects, libcurl hit the maximum amount. Set your limit with CURLOPT_MAXREDIRS.",
        51 => "The remote server's SSL certificate or SSH md5 fingerprint was deemed not OK.",
        53 => "CURLE_SSL_ENGINE_NOTFOUND: The specified crypto engine wasn't found.",
        54 => "CURLE_SSL_ENGINE_SETFAILED: Failed setting the selected SSL crypto engine as default!",
        55 => "Failed sending network data.",
        56 => "Failure with receiving network data",
        58 => "problem with the local client certificate.",
        59 => "Couldn't use specified cipher.",
        60 => "Peer certificate cannot be authenticated with known CA certificates.",
        61 => "Unrecognized transfer encoding.",
        63 => "Maximum file size exceeded.",
        65 => "When doing a send operation curl had to rewind the data to retransmit, but the rewinding operation failed.",
        66 => "Initiating the SSL Engine failed.",
        67 => "The remote server denied curl to login (Added in 7.13.1)",
        75 => "Character conversion failed",
        76 => "Caller must register conversion callbacks.",
        77 => "Problem with reading the SSL CA cert (path? access rights?)",
        78 => "The resource referenced in the URL does not exist.",
        83 => "Issuer check failed (Added in 7.19.0)",
        88 => "Chunk callback reported error."
    ); 
    
    
    /**
     *
     * @param \Exception $previousException 
     */
    public function __construct(\Exception $previousException) {       
        parent::__construct(
                $this->curlCodes[$previousException->curlCode],
                $previousException->curlCode,                
                $previousException
        );     
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isInvalidUrlException() {
        return $this->getCode() == self::MALFORMED_URL_CODE;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isTimeoutException() {
        return $this->getCode() == self::OPERATION_TIMEDOUT;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isDnsLookupFailureException() {
        return $this->getCode() == self::COULDNT_RESOLVE_HOST_CODE;
    }    
    
}