<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Log In to ThinkUp</title>
    <meta name="description" content="">
    {include file="_appheader.tpl"}
  </head>
  <body class="pledge">


{include file="_appusermessage.tpl"}

Logged in as {$logged_in_user}  [<a href="logout.php">Log out</a>]

<h1> Settings go here </h1>

{if isset($thinkup_username)}
<ul>
<li>ThinkUp username: {$thinkup_username} (<a href="{$thinkup_url}">go to ThinkUp</a>)</li>
</ul>
{else}
Choose your ThinkUp username: <input type="text">
{/if}
</body>
</html>
