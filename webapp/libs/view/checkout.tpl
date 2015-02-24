{include file="_appheader.tpl" body_classes="settings menu-off" body_id="settings-checkout"}

    <script src="assets/js/vendor/pay-with-amazon.min.js"></script>
    <script type="text/javascript">
    var payWithAmazon = new PayWithAmazon({
        sellerId: 'A3MIF7Z0W3XC2S', //https://sellercentral.amazon.com/gp/pyop/seller/account/settings/user-settings-view.html/ref=ps_pyopiset_dnav_onconfig_
        clientId: 'amzn1.application-oa2-client.f402aa2a27df4dd0bfce7e297b971176', // https://sellercentral.amazon.com/gp/homepage.html
        button: { id: 'wallet', type: 'large', color: 'Gold' },
        wallet: { id: 'wallet', width: 400 },
        consent: { id: 'consent', width: 400 },
      }).on('change', notify);

      function notify (status) {
        console && console.log.apply(console, arguments);

        if (status.id) {
          document.querySelector('#amazon_billing_agreement_id').value = status.id;
          document.querySelector('#subscribe-btn').style.visibility = 'visible';
        }
      }
    </script>
  <div class="container">

{if $state eq "prompt_for_payment"}
    <header class="container-header">
      <h1>Complete Your Payment</h1>
      <h2>It's safe and easy with your Amazon account.</h2>
    </header>

    <form method="post" action="checkout.php">
        <header class="container-header">
          <h2>Select your plan:</h2>
          <div class="pricing">
            <input type="radio" name="plan" id="plan-monthly" value="{$subscriber->membership_level|strtolower}-monthly">
            <label for="plan-monthly" onclick='$("#wallet").removeClass("disabled")'>
              <h3>${$amount_monthly}</h3>
              per month
            </label>
            <input type="radio" name="plan" id="plan-yearly" value="{$subscriber->membership_level|strtolower}-yearly">
            <label for="plan-yearly" onclick='$("#wallet").removeClass("disabled")'>
              <h3>${$amount_yearly}</h3>
              per year
            </label>
          </div>
        </header>

    <div id="wallet" class="disabled"></div>
    <div id="consent"></div>
        <input id="amazon_billing_agreement_id" name="amazon_billing_agreement_id" value="" hidden="true">
        <input type="Submit" value="Subscribe" id="subscribe-btn" style="visibility:hidden" class="btn-submit" />
    </form>

    <p class="form-note"><a href="{$site_root_path}user/membership.php">No thanks, I'll do this later.</a></p>
{/if}

{if $state eq "payment_successful"}
    <header class="container-header">
      <h1>Thanks! Your payment is complete.</h1>
      <h2>You are now <em>officially</em> a ThinkUp subscriber.</h2>
    </header>


    <div class="pricing">
      <a href="" class="btn btn-pill-large has-note">
        See your ThinkUp insights<br />
        <small>Your insights are almost ready</small>
      </a>
    </div>
{/if}

  </div>

{include file="_appfooter.tpl"}
