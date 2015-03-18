{if $subscriber->is_via_recurly}<a href="https://thinkup.recurly.com/accounts?q={$subscriber->email|urlencode}" class="btn btn-xs btn-primary pull-right" target="_new">See Recurly account&rarr;</a>{/if}
{if $subscriber->is_membership_complimentary eq 0}<a href="subscriber.php?action=comp&id={$subscriber->id}" class="btn btn-xs btn-success pull-right">Comp</a>{else}<a href="subscriber.php?action=decomp&id={$subscriber->id}" class="btn btn-xs btn-success pull-right">Decomp</a>{/if}
{if $subscriber->membership_level eq "Member"}
<a href="subscriber.php?action=setmembershiplevel&id={$subscriber->id}&level=Pro" class="btn btn-xs btn-success pull-right">Upgrade to Pro</a>
{elseif $subscriber->membership_level eq "Pro"}
<a href="subscriber.php?action=setmembershiplevel&id={$subscriber->id}&level=Member" class="btn btn-xs btn-danger pull-right">Downgrade to Member</a>
{/if}
{if $subscriber->subscription_status neq "Payment due" && $subscriber->subscription_status neq "Free trial"}<a href="subscriber.php?action=due&id={$subscriber->id}" class="btn btn-xs btn-danger pull-right">Due</a>{/if}