{include file="_appheader.tpl" include_menu=true
body_classes="settings menu-open" body_id="settings-subscription"}

  <div class="container">
      <header>
        <h1>Membership Info</h1>
        <h2>This is what our database knows.</h2>
      </header>
      <ul class="list-group">
        <li class="list-group-item">
          <div class="list-group-item-label">Username</div>
          <div class="list-group-item-value">{if isset($subscriber->thinkup_username)}
            <a href="{$thinkup_url}">{$subscriber->thinkup_username}</a>{else}<em>None set</em>{/if}</div>
        </li>
        <li class="list-group-item">
          <div class="list-group-item-label">Level</div>
          <div class="list-group-item-value">{$subscriber->membership_level}</div>
        </li>
{*
  Potential values for $membership_status that the template expects:

  * "Free trial" - User is in free trial (either expired or not)
  * "Paid throuh Mon dd, YYYY" - Successful charge
  * "Payment pending" - Authorized and not charged yet, charged and no success returned yet
  * "Payment failed" - Charge failed
  * "Payment due" - User has not attempted payment
  * "Complimentary membership" - Comped
*}
        <li class="list-group-item{if $membership_status eq 'Payment failed' or $membership_status eq 'Payment due'} list-group-item-warning{/if}" id="list-group-status">
          <div class="list-group-item-label">Status</div>
          {if $subscriber->is_account_closed}
            <div class="list-group-item-value">Closed</div>
          {else}
            <div class="list-group-item-value">{$membership_status}{if $membership_status eq 'Free trial'}
            <a href="{$amazon_link}" class="btn btn-default btn-pay">Pay now</a>{/if}</div>
            {if isset($trial_status)}<div class="help-block">{$trial_status}</div>{/if}

          {/if}
        </li>

        {*
          Dear Gina,

          I wrote a really dumb function in the controller so I could test this. I will make it all better
          when you write the *real* function.

          xo,
          Matty
        *}
        {if isset($show_ebook_links) and $show_ebook_links}
        <li class="list-group-item" id="list-group-item-extras">
          <div class="list-group-item-label">Extras</div>
          <div class="list-group-item-value">Download your copy of <em>Insights</em><br><br>
          <a href="//book.thinkup.com/insights.pdf" class="btn btn-default">PDF</a>
          <a href="//book.thinkup.com/insights.kf8" class="btn btn-default">Kindle</a>
          <a href="//book.thinkup.com/insights.epub" class="btn btn-default">ePub</a></div>
        </li>
        {/if}
      </ul>

    {* OMG SO MUCH LOGIC IN THE VIEW :\ :\ :\ *}
    {* I tried to make it A LITTLE better, Gina! -- MBJ *}
    {if !$subscriber->is_account_closed}
      {if isset($failed_cc_amazon_link)}
        <div class="form-message">
          <p>{$failed_cc_amazon_text}</p>
          <a href="{$failed_cc_amazon_link}" class="btn btn-default">Pay via Amazon Payments</a>
        </div>
      {elseif $membership_status eq 'Free trial'}
        <form id="form-membership-close-account" action="membership.php?close=true" method="post">
        <a href="javascript:document.forms['form-membership-close-account'].submit();" onClick="return confirm('Do you really want to close your account?');" class="btn btn-sm btn-transparent btn-close-account">Close your account</a>
         {insert name="csrf_token"}
        </form>
      {else}
        <p class="form-note"><a href="https://payments.amazon.com">View your payment information
          at Amazon Payments.</a></p>
      {/if}
    {else}
        <form id="form-membership-reopen-account" action="membership.php?reopen=true" method="post">
        <p class="form-note"><a href="javascript:document.forms['form-membership-reopen-account'].submit();" class="btn btn-default">Re-open your ThinkUp account</a></p>
        {insert name="csrf_token"}
        </form>
    {/if}

      <p class="form-note">Need help? <a href="mailto:help@thinkup.com" class="show-section"
      {* data-section-selector="#form-membership-contact" *}>Contact us</a></p>

      <form role="form" class="form-horizontal" id="form-membership-contact">
        <fieldset>
          <header>
            <h2>What’s your trouble?</h2>
          </header>
          <div class="form-group">
            <label class="control-label" for="control-type">Type of issue</label>
            <div class="form-control picker">
              <i class="fa fa-chevron-down icon"></i>
              <select id="control-type">
                <option value="">Choose&hellip;</option>
                <option value="level">Change membership level</option>
                <option value="billing">Billing issue</option>
                <option value="cancel">Cancel my membership</option>
                <option value="other">Something else</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label" for="control-body">More info</label>
            <textarea class="form-control" id="control-body"></textarea>
          </div>
        </fieldset>

        <input type="submit" value="Send it" class="btn btn-circle btn-submit">
      </form>

{include file="_appfooter.tpl"}