{include file="_appheader.tpl" body_classes="settings account menu-off"}

  <div class="container">
{if isset($subscriber->thinkup_username)}
    <header class="container-header">
      <h1>You reserved {$subscriber->thinkup_username}.thinkup.com.</h1>
      <h2>You'll get an email shortly when ThinkUp is ready for you.<br>
      <a href="{$site_root_path}user/logout.php">Log out</a>
      </h2>
    </header>
{else}
    <header class="container-header">
      <h1>Pick your username</h1>
      <h2>Choose carefully. You'll only do this once.</h2>
    </header>

    <form method="POST" role="form" id="form-username" action="{$site_root_path}user/choose-username.php">
      <fieldset class="fieldset-no-header">
        <div class="form-group">
          <label class="control-label" for="username">Username</label>
          <input type="text" class="form-control" id="username"
          name="username" placeholder="catlady99">
        </div>
        <div class="help-block">You’re logged in as {$subscriber->email}. <a href="{$site_root_path}user/logout.php">Log out?</a></div>
      </fieldset>

      <input type="submit" value="Gimme" class="btn btn-circle btn-submit">
    </form>
{/if}
{include file="_appfooter.tpl"}