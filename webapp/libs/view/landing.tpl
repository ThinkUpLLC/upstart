{include file="_header.marketing.tpl" body_classes="landing" body_id="landing-home"}

<div class="container" id="container-analytics">
  <div class="section section-screenshot" id="section-analytics">
    <h3 class="section-header">&ldquo;Analytics&rdquo; is for giant companies. ThinkUp is for people.</h3>
    <div class="section-copy">
      <p>ThinkUp tells you what's fun and interesting about your social networking activity in a way that even Facebook or Twitter's analytics can't.</p>
      <div class="illustration">An insight</div>
    </div>
  </div>
</div>

<div class="container" id="container-signup-top">
  <div class="section section-screenshot" id="section-signup-top">
    <h3 class="section-header">Get started for free.</h3>
    <div class="section-copy">
      <p>Sign up for our <strong>Free 14-Day Trial</strong> to see exactly what ThinkUp can do for you. Right from the start, you'll get insights you’ve never seen before.</p>
      <a href="{$site_root_path}join.php" class="btn btn-pill-large">Start your free trial</a>
      <div class="illustration">
        <a href="https://ginatrapani.thinkup.com">See a live demo</a>
      </div>
    </div>
  </div>
</div>

<div class="container" id="container-specs">
  <div class="section section-screenshot" id="section-specs">
    <h3 class="section-header">ThinkUp works the way that you do.</h3>
    <div class="section-copy">
      <p>ThinkUp’s insights come any way you want them: Check them any time on your smartphone, tablet, or computer. And each day, get a personalized email telling you what's up with your friends, followers and fans.</p>
      <div class="illustration">An image of three devices</div>
    </div>
  </div>
</div>

<div class="container" id="container-company">
  <div class="section section-screenshot" id="section-company">
    <h3 class="section-header">A tech company that<br>
      treats you with respect.</h3>
    <div class="section-copy">
      <p>ThinkUp is a different kind of company. We make products for regular people, we collaborate with an <a href="https://github.com/ginatrapani/ThinkUp">open source community</a> and we live by <a href="{$site_root_path}about/values.php">our values</a>.</p>
      <div class="illustration">An insight</div>
    </div>
  </div>
</div>

<div class="container" id="container-not-thinkup">
  <div class="section section-screenshot" id="section-not-thinkup">
    <h3 class="section-header">You’ll love what ThinkUp doesn’t do.</h3>
    <div class="section-copy">
      <ul>
        <li>No popularity contests about how many friends you have.</li>
        <li>No creepy tracking of your behavior.</li>
        <li>No ugly surprises about how your data is used.</li>
        <li>No hassle if you want to cancel your subscription.</li>
      </ul>
      <div class="illustration">An insight</div>
    </div>
  </div>
</div>

<div class="container" id="container-signup-bottom">
  <div class="section" id="section-signup-bottom">
    <h3 class="section-header">Start your <em>100% free</em> trial and get insights immediately</h3>
    <div class="section-copy">
      <a href="{$site_root_path}join.php" class="btn btn-pill-medium">Join now</a>
    </div>
  </div>
</div>

<script src="http://blog.thinkup.com/api/read/json?type=text&num=5"></script>
<div class="container" id="container-footer">
  <div class="section" id="section-footer">
    <div class="subsection" id="subsection-blog">
      <h4 class="subsection-header">The latest from the ThinkUp blog</h4>
      <ul class="blog-posts"></ul>
      <a href="http://blog.thinkup.com" class="blog-link">Go to the blog &raquo;</a>
    </div>
    <div class="subsection" id="subsection-links">
      <h4 class="subsection-header">Important, Etc.</h4>
      <ul class="important-links">
        <li><a href="{$site_root_path}about/privacy.php">Privacy Policy</a></li>
        <li><a href="https://github.com/ginatrapani/ThinkUp">Developers</a></li>
        <li><a href="{$site_root_path}about/contact.php">Contact</a></li>
        <li><a href="{$site_root_path}about/terms.php">Terms of Service</a></li>
        <li><a href="{$site_root_path}about/faq.php">FAQ</a></li>
      </ul>

      <form action="http://thinkup.us6.list-manage.com/subscribe/post?u=62b20c60f2abf6a8724447bf0&amp;id=dffdb8d09e" method="post" name="mc-embedded-subscribe-form" target="_blank" class="newsletter-signup-form">
        <label for="EMAIL">Get updates from ThinkUp</label>
        <input type="email" value="" name="EMAIL" class="email" placeholder="Your Email">
        <button type="submit" value="Subscribe" name="subscribe" class="btn-submit"><i class="fa fa-envelope-o"></i></button>
      </form>

      <div class="copy-tagline">&copy;2014 ThinkUp LLC.<br>
        It is nice to be nice.</div>
    </div>
  </div>
</div>

{include file="_footer.marketing.tpl"}