{if !isset($marketing_page) or !$marketing_page}
  </div><!-- end container -->

  <footer class="footer">
      <div class="footer-container">
        <div class="copyright-privacy">
          <div class="copyright">&copy;2014 ThinkUp, LLC</div>
          <div class="privacy"><a href="{$site_root_path}about/privacy.php">Privacy</a> and <a href="{$site_root_path}about/terms.php">Terms</a></div>
        </div>
        <div class="motto">It is nice to be nice.</div>
        <div class="follow-wrapper">
          <ul class="follow-links">
            <li class="twitter"><a href="https://twitter.com/thinkup"><i class="fa fa-twitter"></i></a></li>
            <li class="facebook"><a href="https://facebook.com/thinkupapp"><i class="fa fa-facebook-square"></i></a></li>
            <li class="google-plus"><a href="https://plus.google.com/109397312975756759279" rel="publisher"><i class="fa fa-google-plus"></i></a></li>
            <li class="github"><a href="https://github.com/ginatrapani/ThinkUp"><i class="fa fa-github"></i></a></li>
          </ul>
        </div>
      </div>
  </footer>
</div><!-- end page-content -->
{else}
  <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-social">
    <ul class="social-items">
      <li class="social-item" id="social-item-blog">
        <header>Read <a href="http://blog.thinkup.com/" class="fa-override-before fa-tumblr-square">ThinkUp Blog</a></header>
      </li>
      <li class="social-item sm-no-border" id="social-item-github">
        <header>Fork <a href="https://github.com/ginatrapani/ThinkUp" class="fa-override-before fa-github-square">ThinkUp Code</a></header>
      </li>
      <li class="social-item sm-border-top" id="social-item-facebook">
        <header>Like <a href="https://www.facebook.com/thinkupapp">Facebook</a></header>
        <div class="social-button">
          <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fthinkup.com&amp;width=90&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>
        </div>
      </li>
      <li class="social-item sm-border-top sm-no-border lg-no-border" id="social-item-twitter">
        <header>Follow <a href="https://twitter.com/thinkup">@ThinkUp</a></header>
        <div class="social-button">
          <a href="https://twitter.com/thinkup" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @thinkup</a>
          {literal}<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>{/literal}
        </div>
      </li>
    </ul>
  </section>

  <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-legal">
    <h3 class="section-header">Our <a href="{$site_root_path}about/privacy.php">privacy policy</a> and <a href="{$site_root_path}about/terms.php">terms of service</a> are actually worth reading. If you don't like them, <a href="mailto:help@thinkup.com">let us know</a>.</h3>
  </section>

  <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-copyright">
    <h3 class="section-header">&copy;2014 ThinkUp LLC. Made in New York City.  It is nice to be nice. <a href="mailto:help@thinkup.com">Contact us</a> or learn <a href="{$site_root_path}about/">about us</a>.</h3>
  </section>


</div>
{/if}

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="{$site_root_path}assets/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
{if !isset($marketing_page) or !$marketing_page}
    <script src="{$site_root_path}assets/js/vendor/bootstrap.min.js"></script>
    <script src="{$site_root_path}assets/js/vendor/jpanelmenu.js"></script>
    <script src="//platform.twitter.com/widgets.js"></script>
     <script src="{$site_root_path}assets/js/thinkup.js "></script>
    {if isset($include_tz_js) and $include_tz_js}
    {if not isset($owner) or $owner->timezone eq 'UTC'}
      <script type="text/javascript" src="{$site_root_path}assets/js/vendor/jstz-1.0.4.min.js"></script>
      <script type="text/javascript">
      {literal}
      var tz_info = jstz.determine();
      var regionname = tz_info.name().split('/');
      var tz_option_id = '#tz-' + regionname[1];
      if( $('#control-timezone option[value="' + tz_info.name() + '"]').length > 0) {
          if( $(tz_option_id) ) {
              $('#control-timezone').val( tz_info.name());
          }
      }
      {/literal}
      {if isset($show_tz_msg) and $show_tz_msg}{literal}
      var app_message = {};
      app_message.msg = "There's no time zone set for your account. We think it's " + $( "#control-timezone option:selected" ).text() + ". Is that right? <a id='msg-action' data-submit-target='#form-settings' href=\"#\">Yep, set it!</a>";
      app_message.type = "info";
      {/literal}{/if}
      </script>
    {/if}
    {/if}
{else}
    <script src="{$site_root_path}assets/js/vendor/jquery.mobile.custom.min.js"></script>
    <script src="{$site_root_path}assets/js/marketing.js "></script>
{/if}

{literal}<script>
  var _gaq=[['_setAccount','UA-76614-5'],['_trackPageview']];
  (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
  g.src='//www.google-analytics.com/ga.js';
  s.parentNode.insertBefore(g,s)}(document,'script'));
</script>

<script type="text/javascript">
var _sf_async_config={uid:2383,domain:"thinkup.com"};
(function(){
  function loadChartbeat() {
    window._sf_endpt=(new Date()).getTime();
    var e = document.createElement('script');
    e.setAttribute('language', 'javascript');
    e.setAttribute('type', 'text/javascript');
    e.setAttribute('src',
       (("https:" == document.location.protocol) ? "https://a248.e.akamai.net/chartbeat.download.akamai.com/102508/" : "http://static.chartbeat.com/") +
       "js/chartbeat.js");
    document.body.appendChild(e);
  }
  var oldonload = window.onload;
  window.onload = (typeof window.onload != 'function') ?
     loadChartbeat : function() { oldonload(); loadChartbeat(); };
})();

</script>{/literal}

</body>

</html>
