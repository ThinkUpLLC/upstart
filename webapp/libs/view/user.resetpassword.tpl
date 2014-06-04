{include file="_appheader.tpl"
body_classes="settings account menu-off"}

  <div class="container">
    <header>
      <h1>Pick a new password</h1>
    </header>

<form action="" method="POST" class="form-horizontal" id="form-reset">
  <fieldset class="fieldset-no-header">
    <div class="form-group">
        <label class="control-label" for="password">New</label>
        <input type="password" class="form-control" id="password"
        name="password" value="" placeholder="********">
    </div>
    <div class="form-group">
        <label class="control-label" for="password_confirm">Confirm</label>
        <input type="password" class="form-control" id="password_confirm"
        name="password_confirm" value="" placeholder="********">
    </div>
  </fieldset>

  <input type="Submit" name="Submit" value="Submit" class="btn btn-circle btn-submit">

  <p class="form-note"><a href="{$site_root_path}user/">Back to Login</a></p>
</form>

{include file="_appfooter.tpl"}