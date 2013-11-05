{include file="_adminheader.tpl"}
    <div class="container">

<div class="row-fluid">
  <div class="span1"></div>
  <div class="span10">
    <div id="logo"><a href="?p=1"><h1>Think<span>Up</span></h1></a></div>
<h2>${$total_authorizations|number_format} pledged by {$total_subscribers|number_format} subscribers</h2>
{if $search_term}
    <h3>Showing search results for <span style="background-color:yellow">{$search_term}</span>. <a href="?p={$page}">Show all.</a></h3>
    {if $subscribers|count eq 0}
        {assign "show_search_form" "true"}
    {/if}
{else}
    {assign "show_search_form" "true"}
{/if}

{if $show_search_form eq "true"}
<form>
<input type="text" name="q" action="index.php" class="form-control" placeholder="Email or name">
<input type="submit" value="Search" class="btn btn-default">
</form>
{/if}

    <table class="table table-hover" style="background-color:white">
      <tr>
          <th></th>
          <th>Name</th>
          <th>Email</th>
          <th>Subscribed</th>
          <th style="text-align:right">Amount</th>
      </tr>
      {foreach $subscribers as $subscriber}
      <tr onclick="document.location = 'subscriber.php?id={$subscriber->subscriber_id}';">
        <td style="cursor:pointer"> {if $subscriber->is_verified}<img src="../assets/img/twitter_verified_icon.png" />{/if}</td>
        <td style="cursor:pointer">{include file="_admin-network_user.tpl"}</td>
        <td style="cursor:pointer">{$subscriber->email} {include file="_admin-confirm_email.tpl"}</th>
        <td style="cursor:pointer">{$subscriber->creation_time}</td>
        <td style="cursor:pointer;text-align:right">${$subscriber->amount}</td>
      </tr>
      {/foreach}
    </table>

<div class="span1"></div>

{if $prev_page}<a href="?p={$prev_page}{if $search_term}&q={$search_term|urlencode}{/if}">&larr; previous</a>{/if} {if $next_page and $prev_page}|{/if} {if $next_page}<a href="?p={$next_page}{if $search_term}&q={$search_term|urlencode}{/if}">next &rarr;</a>{/if}
</div>
</div>
    </div> <!-- /container -->

  </body>
</html>
