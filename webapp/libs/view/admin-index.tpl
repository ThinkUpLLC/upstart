{include file="_adminheader.tpl" page="Subscribers"}

    <div class="container">

<div class="row-fluid">
  <div class="span1"></div>
  <div class="span10">

{if $search_term}
    <h3>Showing search results for <span style="background-color:yellow">{$search_term}</span>. <a href="?p={$page}" class="btn btn-small btn-primary">Show all</a></h3>
{else}
<h2>{$total_daily_signups} signup{if $total_daily_signups neq 1}s{/if}{if $total_new_subscribers > 0}, {$total_new_subscribers|number_format} conversion{if $total_new_subscribers neq 1}s{/if} ({(($total_new_subscribers*100)/$total_daily_signups)|round}%){/if}{if $total_reups > 0}, {$total_reups|number_format} re-up{if $total_reups neq 1}s{/if}{/if}{if $total_daily_refunds > 0}, {$total_daily_refunds|number_format} refund{if $total_daily_refunds neq 1}s{/if}{/if}, ${$total_daily_revenue} today.{if $reups_due > 0} {$reups_due|number_format} more re-up{if $reups_due neq 1}s{/if} due today.{/if}</h2>
<p>{$total_paid_subscribers|number_format} paid members: {$total_paid_subscribers_monthly|number_format} monthly, {$total_paid_subscribers_annual|number_format} annual. {$total_active_installs|number_format} active installations.</p>
<p>Stalest paid installation dispatched {$stalest_dispatch_time_paid|relative_datetime} ago, not paid {$stalest_dispatch_not_paid|relative_datetime} ago.</p>
<p {if !$workers_ok} class="alert alert-danger"{/if}>Dispatch status: <b>{$worker_status}</b></p>
{/if}

<div class="table-responsive">
    <table class="table table-hover" style="background-color:white">
      <tr>
          <th></th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th style="text-align:right">Followers</th>
          <th>Subscribed</th>
          <th>Level</th>
          <th>&nbsp;</th>
      </tr>
      {foreach $subscribers as $subscriber}
      <tr onclick="document.location = 'subscriber.php?id={$subscriber->id}';" class="{if $subscriber->follower_count > 1000}text-primary{/if} {if $subscriber->is_account_closed}text-danger{/if} {if $subscriber->subscription_status eq "Paid"}success{/if} ">
        <td style="cursor:pointer"> {if $subscriber->is_verified}<img src="../assets/img/twitter_verified_icon.png" />{/if}</td>
        <td style="cursor:pointer">{if $subscriber->follower_count > 1000}{include file="_admin-network_user.tpl" link_to_network="true"}{else}{include file="_admin-network_user.tpl" link_to_network="false"}{/if}</td>
        <td style="cursor:pointer">{$subscriber->email}</td>
        <td style="cursor:pointer">{$subscriber->subscription_status}{if $subscriber->subscription_status eq "Paid"} through {$subscriber->paid_through_friendly}{/if}</td>
        <td style="cursor:pointer;text-align:right">{if $subscriber->follower_count > 0}{$subscriber->follower_count|number_format}{/if}</td>
        <td style="cursor:pointer">{$subscriber->creation_time|relative_datetime} ago</td>
        <td style="cursor:pointer">{$subscriber->membership_level}</td>
        <td>{if $subscriber->installation_url neq null} {* show link to installation *}
<a href="{$subscriber->installation_url}" target="_new">{$subscriber->thinkup_username}</a>  <a href="{$subscriber->installation_url}/api/v1/session/login.php?u={$subscriber->email|urlencode}&k={$subscriber->api_key_private}&success_redir={$subscriber->installation_url|urlencode}&failure_redir=https%3A%2F%2Fwww.thinkup.com%2Fjoin%2F%2Fjoin%2F" class="btn btn-xs btn-warning pull-right">Be &rarr;</a>{/if}</td>
      </tr>
      {/foreach}
    </table>
</div>

<ul class="pager">
  {if $prev_page}<li class="previous"><a href="?p={$prev_page}{if $search_term}&q={$search_term|urlencode}{/if}">&larr; Previous</a></li>{/if}
  {if $next_page}<li class="next"><a href="?p={$next_page}{if $search_term}&q={$search_term|urlencode}{/if}">Next &rarr;</a></li>{/if}
</ul>

</div>
</div>
    </div> <!-- /container -->

  </body>
</html>
