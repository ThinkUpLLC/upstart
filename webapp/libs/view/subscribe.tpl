<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Join ThinkUp: Select Your Membership Level</title>
    <meta name="description" content="">
    {include file="_appheader.tpl"}
  </head>
  <body class="pledge">

    <div class="content-wrapper">
      <div class="left-column">
        <header class="page-header">
          <h1>
            Thanks for joining
            <span class="site-name"><strong>Think</strong>Up</span>!
          </h1>
        </header>

        <div class="funding-levels{if $level} level-selected{/if}">
          <header class="funding-levels-header">
            <div class="payment-details">
              {if $level}
                You selected the <strong>{if $level eq 'earlybird'}Late Bird{else}{$level|ucfirst}{/if}</strong> level.<br><a href="{$selected_subscribe_url}">Pay with Amazon</a>
              {else}
                Here are your subscription options&hellip;
              {/if}
            </div>
            <ul class="payment-faq">
              <li>All payments are processed via Amazon Payments.</li>
              <li>We will not charge you anything until 1/15/2014.</li>
            </ul>
          </header>

          <div class="level{if $level eq "member"} selected{/if}" id="level-member" data-name="Member"><a href="{$subscribe_member_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Member</h5>
              </div>
              <div class="level-cost">
                <div class="monthly">$60/year</div>
                <div class="annually">Just 5 bucks a month!</div>
            </header>

            <div class="level-description">
              <p>Join ThinkUp and get:</p>
              <ul class="level-benefits">
                <li>Insights on your Facebook, Twitter or other social network account. (1 per service)</li>
                <li>First place in line to reserve your username on ThinkUp.</li>
              </ul>
            </div>
          </a></div>

          <div class="level{if $level eq "pro"} selected{/if}" id="level-pro" data-name="Pro"><a href="{$subscribe_pro_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Pro</h5>
              </div>
              <div class="level-cost">
                <div class="monthly">$120/year</div>
                <div class="annually">Only $10 a month!</div>
            </header>

            <div class="level-description">
              <p>Folks with multiple social networking accounts and developers get:</p>
              <ul class="level-benefits">
                <li>All the benefits of the <strong class="level-span">Member</strong> level, plus:</li>
                <li>Support for up to <strong>10</strong> social network accounts across all supported services.</li>
                <li>First access to new beta features as they’re developed.</li>
                <li>Access to new APIs and data services for building apps around ThinkUp.</li>
              </ul>
            </div>
          </a></div>

          <div class="level{if $level eq "executive"} selected{/if}" id="level-executive" data-name="Executive"><a href="{$subscribe_executive_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Executive</h5>
              </div>
              <div class="level-cost">
                <div class="monthly">$996/year</div>
                <div class="annually">Only $83 a month!</div>
            </header>

            <div class="level-description">
              <p>If you're a company, an organization, or want a stake in ThinkUp's future:</p>
              <ul class="level-benefits">
                <li>Get all the benefits of the <strong class="level-span">Member</strong> and <strong class="level-span">Pro</strong> levels.</li>
                <li>Let us know how many social networking accounts you need to support.</li>
                <li>Our company's founders will consult with you before making key decisions about ThinkUp's future. You'll get email updates with the same information we send to our investors or advisers (minus anything that would get us in legal trouble!).</li>
              </ul>
            </div>
          </a></div>
        </div>

      </div><!-- end left column -->
    </div>

  {include file="_appfooter.tpl"}
  </body>
</html>