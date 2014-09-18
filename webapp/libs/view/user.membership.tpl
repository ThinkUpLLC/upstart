{include file="_appheader.tpl" include_menu=true
body_classes="settings menu-open" body_id="settings-subscription"}

  <div class="container">
      <header class="container-header">
        <h1>Membership Info</h1>
        <h2>This is what our database knows.</h2>
      </header>

      <ul class="list-group">
        <li class="list-group-item">
          <div class="list-group-item-label">Insights URL</div>
          <div class="list-group-item-value">{if isset({$thinkup_url})}
            <a href="{$thinkup_url}">{$thinkup_url}</a>{else}<em>None set</em>{/if}</div>
        </li>
        <li class="list-group-item">
          <div class="list-group-item-label">Level</div>
          <div class="list-group-item-value">{$subscriber->membership_level}</div>
        </li>
{*
  Potential values for $membership_status that the template expects:

  * "Free trial" - User is in free trial (either expired or not)
  * "Paid throuh Mon dd, YYYY" - Successful charge
  * "Payment failed" - Charge failed
  * "Complimentary membership" - Comped
*}
        <li class="list-group-item{if $membership_status eq 'Payment failed'} list-group-item-warning{/if}" id="list-group-status">
          <div class="list-group-item-label">Status</div>
          {if $subscriber->is_account_closed}
            <div class="list-group-item-value">Closed</div>
          {else}
            <div class="list-group-item-value">{$membership_status}{if isset($trial_status)} that {$trial_status}!{/if}{*<div class="trial-countdown">13<small>days<br>left</small></div>*}</div>
          {/if}
        </li>

        {if isset($ebook_download_link_pdf)}
        <li class="list-group-item" id="list-group-item-extras">
          <div class="list-group-item-label">Extras</div>
          <div class="list-group-item-value">Download the <em>Insights</em> book<br><br>
          <a href="{$ebook_download_link_pdf}" class="btn btn-default btn-with-note">PDF<br><small>8mb</small></a>
          <a href="{$ebook_download_link_kindle}" class="btn btn-default btn-with-note">Kindle<br><small>32mb</small></a>
          <a href="{$ebook_download_link_epub}" class="btn btn-default btn-with-note">iBooks<br><small>22mb</small></a></div>
        </li>
        {/if}
      </ul>

    {if !$subscriber->is_account_closed}
      {if $membership_status eq 'Free trial'}
        <div class="form-message">
          {$amazon_form}
        </div>
      {elseif isset($failed_cc_amazon_form)}
        <div class="form-message">
          <p><small>{$failed_cc_amazon_text}</small></p>
          {$failed_cc_amazon_form}
        </div>
      {else}
        <p class="form-note"><a href="https://payments{if $amazon_sandbox}-sandbox{/if}.amazon.com">View your payment information
          at Amazon Payments.</a></p>
      {/if}
    {/if}

      <p class="form-note">Need help? <a href="mailto:help@thinkup.com" class="show-section"
      data-section-selector="#form-membership-contact">Contact us</a></p>

    {* Don't let annual members close their account; refunds don't work for them *}
    {if !$subscriber->is_account_closed && $subscriber->subscription_recurrence neq '12 months'}
    <button class="btn btn-sm btn-close-account" data-toggle="modal" data-target=".modal-close-account">Close your account</button>

    <div class="modal fade modal-close-account" id="modal-close-account" tabindex="-1" role="dialog" aria-labelledby="closeAccount" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <header class="container-header">
            <h1>Do you really want to close your account?</h1>
            <h2>{if $membership_status neq 'Free trial'}You will receive a refund and all{else}All{/if} your data will be deleted. This cannot be undone.</h2>
          </header>
          <form id="form-membership-close-account" action="membership.php" method="post">
            <input type="hidden" name="close" value="true" />
            <button type="button" class="btn btn-transparent" data-dismiss="modal">Never mind</button>
            <button type="submit" class="btn btn-submit">Close account</button>
             {insert name="csrf_token"}
          </form>
        </div>
      </div>
    </div>
    {/if}

      <form role="form" class="form" id="form-membership-contact">
        <fieldset>
          <header>
            <h2>How can we help you?</h2>
          </header>
          <div class="form-group">
            <label class="control-label no-check" for="control-subject">Type of issue</label>
            <div class="form-control picker">
              <i class="fa fa-chevron-down icon"></i>
              <select id="control-subject">
                <option value="">Choose&hellip;</option>
                <option value="level">Change membership level</option>
                <option value="billing">Billing issue</option>
                <option value="cancel">Cancel my membership</option>
                <option value="other">Something else</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label no-check" for="control-body">More info</label>
            <textarea class="form-control" id="control-body"></textarea>
          </div>
        </fieldset>

        <input type="submit" value="Send it" class="btn btn-circle btn-submit">
      </form>

{include file="_appfooter.tpl"}
