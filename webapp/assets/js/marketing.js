(function() {
  var wt;

  wt = window.tu = {};

  $(window).load(function() {
    if ($('.container').outerHeight(true) < $(window).height()) {
      $('body').addClass('is-not-full-height');
    }
    return wt.appMessage.init();
  });

  wt.appMessage = {
    create: function(message, type) {
      var $el, msgClass;
      if (type == null) {
        type = "info";
      }
      wt.appMessage.destroy();
      msgClass = "content";
      if (type === "warning") {
        msgClass += " fa-override-before fa-exclamation-triangle";
      }
      if (type === "success") {
        msgClass += " fa-override-before fa-check-circle";
      }
      $el = $("<div class=\"app-message\" style=\"display: none\">\n  <div class=\"" + msgClass + "\">" + message + "</div>\n  <a href=\"#\" class=\"app-message-close\"><i class=\"fa fa-times-circle icon\"></i></a>\n</div>");
      if ((type != null) && type !== "") {
        $el.addClass("app-message-" + type);
      }
      $("#section-header, #section-navbar").append($el);
      $(".app-message").fadeIn(150);
      return $("body").addClass("app-message-visible");
    },
    destroy: function() {
      $(".app-message").fadeOut(150);
      return $("body").removeClass("app-message-visible");
    },
    init: function() {
      $("body").on("click", ".app-message .app-message-close", function(e) {
        e.preventDefault();
        return wt.appMessage.destroy();
      });
      if ((typeof app_message !== "undefined" && app_message !== null) && (app_message.msg != null) && (app_message.type != null)) {
        return wt.appMessage.create(app_message.msg, app_message.type);
      }
    }
  };

  $(function() {
    $.getJSON("https://api.tumblr.com/v2/blog/thinkupapp.tumblr.com/posts/text?api_key=IQYCrVox6Ltyy4IqbbJWoIM9Czw0WzPGgzKWPg69WEFIa5mTtm&limit=3&callback=?", function(data) {
      var $posts, d, dateStr, mo, post, _i, _len, _ref, _ref1, _results;
      if (data != null ? (_ref = data.response) != null ? _ref.posts.length : void 0 : void 0) {
        mo = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $posts = $("#subsection-blog .blog-posts");
        _ref1 = data.response.posts;
        _results = [];
        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
          post = _ref1[_i];
          d = new Date(post['timestamp'] * 1000);
          dateStr = "" + mo[d.getMonth()] + " " + (d.getDate()) + ", " + (d.getFullYear());
          _results.push($posts.append("<li class=\"blog-post\"><div class=\"date\">" + dateStr + "</div>\n<a href=\"" + post['post_url'] + "\" class=\"permalink\">" + post['title'] + "</a></li>"));
        }
        return _results;
      }
    });
    return $("#form-contact").submit(function(e) {
      var $form, body, subject;
      e.preventDefault();
      $form = $(this);
      subject = $form.find("#control-subject option:selected").text();
      if (subject === "Choose…") {
        subject = "ThinkUp Help";
      }
      body = $form.find("#control-body").val();
      return window.location.href = "mailto:help@thinkup.com?subject=" + (encodeURI(subject)) + "&body=" + (encodeURI(body));
    });
  });

}).call(this);
