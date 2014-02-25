{include file="_appheader.v2.tpl" marketing_page=true
body_classes="marketing marketing-page" body_id="marketing-subscribe"}

  <div class="container">
    <header class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-header">
      <h1 class="logo"><a href="{$site_root_path}">ThinkUp</a></h1>

      <nav class="navigation-main">
        <a class="nav-link" href="{$site_root_path}user/">Login</a>
        <a class="nav-link" href="{$site_root_path}subscribe.php">Signup</a>
        <a class="nav-link" href="{$site_root_path}about.php">About</a>
      </nav>
    </header>

    <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-page-header">
      <h2 class="section-header">ThinkUp costs money. Because it’s worth it.</h2>
      <h3 class="section-subheader">Choose a level based on how many social networking accounts you have.</h3>
    </section>

    <div class="section-group subscription-levels">

      <section class="section section-subscription-level"
      id="section-subscription-member">
        <h3 class="section-header">Member</h3>
        <div class="level-cost">
          <div class="annually">$60/year</div>
          <div class="monthly">$5 a month!</div>
        </div>

        <div class="level-description">
          <p class="headline">1 account per social network</p>
          <p>For each of your networks</p>
        </div>

        <div class="action-buttons">
          <div class="action-buttons-label">Join with:</div>
          <a href="{$twitter_member_link}" class="btn btn-pill btn-twitter"><i class="fa fa-twitter icon"></i> Twitter</a>
          <a href="{$facebook_member_link}" class="btn btn-pill btn-facebook"><i class="fa fa-facebook icon"></i> Facebook</a>
        </div>
      </section>

      <section class="section section-subscription-level"
      id="section-subscription-pro">
        <h3 class="section-header">Pro</h3>
        <div class="level-cost">
          <div class="annually">$120/year</div>
          <div class="monthly">$10 a month!</div>
        </div>

        <div class="level-description">
          <p class="headline">5 accounts per social network</p>
          <p>Up to ten across all of your networks</p>
        </div>

        <div class="action-buttons">
          <div class="action-buttons-label">Join with:</div>
          <a href="{$twitter_pro_link}" class="btn btn-pill btn-twitter"><i class="fa fa-twitter icon"></i> Twitter</a>
          <a href="{$facebook_pro_link}" class="btn btn-pill btn-facebook"><i class="fa fa-facebook icon"></i> Facebook</a>
        </div>
      </section>

          <section class="section col-md-2 col-lg-3 row-1 col-lg-right section-signup" id="section-signup-bottom">

            <h3 class="section-header">Not ready to subscribe? Get on the waiting list.</h3>

            <div class="action-links">
              <form action="http://thinkup.us6.list-manage.com/subscribe/post?u=62b20c60f2abf6a8724447bf0&amp;id=dffdb8d09e" method="post" name="mc-embedded-subscribe-form" target="_blank" class="newsletter-signup-form">
                <input type="email" value="" name="EMAIL" class="email" placeholder="Your Email">
                <button type="submit" value="Subscribe" name="subscribe" class="btn-submit"><i class="fa fa-envelope-o"></i></button>
              </form>
            </div>
          </section>


      <section class="section col-md-2 col-lg-3 col-lg-right"
      id="section-subscription-executive">
        <h3 class="section-header">Need more? There's an executive option.</h3>

        <div class="level-description">
          <p>If you’re a company or organization with more advanced needs, <a href="mailto:team@thinkup.com">email us</a>. We'll fill you in on our roadmap.</p>
        </div>
      </section>
    </div>

{include file="_appfooter.v2.tpl" marketing_page=true}