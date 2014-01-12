{include file="_appheader.v2.tpl"
body_classes="settings account menu-off"}

  <nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button class="btn menu-trigger">
        <i class="fa fa-bars"></i>
      </button>
      <a class="navbar-brand" href="#"><strong>Think</strong>Up</span></a>
    </div>
  </nav>    

  <div class="container">
    <header>
      <h1>Recover your password</h1>
    </header>

    <form action="{$site_root_path}user/forgot.php" method="POST" role="form" class="form-horizontal" id="form-forgot-password">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
          <label class="control-label" for="control-email">Email</label>
          <input name="email" type="email" class="form-control" id="control-email">
        </div>
      </fieldset>

      <input type="submit" name="Submit" value="Submit" class="btn btn-circle btn-submit">

      <p class="form-note"><a href="{$site_root_path}user/">Back to Login</a></p>
    </form>

{include file="_appfooter.v2.tpl"}