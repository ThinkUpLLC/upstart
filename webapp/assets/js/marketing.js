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
      $("#section-header").append($el);
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
