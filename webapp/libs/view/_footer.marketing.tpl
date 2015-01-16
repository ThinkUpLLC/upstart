{if not isset($hide_footer) or not $hide_footer}
<div class="container" id="container-footer">
  <div class="section" id="section-footer">
    <div class="subsection" id="subsection-blog">
      <h4 class="subsection-header">The latest from the ThinkUp blog</h4>
      <ul class="blog-posts"></ul>
      <a href="http://blog.thinkup.com" class="blog-link">Go to the blog &raquo;</a>
    </div>
    <div class="subsection" id="subsection-links">
      <h4 class="subsection-header">Find out more</h4>
      <ul class="important-links">
        <li><a href="{$site_root_path}about/privacy.php">Privacy Policy</a></li>
        <li><a href="https://github.com/ginatrapani/ThinkUp">Developers</a></li>
        <li><a href="{$site_root_path}about/contact.php">Contact</a></li>
        <li><a href="{$site_root_path}about/terms.php">Terms of Service</a></li>
        <li><a href="{$site_root_path}about/">About</a></li>
        <li><a href="{$site_root_path}about/faq.php">FAQ</a></li>
      </ul>

      <div class="copy-tagline">&copy;2014-2015 ThinkUp LLC.<br>
        It is nice to be nice.</div>
    </div>
  </div>
</div>
{/if}


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="{$site_root_path}assets/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
<script src="{$site_root_path}assets/js/vendor/jquery.mobile.custom.min.js"></script>
<script src="{$site_root_path}assets/js/marketing.js "></script>

{include file="_footer.common.tpl"}
</body>

</html>
