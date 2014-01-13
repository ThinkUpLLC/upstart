{include file="_appheader.v2.tpl"
body_classes="settings account menu-off"}
  
  <div class="container">
    <header>
      <h1>Log in (please)</h1>
    </header>

{if isset($logged_in_user)}
    <form class="form-horizontal">
      <p class="form-note">You’re already logged in as {$logged_in_user}.<br>
      Do you want to <a href="{$site_root_path}user/logout.php">log out</a>?</p>
    </form>
{else}
    <form action="index.php" method="POST" class="form-horizontal" id="form-signin">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
          <label class="control-label" for="email">Email</label>
          <input type="email" name="email" class="form-control" id="email" {if isset($email)}value="{$email|filter_xss}"{/if}>
        </div>
        <div class="form-group">
          <label class="control-label" for="pwd">Password</label>
          <input type="password" class="form-control" id="pwd" name="pwd" value="">
        </div>
      </fieldset>

      <input type="Submit" name="Submit" value="Log In" class="btn btn-circle btn-submit">

      <p class="form-note"><a href="{$site_root_path}user/forgot.php">Forgot Password?</a></p>
    </form>
{/if}

{include file="_appfooter.v2.tpl"}