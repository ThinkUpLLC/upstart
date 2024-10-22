{include file="_appheader.tpl" body_classes="settings menu-off" body_id="settings-registration"}
  <div class="container">
    <header class="container-header">
      <h1>Hi{if isset($network_username)}{if $network eq 'facebook'},{/if} {$network_username}{/if}!</h1>
      <h2>We just need a few personal details.</h2>
    </header>

    {assign var="missing_fields" value="false"}
    <form method="POST" id="form-register" action="register.php{if isset($smarty.get.level)}?level={$smarty.get.level}{/if}">
      <fieldset class="fieldset-no-header">
        <div class="form-group{if isset($error_msgs.email)} form-group-warning{/if}
          {if isset($email) and $email != "" and !isset($error_msgs.email)} form-group-ok{/if}">
          {capture name="email_message"}{include file="_appusermessage.tpl" field="email"}{/capture}
          {if preg_match('/Please enter an email address/', $smarty.capture.email_message)}
            {assign var="missing_fields" value="true"}{/if}
          {$smarty.capture.email_message}
          <label class="control-label{if isset($email)} with-focus{/if}" for="email">Email</label>
          <input type="email" name="email" class="form-control" id="email"
          {if isset($email)}value="{$email|filter_xss}"{/if}>
        </div>
        <div class="form-group has-addon{if isset($error_msgs.username)} form-group-warning{/if}
          {if isset($username) and $username != "" and !isset($error_msgs.username)} form-group-ok{/if}">
          {capture name="username_message"}{include file="_appusermessage.tpl" field="username"}{/capture}
          {if preg_match('/Please choose your Insights URL/', $smarty.capture.username_message)}
            {assign var="missing_fields" value="true"}{/if}
          {$smarty.capture.username_message}
          <label class="control-label with-focus" for="username">Insights URL</label>
          <div class="input-with-domain">
            <input type="text" class="form-control" id="username" autocomplete="off"
            placeholder="yourusername.thinkup.com" {if isset($username)}value="{$username|filter_xss}"{/if} name="username">
            <span class="domain">.thinkup.com</span>
            <div id="username-length">{if isset($username)}{$username|filter_xss}{/if}</div>
          </div>
        </div>
        <div class="form-group{if isset($error_msgs.password)} form-group-warning{/if}
        {if isset($password) and $password != "" and !isset($error_msgs.password)} form-group-ok{/if}" id="form-group-password">
          {capture name="password_message"}{include file="_appusermessage.tpl" field="password"}{/capture}
          {if preg_match('/Please enter a password/', $smarty.capture.password_message)}
            {assign var="missing_fields" value="true"}{/if}
          {$smarty.capture.password_message}
          <label class="control-label{if isset($password)} with-focus{/if}" for="pwd">Password</label>
          <input type="password" class="form-control" id="pwd" name="password" value="{if isset($password)}{$password|filter_xss}{/if}">
        </div>
        {if $missing_fields eq "true"}{literal}
        <script>
          var app_message = {};
          app_message.msg = "Looks like you missed a spot.";
          app_message.type = "warning";
        </script>
        {/literal}{/if}
        <div class="form-group form-group-select{if isset($error_msgs.timezone)} form-group-warning{/if}">
          {* The jstz javascript is in the footer, added via a boolean variable *}
          <label class="control-label" for="control-timezone">Time zone</label>
          <div class="form-control picker">
            <i class="fa fa-chevron-down icon"></i>
            <select id="control-timezone" name="timezone">
              <option value=""{if isset($current_tz) && $current_tz eq ''} selected{/if}>Select a time zone:</option>
              {foreach from=$tz_list key=group_name item=group}
                <optgroup label="{$group_name}">
                {foreach from=$group item=tz}
                  <option id="tz-{$tz.display}" value='{$tz.val}'{if isset($current_tz) && $current_tz eq $tz.val} selected{/if}>{$tz.display}</option>
                {/foreach}
                </optgroup>
              {/foreach}
            </select>
          </div>
          {include file="_appusermessage.tpl" field="timezone"}
        </div>
        <div class="form-group form-group-terms{if isset($error_msgs.terms)} form-group-warning{/if}">
          <div class="form-control">
            <input type="checkbox" class="radio-control" id="terms" name="terms" value="agreed" {if isset($terms) && $terms eq 'agreed'}checked{/if}>
          </div>
          <label class="control-label" for="terms">I’ll follow the <a href="{$site_root_path}about/terms.php" tabindex="-1" target="_blank">terms of service</a></label>
          {include file="_appusermessage.tpl" field="terms"}
        </div>

      </fieldset>

      <input type="Submit" name="Submit" value="Create Account" class="btn btn-pill btn-submit is-static">
    </form>

<script>
ga('set', 'title', 'Register');
ga('set', 'page', '/register.php{if isset($network_username)}?n={$network}{/if}{if isset($smarty.get.level)}&level={$smarty.get.level}{/if}');
</script>

{literal}
<script src="//platform.twitter.com/oct.js" type="text/javascript"></script>
<script type="text/javascript">twttr.conversion.trackPid('l6g1c', { tw_sale_amount: 0, tw_order_quantity: 0 });</script>
<noscript>
<img height="1" width="1" style="display:none;" alt="" src="https://analytics.twitter.com/i/adsct?txn_id=l6g1c&p_id=Twitter&tw_sale_amount=0&tw_order_quantity=0" />
<img height="1" width="1" style="display:none;" alt="" src="//t.co/i/adsct?txn_id=l6g1c&p_id=Twitter&tw_sale_amount=0&tw_order_quantity=0" />
</noscript>
{/literal}

{include file="_appfooter.tpl" include_tz_js=true}
