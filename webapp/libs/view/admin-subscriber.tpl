{include file="_adminheader.tpl"}
    <div class="container">

<div class="row-fluid">
  <div class="span3"></div>
  <div class="span6">

      {include file="_adminusermessage.tpl"}
      {if $subscriber}
      <h2 style="display: inline-block;">{if $subscriber->full_name neq ''}{$subscriber->full_name}{else}[No name]{/if}</h2>&nbsp;&nbsp;&nbsp;<span>{$subscriber->membership_level} since {$subscriber->creation_time} &nbsp;&nbsp;&nbsp; {include file="_admin-comp.tpl"} &#8226; <a href="subscriber.php?action=archive&id={$subscriber->id}" class="text-danger" onClick="return confirm('Do you really want to archive this subscriber?');">Archive</a>
        </span>
      <table class="table table-condensed table-hover">
      <tr>
        <td>Email</td>
        <td>{$subscriber->email} {include file="_admin-confirm_email.tpl"}</td>
      </tr>
      {if isset($subscriber->network) && isset($subscriber->network_user_name)}
      <tr>
        <td>{$subscriber->network|ucfirst}</td>
        <td>{include file="_admin-network_user.tpl"}</td>
      </tr>
      {/if}
      <tr>
        <td>Username</td>
        <td>{include file="_admin-thinkup_username.tpl"} {if $install_results}<h5>Install log:</h5><ul>{$install_results}{/if}</td>
      </tr>
      {if $subscriber->is_membership_complimentary}
      <tr>
        <td>Account status</td>
        <td><span class="text-success">Complimentary membership</span></td>
      </tr>
      {/if}
      {if $subscriber->is_email_verified eq 0 && $subscriber->installation_url neq null}
      <tr>
        <td>Change email</td>
        <td><form action="subscriber.php?action=updateemail&id={$subscriber->id}" method="get"><input type="text" width="10" value="" placeholder="" name="email"> <input type="hidden" name="id" value="{$subscriber->id}"> <input type="hidden" name="action" value="setemail"><input type="submit" value="Save" class="btn btn-default"></form>
        </td>
      </tr>
      {/if}
      </table>
{if $payments}
<br>
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
            <td><a href="https://payments{if $is_in_sandbox}-sandbox{/if}.amazon.com/sdui/sdui/txndetail?transactionId={$payment.transaction_id}" target="_new">{$payment.transaction_id}</a></td>
          {else}
            <td class="text-danger">ERROR</td>
            <td>${$payment.amount}</td>
            <td>{$payment.status_message|filter_xss}</td>
          {/if}
       </tr>
    {/foreach}
  </table>
{/if}

{if $authorizations}
<br >
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
        <td>Payment method expiry</td>
        <td>{$authorization->payment_method_expiry}</td>
      </tr>
      <tr>
        <td>Valid starting</td>
        <td>{$authorization->token_validity_start_date}</td>
      </tr>
      <tr>
        <td></td>
        <td>{if !$paid && $smarty.now > $authorization->token_validity_start_date_ts & !$subscriber->is_membership_complimentary}<a href="subscriber.php?id={$subscriber->id}&action=charge&token_id={$authorization->token_id}&amount={$authorization->amount|urlencode}" class="btn btn-success btn-mini">Charge</a>{/if}</td>
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
{if $install_log_entries}
<br>
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
{/if}
  </div>
  <div class="span3"></div>
</div>


</div>
</body>
</html>
