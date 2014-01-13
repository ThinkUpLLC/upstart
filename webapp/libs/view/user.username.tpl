{include file="_appheader.v2.tpl" body_classes="settings account menu-off"}

  <div class="container">
{if isset($subscriber->thinkup_username)}
    <header>
      <h1>You picked a username!</h1>
      <h2>Good job. We'll shoot you an email when ThinkUp is ready.<br>
      <a href="{$site_root_path}user/logout.php">Log out</a>
      </h2>
    </header>
{else}
    <header>
      <h1>Pick your username</h1>
      <h2>Think carefully. You only pick this once.</h2>
    </header>

    <form method="POST" role="form" class="form-horizontal" id="form-username" action="{$site_root_path}user/choose-username.php">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
          <label class="control-label" for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username">
        </div>
        <div class="help-block">You’re logged in as {$subscriber->email}. <a href="{$site_root_path}user/logout.php">Log out?</a></div>
      </fieldset>

      <input type="submit" value="Submit" class="btn btn-circle btn-submit">
    </form>
{/if}
{include file="_appfooter.v2.tpl"}