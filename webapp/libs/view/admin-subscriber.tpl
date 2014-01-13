{include file="_adminheader.tpl"}
    <div class="container">

<div class="row-fluid">
  <div class="span3"></div>
  <div class="span6">

      {include file="_adminusermessage.tpl"}
      {if $subscriber}
      <h2 style="display: inline-block;">{if $subscriber->full_name neq ''}{$subscriber->full_name}{else}[No name]{/if}</h2>&nbsp;&nbsp;&nbsp;<span>{$subscriber->membership_level} since {$subscriber->creation_time}</span>
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
      <h4>Authorization</h3>
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
        <td>{if !$paid && $smarty.now > $authorization->token_validity_start_date_ts}<a href="subscriber.php?id={$subscriber->id}&action=charge&token_id={$authorization->token_id}&amount={$authorization->amount|urlencode}" class="btn btn-success btn-mini">Charge</a>{/if}</td>
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

{if $payments}
<div class="row-fluid">
<div class="span3"></div>
  <div class="span6">
  <h4>Payments</h4>
  <table class="table table-condensed table-hover">
    <tr>
      <th>Timestamp</th><th>Status</th><th>Amount</th><th>Transaction ID or Error</th>
    {foreach from=$payments item=payment}
       <tr>
          <td>{$payment.timestamp}</td>
          {if $payment.transaction_status}
            <td>{$payment.transaction_status}</td>
            <td>${$payment.amount}</td>
            <td>{$payment.transaction_id}</td>
          {else}
            <td class="text-danger">ERROR</td>
            <td>${$payment.amount}</td>
            <td>{$payment.error_message|filter_xss}</td>
          {/if}
       </tr>
    {/foreach}
  </table>
  </div>
  <div class="span3"></div>
</div>
{/if}

{if $install_log_entries}
<div class="row-fluid">
<div class="span3"></div>
  <div class="span6">
  <h4>Install Log</h4>
  <table class="table table-condensed table-hover">
    <tr>
      <th>Date</th><th>Message</th>
    {foreach from=$install_log_entries item=entry}
       <tr>
          <td>{$entry.timestamp}</td>
          {if $entry.migration_success eq 1}
            <td>{$entry.migration_message}
          {else}
            <td class="text-danger">{$entry.migration_message}
          {/if}
          <br><br><a href="https://github.com/ginatrapani/ThinkUp/commit/{$entry.commit_hash}">{$entry.commit_hash}</a>
          </td>
          <td></td>
       </tr>
    {/foreach}
  </table>
  </div>
  <div class="span3"></div>
</div>
{/if}

</div>
</body>
</html>
