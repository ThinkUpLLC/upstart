  </div><!-- end container -->

  <footer class="footer">
      <div class="footer-container">
        <div class="copyright-privacy">
          <div class="copyright">&copy;2013 ThinkUp, LLC</div>
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


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="{$site_root_path}assets/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>

    <script src="{$site_root_path}assets/js/vendor/bootstrap.min.js"></script>
    <script src="{$site_root_path}assets/js/vendor/jpanelmenu.js"></script>
    <script src="//platform.twitter.com/widgets.js"></script>
    <script src="{$site_root_path}assets/js/thinkup.js "></script>

    {if isset($include_tz_js) and $include_tz_js}
    {if $subscriber->timezone eq 'UTC'}
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
    </script>
    {/if}
    {/if}
</body>

</html>
