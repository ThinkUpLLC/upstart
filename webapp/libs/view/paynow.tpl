{include file="_header.marketing.tpl" marketing_page=true hide_join=true
body_classes="marketing marketing-page" body_id="marketing-welcome"}

  <div class="container">
    <section class="section section-marketing-text" id="section-pay-now">
      <h2 class="section-header">Terrific! Your 14-day free trial has begun.</h2>

      <img src="{$site_root_path}assets/img/book-cover.png" class="illustration book-cover">

      <p class="break-word">ThinkUp is analyzing your data now. You’ll get an email when your first insights are ready, and they’ll show up at your personal address: <a href="{$thinkup_url}">{$thinkup_url}</a>.</p>

      {$checkout_button}
      <a class="alt-to-btn-large" id="btn-claim-code" href="#">Got a coupon code?</a></p>

      <form class="marketing-form{if !isset($claim_code)} hidden{/if}" id="form-claim-code" method="post" action="paynow.php">
        <label for="claim_code">Enter your code:</label>
        <input type="text" value="{if isset($claim_code)}{$claim_code}{/if}" id="claim_code" name="claim_code" class="input" placeholder="1234 5678 90AB">
        <button type="submit" value="Submit code" name="submit" class="btn-submit"><i class="fa fa-chevron-right"></i></button>
      </form>

      <p>Join ThinkUp now for just ${if $new_subscriber->membership_level == 'Pro'}10{else}5{/if} a month and get <strong>the book we wrote for you</strong>! Our exclusive ebook <em>Insights</em> offers interviews with the smartest people in social media.</p>
    </section>

<script>
ga('set', 'title', 'Registration Complete');
</script>
{include file="_footer.marketing.tpl" hide_footer=true}

</body>

</html>
