<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Forgot password</title>
    <meta name="description" content="">
    {include file="_appheader.tpl"}
  </head>
  <body class="pledge">


<h1>Reset Your Password</h1>

{include file="_appusermessage.tpl"}

{if !isset($error_msg) && !isset($success_msg)}
<form name="form1" method="post" action="" class="login form-horizontal">

    <fieldset style="background-color : white; padding-top : 30px;">
        <div class="control-group">
        <label class="control-label" for="password">New Password</label>
        <div class="controls">
            <span class="input-prepend">
                <span class="add-on"><i class="icon-key"></i></span>
                <input type="password" name="password" id="password"
                {literal}pattern="^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$"{/literal} class="password" required
                data-validation-required-message="<i class='icon-exclamation-sign'></i> You'll need a enter a password of at least 8 characters."
                data-validation-pattern-message="<i class='icon-exclamation-sign'></i> Must be at least 8 characters, with both numbers & letters.">
            </span>

            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="confirm_password">Confirm&nbsp;new Password</label>
            <div class="controls">
                <span class="input-prepend">
                    <span class="add-on"><i class="icon-key"></i></span>
                    <input type="password" name="password_confirm" id="confirm_password" required
                     class="password"
                    data-validation-required-message="<i class='icon-exclamation-sign'></i> Password confirmation is required."
                    data-validation-match-match="password"
                    data-validation-match-message="<i class='icon-exclamation-sign'></i> Make sure this matches the password you entered above." >
                </span>
                <span class="help-block"></span>
                {include file="_usermessage.tpl" field="password"}
            </div>
        </div>

        <div class="form-actions">
                <input type="submit" id="login-save" name="Submit" class="btn btn-primary" value="Submit">
                <span class="pull-right">
                    <div class="btn-group">
                        <a href="index.php" class="btn btn-mini">Log In</a>
                    </div>
                </span>
        </div>

    </fieldset>

</form>
{/if}

</body>
</html>
