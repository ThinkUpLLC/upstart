{include file="_appheader.v2.tpl" marketing_page=true
body_classes="marketing header-compressed" body_id="marketing-subscribe"}

  <div class="container">
    <header class="section col-md-2 col-lg-2 row-1 col-lg-left" id="section-header">
      <h1 class="logo"><a href="{$site_root_path}">ThinkUp</a></h1>
      <h2 class="headline">What if analytics were<br>for real people?</h2>
    </header>

    <section class="section col-md-2 col-lg-1 row-1 col-lg-right section-signup" id="section-signup-top">
      <h3 class="section-header">Not ready to commit?<br>
      Get on the list.</h3>

      <div class="action-links">
        <form action="http://thinkup.us6.list-manage.com/subscribe/post?u=62b20c60f2abf6a8724447bf0&amp;id=dffdb8d09e" method="post" name="mc-embedded-subscribe-form" target="_blank" class="newsletter-signup-form">
          <input type="email" value="" name="EMAIL" class="email" placeholder="Your Email">
          <button type="submit" value="Subscribe" name="subscribe" class="btn-submit"><i class="fa fa-envelope-o"></i></button>
        </form>
        <div class="login-link">Beta member? <a href="user/">Log in here</a></div>
      </div>
    </section>

    <section class="section col-md-2 col-lg-3 row-1 col-lg-right" id="section-page-header">
      <h2 class="section-header">Choose a subscription level</h2>
      <h3 class="section-subheader">All payments are processed via Amazon Payments.</h3>
    </section>

    <div class="section-group subscription-levels{if $level} is-preselected{/if}">

      <section class="section col-lg-1 col-lg-left section-subscription-level{if $level eq "member"} is-active{/if}"
      id="section-subscription-member" data-subscribe-url="{$subscribe_member_url}">
        <h3 class="section-header">Member</h3>
        <div class="level-cost">
          <div class="monthly">$60/year</div>
          <div class="annually">Only $5 a month!</div>
        </div>

        <a href="{$subscribe_member_url}" class="btn btn-circle btn-subscribe">Subscribe</a>

        <div class="level-description">
          <p>Join ThinkUp and get:</p>
          <ul class="level-benefits">
            <li>Insights on your Facebook, Twitter or other social network account. (1 per service)</li>
            <li>First place in line to reserve your username on ThinkUp.</li>
          </ul>
        </div>
      </section>

      <section class="section col-lg-1 col-lg-left section-subscription-level
      {if $level eq "pro" or ($level neq "executive" and $level neq "member")} is-active{/if}"
      id="section-subscription-pro" data-subscribe-url="{$subscribe_pro_url}">
        <h3 class="section-header">Pro</h3>
        <div class="level-cost">
          <div class="monthly">$120/year</div>
          <div class="annually">Just 10 bucks a month!</div>
        </div>

        <a href="{$subscribe_pro_url}" class="btn btn-circle btn-subscribe">Subscribe</a>

        <div class="level-description">
          <p>Folks with multiple social networking accounts and developers get:</p>
          <ul class="level-benefits">
            <li>All the benefits of the <strong class="level-span">Member</strong> level, plus:</li>
            <li>Support for up to <strong>10</strong> social network accounts across all supported services.</li>
            <li>First access to new beta features as they’re developed.</li>
            <li>Access to new APIs and data services for building apps around ThinkUp.</li>
          </ul>
        </div>
      </section>

      <section class="section col-md-2 col-lg-1 col-lg-right section-subscription-level
      {if $level eq "executive"} is-active{/if}"
      id="section-subscription-executive" data-subscribe-url="{$subscribe_executive_url}">
        <h3 class="section-header">Executive</h3>
        <div class="level-cost">
          <div class="monthly">$996/year</div>
          <div class="annually">Only $83 a month!</div>
        </div>

        <a href="{$subscribe_executive_url}" class="btn btn-circle btn-subscribe">Subscribe</a>

        <div class="level-description">
          <p>If you're a company, an organization, or want a stake in ThinkUp's future:</p>
          <ul class="level-benefits">
            <li>Get all the benefits of the <strong class="level-span">Member</strong> and <strong class="level-span">Pro</strong> levels.</li>
            <li>Let us know how many social networking accounts you need to support.</li>
            <li>Our company's founders will consult with you before making key decisions about ThinkUp's future. You'll get email updates with the same information we send to our investors or advisers (minus anything that would get us in legal trouble!).</li>
          </ul>
        </div>
      </section>
    </div>

{include file="_appfooter.v2.tpl" marketing_page=true}