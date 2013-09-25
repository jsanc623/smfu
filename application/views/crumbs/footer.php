        
                <div id="footer" class="footer">
                    <ul class="nav nav-pills footerNav">
                        <li><a href="<?php echo base_url("/"); ?>">Home</a></li>
                    	<li><a href="<?php echo base_url("/site/about"); ?>">About</a></li>
                    	<?php if($this->common->isUserLoggedIn() == FALSE){ ?>
                        <li><a href="<?php echo base_url("/users/login"); ?>">Login</a></li>
                        <li><a href="<?php echo base_url("/users/register"); ?>">Register</a></li>
                        <?php } else { ?>
                        <li><a href="<?php echo base_url("/users/profile"); ?>">Profile</a></li>
                        <li><a href="<?php echo base_url("/users/logout"); ?>">Logout</a></li>
                        <li><a href="<?php echo base_url("/api/documentation"); ?>">Documentation</a></li>
                        <?php } ?>
                        <li><a href="<?php echo base_url("/site/stats"); ?>">Stats</a></li>
                    </ul>
                </div>

                <div class="clear"></div>

                <div style="text-align:center;">
                    <!-- Place this tag where you want the +1 button to render. -->
                    <div class="g-plusone" data-size="medium"></div>

                    <!-- Place this tag after the last +1 button tag. -->
                    <script type="text/javascript">
                      (function() {
                        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                        po.src = 'https://apis.google.com/js/plusone.js';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                      })();
                    </script>

                    <div class="fb-like" style="left:-15px;" data-href="http://smfu.in" data-width="200" data-layout="button_count" data-show-faces="true" data-send="false"></div>

                    <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://smfu.in" data-text="Check out this site" data-via="smfuDOTin" data-hashtags="smfuin">Tweet</a>
                    <script>
                    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
                    if(!d.getElementById(id)){js=d.createElement(s);
                    js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);
                    }}(document, 'script', 'twitter-wjs');
                    </script>
                </div>
                
                <div class="clear"></div> <div class="clear"></div>
                <div style="text-align:center">
                    <script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- SMFU Homepage -->
                    <ins class="adsbygoogle"
                         style="display:inline-block;width:728px;height:90px"
                         data-ad-client="ca-pub-7636574386929429"
                         data-ad-slot="4493539218"></ins>
                    <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
        </div>
        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-2.0.3.min.js"><\/script>')</script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.zclip.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/vendor/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
        <script>
            <?php if(isset($DirectorJS)){ echo $DirectorJS; } ?>
        
            var _gaq=[['_setAccount','{google_analytics}'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
    </body>
</html>