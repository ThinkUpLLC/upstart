{include file="_adminheader.tpl"}
    <div class="container">

<div class="row-fluid">
  <div class="span3"></div>
  <div class="span6">

      {include file="_adminusermessage.tpl"}
      {if $subscriber}
      <h2 style="display: inline-block;">{if $subscriber->full_name neq ''}{$subscriber->full_name}{else}[No name]{/if}</h2>&nbsp;&nbsp;&nbsp;<span>Member since {$subscriber->creation_time}</span>
      <table class="table table-condensed table-hover">
      <tr>
        <td>Email</td>
        <td>{$subscriber->email} {include file="_admin-confirm_email.tpl"}</td>
      </tr>
      <tr>
        <td>{$subscriber->network|ucfirst}</td>
        <td>{include file="_admin-network_user.tpl"}</td>
      </tr>
      <tr>
        <td>Username</td>
        <td>{include file="_admin-thinkup_username.tpl"} {if $install_results}<h5>Install log:</h5><ul>{$install_results}{/if}</td>
      </tr>
      <tr>
        <td></td>
        <td><a href="subscriber.php?action=archive&id={$subscriber->id}" class="btn btn-danger btn-mini" onClick="return confirm('Do you really want to archive this subscriber?');">Archive</a>
        </td>
      </tr>
      </table>
      {if $authorizations}
      <h4>Payment Authorization</h3>
      <table class="table table-condensed table-hover">
      {foreach $authorizations as $authorization}
      <tr>
        <td>Token</td>
        <td><input type="text" width="15" value="{$authorization->token_id}"></td>
      </tr>
      <tr>
        <td>Amount</td>
        <td>${$authorization->amount} every {$authorization->recurrence_period}</td>
      </tr>
      <tr>
        <td>Status</td>
        <td>{$authorization->description}</td>
      </tr>
      <tr>
        <td>Payment method expiry</td>
        <td>{$authorization->payment_method_expiry}</td>
      </tr>
      <tr>
        <td>Valid starting</td>
        <td>{$authorization->token_validity_start_date}</td>
      </tr>
      <tr>
        <td></td>
        <td>{if $smarty.now > $authorization->token_validity_start_date_ts}<a href="charge.php?token_id={$authorization->token_id}&amount={$authorization->amount|urlencode}" class="btn btn-success btn-mini">Charge</a>{/if}</td>
      </tr>
      {if $subscriber->error_message}
      <tr class="danger">
        <td>Amazon error</td>
        <td>{$subscriber->error_message}</td>
      </tr>
      {/if}
      {/foreach}
    </table>
    {/if}
    {/if}
  </div>
  <div class="span3"></div>
</div>

</div>
</body>
</html>
