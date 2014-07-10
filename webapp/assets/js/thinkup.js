(function() {
  var $lastActiveDateGroup, animateContentShift, checkEmailAvailability, checkEmailField, checkEmailFormat, checkPasswordField, checkPasswordFormat, checkSettingsPasswordField, checkTermsField, checkUsername, constants, featureTest, focusField, pinDateMarker, positionUsernameHelper, setActiveDateGroup, setDateGroupData, setFixedPadding, setListOpenData, setNavHeight, timerEmail, timerPassword, timerUsername, wt;

  wt = window.tu = {};

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

  setNavHeight = function(fixPadding) {
    var oldHeight;
    if (fixPadding == null) {
      fixPadding = false;
    }
    oldHeight = wt.navHeight;
    if ($(".app-message").length && $("body").hasClass("app-message-visible")) {
      wt.navHeight = $(".app-message").outerHeight(true) + $(".app-message").offset().top;
    } else {
      wt.navHeight = $(".navbar").outerHeight(true);
    }
    if (fixPadding && (oldHeight !== wt.navHeight)) {
      return setFixedPadding();
    }
  };

  setFixedPadding = function() {
    $(".container").css("padding-top", wt.navHeight);
    return $(".date-marker").css("top", wt.navHeight + 14);
  };

  animateContentShift = function(state) {
    var leftPos, pos, selector;
    pos = state === "open" ? "280px" : "0";
    selector = ".navbar-default";
    if ($(".app-message").length && $("body").hasClass("app-message-visible")) {
      selector += ", .app-message";
    }
    $(selector).animate({
      left: pos
    }, 150, function() {
      if (pos === "0") {
        return $(selector).css("left", "");
      }
    });
    if ($(".date-marker.fixed").length) {
      leftPos = $(".date-marker.fixed").offset().left;
      pos = state === "open" ? "" + (leftPos + 280) + "px" : "" + (leftPos - 280) + "px";
      $(".date-marker.fixed").animate({
        left: pos
      }, 150, function() {
        return $(".date-marker.fixed").css("left", "");
      });
    }
    if ($(window).width() <= 540) {
      pos = state === "open" ? "-280px" : "0";
      return $(".btn-submit").animate({
        right: pos
      }, 150);
    }
  };

  wt.appMessage = {
    paddingChange: wt.navHeight - $(".navbar-default").outerHeight(true),
    create: function(message, type) {
      var $el, msgClass;
      if (type == null) {
        type = "info";
      }
      if ($(".app-message").text().trim() !== $('<div/>').html(message).text()) {
        wt.appMessage.destroy();
        msgClass = "content";
        if (type === "warning") {
          msgClass += " fa-override-before fa-exclamation-triangle";
        }
        if (type === "success") {
          msgClass += " fa-override-before fa-check-circle";
        }
        $el = $("<div class=\"app-message app-message-" + type + "\" style=\"display: none\">\n  <div class=\"" + msgClass + "\">" + message + "</div>\n  <a href=\"#\" class=\"app-message-close\"><i class=\"fa fa-times-circle icon\"></i></a>\n</div>");
        $("#page-content").append($el);
        return $(".container").animate({
          paddingTop: "+=" + wt.appMessage.paddingChange
        }, 150, function() {
          $(".app-message").fadeIn(150);
          $("body").addClass("app-message-visible");
          if (!$("body").hasClass("account")) {
            return setNavHeight(true);
          }
        });
      }
    },
    destroy: function() {
      var $appMessage;
      $appMessage = $(".app-message");
      $appMessage.fadeOut(150);
      return $(".container").animate({
        paddingTop: "+=-" + wt.appMessage.paddingChange
      }, 150, function() {
        $appMessage.remove();
        $("body").removeClass("app-message-visible");
        if (!$("body").hasClass("account")) {
          return setNavHeight(true);
        }
      });
    }
  };

  constants = window.tu.constants = {};

  constants.colors = {
    pea: "#9dd767",
    pea_dark: "#5fac1c",
    pea_darker: "#417505",
    salmon: "#fc939e",
    salmon_dark: "#da6070",
    salmon_darker: "#d0374b",
    creamsicle: "#ffbb4e",
    creamsicle_dark: "#ff8f41",
    creamsicle_darker: "#f36400",
    sepia: "#c0bdaf",
    sepia_dark: "#a19f8b",
    sepia_darker: "#8a876f",
    historical: "#c0bdaf",
    historical_dark: "#a19f8b",
    historical_darker: "#8a876f",
    purple: "#b690e2",
    purple_dark: "#8e69c2",
    purple_darker: "#7348b0",
    mint: "#41dab3",
    mint_dark: "#24b98f",
    mint_darker: "#1c8e6e",
    bubblegum: "#f576b5",
    bubblegum_dark: "#b3487c",
    bubblegum_darker: "#8f3963",
    seabreeze: "#44c9d7",
    seabreeze_dark: "#198a9c",
    seabreeze_darker: "#126370",
    dijon: "#e4bf28",
    dijon_dark: "#c59301",
    dijon_darker: "#926d01",
    sandalwood: "#fd8560",
    sandalwood_dark: "#d13a0a",
    sandalwood_darker: "#a02c08",
    caramel: "#dd814b",
    caramel_dark: "#9e5e14",
    caramel_darker: "#71430e"
  };

  setListOpenData = function(includeClosed, setHeight) {
    if (includeClosed == null) {
      includeClosed = false;
    }
    if (setHeight == null) {
      setHeight = false;
    }
    return $(".body-list-show-some").each(function() {
      var $list, listOpenHeight, padding;
      $list = $(this);
      if (includeClosed) {
        $list.height($list.height());
        $list.data("height-closed", $list.outerHeight(true));
        $list.find(".list-item").show();
      }
      if (($list.data("rows") != null) && ($list.data("row-height") != null)) {
        padding = $list.data("row-padding") ? $list.data("row-padding") : 0;
        listOpenHeight = ($list.data("rows") * $list.data("row-height")) + (($list.data("rows") - 1) * padding);
      } else {
        listOpenHeight = 0;
        $list.find(".list-item").each(function() {
          return listOpenHeight += $(this).outerHeight(true);
        });
      }
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
        if (($(this).offset().top < (_ref = $(window).scrollTop() + wt.navHeight) && _ref < $(this).data("scroll-bottom") - 14)) {
          anyActive = true;
          if ($lastActiveDateGroup == null) {
            $lastActiveDateGroup = $(this);
          }
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
    if (position == null) {
      position = "fixed";
    }
    if (clearClasses == null) {
      clearClasses = true;
    }
    if (clearClasses) {
      $(".date-marker").removeClass("fixed absolute").css("top", "");
    }
    $dm = $container.find(".date-marker");
    $dm.addClass(position);
    if (position === "absolute") {
      return $dm.css("top", $container.data("scroll-bottom"));
    }
  };

  timerUsername = null;

  checkUsername = function($el) {
    if ($el.val() !== "") {
      if (timerUsername) {
        clearTimeout(timerUsername);
      }
      return timerUsername = setTimeout(function() {
        var $group, _ref;
        $group = $el.parents(".form-group");
        if (((_ref = $el.val().match(/^[\w]{3,15}$/gi)) != null ? _ref.length : void 0) !== 1) {
          $group.removeClass("form-group-ok").addClass("form-group-warning");
          return wt.appMessage.create("Your username must be between 3 - 15 unaccented numbers or letters.", "warning");
        } else {
          return $.getJSON("user/check.php?un=" + (encodeURIComponent($el.val())), function(data) {
            if (!data.available) {
              $group.removeClass("form-group-ok").addClass("form-group-warning");
              return wt.appMessage.create("Sorry, someone already grabbed that name. Please try again.", "warning");
            } else {
              $group.addClass("form-group-ok").removeClass("form-group-warning");
              $group.find(".help-block").remove();
              return wt.appMessage.destroy();
            }
          });
        }
      }, 500);
    }
  };

  positionUsernameHelper = function($input) {
    var $ig, $uh, $ul, gWidth, tWidth;
    $ul = $("#username-length");
    $ig = $input.parents(".input-with-domain");
    $uh = $ig.find(".domain");
    $ul.text($input.val());
    gWidth = $ig.width();
    tWidth = $ul.width();
    if (tWidth + 15 + 110 < gWidth && $ul.text().length) {
      console.log("" + ($ul.text()) + ": " + tWidth);
      return $uh.css("left", tWidth + 15);
    } else {
      return $uh.css("left", "6.95em");
    }
  };

  checkEmailAvailability = function(email, cb) {
    var isGood;
    isGood = false;
    if ((email != null) && email !== "") {
      return $.getJSON("user/check-email.php?em=" + (encodeURIComponent(email)), function(data) {
        if (data.available) {
          isGood = true;
        }
        return cb(isGood);
      });
    } else {
      return cb(isGood);
    }
  };

  checkPasswordFormat = function(value) {
    return value.match(/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$/gi) != null;
  };

  checkEmailFormat = function(value) {
    return value.match(/^([a-z0-9_\.\+-]+)@([\da-z\.-]+)\.([a-z\.]{2,8})$/) != null;
  };

  timerPassword = null;

  checkPasswordField = function($el) {
    if ($el.val() !== "") {
      if (timerPassword) {
        clearTimeout(timerPassword);
      }
      return timerPassword = setTimeout(function() {
        var $group;
        $group = $el.parents(".form-group");
        if (checkPasswordFormat($el.val())) {
          $group.addClass("form-group-ok").removeClass("form-group-warning");
          $group.find(".help-block").remove();
          return wt.appMessage.destroy();
        } else {
          $group.removeClass("form-group-ok").addClass("form-group-warning");
          return wt.appMessage.create("Your password must be at least 8 characters and contain both numbers &amp; letters.", "warning");
        }
      }, 500);
    }
  };

  checkSettingsPasswordField = function($form, e) {
    $form.find('#control-password-new, #control-password-verify').parent().removeClass('form-group-warning');
    wt.appMessage.destroy();
    if ($form.find("#control-password-current").length) {
      if ($form.find("#control-password-current").val().length !== 0) {
        if ($form.find("#control-password-new").val().length === 0 && $form.find("#control-password-verify").val().length === 0) {
          wt.appMessage.create("You didn't provide a new password in both fields.", "warning");
          $form.find("#control-password-new, #control-password-verify").parent().addClass("form-group-warning");
          return e.preventDefault();
        } else if (!checkPasswordFormat($form.find("#control-password-new").val())) {
          wt.appMessage.create("Your password must be at least 8 characters, contain both numbers &amp; letters, " + "and omit special characters.", "warning");
          $form.find("#control-password-new").parent().addClass("form-group-warning");
          return e.preventDefault();
        } else if ($form.find("#control-password-new").val() !== $form.find("#control-password-verify").val()) {
          e.preventDefault();
          wt.appMessage.create("The passwords must match.", "warning");
          return $form.find("#control-password-new, #control-password-verify").parent().addClass("form-group-warning");
        }
      }
    }
  };

  timerEmail = null;

  checkEmailField = function($el) {
    var val;
    val = $el.val();
    if ($el.val() !== "") {
      if (timerEmail) {
        clearTimeout(timerEmail);
      }
      return timerEmail = setTimeout(function() {
        var $group;
        $group = $el.parents(".form-group");
        if (checkEmailFormat($el.val())) {
          return checkEmailAvailability($el.val(), function(isGood) {
            if (isGood) {
              $group.addClass("form-group-ok").removeClass("form-group-warning");
              $group.find(".help-block").remove();
              return wt.appMessage.destroy();
            } else {
              $group.removeClass("form-group-ok").addClass("form-group-warning");
              return wt.appMessage.create("An existing account is using this email address.", "warning");
            }
          });
        } else {
          $group.removeClass("form-group-ok").addClass("form-group-warning");
          return wt.appMessage.create("Please enter a valid email address.", "warning");
        }
      }, 500);
    }
  };

  checkTermsField = function($el) {
    if ($el.is(":checked")) {
      $el.parent().next(".help-block").remove();
      if ($(".app-message").text().trim() === "Please review and agree to the terms of service.") {
        return wt.appMessage.destroy();
      }
    } else {
      return wt.appMessage.create("Please review and agree to the terms of service.", "warning");
    }
  };

  focusField = function($el_array) {
    var $el, _i, _len, _results;
    _results = [];
    for (_i = 0, _len = $el_array.length; _i < _len; _i++) {
      $el = $el_array[_i];
      if ($el.val() === "" || $el.parents(".form-group").hasClass("form-group-warning")) {
        $el.focus();
        break;
      } else {
        _results.push(void 0);
      }
    }
    return _results;
  };

  $(function() {
    var isjPMon, jPM;
    setListOpenData(true);
    $(window).load(function() {
      return setDateGroupData();
    });
    setNavHeight();
    isjPMon = false;
    jPM = $.jPanelMenu({
      openPosition: "280px",
      keyboardShortcuts: false,
      beforeOpen: function() {
        return animateContentShift("open");
      },
      beforeClose: function() {
        return animateContentShift("close");
      },
      afterOff: function() {
        return $(".app-message, .navbar-default").stop(true, true).css("left", "");
      }
    });
    if ((!$("body").hasClass("menu-open") || $(window).width() < 820) && !$("body").hasClass("menu-off")) {
      $("#page-content").css({
        minHeight: $(window).height() - wt.navHeight
      });
      jPM.on();
      isjPMon = true;
    }
    $(window).resize(function() {
      setListOpenData(false, true);
      setNavHeight(true);
      if (!$("body").hasClass("menu-off") && $("body").hasClass("menu-open") && $(window).width() < 820 && (!isjPMon)) {
        jPM.on();
        isjPMon = true;
      }
      if (!$("body").hasClass("menu-off") && $("body").hasClass("menu-open") && $(window).width() >= 820 && isjPMon) {
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
    $("body").on("click", ".panel-body .btn-see-all", function(e) {
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
    $(window).load(function() {
      if ($("body").data("app-message-text")) {
        wt.appMessage.create($("body").data("app-message-text"), $("body").data("app-message-type"));
      }
      if ((typeof app_message !== "undefined" && app_message !== null) && (app_message.msg != null) && (app_message.type != null)) {
        return wt.appMessage.create(app_message.msg, app_message.type);
      }
    });
    $("body").on("click", "#msg-action", function(e) {
      var $this;
      e.preventDefault();
      $this = $(this);
      if ($this.data("submit-target") != null) {
        return $($this.data("submit-target")).find(".btn-submit").click();
      }
    });
    $("body").on("click", ".app-message .app-message-close", function(e) {
      e.preventDefault();
      return wt.appMessage.destroy();
    });
    if ($("#form-register").length) {
      focusField([$("#email"), $("#username"), $("#pwd")]);
      positionUsernameHelper($("#username"));
      $("#username, #pwd, #email").on("blur", function(e) {
        if ($(this).val().length) {
          return $(this).data("do-validate", "1").keyup();
        }
      });
      $("#username").on("keyup", function() {
        positionUsernameHelper($(this));
        if ($(this).data("do-validate") === "1") {
          return checkUsername($(this));
        }
      });
      $("#pwd").on("keyup", function() {
        if ($(this).data("do-validate") === "1") {
          return checkPasswordField($(this));
        }
      });
      $("#email").on("keyup", function() {
        if ($(this).data("do-validate") === "1") {
          return checkEmailField($(this));
        }
      });
      $("#terms").on("click", function() {
        if ($(this).data("do-validate") === "1") {
          return checkTermsField($(this));
        }
      });
    }
    $("#form-reset").on("submit", function(e) {
      if ($(this).find("#password").val().length === 0 || $(this).find("#password_confirm").val().length === 0) {
        wt.appMessage.create("You must fill in both fields", "warning");
        return e.preventDefault();
      } else if (!checkPasswordFormat($(this).find("#password").val())) {
        wt.appMessage.create("Your password must be at least 8 characters, contain both numbers &amp; letters, " + "and omit special characters.", "warning");
        return e.preventDefault();
      } else if ($(this).find("#password").val() !== $(this).find("#password_confirm").val()) {
        e.preventDefault();
        return wt.appMessage.create("Passwords must match", "warning");
      } else {
        return wt.appMessage.destroy();
      }
    });
    $("#form-settings").on("submit", function(e) {
      return checkSettingsPasswordField($(this), e);
    });
    return $("body").on("click", ".show-section", function(e) {
      var $el;
      $el = $($(this).data("section-selector"));
      if ($el.length) {
        e.preventDefault();
        if ($el.length) {
          return $el.show();
        }
      }
    });
  });

}).call(this);
