{include file="_header.marketing.tpl" marketing_page=true hide_join=true
body_classes="marketing marketing-page" body_id="marketing-confirm"}

  <div class="container">
    <section class="section section-legal-text" id="section-confirm">
      <h2 class="section-header">Welcome to the ThinkUp community!</h2>

      <img src="{$site_root_path}assets/img/landing/crowd@2x.png" class="illustration book-cover">

      <p>We’re delighted you’ve decided to join us. Keep an eye out for that first email, but here are a few things you can do now:</p>

      <h3 class="hc">1. Add another service</h3>
      <p>ThinkUp is better with more data. When you authenticate with another service, it unlocks new types of insights. Since you already have a{if $subscriber->network == 'twitter'} Twitter account&hellip;</p>
      <p><a class="btn btn-pill var-width" href="https://{$subscriber->thinkup_username}.thinkup.com/account/?p=facebook">Add a Facebook account</a>{else} Facebook account&hellip;</p>
      <p><a class="btn btn-pill var-width" href="https://{$subscriber->thinkup_username}.thinkup.com/account/?p=twitter">Add a Twitter account</a>{/if}</p>

      <h3 class="hc">2. Download your copy of <em>Insights</em></h3>
      <p>To thank you, and all our community members, we put together an ebook of interviews with the smartest people in social media. <a href="{$site_root_path}user/membership.php">Download <em>Insights</em> from your membership page</a>.</p>

      <h3 class="hc">3. Ask questions, get answers</h3>
      <p>We love hearing from our members, whether it’s a bug report, feature request, or quick hello. If you need anything, email us at <a href="mailto:help@thinkup.com">help@thinkup.com</a>. You can also look at our <a href="{$site_root_path}about/faq.php">FAQ</a> for, well, frequently asked questions.</p>

      <p><a href="https://blog.thinkup.com/">Our blog</a> is also a great source of information. We post about feature releases, share our investor updates, and explain how we make decisions. To date, there has been a lack of animated GIFs, but it’s high on our list of bugs to fix.</p>


    </section>

{include file="_footer.marketing.tpl"}