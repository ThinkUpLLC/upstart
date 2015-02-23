{include file="_appheader.tpl" body_classes="settings menu-off" body_id="settings-checkout"}

    <script src="assets/js/vendor/pay-with-amazon.min.js"></script>
    <script type="text/javascript">
    var payWithAmazon = new PayWithAmazon({
        sellerId: 'A3MIF7Z0W3XC2S', //https://sellercentral.amazon.com/gp/pyop/seller/account/settings/user-settings-view.html/ref=ps_pyopiset_dnav_onconfig_
        clientId: 'amzn1.application-oa2-client.f402aa2a27df4dd0bfce7e297b971176', // https://sellercentral.amazon.com/gp/homepage.html
        button: { id: 'wallet', type: 'large', color: 'LightGray' },
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
    <header class="container-header">
      <h1>Complete Your Payment</h1>
      <h2>It's safe and easy with your Amazon account.</h2>
    </header>

<form method="post" action="checkout.php">
    <header class="container-header">
      <h2>Select your plan</h2>
      <input type="radio" name="plan" value="{$subscriber->membership_level|strtolower}-monthly" checked>  ${$amount_monthly} per month<br>
      <input type="radio" name="plan" value="{$subscriber->membership_level|strtolower}-yearly">  ${$amount_yearly} per year<br>
    </header>

<div id="wallet"></div>
<div id="consent"></div>

{if $show_form}
    <input id="amazon_billing_agreement_id" name="amazon_billing_agreement_id" value="" hidden="true">
    <input type="Submit" value="Subscribe" id="subscribe-btn" style="visibility:hidden" class="btn-submit" />
{/if}
</form>

<p class="form-note">Login to Amazon Payments:<br>lpa-test-user1@thinkup.com<br>lpa-test-user2@thinkup.com <br> Password testme</p>

</div>

{include file="_appfooter.tpl"}
