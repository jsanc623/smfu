<?php

$Rest = new REST;

$time = $Rest->Request("http://www.smfu.in/api/getTime");
echo $time['time'];

class REST{
    function Request($url, $params = null, $verb = 'GET', $format = 'json'){
        $cparams = array(
            'http' => array(
                'method' => $verb,
                'ignore_errors' => true
            )
        );
        
        if ($params !== null) {
            $params = http_build_query($params);
            if ($verb == 'POST') {
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