{include file="_header.marketing.tpl" marketing_page=true
body_classes="marketing marketing-page" body_id="marketing-about"}

  <div class="container">
    <section class="section section-marketing-text" id="section-about-text">
      <h2 class="section-header">About ThinkUp</h2>
      {include file="_about.nav.tpl" active="about"}

      <p>ThinkUp is a simple new app that gives you daily insights about you and your friends on social networks like Twitter and Facebook. ThinkUp uses plain English to explain how you’re doing and to help you achieve your goals, whatever they are. ThinkUp is a fun way to get more out of the time we spend online.</p>

      <h3 class="text-header">Our Company</h3>

      <div class="headshots">
        <img class="headshot" src="{$site_root_path}assets/img/gina-headshot@2x.jpg" alt="Gina Trapani">
        <img class="headshot" src="{$site_root_path}assets/img/anil-headshot@2x.jpg" alt="Anil Dash">
      </div>

      <p>ThinkUp is also the name of a small company based in New York City. Gina Trapani and Anil Dash cofounded ThinkUp, and are working to make ThinkUp the company that treats its customers and community better than any other in the technology industry. To that end, we’ve published <a href="{$site_root_path}about/values.php">our values</a>, and we'd love to hear your what you think about them.</p>


      <div class="download-our-logo">
        <a href="{$site_root_path}assets/img/thinkup-logo-hires.png"><img src="{$site_root_path}assets/img/thinkup-logo@2x.png" alt="ThinkUp"><br>
          Download our logo</a>
      </div>

      <p>Given our company's goals and values, it’s only natural that the ThinkUp software is open source. ThinkUp’s source code went public in 2009, and since then, has become one of the most popular open source projects on GitHub, currently among the top 15 most active PHP projects. Our community is inclusive and welcoming, and our mailing list has never had a flame war. It really is nice to be nice.</p>

      <h3 class="text-header">Our Investors</h3>

      <p>ThinkUp is backed first and foremost by its customers, who funded our launch with a crowdfunding campaign in late 2013. We also have a number of extraordinary investors, including Bloomberg Beta, Quotidian Ventures, SK Ventures and 500 Startups. Our individual angel advisors are Amol Sarva and Jalak Jobanputra. We’ve <a href="http://blog.thinkup.com/post/64323671907/thank-you-to-everyone-whos-invested-in-our-success">publicly disclosed the structure of our funding</a>, for people who are interested in such things.</p>

      <div class="investor-logos">
        <div class="investor-logos-row">
          <img class="investor-logo is-first" src="{$site_root_path}assets/img/investor-logo-bloomberg.png" alt="Bloomberg Beta">
          <img class="investor-logo" src="{$site_root_path}assets/img/investor-logo-sk-ventures.png" alt="SK Ventures">
        </div>
        <div class="investor-logos-row">
          <img class="investor-logo" src="{$site_root_path}assets/img/investor-logo-500-startups.png" alt="500 Startups">
          <img class="investor-logo" src="{$site_root_path}assets/img/investor-logo-quotidian.png" alt="Quotidian Ventures">
        </div>
      </div>

    </section>

{include file="_footer.marketing.tpl"}