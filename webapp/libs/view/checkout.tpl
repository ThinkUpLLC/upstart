{*
Checkout page.

Parameters:
$context (required) 'signup' or 'membership'
$membership_status (required) 'trial' or not (expired, due, failed)
$state (required) 'pay' or 'success' or 'error' or 'error-fullname'
*}

{include file="_appheader.tpl" body_classes="settings menu-off" body_id="settings-checkout"}

  {if $state eq 'pay' OR $state eq 'error'}
    <script src="assets/js/vendor/pay-with-amazon.min.js"></script>
    <script type="text/javascript">
    var payWithAmazon = new PayWithAmazon({
        sellerId: 'A3MIF7Z0W3XC2S', //https://sellercentral.amazon.com/gp/pyop/seller/account/settings/user-settings-view.html/ref=ps_pyopiset_dnav_onconfig_
        clientId: 'amzn1.application-oa2-client.f402aa2a27df4dd0bfce7e297b971176', // https://sellercentral.amazon.com/gp/homepage.html
        button: { id: 'wallet', type: 'large', color: 'Gold' },
        wallet: { id: 'wallet', width: 400 },
        consent: { id: 'consent', width: 400 },
        production : true
      }).on('change', notify);

      function notify (status) {
        console && console.log.apply(console, arguments);

        if (status.id) {
          document.querySelector('#amazon_billing_agreement_id').value = status.id;
          document.querySelector('#subscribe-btn').style.visibility = 'visible';
        }
      }
    </script>
  {/if}


  <div class="container">

    <header class="container-header">


    {if $state eq 'error'}

        <h1>Whoops, sorry!</h1>

        <h2>There was problem processing your payment. {if $membership_status neq 'trial'}In order to keep your account in good standing, p{else}P{/if}lease try again. If you get stuck, <a href="{$site_root_path}about/contact.php?type=billing">contact us</a>.</h2>
    {elseif $state eq 'error-fullname'}

        <h1>Whoops, sorry!</h1>
        <h2>We'll need your full name to complete the Amazon payment.</h2>

    {elseif $state eq 'success'}

        <h1>Thanks! Your payment is complete.</h1>
        <h2>You are now <em>officially</em> a ThinkUp subscriber.</h2>

    {elseif $state eq 'pay'}

        <h1>Subscribe to ThinkUp today!</h1>
        <h2>It's safe and easy with your Amazon account.</h2>

    {/if}


    </header>


  {if $state eq 'error-fullname'}

    {assign var="missing_fields" value="false"}
    <form method="POST" id="form-fullname" action="">
      <fieldset class="fieldset-no-header">
        <div class="form-group has-addon{if isset($error_msgs.firstname)} form-group-warning{/if}
          {if isset($firstname) and $firstname != "" and !isset($error_msgs.firstname)} form-group-ok{/if}">
          {capture name="firstname_message"}{include file="_appusermessage.tpl" field="firstname"}{/capture}
          {if preg_match('/Please enter your first name/', $smarty.capture.firstname_message)}
            {assign var="missing_fields" value="true"}{/if}
          {$smarty.capture.firstname_message}
          <label class="control-label with-focus" for="firstname">First name</label>
          <input type="text" class="form-control" id="firstname" placeholder="First" name="firstname"
            autofocus="autofocus" autocomplete="firstname" />
        </div>

        <div class="form-group has-addon{if isset($error_msgs.lastname)} form-group-warning{/if}
          {if isset($lastname) and $lastname != "" and !isset($error_msgs.lastname)} form-group-ok{/if}">
          {capture name="lastname_message"}{include file="_appusermessage.tpl" field="lastname"}{/capture}
          {if preg_match('/Please enter your first name/', $smarty.capture.lastname_message)}
            {assign var="missing_fields" value="true"}{/if}
          {$smarty.capture.lastname_message}
          <label class="control-label with-focus" for="lastname">Last name</label>
          <input type="text" class="form-control" id="lastname" placeholder="Last" name="lastname"
            autofocus="autofocus" autocomplete="lastname" />
        </div>

        {if $missing_fields eq "true"}{literal}
        <script>
          var app_message = {};
          app_message.msg = "Looks like you missed a spot.";
          app_message.type = "warning";
        </script>
        {/literal}{/if}

      </fieldset>

      <input type="Submit" name="Submit" value="Let's try that again!" class="btn btn-pill btn-submit is-static">
    </form>

  {elseif $state eq 'success'}

    <div class="pricing">
      <a href="{$user_installation_url}" class="btn btn-pill-large {if $context eq 'signup'}has-note{/if}">
        Go to your ThinkUp
        {if $context eq 'signup'}
        <br />
        <small>Your insights are almost ready</small>
        {/if}
      </a>
    </div>

  {/if}

  {if $state eq 'pay' OR $state eq 'error'}

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

    {if $membership_status eq 'trial'}
      <p class="form-note"><a href="{$user_installation_url}">No thanks, I'll do this later.</a></p>
    {/if}

  {/if}

  </div>


{include file="_appfooter.tpl"}