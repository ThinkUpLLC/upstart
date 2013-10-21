{if isset($success_msg) && $success_msg eq "Hooray! You're now a ThinkUp member!"}
{assign var="confirming_email" value="true"}
{else}
{assign var="confirming_email" value="false"}
{/if}
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Join ThinkUp: Create Your Account</title>
    <meta name="description" content="">
    {include file="_appheader.tpl"}
  </head>
  <body {if $confirming_email eq "true"} class="email-confirmation transactional"{else}class="new-user transactional"{/if}>

    <div class="content-wrapper">
      <div class="left-column">
        <header class="page-header">
          <h1><span class="site-name"><strong>Think</strong>Up</span></h1>
            {if $confirming_email eq "true"}
            <h2>Confirm your email</h2>
            {else}
           <h2>Create your account</h2>
           {/if}
        </header>
        <div class="content">
            {if isset($error_msg)}
                <div class="message warning"><i class="icon icon-warning-sign"></i> {$error_msg}</div>
            {/if}
            {if isset($success_msg)}
                <div class="message success"><i class="icon icon-ok-sign"></i> {$success_msg}</div>
            {/if}

          {if $confirming_email eq "true"}
          <p>We sent you an email to confirm your address. Just click the link in that message and you're all set.</p>

          <div class="spread-the-word">
            <header>In the meantime, please spread the word!</header>
            {include file="_appsharebuttons.tpl"}
          </div>
          {/if}


          {if $do_show_form eq true}
          <div class="form-wrapper">
          <header>You won't be charged until ThinkUp launches in January. To finish creating your account, enter your email, choose a password, and connect to Twitter or Facebook. (You'll be able to add other accounts after you sign up.)</header>

          <form method="post" action="">
            <fieldset class="credentials">
              <div class="field-group">
                {include file="_appusermessage.tpl" field="email"}
                <label for="email">Email</label>
                <input type="email" class="input-text" placeholder="yourname@example.com" name="email" value="{if isset($prefill_email)}{$prefill_email}{/if}">
              </div>
              <div class="field-group">
                {include file="_appusermessage.tpl" field="password"}
                <label for="password">Password</label>
                <input type="password" class="input-text" placeholder="(At least 8 characters)" name="password" value="">
            </fieldset>

            <fieldset class="buttons">
              <button class="button twitter" type="submit" name="n" value="twitter"><i class="icon-twitter"></i> Connect via Twitter</button>
              <button class="button facebook" type="submit" name="n" value="facebook"><i class="icon-facebook"></i> Connect via Facebook</button>
            </fieldset>
          </form>
          </div>
          {/if}

        {if isset($do_show_just_auth_buttons) && $do_show_just_auth_buttons eq true}
          <div class="form-wrapper">
          <form method="post" action="">
            <fieldset class="buttons">
              <button class="button twitter"><a href="{$twitter_auth_link}" style="color:white;text-decoration:none"><i class="icon-twitter"></i> Connect via Twitter</a></button>
              <button class="button facebook"><a href="{$fb_connect_link}" style="color:white;text-decoration:none"><i class="icon-facebook"></i> Connect via Facebook</a></button>
            </fieldset>
          </form>
          </div>
        {/if}
        </div> 

      </div><!-- end left column -->
    </div>

    {include file="_appfooter.tpl"}
  </body>
</html>
