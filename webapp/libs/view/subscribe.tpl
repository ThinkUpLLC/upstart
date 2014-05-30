{include file="_appheader.tpl" marketing_page=true
body_classes="marketing marketing-page hide-social" body_id="marketing-subscribe"}

  <div class="container">
    {include file="_marketing-navigation.tpl"}

    <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-page-header">
      <h2 class="section-header">Join thousands of happy ThinkUp members.</h2>
      <h3 class="section-subheader">100% money-back guarantee (but almost nobody ever asks)</h3>
    </section>

    <div class="section-group subscription-levels">

      <section class="section section-subscription-level"
      id="section-subscription-member">
        <h3 class="section-header">1 account
          <span class="descriptor">per social network</span></h3>
        <div class="level-cost">
          <div class="annually">$60/year</div>
          <div class="monthly">$5 a month!</div>
        </div>

        <div class="level-description">
          <p class="headline">Connect your Twitter &amp; Facebook accounts</p>
          <p>&nbsp;</p>
        </div>

        <div class="action-buttons">
          <div class="action-buttons-label">Sign up with:</div>
          <div class="actual-buttons">
            <a href="{$twitter_member_link}" class="btn btn-pill btn-twitter"><i class="fa fa-twitter icon"></i> Twitter</a>
            <a href="{$facebook_member_link}" class="btn btn-pill btn-facebook"><i class="fa fa-facebook icon"></i> Facebook</a>
          </div>
        </div>
      </section>

      <section class="section section-subscription-level"
      id="section-subscription-pro">
        <h3 class="section-header">5 accounts
          <span class="descriptor">per social network</span></h3>
        <div class="level-cost">
          <div class="annually">$120/year</div>
          <div class="monthly">$10 a month!</div>
        </div>

        <div class="level-description">
          <p class="headline">Perfect for power users or companies</p>
          <p>Up to 10 accounts total</p>
        </div>

        <div class="action-buttons">
          <div class="action-buttons-label">Sign up with:</div>
          <div class="actual-buttons">
            <a href="{$twitter_pro_link}" class="btn btn-pill btn-twitter"><i class="fa fa-twitter icon"></i> Twitter</a>
            <a href="{$facebook_pro_link}" class="btn btn-pill btn-facebook"><i class="fa fa-facebook icon"></i> Facebook</a>
          </div>
        </div>
      </section>

      <section class="section col-md-2 col-lg-3 col-lg-right"
      id="section-subscription-executive">
        <h3 class="section-header">For bigger organizations: ThinkUp Executive</h3>

        <div class="level-description">
          <p>If you’re a company or institution with more advanced needs, <a style="color:#2785d3;" href="mailto:team@thinkup.com?subject=ThinkUp+Executive">email us</a>. We’ve got just the thing.</p>
        </div>
      </section>

      <section class="section col-md-2 col-lg-3 row-1 col-lg-right section-signup" id="section-signup-bottom">

        <h3 class="section-header">Not ready to join yet? Get on the updates list.</h3>

        <div class="action-links">
          <form action="http://thinkup.us6.list-manage.com/subscribe/post?u=62b20c60f2abf6a8724447bf0&amp;id=dffdb8d09e" method="post" name="mc-embedded-subscribe-form" target="_blank" class="newsletter-signup-form">
            <input type="email" value="" name="EMAIL" class="email" placeholder="Your Email">
            <button type="submit" value="Subscribe" name="subscribe" class="btn-submit"><i class="fa fa-envelope-o"></i></button>
          </form>
        </div>
      </section>
    </div>

{include file="_footer.marketing.tpl"}