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

<form name="forgot-form" method="post" action="" class="login form-horizontal">

    <fieldset style="background-color : white; padding-top : 30px;">

    <div class="control-group">
        <label class="control-label" for="site_email">Email&nbsp;Address</label>
        <div class="controls">
            <span class="input-prepend">
                <span class="add-on"><i class="icon-envelope"></i></span>
                <input type="email" name="email" id="email" required
                data-validation-required-message="<i class='icon-exclamation-sign'></i> A valid email address is required.">
            </span>
        </div>
    </div>
    <div class="form-actions">
            <input type="submit" id="login-save" name="Submit" class="btn btn-primary" value="Send Reset">
    </div>

    </fieldset>
</form>

</body>
</html>
