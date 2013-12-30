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

{if isset($logged_in_user)}
Logged in as {$logged_in_user}  [<a href="logout.php">Log out</a>]
{else}
<h1>Log in to Upstart and ThinkUp</h1>

	<form action="index.php" method="POST">
		Email address: <input type="text" name="email" {if isset($email)}value="{$email|filter_xss}"{/if}><br />
		Password: <input type="text" name="pwd" value=""><br/>
		<input type="Submit" name="Submit" value="Log In">
	</form>
{/if}
</body>
</html>
