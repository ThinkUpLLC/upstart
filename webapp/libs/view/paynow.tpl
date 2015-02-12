{include file="_appheader.tpl" body_classes="settings menu-off" body_id="settings-paynow"}

  <div class="container">
    <header class="container-header">
      <h1>Terrific! Your 14-day free trial has begun.</h1>
      <h2>ThinkUp is analyzing your data now.</h2>

       <p>You’ll get an email when your first insights are ready, and they’ll show up at your personal address: <a href="{$thinkup_url}">{$thinkup_url}</a>.</p>
    </header>


    <section class="section section-marketing-text" id="section-pay-now">

      {$pay_with_amazon_form}
      <a class="alt-to-btn-large" id="btn-claim-code" href="#">Got a coupon code?</a></p>

      <form class="marketing-form{if !isset($claim_code)} hidden{/if}" id="form-claim-code" method="post" action="paynow.php">
        <label for="claim_code">Enter your code:</label>
        <input type="text" value="{if isset($claim_code)}{$claim_code}{/if}" id="claim_code" name="claim_code" class="input form-control" placeholder="1234 5678 90AB">
        <button type="submit" value="Submit code" name="submit" class="btn-submit"><i class="fa fa-chevron-right"></i></button>
      </form>

      <p>Join ThinkUp now for just ${if $new_subscriber->membership_level == 'Pro'}10{else}5{/if} a month.</p>
    </section>
  </div>

<script>
ga('set', 'title', 'Registration Complete');
</script>

{include file="_appfooter.tpl"}