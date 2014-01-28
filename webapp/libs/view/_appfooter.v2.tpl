{if !isset($landing_page) or !$landing_page}
  </div><!-- end container -->

  <footer class="footer">
      <div class="footer-container">
        <div class="copyright-privacy">
          <div class="copyright">&copy;2014 ThinkUp, LLC</div>
          <a class="privacy" href="https://github.com/ThinkUpLLC/policy">Privacy and stuff</a>
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
{/if}

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="{$site_root_path}assets/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
{if !isset($landing_page) or !$landing_page}
    <script src="{$site_root_path}assets/js/vendor/bootstrap.min.js"></script>
    <script src="{$site_root_path}assets/js/vendor/jpanelmenu.js"></script>
    <script src="//platform.twitter.com/widgets.js"></script>
    {if isset($include_tz_js) and $include_tz_js and isset($owner)}
    {if $owner->timezone eq 'UTC'}
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
      var app_message = {};
      app_message.msg = "There's no time zone set for your account. We think it's " + $( "#control-timezone option:selected" ).text() + ". Is that right? <a id='msg-action' data-submit-target='#form-settings' href=\"#\">Yep, set it!</a>";
      app_message.type = "info";
      {/literal}
      </script>
    {/if}
    {/if}
{/if}

    <script src="{$site_root_path}assets/js/thinkup.js "></script>

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
