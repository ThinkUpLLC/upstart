{include file="_userheader.tpl" body_type="settings account menu-off"}
{include file="_usernavigation.tpl"}

{include file="_appusermessage.tpl"}


{if !isset($logged_in_user)}

    <div class="container">
      <header>
        <h1>Log in (please)</h1>
      </header>
 
      <form action="index.php" method="POST" role="form" class="form-horizontal" id="form-signin">
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
 
        <input type="Submit" name="Submit" value="Log in" class="btn btn-circle btn-submit">
 
        <p class="form-note"><a href="{$site_root_path}user/forgot.php">Forgot Password?</a></p>
      </form>

{/if}

{include file="_userfooter.tpl"}