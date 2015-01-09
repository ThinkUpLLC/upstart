{include file="_adminheader.tpl"}
    <div class="container">

<div class="row-fluid">
  <div class="span3"></div>
  <div class="span6">

      {include file="_adminusermessage.tpl"}

      {if $subscriber}

      <div class="panel panel-default panel-primary {if $subscriber->is_account_closed}panel-danger{/if}">
        <div class="panel-heading">
          <h3>{if $subscriber->full_name neq ''}{$subscriber->full_name}{else}[No name]{/if}</h3>
          <h5>{$subscriber->membership_level} since {$subscriber->creation_time} </h5>
        </div>

        <table class="table ">
          <tr>
            <td>Username</td>
            <td>{include file="_admin-thinkup_username.tpl"} {if $install_results}<h5>Install log:</h5><ul>{$install_results}{/if}</td>
          </tr>

          <tr>
            <td>Status</td>
            <td>{$subscriber->subscription_status}{if $subscriber->subscription_status eq "Paid"} through {$subscriber->paid_through_friendly}{/if}{if $subscriber->is_account_closed} - <span class="text-danger">Account closed</span>{/if} {include file="_admin-comp.tpl"}
            </td>
          </tr>
          {if $subscriber->total_payment_reminders_sent > 0}
          <tr>
          <td>Reminders</td>
            <td>
            {$subscriber->total_payment_reminders_sent} sent, last one {$subscriber->payment_reminder_last_sent|relative_datetime} ago
            </td>
          </tr>
          {/if}
          <tr>
            <td>Email</td>
            <td>
              <a href="mailto:{$subscriber->email}">{$subscriber->email}</a> <a href="https://mandrillapp.com/activity?q=full_email:{$subscriber->email|urlencode}" class="btn btn-xs btn-primary pull-right" target="_new">See email activity&rarr;</a><br>
              <form action="subscriber.php?action=updateemail&id={$subscriber->id}" method="get"><input type="email" width="10" value="" placeholder="" name="email"> <input type="hidden" name="id" value="{$subscriber->id}"> <input type="hidden" name="action" value="setemail"><input type="submit" value="Change" class="btn btn-xs btn-default"></form>
            </td>
          </tr>
          {if isset($subscriber->network) && isset($subscriber->network_user_name)}
          <tr>
            <td>{$subscriber->network|ucfirst}</td>
            <td>
              {include file="_admin-network_user.tpl" link_to_network="true"}
              {if $subscriber->follower_count > 0}({$subscriber->follower_count|number_format} followers){/if}
            </td>
          </tr>
          {/if}

          {if $subscriber->last_dispatched}
          <tr>
            <td>Crawled</td>
            <td>
              {$subscriber->last_dispatched|relative_datetime} ago
              {if $subscriber->installation_url neq null}<a href="https://www.thinkup.com/phpyouradmin/index.php?server=2&db=thinkupstart_{$subscriber->thinkup_username}" target="_new" class="btn btn-xs btn-primary pull-right">See database&rarr;</a>{/if}

              <!-- https://www.thinkup.com/dispatch/monitor.php?auth_token=itisnicetobenice104&install_name={$subscriber->thinkup_username} -->
            </td>
          </tr>
          {/if}

        </table>

        <div class="panel-footer">
          <a href="subscriber.php?action=archive&id={$subscriber->id}" class="btn btn-xs btn-danger pull-right" onClick="return confirm('Do you really want to archive this subscriber?');">Archive</a>
          <a href="subscriber.php?action=uninstall&id={$subscriber->id}" class="btn btn-xs btn-danger pull-right" onClick="return confirm('Do you really want to UNINSTALL this subscriber?');">Uninstall</a>

          <h5 class="text-muted">Dangerous Actions</h5>
        </div>
      </div>

  {if isset($claim_code)}
  <div class="panel panel-success">
    <div class="panel-heading">
      <h4>Claim code {$claim_code.readable_code}</h4>
    </div>
    <table class="table table-hover">
    <tr>
      <td>Type</td>
      <td>{$claim_code.type}</td>
    </tr>
    <tr>
      <td>Purchased</td>
      <td>{$claim_code.timestamp|substr:0:10}</th>
    </tr>
    <tr>
      <td>Redeemed</td>
      <td>{$claim_code.redemption_date|substr:0:10}</th>
    </tr>
    <tr>
      <td>Good for</th>
      <td>{$claim_code.number_days} days <span class="text-muted">(expires {$subscriber->paid_through|substr:0:10})</span></td>
    </tr>
    <tr>
      <td>Purchased by</th>
      <td>{$claim_code.buyer_name} <span class="text-muted">{$claim_code.buyer_email}</span></td>
    </tr>
    <tr>
      <td>Amazon Transaction ID</th>
      <td><a href="https://payments{if $is_in_sandbox}-sandbox{/if}.amazon.com/sdui/sdui/txndetail?transactionId={$claim_code.transaction_id}" target="_new">{$claim_code.transaction_id}</a></td>
    </tr>
    </table>
  </div>
  {/if}

{if $subscription_operations}
<div class="panel panel-success">
  <div class="panel-heading">
    <h4>Subscription ID: {$subscription_operations[0]->amazon_subscription_id}<br><br>Amazon email: {$subscription_operations[0]->buyer_email}</h4>
  </div>
  <table class="table table-hover">
    <tr>
      <th>Time</th><th>Operation</th><th>Status Code</th><th>Frequency</th><th>Amount</th>
    </tr>
    {foreach from=$subscription_operations item=operation}
       <tr>
          <td>{$operation->timestamp}</td>
          <td>{$operation->operation}</td>
          <td>{$operation->status_code}<br>{$operation->status_description}</td>
          <td>{$operation->recurring_frequency}</td>
          <td>{$operation->transaction_amount}</td>
       </tr>
    {/foreach}
  </table>
</div>
{/if}

{if $payments}
<div class="panel panel-success">
  <div class="panel-heading">
    <h4>Payments</h4>
  </div>
  <table class="table table-hover">
    <tr>
      <th>Timestamp</th><th>Status</th><th>Amount</th><th>Transaction ID and Message</th>
    </tr>
    {foreach from=$payments item=payment}
       <tr>
          <td>{$payment.timestamp}</td>
          {if $payment.transaction_status}
            <td>{$payment.transaction_status}</td>
            <td>${$payment.amount}</td>
            <td> <a href="https://payments{if $is_in_sandbox}-sandbox{/if}.amazon.com/sdui/sdui/txndetail?transactionId={$payment.transaction_id}" target="_new">{$payment.transaction_id}</a><br/>{if $payment.status_message}{$payment.status_message|filter_xss}{/if}</td>
          {else}
            <td class="text-danger">ERROR</td>
            <td>${$payment.amount}</td>
            <td>{$payment.status_message|filter_xss}</td>
          {/if}
       </tr>
    {/foreach}
  </table>
</div>
{/if}

{if $authorizations}
<div class="panel panel-success">
  <div class="panel-heading">
      <h4>Authorization</h4>
  </div>
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
      {if !$paid && $smarty.now > $authorization->token_validity_start_date_ts & !$subscriber->is_membership_complimentary}
      <!-- Show Charge button -->
      <tr>
        <td></td>
        <td><a href="subscriber.php?id={$subscriber->id}&action=charge&token_id={$authorization->token_id}&amount={$next_annual_charge_amount|urlencode}" class="btn btn-success btn-mini">Charge</a></td>
      </tr>
      {/if}
      {if $subscriber->error_message}
      <tr class="danger">
        <td>Amazon error</td>
        <td>{$subscriber->error_message}</td>
      </tr>
      {/if}
      {/foreach}
    </table>
  </div>
  {/if}

{/if}


{if $install_log_entries}
<div class="panel panel-info">
  <div class="panel-heading">
    <h4>Install Log</h4>
  </div>
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
{/if}
  </div>
  <div class="span3"></div>
</div>


</div>
</body>
</html>
