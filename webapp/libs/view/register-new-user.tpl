{include file="_appheader.v2.tpl" body_classes="settings menu-off" body_id="settings-registration"}
  <div class="container">
    <header class="container-header">
      <h1>Welcome{if isset($network_username)}, {$network_username}{/if}!</h1>
      <h2>Now we need a few personal details. If a field is empty, please fill it in. If it’s not, please check to make sure it’s correct.</h2>
    </header>

    <form method="POST" class="form-horizontal" id="form-signin" action="register.php{if isset($smarty.get.level)}?level={$smarty.get.level}{/if}">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
        {include file="_appusermessage.tpl" field="email"}
          <label class="control-label" for="email">Email</label>
          <input type="email" name="email" class="form-control" id="email"
          {if isset($email)}value="{$email|filter_xss}"{/if} placeholder="you@example.com">
        </div>
        <div class="form-group">
          {include file="_appusermessage.tpl" field="username"}
          <label class="control-label" for="username">Username</label>
          <input type="text" class="form-control" id="username"
          {if isset($username)}value="{$username|filter_xss}"{/if} name="username" placeholder="catlady99">
        </div>
        <div class="form-group">
          {include file="_appusermessage.tpl" field="password"}
          <label class="control-label" for="pwd">Password</label>
          <input type="password" class="form-control" id="pwd" name="password" value="{if isset($password)}{$password|filter_xss}{/if}" placeholder="********">
        </div>
        <div class="form-group">
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
        </div>
        <div class="form-group form-group-radio">
          {include file="_appusermessage.tpl" field="terms"}
          <label class="control-label" for="terms">I agree to the <a href="https://github.com/ThinkUpLLC/policy">terms of service</a></label>
          <div class="form-control">
            <input type="checkbox" class="radio-control" id="terms" name="terms" value="agreed" {if isset($terms) && $terms eq 'agreed'}checked="true"{/if}>
          </div>
        </div>

      </fieldset>

      <input type="Submit" name="Submit" value="Pay with Amazon" class="btn btn-pill btn-submit is-static">
    </form>

{include file="_appfooter.v2.tpl" include_tz_js=true}
