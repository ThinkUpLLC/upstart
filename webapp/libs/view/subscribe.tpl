<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Help launch ThinkUp: Select Your Membership Level</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <link rel="stylesheet" href="{$site_root_path}assets/css/vendor/normalize.min.css">
    <link href='http://fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{$site_root_path}assets/css/main.css">

  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script>window.html5 || document.write('<script src="{$site_root_path}assets/js/vendor/html5shiv.js"><\/script>')</script>
  <![endif]-->
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
                You selected the <strong>{if $level eq 'earlybird'}Early Bird{else}{$level|ucfirst}{/if}</strong> level.<br><a href="{$selected_subscribe_url}">Pay with Amazon</a>
              {else}
                Here are your subscription options&hellip;
              {/if}
            </div>
            <ul class="payment-faq">
              <li>All payments are processed via Amazon Payments.</li>
              <li>We will not charge you anything until 1/15/2014.</li>
            </ul>
          </header>
          
          <div class="level{if $level eq "earlybird"} selected{/if}" id="level-earlybird" data-name="Early Bird"><a href="{$subscribe_earlybird_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Early Bird</h5>
                <div class="backers">{if isset($subscriber_counts[50])}{$subscriber_counts[50]|number_format}{else}0{/if} backers</div>
              </div>
              <div class="level-cost">
                <div class="annually">$50/year</div>
                <div class="monthly">Only {250 - $subscriber_counts[50]} of 250 left!</div>
            </header>

            <div class="level-description">
              <p>Get all the benefits of the <strong class="level-span">Member</strong> level and save 10 bucks! Limited to first 250 backers.</p>
            </div>
          </a></div>

          <div class="level{if $level eq "member"} selected{/if}" id="level-member" data-name="Member"><a href="{$subscribe_member_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Member</h5>
                <div class="backers">{if isset($subscriber_counts[60])}{$subscriber_counts[60]|number_format}{else}0{/if} backers</div>
              </div>
              <div class="level-cost">
                <div class="annually">$60/year</div>
                <div class="monthly">Just 5 bucks a month!</div>
            </header>

            <div class="level-description">
              <p>Join ThinkUp and get all this cool stuff:</p>
              <ul class="level-benefits">
                <li>Insights on your Facebook, Twitter or other social network account. (1 per service)</li>
                <li>First place in line to reserve your username on ThinkUp.</li>
                <li class="book-offer">We wrote you a book! <em>Insights</em> is a series of interviews with dozens of super creative people about the future of social networking.</li>
              </ul>
            </div>
          </a></div>

          <div class="level{if $level eq "pro"} selected{/if}" id="level-pro" data-name="Pro"><a href="{$subscribe_pro_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Pro</h5>
                <div class="backers">{if isset($subscriber_counts[120])}{$subscriber_counts[120]|number_format}{else}0{/if} backers</div>
              </div>
              <div class="level-cost">
                <div class="annually">$120/year</div>
                <div class="monthly">Only $10 a month!</div>
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
                <div class="backers">{if isset($subscriber_counts[996])}{$subscriber_counts[996]|number_format}{else}0{/if} backers</div>
              </div>
              <div class="level-cost">
                <div class="annually">$996/year</div>
                <div class="monthly">Only $83 a month!</div>
            </header>

            <div class="level-description">
              <p>If you're a company, an organization, or want a stake in ThinkUp's future:</p>
              <ul class="level-benefits">
                <li>Get all the benefits of the <strong class="level-span">Member</strong> and <strong class="level-span">Pro</strong> levels.</li>
                <li>Let us know how many social networking accounts you need to support.</li>
                <li>Our company's founders will consult with you before making key decisions about ThinkUp's future. You'll get email updates with the same information we send to our investors or advisers (minus anything that would get us in legal trouble!).</li>
                <li class="book-offer">We'll <strong>personalize</strong> the book we wrote for you. Our founders will call out which part of <em>Insights</em> are most relevant to your work or your goals.</li>
              </ul>
            </div>
          </a></div>
        </div>

      </div><!-- end left column -->
    </div>

    <footer class="page-footer">
      <div class="copyright">&copy;2013 ThinkUp, LLC</div>
      <div class="motto">It is nice to be nice.</div>
    </footer>


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="{$site_root_path}assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
    <script src="{$site_root_path}assets/js/vendor/bootstrap-carousel.min.js"></script>
    <script src="{$site_root_path}assets/js/vendor/jquery.mobile.custom.min.js"></script>
    <script src="{$site_root_path}assets/js/main.js"></script>

    {literal}<script>
      var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
      (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
      g.src='//www.google-analytics.com/ga.js';
      s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>{/literal}
  </body>
</html>