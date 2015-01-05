{if $link_to_network eq "true"}
{if $subscriber->network eq 'twitter'}<a href="https://twitter.com/intent/user?user_id={$subscriber->network_user_id}">@{$subscriber->network_user_name}{/if}{if $subscriber->network eq 'facebook'}<a href="https://facebook.com/{$subscriber->network_user_id}">{$subscriber->full_name}{/if}{if $subscriber->network}</a>{/if}
{else}
{if $subscriber->network eq 'twitter'}@{$subscriber->network_user_name}{/if}{if $subscriber->network eq 'facebook'}{$subscriber->full_name}{/if}
{/if}