{if $subscriber->is_membership_complimentary eq 0}<a href="subscriber.php?action=comp&id={$subscriber->id}" class="text-success">Comp</a>{else}<a href="subscriber.php?action=decomp&id={$subscriber->id}" class="text-success">Decomp</a>{/if}