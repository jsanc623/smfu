
                <div class="col-md-12" id="registerForm">
                    <h1>SMFU API v1.0 Documentation</h1>
                    
                    <div class="clear"></div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3 class="panel-title">Getting Started</h3></div>
                        <div class="panel-body">
                            <p>First off, thanks for using the SMFU API.</p>
                            
                            <p>
                                Getting started couldn't be simpler! Just 
                                <a href="<?php echo base_url("/users/register"); ?>">register for an account</a>. 
                                Upon doing this, visit your profile page and take note of your <strong>key</strong>. You will
                                require this key whenever you make a request to our API.
                            </p>
                            
                            <p>The API will always return JSON. The development roadmap does not include plans for XML, 
                               however, we will revise this if enough requests are received.</p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3 class="panel-title">Looking Towards v2.0 & Libraries</h3></div>
                        <div class="panel-body">
                            <p>This API is rather cumbersome. For that I apologize profusely. <strike>As well, there are no libraries
                               in any language to support it.</strike> You can find libraries here: 
                               <a href="https://github.com/jsanc623/SMFULibraries" target="_blank">https://github.com/jsanc623/SMFULibraries</a>. 
                               Feel free to contribute to those libraries in your preferred language.</p>
                               
                            <p>Remember this is version 1 of this API as well as version 
                               1 of this new iteration of a website with little traffic. As we grow, we will write 
                               libraries and better the API so that you, the user, can have a better experience. </p>
                               
                            <p>I am of course very open to having libraries wrap our API, and if you do decide to 
                               write one, shoot me a link at <code>juan.sanchez@juanleonardosanchez.com</code> and I'll
                               make mention of it here on the website.</p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3 class="panel-title">Accepted Requests</h3></div>
                        <div class="panel-body">
                            <p>The v1.0 API accepts two types of requests: <em>GET</em> and <em>POST</em>. Each API 
                               endpoint wil make mention of what type of request it accepts. Considering the v1.0 API
                               is open and simple, there's little need to accept other requests (HEAD, PUT, etc). In the 
                               future, with an expansion of the service and the API, we will revisit these requirements.</p>
                               
                            <p>To determine what type of request an endpoint accepts, simply look for this section within each 
                                endpoint information panel.
                               
                               <div class="clear"></div>
                               <span class="alert alert-warning">Request Type: <em>GET/POST</em></span>
                               <div class="clear"></div>
                            </p>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">How To</h3>
                        </div>
                        <div class="panel-body">
                            <p>To shorten a URL, you'll need to make four requests, as listed below, in that order:</p>
                            <ul>
                            	<li>
                            	    <code><em><strong>/api/getTime</strong></em></code>
                            	    <p>[GET] Returns an encrypted time signature.</p>
                            	</li>
                            	<li>
                            	    <code><em><strong>/api/genMarker</strong></em></code>
                                    <p>[GET] Returns a high entropy user marker.</p>
                            	</li>
                            	<li>
                            	    <code><em><strong>/api/dockRequestReady</strong></em></code>
                                    <p>[POST] Inputs the user marker and time from the two previous requests as well as the URL into the queue.</p>
                            	</li>
                            	<li>
                            	    <code><em><strong>/api/shorten</strong></em></code>
                                    <p>[POST] Shortens the URL in the queue.</p>
                            	</li>
                            </ul>
                            
                            <p>Once a URL is in the queue (after <code>/api/dockRequestReady</code>) you don't have to 
                               immediately call <code>/api/shorten</code>. So long as you maintain the data returned by
                               <code>dockRequestReady</code>, you can make the request to shorten at your leisure. 
                               Granted, most users will immediately call <code>shorten</code>, but I figured this would
                               be a nice feature to have.</p>
                               
                            <p>Let's try a small sample in PHP, assumming that the callPost() function is defined as 
                                follows, and automatically parses the response JSON into an array and only returns the 
                                relevant value:</p>
                            
                            <pre><code>&lt;?php
$data = array("key" => "value", "key" => "value");

function callPost($url, $data){
    $response = makePostRequest($url, $data);
    return $response;
}

function callGet($url){
    $response = makeGetRequest($url);
    return $response;
}</code></pre>
                            
                            <p>Now, the PHP script to shorten a URL:</p>
                            <pre><code>&lt;?php
$url = "http://google.com";
$apikey = "";

$encryptedTime = callGet("http://smfu.in/api/getTime");
$userMarker = callGet("http://smfu.in/api/genMarker");

$dockRequestArray = array("key" => $apikey, "time" => $encryptedTime, "marker" => $userMarker);
$dockRequest = callPost("http://smfu.in/api/dockRequestReady", $dockRequestArray);

$shortenRequestArray = array("key" => $apikey, 
                             "time" => $encryptedTime, 
                             "userMarker" => $dockRequest['userMarker'],
                             "requestMarker" => $dockRequest['requestMarker'], 
                             "url" => $url);
$shortenRequest = callPost("http://smfu.in/api/shorten", $shortenRequestArray);</code></pre>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Endpoints</h3>
                        </div>
                        <div class="panel-body">
                            <div class="well">
                                <a href="#" id="api-getTime" class="api-link pull-right">[link]</a>
                            	<code><strong>/api/getTime</strong></code>
                            	<div class="clear"></div>
                                <div class="alert alert-warning">
                                    Request Type:
                                    <p class="indent">
                                        GET
                                    </p>
                                </div>
                                <div class="alert alert-info">
                                    Request Payload:
                                    <p class="indent">
                                        None
                                    </p>
                                </div>
                                <div class="alert alert-success">
                                    Request Response:<br/>
                                    <p class="indent pre">
                                        {<br/>
                                            &nbsp;&nbsp;&nbsp;&nbsp;"time": "5czN3MTM==wM2gzN"<br/>
                                        }
                                    </p>
                                </div>
                            </div>
                            
                            <div class="well">
                                <a href="#" id="api-genMarker" class="api-link pull-right">[link]</a>
                            	<code><strong>/api/genMarker</strong></code>
                                <div class="clear"></div>
                                <div class="alert alert-warning">
                                    Request Type:
                                    <p class="indent">
                                        GET
                                    </p>
                                </div>
                                <div class="alert alert-info">
                                    Request Payload:
                                    <p class="indent">
                                        None
                                    </p>
                                </div>
                                <div class="alert alert-success">
                                    Request Response:
                                    <p class="indent pre">
                                        {<br/>
                                            &nbsp;&nbsp;&nbsp;&nbsp;"userMarker": "1377798110-521f87de7d79f7.44374631"<br/>
                                        }
                                    </p>
                                </div>
                            </div>
                            
                            <div class="well">
                                <a href="#" id="api-dockRequestReady" class="api-link pull-right">[link]</a>
                            	<code><strong>/api/dockRequestReady</strong></code>
                                <div class="clear"></div>
                                <div class="alert alert-warning">
                                    Request Type:
                                    <p class="indent">
                                        POST
                                    </p>
                                </div>
                                <div class="alert alert-info">
                                    Request Payload:
                                    <p class="indent">
                                        <strong>Key (key)</strong>: Your API Key.<br/>
                                        <strong>Time (time)</strong>: Encrypted time string from your request to /api/getTime/.<br />
                                        <strong>Marker (marker)</strong>: String from your request to /api/genMarker.<br />
                                    </p>
                                </div>
                                <div class="alert alert-success">
                                    Request Response:
                                    <p class="indent pre">
                                        {<br/>
                                            &nbsp;&nbsp;&nbsp;&nbsp;"userMarker": "0575457731-521bae16a34c52.75132943",<br/>
                                            &nbsp;&nbsp;&nbsp;&nbsp;"requestMarker": "0575457731-521f8b3caa8258.92792015"<br/>
                                        }
                                    </p>
                                </div>
                            </div>
                            
                            <div class="well">
                            	<a href="#" id="api-shorten" class="api-link pull-right">[link]</a>
                            	<code><strong>/api/shorten</strong></code>
                                <div class="clear"></div>
                                <div class="alert alert-warning">
                                    Request Type:
                                    <p class="indent">
                                        POST
                                    </p>
                                </div>
                                <div class="alert alert-info">
                                    Request Payload:
                                    <p class="indent">
                                        <strong>Time (time)</strong>: Encrypted string from your request to /api/getTime.<br/>
                                        <strong>URL (url)</strong>: The URL to be shortened.<br/>
                                        <strong>User Marker (userMarker)</strong>: String from your request to /api/genMarker.<br/>
                                        <strong>Request Marker (requestMarker)</strong>: String received from call to /api/dockRequestReady.<br/>
                                        <strong>Key (key)</strong>: Your API Key.<br/>
                                    </p>
                                </div>
                                <div class="alert alert-success">
                                    Request Response:
                                    <p class="indent">
                                        [<br/>
                                            &nbsp;&nbsp;&nbsp;&nbsp;{<br/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id": "9",<br/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"urlkey": "7OtnKD",<br/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"url": "http://www.google.com",<br/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"time": "1377799950"<br/>
                                            &nbsp;&nbsp;&nbsp;&nbsp;}<br/>
                                        ]<br/>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="clear"></div>
                </div>