{include file="_appheader.v2.tpl" marketing_page=true
body_classes="landing" body_id="landing-home"}

  <div class="container">
    <header class="section col-md-2 col-lg-2 row-1 col-lg-left" id="section-header">
      <h1 class="logo">ThinkUp</h1>
      <h2 class="headline">What if analytics were<br>for real people?</h2>
    </header>

    <section class="section col-md-2 col-lg-1 row-1 col-lg-right section-signup" id="section-signup-top">
      <h3 class="section-header">ThinkUp subscriptions are <strong>now open</strong>.
      <span class="color">Be one of the first.</span></h3>
      <div class="action-links">
        <a href="{$site_root_path}subscribe.php" class="btn signup-link">Become a<br>member</a>
        <div class="login-link">Or <a href="{$site_root_path}user/">log in here</a></div>
      </div>
    </section>

    <section class="section col-lg-1 row-1 col-lg-left" id="section-networks">
      <h3 class="section-header">ThinkUp makes your experiences on Facebook and Twitter richer.</h3>
    </section>

    <section class="section col-lg-1 row-1 col-lg-right" id="section-devices">
      <h3 class="section-header">Use ThinkUp on your phone, tablet, or computer.</h3>
    </section>

    <section class="section col-lg-1 row-2 col-lg-none col-lg-offset-down" id="section-stats">
      <h3 class="section-header">More than statistics! ThinkUp makes you feel good about the time you spend online.</h3>
    </section>

    <section class="section col-lg-1 row-1 col-lg-left col-lg-offset-up" id="section-goals">
      <h3 class="section-header">You’ll get social network superpowers from ThinkUp’s customized insights.</h3>
    </section>

    <section class="section col-lg-1 row-1 col-lg-right col-lg-offset-up" id="section-matters">
      <h3 class="section-header">Understand what matters most to your friends, followers, and fans.</h3>
    </section>

    <section class="section col-lg-1 row-1 col-lg-left" id="section-community">
      <h3 class="section-header">ThinkUp is built by you. We listen carefully to our members and collaborate with our open source community every day.</h3>
    </section>

    <section class="section col-lg-1 row-1 col-lg-left" id="section-companies">
      <h3 class="section-header">With ThinkUp, you can know just as much about your social activity as the big companies do.</h3>
    </section>

    <section class="section col-lg-1 row-1 col-lg-right" id="section-meaning">
      <h3 class="section-header">ThinkUp doesn't just show you what's popular &mdash; it tells you if you really connected with people.</h3>
    </section>

    <section class="section col-lg-1 row-1 col-lg-left" id="section-different">
      <h3 class="section-header">ThinkUp is a better kind of technology company.</h3>

      <div class="headshots">
        <img class="headshot" src="{$site_root_path}assets/img/gina-headshot@2x.jpg" alt="Gina Trapani">
        <img class="headshot" src="{$site_root_path}assets/img/anil-headshot@2x.jpg" alt="Anil Dash">
      </div>

      <p>We want to build a great tech company that’s focused on doing the right thing for our users, our community, and the web. To do it right, we’re building a company that respects users more than any other tech company.</p>
    </section>

    <section class="section col-md-2 col-lg-2 row-1 col-lg-right" id="section-doesnt">
      <h3 class="section-header">ThinkUp is just as amazing for what it <em>doesn’t</em> do.</h3>

      <ul>
        <li>ThinkUp does NOT try to turn your social network into a popularity contest. Life is about meaningful connections, whether you have five followers or five million.</li>
        <li>ThinkUp does NOT do things with your personal data without your permission.</li>
      </ul>
      <ul>
        <li>ThinkUp does NOT lock up your data. We keep your info secure, but we also make it easy to download. Our code is open-source, too. No proprietary lock-in, just great service.</li>
        <li>ThinkUp does NOT change its terms of service without consulting with the community.</li>
      </ul>
    </section>

    <section class="section col-md-2 col-lg-3 row-1 col-lg-right section-signup" id="section-signup-bottom">

      <h3 class="section-header">Not ready to subscribe? Get on the waiting list.</h3>

      <div class="action-links">
        <form action="http://thinkup.us6.list-manage.com/subscribe/post?u=62b20c60f2abf6a8724447bf0&amp;id=dffdb8d09e" method="post" name="mc-embedded-subscribe-form" target="_blank" class="newsletter-signup-form">
          <input type="email" value="" name="EMAIL" class="email" placeholder="Your Email">
          <button type="submit" value="Subscribe" name="subscribe" class="btn-submit"><i class="fa fa-envelope-o"></i></button>
        </form>
        <div class="login-link">Already a member? <a href="{$site_root_path}user/">Log in here</a></div>
      </div>

{*
      <div class="section-container">
        <h3 class="section-header">Get started now. Choose the level that’s right for you.</h3>
        <div class="action-links">
          <a href="#" class="btn signup-link">Sign Up<br>Now!</a>
          <div class="login-link">Or <a href="#">log in here</a></div>
        </div>
      </div>
*}
    </section>

{include file="_appfooter.v2.tpl" marketing_page=true}