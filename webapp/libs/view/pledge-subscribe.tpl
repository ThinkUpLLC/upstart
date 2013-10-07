<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Help launch ThinkUp: Select Your Pledge Level</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <link rel="stylesheet" href="{$site_root_path}assets/css/vendor/normalize.min.css">
    <link href='http://fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{$site_root_path}assets/css/main.css">

  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
  <![endif]-->
  </head>
  <body class="pledge">

    <div class="content-wrapper">
      <div class="left-column">
        <header class="page-header">
          <h1>
            Thanks for supporting
            <span class="site-name"><strong>Think</strong>Up</span>!
          </h1>
        </header>

        <div class="funding-levels">
          <header class="funding-levels-header"><span class="wide-line-one">Choose from</span> one of these subscription options&hellip;</header>
          <div class="level" id="level-member"><a href="{$subscribe_member_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Member</h5>
                <div class="backers">{$total_member_subscribers} backers</div>
              </div>
              <div class="level-cost">
                <div class="monthly">$5/month</div>
                <div class="annually">paid as $60 annually</div>
            </header>

            <div class="level-description">
              <p>Subscribe to ThinkUp and get some incredible benefits:</p>
              <ul class="level-benefits">
                <li>Insights on your use of Facebook, Twitter, Instagram, Google+ or any other supported service, on all of your devices</li>
                <li>Invites to bring your friends in as soon as ThinkUp.com launches to the public</li>
                <li class="book-offer">A copy of <em>Insights</em>, our exclusive e-book about what matters in social networking, based on interviews with dozens of the most influential and creative people online.</li>
              </ul>
            </div>
          </a></div>

          <div class="level" id="level-developer"><a href="{$subscribe_developer_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Developer</h5>
                <div class="backers">{$total_developer_subscribers|number_format} backers</div>
              </div>
              <div class="level-cost">
                <div class="monthly">$10/month</div>
                <div class="annually">paid as $120 annually</div>
            </header>

            <div class="level-description">
              <p>For developers, designers, and serious social networking users:</p>
              <ul class="level-benefits">
                <li>All the benefits of the Member level</li>
                <li>Support for multiple Facebook, Twitter or other social networking accounts</li>
                <li>First access to new Beta features as they’re developed</li>
                <li>Access to new APIs and data services for building apps around ThinkUp</li>
                <li>Weigh in on which new features we prioritize for ThinkUp</li>
              </ul>
            </div>
          </a></div>

          <div class="level" id="level-executive"><a href="{$subscribe_executive_url}">
            <header class="level-header">
              <div class="level-name">
                <h5>Executive</h5>
                <div class="backers">{$total_executive_subscribers|number_format} backers</div>
              </div>
              <div class="level-cost">
                <div class="monthly">$83/month</div>
                <div class="annually">paid as $996 annually</div>
            </header>

            <div class="level-description">
              <p>For businesses and institutions, and people who want to be directly involved in ThinkUp’s evolution:</p>
              <ul class="level-benefits">
                <li>All the benefits of the Member and Pro levels</li>
                <li>Support for any reasonable number of social networking accounts</li>
                <li>Get emails directly from our company founders about the company’s progress, decisions in progress, and the same updates we send to investors or advisors (minus anything that’d get us in trouble legally)</li>
                <li class="book-offer">A <strong>personalized</strong> copy of the <em>Insights</em> book, where our founders have called out which parts are most relevant to your work or your goals
</li>
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
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
    <script src="js/vendor/bootstrap-carousel.min.js"></script>
    <script src="js/vendor/jquery.mobile.custom.min.js"></script>
    <script src="js/main.js"></script>

    <script>
      var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
      {literal}
      (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
      g.src='//www.google-analytics.com/ga.js';
      s.parentNode.insertBefore(g,s)}(document,'script'));
      {/literal}
    </script>
  </body>
</html>