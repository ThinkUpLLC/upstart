{include file="_appheader.v2.tpl" body_classes="settings menu-off" body_id="settings-registration"}
  <div class="container">
    <header class="container-header">
      <h1>Welcome{if isset($network_username)}, {$network_username}{/if}!</h1>
      <h2>Now we need a few personal details. If a field is empty, please fill it in. If it’s not, please check to make sure it’s correct.</h2>
    </header>

    <form method="POST" class="form-horizontal" id="form-register" action="register.php{if isset($smarty.get.level)}?level={$smarty.get.level}{/if}">
      <fieldset class="fieldset-no-header">
        <div class="form-group{if isset($error_msgs.email)} form-group-warning{/if}">
          <label class="control-label" for="email">Email</label>
          <input type="email" name="email" class="form-control" id="email"
          {if isset($email)}value="{$email|filter_xss}"{/if} placeholder="you@example.com">
          {include file="_appusermessage.tpl" field="email"}
        </div>
        <div class="form-group{if isset($error_msgs.username)} form-group-warning{/if}">
          <label class="control-label" for="username">Username</label>
          <input type="text" class="form-control" id="username"
          {if isset($username)}value="{$username|filter_xss}"{/if} name="username" placeholder="catlady99">
          {include file="_appusermessage.tpl" field="username"}
        </div>
        <div class="form-group{if isset($error_msgs.password)} form-group-warning{/if}" id="form-group-password">
          <label class="control-label" for="pwd">Password</label>
          <input type="password" class="form-control" id="pwd" name="password" value="{if isset($password)}{$password|filter_xss}{/if}" placeholder="********">
          {include file="_appusermessage.tpl" field="password"}
        </div>
        <div class="form-group{if isset($error_msgs.timezone)} form-group-warning{/if}">
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
        <div class="form-group form-group-radio{if isset($error_msgs.terms)} form-group-warning{/if}">
          <label class="control-label" for="terms">I’ll follow the <a href="https://github.com/ThinkUpLLC/policy">terms of service</a></label>
          <div class="form-control">
            <input type="checkbox" class="radio-control" id="terms" name="terms" value="agreed" {if isset($terms) && $terms eq 'agreed'}checked{/if}>
          </div>
          {include file="_appusermessage.tpl" field="terms"}
        </div>

      </fieldset>

      <input type="Submit" name="Submit" value="Pay with Amazon" class="btn btn-pill btn-submit is-static">
    </form>

{include file="_appfooter.v2.tpl" include_tz_js=true}
