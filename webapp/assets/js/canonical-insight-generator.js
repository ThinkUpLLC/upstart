// Generated by IcedCoffeeScript 1.4.0a
(function() {
  var $lastActiveDateGroup, buildInsight, canonical_insights, featureTest, genInsightTemplate, genTweetTemplate, insightTypes, loopThroughInsights, navbarAnimate, pinDateMarker, setActiveDateGroup, setDateGroupData, setListOpenData;

  genInsightTemplate = function(insight) {
    return "<div class=\"panel panel-default insight " + insight.classes + "\" id=\"" + insight.id + "\">\n  <div class=\"panel-heading " + insight.heading_classes + "\">\n    <h2 class=\"panel-title\">" + insight.title + "</h2>\n    " + insight.subtitle + "\n    " + insight.header_graphic + "\n  </div>\n  <div class=\"panel-desktop-right\">\n    <div class=\"panel-body\">\n      " + insight.hero_image + "\n      " + insight.body + "\n    </div>\n    <div class=\"panel-footer\">\n      <div class=\"insight-metadata\">\n        <i class=\"fa fa-" + insight.network_icon + " icon icon-network\"></i>\n        <a class=\"permalink\" href=\"#\">" + insight.date + "</a>\n      </div>\n      <div class=\"share-menu\">\n        <a class=\"share-button-open\" href=\"#\"><i class=\"fa fa-share-square-o icon icon-share\"></i></a>\n        <ul class=\"share-services\">\n          <li class=\"share-service\"><a href=\"#\"><i class=\"fa fa-twitter icon icon-share\"></i></a></li>\n          <li class=\"share-service\"><a href=\"#\"><i class=\"fa fa-facebook icon icon-share\"></i></a></li>\n          <li class=\"share-service\"><a href=\"#\"><i class=\"fa fa-google-plus icon icon-share\"></i></a></li>\n          <li class=\"share-service\"><a href=\"#\"><i class=\"fa fa-envelope icon icon-share\"></i></a></li>\n        </ul>\n        <a class=\"share-button-close\" href=\"#\"><i class=\"fa fa-times-circle icon icon-share\"></i></a>\n      </div>\n    </div>\n  </div>\n</div>";
  };

  genTweetTemplate = function(tweet) {
    return "<blockquote class=\"tweet " + tweet.classes + "\">\n  <img src=\"" + tweet.user.profile_image_url + "\" alt=\"" + tweet.user.name + "\" width=\"60\" height=\"60\" class=\"img-circle pull-left tweet-photo\">\n  <div class=\"byline\"><strong>" + tweet.user.name + "</strong> <span class=\"username\">@" + tweet.user.screen_name + "</span></div>\n  <div class=\"tweet-body\">" + tweet.html + "</div>\n  <div class=\"tweet-actions\">\n    <a href=\"#\" class=\"tweet-action\"><i class=\"fa fa-reply icon\"></i></a>\n    <a href=\"#\" class=\"tweet-action\"><i class=\"fa fa-retweet icon\"></i></a>\n    <a href=\"#\" class=\"tweet-action\"><i class=\"fa fa-star icon\"></i></a>\n</blockquote>";
  };

  buildInsight = function(data) {
    var body_content, cd, heading_classes, hg, i, insight_classes, it, td, template_data, tweet, _i, _len, _ref;
    cd = data.content;
    heading_classes = [];
    insight_classes = [];
    td = template_data = {
      title: cd.title,
      network_icon: data.network_icon,
      date: data.date,
      id: data.id
    };
    td.subtitle = cd.subtitle != null ? "<p class=\"panel-subtitle\">" + cd.subtitle + "</p>" : "";
    if (cd.header_graphic != null) {
      hg = cd.header_graphic;
      heading_classes.push("panel-heading-illustrated");
      td.header_graphic = "<img src=\"" + hg.asset_url + "\" alt=\"" + hg.alt_text + "\" width=\"50\" height=\"50\" class=\"img-circle userpic userpic-featured\">";
    } else {
      td.header_graphic = "";
    }
    if (cd.body.hero_image != null) {
      td.hero_image = "<img src=\"" + cd.body.hero_image.asset_url + "\" alt=\"" + cd.body.hero_image.alt_text + "\" class=\"img-responsive\">";
    } else {
      td.hero_image = "";
    }
    body_content = "";
    if (cd.body.text != null) {
      body_content += "<p>" + cd.body.text + "</p>";
    } else {
      "";
    }
    if (cd.body.action_button != null) {
      body_content += "<a href=\"" + cd.body.action_button.url + "\" class=\"btn btn-default\">" + cd.body.action_button.text + "</a>";
    }
    if (cd.body.tweets != null) {
      if (insightTypes[data.insight_type].is_list) {
        body_content += "<ul class=\"body-list tweet-list\">";
      }
      _ref = cd.body.tweets;
      for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
        tweet = _ref[i];
        if (insightTypes[data.insight_type].is_list) {
          body_content += "<li class=\"list-item\">";
        }
        tweet.classes = insightTypes[data.insight_type].tweets.include_photo ? "tweet-with-photo" : "";
        body_content += genTweetTemplate(tweet);
        if (insightTypes[data.insight_type].is_list) body_content += "</li>";
      }
      if (insightTypes[data.insight_type].is_list) {
        body_content += "</ul>\n<button class=\"btn btn-default btn-block btn-see-all\" data-text=\"Actually, please hide them\"><span class=\"btn-text\">See all " + cd.body.tweets.length + " tweets</span> <i class=\"fa fa-chevron-down icon\"></i></button>";
      }
    }
    if (body_content.length) {
      td.body = "<div class=\"panel-body-inner\">" + body_content + "</div>";
    } else {
      td.body = "";
    }
    insight_classes.push("insight-" + (data.insight_type.replace("_", "-")));
    if (it = (insightTypes[data.insight_type] != null) && insightTypes[data.insight_type] !== "default") {
      insight_classes.push("insight-" + insightTypes[data.insight_type].theme);
    }
    switch (data.insight_type) {
      case "editorial":
        insight_classes.push("insight-hero");
        insight_classes.push("insight-wide");
        break;
      case "flashback":
      case "favorite_flashback":
        if (cd.body.tweets != null) {
          td.date = "<span class=\"prefix\">From</span> " + cd.body.tweets[0].date;
        }
    }
    td.heading_classes = heading_classes.join(" ");
    td.classes = insight_classes.join(" ");
    return genInsightTemplate(td);
  };

  loopThroughInsights = function(insightsArray, callback) {
    var change_count, date_string, html, i, insight, _i, _len;
    date_string = null;
    change_count = 0;
    html = "";
    for (i = _i = 0, _len = insightsArray.length; _i < _len; i = ++_i) {
      insight = insightsArray[i];
      insight.id = "insight-" + (i + 1);
      if (date_string === null) {
        date_string = insight.date;
        html += "<div class=\"date-group date-group-today\">\n  <div class=\"date-marker\">\n    <div class=\"relative\">Today</div>\n    <div class=\"absolute\">" + insight.date + ", 2013</div>\n  </div>";
        html += buildInsight(insight);
      } else if (date_string === insight.date) {
        html += buildInsight(insight);
      } else {
        date_string = insight.date;
        change_count++;
        html += "</div>\n<div class=\"date-group\">\n  <div class=\"date-marker\">\n    <div class=\"relative\">" + (change_count === 1 ? "Yesterday" : "" + change_count + " days ago") + "</div>\n    <div class=\"absolute\">" + insight.date + ", 2013</div>\n  </div>";
        html += buildInsight(insight);
      }
    }
    html += "</div>";
    $(".stream").append(html);
    return callback();
  };

  canonical_insights = [
    {
      insight_type: "biggest_fan",
      network_icon: "facebook-square",
      date: "Nov 12",
      content: {
        title: "Jennifer is your biggest fan from the past week",
        subtitle: "She liked 23 of your status updates!",
        header_graphic: {
          asset_url: "https://www.thinkup.com/join/assets/img/hilary-mason.jpg",
          alt_text: "Hillary Mason"
        },
        body: {
          hero_image: null,
          text: "Let Jennifer know you appreciate her support.",
          action_button: {
            text: "Send message",
            url: "#"
          }
        }
      }
    }, {
      insight_type: "editorial",
      network_icon: "twitter-square",
      date: "Nov 12",
      content: {
        title: "You tweeted about #snowpocalypse — need to warm up?",
        subtitle: "Your friends @billjones and @vanessegg had the best weather of anyone you talked to yesterday. Maybe ask them to tweet you a picture?",
        body: {
          hero_image: {
            asset_url: "http://distilleryimage0.ak.instagram.com/5603f97068cc11e29ca422000a1fb149_7.jpg",
            alt_text: "A pretty beach"
          }
        }
      }
    }, {
      insight_type: "flashback",
      network_icon: "twitter-square",
      date: "Nov 11",
      content: {
        title: "On this day last year &hellip;",
        subtitle: "This tweet you wrote got 32 favorites",
        body: {
          tweets: [
            {
              user: {
                name: "Matt Jacobs",
                screen_name: "capndesign",
                profile_image_url: "https://pbs.twimg.com/profile_images/14177592/twitter_bigger.jpg"
              },
              html: "I just called in with the <a href=\"https://twitter.com/search?q=%23Halo4Flu\">#Halo4Flu</a>. I feel like it's gonna last all week.",
              date: "Nov 11, 2012"
            }
          ]
        }
      }
    }, {
      insight_type: "all_about_you",
      network_icon: "twitter-square",
      date: "Nov 11",
      content: {
        title: "You mentioned yourself <strong>48 times</strong> in the last week",
        body: {
          text: "That's <strong>48</strong> of @anildash's tweets using the words \"I\", \"me\", \"my\", \"mine\", or \"myself\", 6 more times than the week before."
        }
      }
    }, {
      insight_type: "archived_posts",
      network_icon: "twitter-square",
      date: "Nov 11",
      content: {
        title: "ThinkUp captured 4 days 23 hours 20 minutes 15 seconds of your life.",
        body: {
          text: "ThinkUp has captured over <strong>28,600 tweets</strong> by @anildash, which really adds up if you estimate 15 seconds per tweet."
        }
      }
    }, {
      insight_type: "frequency",
      network_icon: "facebook-square",
      date: "Nov 11",
      content: {
        title: "@anildash posted <strong>108 times</strong> in the past week.",
        body: {
          text: "That's ramping up to 8 more times than the prior week."
        }
      }
    }, {
      insight_type: "favorite_flashback",
      network_icon: "twitter-square",
      date: "Nov 10",
      content: {
        title: "You were quick on the fave trigger",
        subtitle: "On this day last year, you favorited 6 tweets",
        body: {
          tweets: [
            {
              user: {
                name: "Matt Jacobs",
                screen_name: "capndesign",
                profile_image_url: "https://pbs.twimg.com/profile_images/14177592/twitter_bigger.jpg"
              },
              html: "I just called in with the <a href=\"https://twitter.com/search?q=%23Halo4Flu\">#Halo4Flu</a>. I feel like it's gonna last all week.",
              date: "Nov 10, 2012"
            }, {
              user: {
                name: "Matt Jacobs",
                screen_name: "capndesign",
                profile_image_url: "https://pbs.twimg.com/profile_images/14177592/twitter_bigger.jpg"
              },
              html: "Oh boy, I am a second tweet in this list of tweets!",
              date: "Nov 10, 2012"
            }, {
              user: {
                name: "Matt Jacobs",
                screen_name: "capndesign",
                profile_image_url: "https://pbs.twimg.com/profile_images/14177592/twitter_bigger.jpg"
              },
              html: "Many of these public “apologies” read as “I'm sorry you found out I’m an asshole and it ruined my career. Also, my friends like me.”",
              date: "Nov 10, 2012"
            }, {
              user: {
                name: "Matt Jacobs",
                screen_name: "capndesign",
                profile_image_url: "https://pbs.twimg.com/profile_images/14177592/twitter_bigger.jpg"
              },
              html: "“You’re a pretty monkey, and you know where *all* the bananas are.” <a href=\"https://medium.com/p/be7e772b2cb5\">https://medium.com/p/be7e772b2cb5</a>",
              date: "Nov 10, 2012"
            }, {
              user: {
                name: "Matt Jacobs",
                screen_name: "capndesign",
                profile_image_url: "https://pbs.twimg.com/profile_images/14177592/twitter_bigger.jpg"
              },
              html: "I just called in with the <a href=\"https://twitter.com/search?q=%23Halo4Flu\">#Halo4Flu</a>. I feel like it's gonna last all week.",
              date: "Nov 10, 2012"
            }, {
              user: {
                name: "Matt Jacobs",
                screen_name: "capndesign",
                profile_image_url: "https://pbs.twimg.com/profile_images/14177592/twitter_bigger.jpg"
              },
              html: "I just called in with the <a href=\"https://twitter.com/search?q=%23Halo4Flu\">#Halo4Flu</a>. I feel like it's gonna last all week.",
              date: "Nov 10, 2012"
            }
          ]
        }
      }
    }, {
      insight_type: "new_group_memberships",
      network_icon: "twitter-square",
      date: "Nov 10",
      content: {
        title: "Do \"<a href=\"http://twitter.com/jalrobinson/media-tech\">media-tech</a>\", \"<a href=\"http://twitter.com/DunbarProject/design-web-design-2\">design-web-design-2</a>\" and \"<a href=\"http://twitter.com/DunbarProject/marketing-digital-onli-2\">marketing-digital-onli-2</a>\" seem like good descriptions of @anildash?",
        body: {
          text: "Those are the 3 lists @anildash got added to this week, bringing the total to <strong>56 lists</strong>."
        }
      }
    }, {
      insight_type: "response_time",
      network_icon: "twitter-square",
      date: "Nov 10",
      content: {
        title: "@anildash has been getting <strong>1 new favorite</strong> every <strong>10 minutes</strong> on tweets over the last week",
        body: {
          text: "That's faster than the previous week's average of 1 favorite every 11 minutes."
        }
      }
    }, {
      insight_type: "link_prompt",
      network_icon: "twitter-square",
      date: "Nov 10",
      content: {
        title: "@anildash hasn't tweeted a link on twitter in the last 2 days.",
        body: {
          text: "Maybe you've got an interesting link to share with your followers."
        }
      }
    }
  ];

  insightTypes = {
    biggest_fan: {
      theme: "default"
    },
    flashback: {
      theme: "historical",
      tweets: {
        include_photo: false
      }
    },
    editorial: {
      theme: "green"
    },
    all_about_you: {
      theme: "default"
    },
    archived_posts: {
      theme: "purple"
    },
    frequency: {
      theme: "default"
    },
    favorite_flashback: {
      theme: "historical",
      is_list: true,
      tweets: {
        include_photo: true
      }
    }
  };

  canonical_insights = '';

  featureTest = function(property, value, noPrefixes) {
    var el, mStyle, prop;
    prop = property + ':';
    el = document.createElement('test');
    mStyle = el.style;
    if (!noPrefixes) {
      mStyle.cssText = prop + ['-webkit-', '-moz-', '-ms-', '-o-', ''].join(value + ';' + prop) + value + ';';
    } else {
      mStyle.cssText = prop + value;
    }
    return mStyle[property].indexOf(value) !== -1;
  };

  setListOpenData = function(includeClosed, setHeight) {
    if (includeClosed == null) includeClosed = false;
    if (setHeight == null) setHeight = false;
    return $(".body-list").each(function() {
      var $list, listOpenHeight;
      $list = $(this);
      if (includeClosed) {
        $list.height($list.height());
        $list.data("height-closed", $list.outerHeight(true));
        $list.find(".list-item").show();
      }
      listOpenHeight = 0;
      $list.find(".list-item").each(function() {
        return listOpenHeight += $(this).outerHeight(true);
      });
      $list.data("height-open", listOpenHeight);
      if (setHeight && $list.hasClass("all-items-visible")) {
        return $list.height(listOpenHeight);
      }
    });
  };

  setDateGroupData = function() {
    return $(".date-group").each(function(i) {
      $(this).data("scroll-top", $(this).offset().top);
      return $(this).data("scroll-bottom", $(this).offset().top + $(this).height() - $(this).find(".date-marker").height());
    });
  };

  $lastActiveDateGroup = null;

  setActiveDateGroup = function() {
    var anyActive;
    if ($(window).scrollTop() + $(window).height() <= $("body").height()) {
      anyActive = false;
      $(".date-group").each(function(i) {
        var _ref;
        if (($(this).offset().top < (_ref = $(window).scrollTop() + 45) && _ref < $(this).data("scroll-bottom") - 14)) {
          $("#active-group").text;
          anyActive = true;
          if ($lastActiveDateGroup == null) $lastActiveDateGroup = $(this);
          pinDateMarker($(this));
          if (($lastActiveDateGroup != null) && !$(this).is($lastActiveDateGroup)) {
            return $lastActiveDateGroup = $(this);
          }
        } else {
          return $(this).find(".date-marker").removeClass("fixed absolute").css("top", "");
        }
      });
      if (($lastActiveDateGroup != null) && !anyActive) {
        if ($lastActiveDateGroup.data("scroll-top") < $(window).scrollTop()) {
          pinDateMarker($lastActiveDateGroup, "absolute");
        }
        return pinDateMarker($lastActiveDateGroup.prev(), "absolute", false);
      }
    }
  };

  pinDateMarker = function($container, position, clearClasses) {
    var $dm;
    if (position == null) position = "fixed";
    if (clearClasses == null) clearClasses = true;
    if (clearClasses) {
      $(".date-marker").removeClass("fixed absolute").css("top", "");
    }
    $dm = $container.find(".date-marker");
    $dm.addClass(position);
    if (position === "absolute") {
      return $dm.css("top", $container.data("scroll-bottom"));
    }
  };

  navbarAnimate = function(state) {
    var pos;
    pos = state === "open" ? "280px" : "0";
    return $(".navbar-default").animate({
      left: pos
    }, 150);
  };

  $(function() {
    var isjPMon, jPM;
    loopThroughInsights(canonical_insights, function() {
      setListOpenData(true);
      return $(window).load(function() {
        return setDateGroupData();
      });
    });
    isjPMon = false;
    jPM = $.jPanelMenu({
      openPosition: "280px",
      keyboardShortcuts: false,
      beforeOpen: function() {
        return navbarAnimate("open");
      },
      beforeClose: function() {
        return navbarAnimate("close");
      }
    });
    if ((!$("body").hasClass("menu-open")) || $(window).width() < 820) {
      $("#page-content").css({
        minHeight: $(window).height() - 45
      });
      jPM.on();
      isjPMon = true;
    }
    $(window).resize(function() {
      setListOpenData(false, true);
      $("#page-content").css({
        minHeight: $(window).height() - 45
      });
      if ($("body").hasClass("menu-open") && $(window).width() < 820 && (!isjPMon)) {
        jPM.on();
        isjPMon = true;
      }
      if ($("body").hasClass("menu-open") && $(window).width() >= 820 && isjPMon) {
        jPM.off();
        return isjPMon = false;
      }
    });
    if ($("body").hasClass("insight-stream")) {
      if (featureTest("position", "sticky")) {
        $(".date-marker").addClass("sticky");
      } else {
        $(window).scroll(function() {
          return setActiveDateGroup();
        });
      }
    }
    $("body").on("click", ".share-button-open", function(e) {
      var $menu, rightOffset;
      e.preventDefault();
      $menu = $(this).parent();
      $menu.toggleClass("open");
      rightOffset = $menu.parent().outerWidth(true) - $menu.outerWidth(true) + 4;
      return $menu.animate({
        right: rightOffset
      }, 250);
    });
    $("body").on("click", ".share-button-close", function(e) {
      var $menu;
      e.preventDefault();
      $menu = $(this).parent();
      $menu.toggleClass("open");
      return $menu.animate({
        right: "-275px"
      }, 250);
    });
    return $("body").on("click", ".panel-body .btn-see-all", function(e) {
      var $btn, $list, listHeight;
      $btn = $(this);
      $list = $btn.prev(".body-list");
      listHeight = $list.hasClass("all-items-visible") ? $list.data("height-closed") : $list.data("height-open");
      return $list.animate({
        height: listHeight
      }, 250, function() {
        var oldText;
        oldText = $btn.find(".btn-text").text();
        $btn.find(".btn-text").text($btn.data("text"));
        $btn.data("text", oldText);
        $list.toggleClass("all-items-visible");
        return $btn.toggleClass("active");
      });
    });
  });

}).call(this);
