{include file="_appheader.v2.tpl" body_classes="settings menu-off" body_id="settings-registration"}
  <div class="container">
    <header class="container-header">
      <h1>Welcome!</h1>
      <h2>Now we need a few personal details. If a field is empty, please fill it in. If it’s not, please check to make sure it’s correct.</h2>
    </header>

    <form action="" method="POST" class="form-horizontal" id="form-signin">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
          <label class="control-label" for="email">Email</label>
          <input type="email" name="email" class="form-control" id="email"
          {if isset($email)}value="{$email|filter_xss}"{/if} placeholder="you@example.com">
        </div>
        <div class="form-group">
          <label class="control-label" for="username">Username</label>
          <input type="text" class="form-control" id="username"
          {if isset($username)}value="{$username|filter_xss}"{/if} name="username" placeholder="catlady99">
        </div>
        <div class="form-group">
          <label class="control-label" for="pwd">Password</label>
          <input type="password" class="form-control" id="pwd" name="pwd" value=""
          placeholder="********">
        </div>
        <div class="form-group">
          {* The jstz javascript is in the footer, added via a boolean variable *}
          <label class="control-label" for="control-timezone">Time zone</label>
          <div class="form-control picker">
            <i class="fa fa-chevron-down icon"></i>
            <select id="control-timezone" name="timezone">
              <option value=""{if $current_tz eq ''} selected{/if}>Select a time zone:</option>
              {foreach from=$tz_list key=group_name item=group}
                <optgroup label="{$group_name}">
                {foreach from=$group item=tz}
                  <option id="tz-{$tz.display}" value='{$tz.val}'{if $current_tz eq $tz.val} selected{/if}>{$tz.display}</option>
                {/foreach}
                </optgroup>
              {/foreach}
            </select>
          </div>
        </div>
      </fieldset>

      <input type="Submit" name="Submit" value="Save and continue" class="btn btn-pill btn-submit">
    </form>

{include file="_appfooter.v2.tpl" include_tz_js=true}
