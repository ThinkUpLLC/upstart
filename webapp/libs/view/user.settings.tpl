{include file="_appheader.v2.tpl" include_menu=true
body_classes="settings menu-open" body_id="settings-main"}

  <div class="container">
    <header>
      <h1>Settings</h1>
    </header>

        <form role="form" class="form-horizontal" id="form-settings">
          <fieldset class="fieldset-personal">
            <header>
              <h2>Personal</h2>
            </header>
            <div class="form-group">
              <label class="control-label" for="control-email">Email</label>
              <input type="email" class="form-control" id="control-email"value="{$subscriber->email}">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-name">Name</label>
              <input type="text" class="form-control" id="control-name" value="{$subscriber->full_name}">
            </div>
            <div class="form-group">
              {* The jstz javascript is in the footer, added via a boolean variable *}
              <label class="control-label" for="control-timezone">Time Zone</label>
              <div class="form-control picker">
              <i class="fa fa-chevron-down icon"></i>
              <select id="control-timezone">
                <option value="">Select a timezone</option>
              </select>
            </div>

          </fieldset>

          <fieldset class="fieldset-password">
            <header>
              <h2>Change Password</h2>
            </header>
            <div class="form-group">
              <label class="control-label" for="control-password-current">Current</label>
              <input type="password" class="form-control" id="control-password-current">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-password-new">New</label>
              <input type="password" class="form-control" id="control-password-new">
            </div>
            <div class="form-group">
              <label class="control-label" for="control-password-verify">Veryify New</label>
              <input type="password" class="form-control" id="control-password-verify">
            </div>
          </fieldset>

{* We're not going to have a privacy setting at launch
          <fieldset class="fieldset-privacy">
            <header>
              <h2>Privacy</h2>
              <div class="help-text">Who can view your stream and shared insights?</div>
            </header>
            <div class="form-group form-group-toggle">
              <input type="checkbox" id="control-privacy" checked>
              <label class="control-label" for="control-privacy">Anyone with a link can view</label>
            </div>
          </fieldset>
*}
          <input type="submit" value="Done" class="btn btn-circle btn-submit">
        </form>

{include file="_appfooter.v2.tpl" include_tz_js=true}