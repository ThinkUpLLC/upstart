{include file="_appheader.tpl"
body_classes="settings account menu-off"}

  <div class="container">
    <header class="container-header">
      <h1>Reset your password</h1>
      <h2>You'll get a password reset email.</h2>
    </header>

    <form action="{$site_root_path}user/forgot.php" method="POST" role="form" id="form-forgot-password">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
          <label class="control-label" for="control-email">Email</label>
          <input name="email" type="email" class="form-control"
          id="control-email" placeholder="you@example.com">
        </div>
      </fieldset>

      <input type="submit" name="Submit" value="Send" class="btn btn-circle btn-submit">

      <p class="form-note"><a href="{$site_root_path}user/">Back to login</a></p>
    </form>

{include file="_appfooter.tpl"}