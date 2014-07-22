{include file="_header.marketing.tpl" marketing_page=true hide_join=true
body_classes="marketing marketing-page" body_id="marketing-welcome"}

  <div class="container">
    <section class="section section-marketing-text" id="section-pay-now">
      <h2 class="section-header">Terrific! Your 14-Day free trial has begun.</h2>

      <img src="{$site_root_path}assets/img/book-cover.png" class="illustration book-cover">

      <p class="break-word">Thinkup is analyzing your data now. You’ll get an email when your first insights are ready, and they’ll show up at your personal address: <a href="{$thinkup_url}">{$thinkup_url}</a>.</p>

      <p class="btn-wrapper"><a href="{$pay_with_amazon_url}" class="btn btn-pill-large has-note">Pay now with Amazon<br><small>$60 per year</small></a><br>
      <a class="alt-to-btn-large" href="{$thinkup_url}">I’m not ready to pay yet.</a></p>

      <p>Join ThinkUp now for just $60 a year and get <strong>the book we wrote for you</strong>! Our exclusive ebook <em>Insights</em> offers interviews with the smartest people in social media.</p>
    </section>

{include file="_footer.marketing.tpl" hide_footer=true}