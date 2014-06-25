{include file="_header.marketing.tpl" marketing_page=true hide_join=true
body_classes="marketing marketing-page hide-social" body_id="marketing-subscribe"}

  <div class="container">

    <section class="section" id="section-page-header">
      <h2 class="section-header">Start Your 14-day Free Trial.</h2>
      <h3 class="section-subheader">First, pick the level that’s right for you.</h3>
    </section>
  </div>
  <div class="container">

    <div class="section subscription-levels">
      <section class="subsection section-subscription-level"
      id="section-subscription-member">
        <h3 class="subsection-header">1 account
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
          <div class="action-buttons-label">Get started with:</div>
          <div class="actual-buttons">
            <a href="{$twitter_member_link}" class="btn btn-pill btn-twitter"><i class="fa fa-twitter icon"></i> Twitter</a>
            <a href="{$facebook_member_link}" class="btn btn-pill btn-facebook"><i class="fa fa-facebook icon"></i> Facebook</a>
          </div>
        </div>
      </section>

      <section class="subsection section-subscription-level"
      id="section-subscription-pro">
        <h3 class="subsection-header">5 accounts
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
          <div class="action-buttons-label">Get started with:</div>
          <div class="actual-buttons">
            <a href="{$twitter_pro_link}" class="btn btn-pill btn-twitter"><i class="fa fa-twitter icon"></i> Twitter</a>
            <a href="{$facebook_pro_link}" class="btn btn-pill btn-facebook"><i class="fa fa-facebook icon"></i> Facebook</a>
          </div>
        </div>
      </section>
    </div>
  </div>
  <div class="container">
    <section class="section" id="section-subscription-executive">
      <h3 class="subsection-header">For bigger organizations: ThinkUp Executive</h3>

      <div class="level-description">
        <p>If you’re a company or institution with more advanced needs, <a style="color:#2785d3;" href="mailto:team@thinkup.com?subject=ThinkUp+Executive">email us</a>. We’ve got just the thing.</p>
      </div>
    </section>
  </div>
{include file="_footer.marketing.tpl"}