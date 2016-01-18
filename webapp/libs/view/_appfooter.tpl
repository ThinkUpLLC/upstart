  </div><!-- end container -->

  <footer class="footer">
      <div class="footer-container">
        <div class="copyright-privacy">
          <div class="copyright">&copy;2014-2016 ThinkUp LLC</div>
          <div class="privacy"><a href="{$site_root_path}about/privacy.php">Privacy</a> and <a href="{$site_root_path}about/terms.php">Terms</a></div>
        </div>
        <div class="motto">It is nice to be nice.</div>
        <div class="follow-wrapper">
          <ul class="follow-links">
            <li class="twitter"><a href="https://twitter.com/thinkup"><i class="fa fa-twitter"></i></a></li>
            <li class="facebook"><a href="https://facebook.com/thinkupapp"><i class="fa fa-facebook-square"></i></a></li>
            <li class="github"><a href="https://github.com/ThinkUpLLC/ThinkUp"><i class="fa fa-github"></i></a></li>
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
     <script src="{$site_root_path}assets/js/thinkup.min.js "></script>
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
              $('#control-timezone')
              .val( tz_info.name())
              .closest(".form-group").addClass("form-group-ok");

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

{include file="_footer.common.tpl"}

</body>

</html>
