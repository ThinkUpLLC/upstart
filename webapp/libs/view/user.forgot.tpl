{include file="_userheader.tpl" body_type="settings account menu-off"}
{include file="_usernavigation.tpl"}

    <div class="container">
      <header>
        <h1>Recover your password</h1>
      </header>
 
      <form role="form" class="form-horizontal" id="form-forgot-password">
        <fieldset class="fieldset-no-header">
          <div class="form-group">
            <label class="control-label" for="control-email">Email</label>
            <input type="email" class="form-control" id="control-email">
            <span class="help-block">Be sure to enter the address you used during signup.</span>
          </div>
        </fieldset>
 
        <input type="submit" value="Submit" class="btn btn-circle btn-submit">
 
        <p class="form-note"><a href="{$site_root_path}user/">Back to Login</a></p>
      </form>

{include file="_userfooter.tpl"}