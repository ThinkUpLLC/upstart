{include file="_header.marketing.tpl" marketing_page=true hide_join=true
body_classes="marketing marketing-page" body_id="marketing-confirm"}

  <div class="container">
    <section class="section section-legal-text" id="section-confirm">
      <h2 class="section-header">Welcome to ThinkUp!</h2>

      <img src="{$site_root_path}assets/img/landing/crowd@2x.png" class="illustration book-cover">

      <p>We’re delighted you've joined ThinkUp. Remember, an email containing your first insights will arrive in your inbox soon.</p>

      {if $subscriber->network == 'twitter'}
        {assign var='service' value='Facebook'}
      {else}
        {assign var='service' value='Twitter'}
      {/if}
      <h3 class="hc">Add another service</h3>
      <p>Have a {$service} account? Connect it to ThinkUp to unlock even more insights about the time you spend online.
      <p><a class="btn btn-pill var-width" href="https://{$subscriber->thinkup_username}.thinkup.com/account/?p={$service|strtolower}">Add a {$service} account</a>

      <h3 class="hc">Download your copy of <em>Insights</em></h3>
      <p>We asked some of the most influential and innovative thinkers on the web about the future of social networking and social media. We want to share their wisdom with you. <a href="{$site_root_path}user/membership.php">Download <em>Insights</em> from your membership page</a>.</p>

    </section>

{include file="_footer.marketing.tpl"}