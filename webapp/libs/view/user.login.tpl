{include file="_appheader.tpl"
body_classes="settings account menu-off"}
  <div class="container">
    <header class="container-header">
      <h1>Welcome!</h1>
      <h2>Please log in.</h2>
    </header>

    <form action="index.php{if isset($usr) && isset($smarty.get.code)}?usr={$usr}&code={$smarty.get.code}{/if}" method="POST" id="form-signin">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
          <label class="control-label" for="email">Email</label>
          <input type="email" name="email" class="form-control" id="email"
          {if isset($email)}value="{$email|filter_xss}"{/if} placeholder="you@example.com">
        </div>
        <div class="form-group">
          <label class="control-label" for="pwd">Password</label>
          <input type="password" class="form-control" id="pwd" name="pwd" value=""
          placeholder="********">
        </div>
        {if isset($redirect)}
        <input type="hidden" name="redirect" value="{$redirect}">
        {/if}
      </fieldset>

      <input type="Submit" name="Submit" value="Log In" class="btn btn-circle btn-submit">

      <p class="form-note"><a href="{$site_root_path}user/forgot.php">Forgot your password?</a></p>
    </form>

{include file="_appfooter.tpl"}