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
          <div class="list-group-item-value">{if $subscriber->membership_level eq 'Early Bird' || $subscriber->membership_level eq 'Late Bird'}Member{else}{$subscriber->membership_level}{/if}</div>
        </li>
{*
  Potential values for $membership_status that the template expects:

  * "Free trial" - User is in free trial (either expired or not)
  * "Paid through Mon dd, YYYY" - Successful charge
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

        {if !$subscriber->is_account_closed and $subscriber->subscription_status eq "Paid" and $subscriber->subscription_recurrence neq 'None'}
        <li class="list-group-item" id="list-group-status">
          <div class="list-group-item-label">Subscription</div>
          <div class="list-group-item-value">
          {if $subscriber->subscription_recurrence eq '1 month'}
            {if $subscriber->membership_level eq 'Member'}
              $5 per month
            {elseif $subscriber->membership_level eq 'Pro'}
              $10 per month
            {/if}
             via <a href="https://payments{if $amazon_sandbox}-sandbox{/if}.amazon.com">Amazon Payments</a>
          {elseif $subscriber->subscription_recurrence eq '12 months'}
            {if $subscriber->membership_level eq 'Member'}
              $60 per year via <a href="https://payments{if $amazon_sandbox}-sandbox{/if}.amazon.com">Amazon Payments</a>
            {elseif $subscriber->membership_level eq 'Early Bird' || $subscriber->membership_level eq 'Late Bird'}
              <strike style="color:red"><span style="color:black;">$60</span></strike> $50 per year via <a href="https://payments{if $amazon_sandbox}-sandbox{/if}.amazon.com">Amazon Payments</a>
            {elseif $subscriber->membership_level eq 'Pro'}
              $120 per year via <a href="https://payments{if $amazon_sandbox}-sandbox{/if}.amazon.com">Amazon Payments</a>
            {elseif $subscriber->membership_level eq 'Exec'}
              $996 per year via <a href="https://payments{if $amazon_sandbox}-sandbox{/if}.amazon.com">Amazon Payments</a>
            {/if}
          {/if}
          </div>
        </li>
          {if $subscriber->membership_level eq 'Early Bird' || $subscriber->membership_level eq 'Late Bird'}
          <li class="list-group-item" id="list-group-status">
            <div class="list-group-item-label">Discount</div>
            <div class="list-group-item-value">
              <p style="color:green;">{$subscriber->membership_level} backer: <b>2 months free!</b></p>
            </div>
          </li>
          {/if}
        {/if}

        {if isset($ebook_download_link_pdf)}
        <li class="list-group-item" id="list-group-item-extras">
          <div class="list-group-item-label">Extras</div>
          <div class="list-group-item-value">Download <em>Insights</em> ebook as <a href="{$ebook_download_link_pdf}">PDF</a>, <a href="{$ebook_download_link_kindle}">Kindle</a>, or <a href="{$ebook_download_link_epub}">iBooks</a>
          </div>
        </li>
        {/if}
      </ul>

    {if !$subscriber->is_account_closed}
      {if $membership_status eq 'Free trial'}
        <div class="form-message">
          {$amazon_form}

          <p><a class="alt-to-btn-large" id="btn-claim-code" href="#">Got a coupon code?</a></p>
        </div>

        <form class="form-claim-code hidden inline-submit" method="post" >
          <fieldset class="fieldset-no-header">
            <div class="form-group">
              <label class="control-label no-check" for="claim_code">Enter your code:</label>
              <input type="text" value="" id="claim_code" name="claim_code" class="form-control" placeholder="1234 5678 90AB">
            </div>
          </fieldset>
          <button type="submit" value="Submit code" name="submit" class="btn-submit">Submit</button>
        </form>
      {elseif isset($failed_cc_amazon_form)}
        <div class="form-message">
          <p><small>{$failed_cc_amazon_text}</small></p>
          {$failed_cc_amazon_form}
        </div>
      {/if}
    {/if}

    {if !$subscriber->is_account_closed}
    <button class="btn btn-sm btn-close-account" data-toggle="modal" data-target=".modal-close-account">Close your account</button>

    <div class="modal fade modal-close-account" id="modal-close-account" tabindex="-1" role="dialog" aria-labelledby="closeAccount" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <header class="container-header">
            <h1>Do you really want to close your account?</h1>
            <h2>{if $membership_status neq 'Free trial' && $subscriber->subscription_recurrence eq '1 month'}You will receive a refund and all{else}All{/if} your data will be deleted. This cannot be undone.</h2>
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

      <p class="form-note">Need help? <a href="mailto:help@thinkup.com" class="show-section"
      data-section-selector="#form-membership-contact">Contact us</a></p>

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
