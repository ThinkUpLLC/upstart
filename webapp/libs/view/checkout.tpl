<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script src="assets/js/vendor/pay-with-amazon.min.js"></script>
    <script type="text/javascript">
    var payWithAmazon = new PayWithAmazon({
        sellerId: 'A3MIF7Z0W3XC2S', //https://sellercentral.amazon.com/gp/pyop/seller/account/settings/user-settings-view.html/ref=ps_pyopiset_dnav_onconfig_
        clientId: 'amzn1.application-oa2-client.f402aa2a27df4dd0bfce7e297b971176', // https://sellercentral.amazon.com/gp/homepage.html
        button: { id: 'pay-with-amazon', type: 'large', color: 'LightGray' },
        wallet: { id: 'wallet', width: 400 },
        consent: { id: 'consent', width: 400 },
      }).on('change', notify);

      function notify (status) {
        console && console.log.apply(console, arguments);

        if (status.id) {
          document.querySelector('#amazon_billing_agreement_id').value = status.id;
        }
      }
    </script>
</head>

<body>
{include file="_usermessage.tpl"}

<div id="pay-with-amazon"></div>
<div id="wallet"></div>
<div id="consent"></div>

<p>Login to Amazon Payments: lpa-test-user1@thinkup.com OR lpa-test-user2@thinkup.com / Password testme</p>

{if $show_form}
<form method="post" action="checkout.php">
    <input id="amazon_billing_agreement_id" name="amazon_billing_agreement_id" value="" hidden='true'>
    <input type="Submit" value="Subscribe" />
</form>
{/if}

</body>
</html>
