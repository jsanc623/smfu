<?php
/**
 * SMFU
 *
 * An open source application development framework for PHP 5.3 or newer
 *
 * NOTICE OF LICENSE
 *
 * The MIT License (MIT)
 * 
 * Copyright (c) 2013 Juan L. Sanchez, SMFU &| SMFU.in
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     SMFUShorten
 * @author      Juan L. Sanchez
 * @copyright   Copyright (c) 2013, Juan L. Sanchez. (http://juanleonardosanchez.com.com/)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link        http://smfu.in
 * @since       Version 1.0
 * @filesource
 */

$ROOT = "http://smfu.in";
define("SMFUgetTimeUrl",       $ROOT."/api/getTime");
define("SMFUgenMarkerUrl",     $ROOT."/api/genMarker");
define("SMFUdockRequestReady", $ROOT."/api/dockRequestReady");
define("SMFUshortenUrl",       $ROOT."/api/shorten");

class SMFU{
    public  $url;
    public  $shortUrl;
    private $apiKey;
    private $encryptedTime; 
    private $userMarker;
    private $requestMarker;
    private $fullURLOnly;
    
    public function __construct($url, $apiKey = "", $fullURLOnly = FALSE){
        if(empty($apiKey)){
            throw new RuntimeException("API Key Missing.");
        } else {
            $this->apiKey = $apiKey;
        }
        
        if(empty($url)){
            throw new RuntimeException("URL Missing.");
        } else {
            $this->url = $url;
        }
        
        if($fullURLOnly == TRUE){
            $this->fullURLOnly = TRUE;
        }
        
        $this->shortUrl = $this->getTime()->genMarker()->dockRequestReady()->shortenUrl();
        $this->shortUrl['cabal'] = "http://smfu.in/" . $this->shortUrl['urlkey'];
    }
    
    public function get(){
        if(isset($this->shortUrl) && gettype($this->shortUrl) == "array"){
            if($this->fullURLOnly == TRUE){
                return $this->shortUrl['cabal'];
            }
            return $this->shortUrl;
        }
    }
    
    private function shortenUrl(){
        $data = array("time" => $this->encryptedTime, "url" => $this->url, 
                      "userMarker" => $this->userMarker, "key" => $this->apiKey, 
                      "requestMarker" => $this->requestMarker);
        $short = $this->Request(SMFUshortenUrl, $data, "POST");
        return (array)$short[0];
    }
    
    private function dockRequestReady(){
        $data = array("key" => $this->apiKey, "time" => $this->encryptedTime, "marker" => $this->userMarker);
        $dock = $this->Request(SMFUdockRequestReady, $data, "POST");
        $this->userMarker = $dock['userMarker'];
        $this->requestMarker = $dock['requestMarker'];
        return $this;
    }
    
    private function genMarker(){
        $marker = $this->Request(SMFUgenMarkerUrl);
        $this->userMarker = $marker['userMarker'];
        return $this;
    }
    
    private function getTime(){
        $time = $this->Request(SMFUgetTimeUrl);
        $this->encryptedTime = $time['time'];
        return $this;
    }
    
    
    /**
     * "Borrowed" from http://wezfurlong.org/blog/2006/nov/http-post-from-php-without-curl/
     * Full credit for code below goes to Wez Furlong
     */
    private function Request($url, $params = null, $verb = 'GET', $format = 'json'){
        $cparams = array(
            'http' => array(
                'method' => $verb,
                'ignore_errors' => true
            )
        );

        if ($params !== null) {
            $params = http_build_query($params);
            if ($verb == 'POST') {
                $cparams['http']['header'] = "Content-type: application/x-www-form-urlencoded\r\n";
                $cparams['http']['content'] = $params;
            } else {
                $url .= '?' . $params;
            }
        }
        
        $context = stream_context_create($cparams);
        $fp = fopen($url, 'rb', false, $context);

        if (!$fp) {
            $res = false;
        } else {
            # $meta = stream_get_meta_data($fp);
            # var_dump($meta['wrapper_data']);
            $res = stream_get_contents($fp);
        }
        
        if ($res === false) {
            throw new Exception("$verb $url failed: $php_errormsg");
        }
        
        switch ($format) {
            case 'json':
                $r = json_decode($res);
                if ($r === null) {
                    throw new Exception("Failed to decode $res as json.");
                }
                return (array)$r;
            break;

            case 'xml':
                $r = simplexml_load_string($res);
                    if ($r === null) {
                        throw new Exception("Failed to decode $res as xml.");
                    }
                return $r;
            break;
        }
    
        return $res;
    }
}

$SMFU = new SMFU("http://facebook.com/", "a449b5dd070a10be0fbffbef5cb2484888ac49e656272498833e3df3e92d86f7");
$response = $SMFU->get();

echo "<pre>";
print_r($response);