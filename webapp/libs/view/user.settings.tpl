{include file="_appheader.v2.tpl" include_menu=true
body_classes="settings menu-open" body_id="settings-main"}

  <div class="container">
    <header>
      <h1>Settings</h1>
    </header>
    {include file="_usermessage.tpl" field="timezone"}
        <form role="form" id="form-settings" name="form-settings"
        class="form-horizontal" method="post">

          <fieldset class="fieldset-password">
            <header>
              <h2>Change Password</h2>
            </header>
            <div class="form-group">
              <label class="control-label" for="control-password-current">Current</label>
              <input type="password" class="form-control" id="control-password-current" name="current_password">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-password-new">New</label>
              <input type="password" class="form-control" id="control-password-new" name="new_password1">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-password-verify">Verify New</label>
              <input type="password" class="form-control" id="control-password-verify" name="new_password2">
            </div>
          </fieldset>
          <fieldset class="fieldset-personal">
            <header>
            </header>
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
            <div class="form-group">
              <label class="control-label" for="control-timezone">Insights email</label>
              <div class="form-control picker">
              <i class="fa fa-chevron-down icon"></i>
              <select name="control-notification-frequency">
             {foreach from=$notification_options item=description key=key}
                 <option value="{$key}" {if $key eq $owner->email_notification_frequency}selected="selected"{/if}>{$description}</option>
             {/foreach}
             </select>
            </div>
               {insert name="csrf_token"}
          </fieldset>
          <input type="submit" value="Save" name="Done" class="btn btn-circle btn-submit">
        </form>

{include file="_appfooter.v2.tpl" include_tz_js=true show_tz_msg=true}
