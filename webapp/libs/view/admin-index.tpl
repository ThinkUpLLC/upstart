{include file="_adminheader.tpl" page="Subscribers"}

    <div class="container">

<div class="row-fluid">
  <div class="span1"></div>
  <div class="span10">

{if $search_term}
    <h3>Showing search results for <span style="background-color:yellow">{$search_term}</span>. <a href="?p={$page}" class="btn btn-small btn-primary">Show all</a></h3>
{else}
<h2>{$total_daily_signups} signup{if $total_daily_signups neq 1}s{/if}{if $total_daily_payments > 0}, {$total_daily_payments|number_format} conversion{if $total_daily_payments neq 1}s{/if} ({(($total_daily_payments*100)/$total_daily_signups)|round}%){/if}{if $total_daily_refunds > 0}, {$total_daily_refunds|number_format} refund{if $total_daily_refunds neq 1}s{/if}{/if} today.</h2>
<p>{$total_paid_subscribers|number_format} paid members. {$total_active_installs|number_format} active installations.</p>
<p>Stalest paid installation dispatched {$stalest_dispatch_time_paid|relative_datetime} ago, not paid {$stalest_dispatch_not_paid|relative_datetime} ago.</p>
<p {if !$workers_ok} class="alert alert-danger"{/if}>Dispatch status: <b>{$worker_status}</b></p>
{/if}

    <table class="table table-hover" style="background-color:white">
      <tr>
          <th></th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th style="text-align:right">Followers</th>
          <th>Subscribed</th>
          <th>Level</th>
      </tr>
      {foreach $subscribers as $subscriber}
      <tr onclick="document.location = 'subscriber.php?id={$subscriber->id}';" class="{if $subscriber->follower_count > 1000}text-primary{/if} {if $subscriber->is_account_closed}text-danger{/if} ">
        <td style="cursor:pointer"> {if $subscriber->is_verified}<img src="../assets/img/twitter_verified_icon.png" />{/if}</td>
        <td style="cursor:pointer">{include file="_admin-network_user.tpl" link_to_network="false"}</td>
        <td style="cursor:pointer">{$subscriber->email}</th>
        <td style="cursor:pointer">{$subscriber->subscription_status}</th>
        <td style="cursor:pointer;text-align:right">{if $subscriber->follower_count > 0}{$subscriber->follower_count|number_format}{/if}</td>
        <td style="cursor:pointer">{$subscriber->creation_time|relative_datetime} ago</td>
        <td style="cursor:pointer">{$subscriber->membership_level}</td>
      </tr>
      {/foreach}
    </table>

<div class="span1"></div>

<ul class="pager">
  {if $prev_page}<li class="previous"><a href="?p={$prev_page}{if $search_term}&q={$search_term|urlencode}{/if}">&larr; Previous</a></li>{/if}
  {if $next_page}<li class="next"><a href="?p={$next_page}{if $search_term}&q={$search_term|urlencode}{/if}">Next &rarr;</a></li>{/if}
</ul>

</div>
</div>
    </div> <!-- /container -->

  </body>
</html>
