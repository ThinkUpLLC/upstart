wt = window.tu = {}

# As it says, this lets us test if a browser has a particular CSS feature
featureTest = ( property, value, noPrefixes ) ->
  # Thanks Modernizr! https://github.com/phistuck/Modernizr/commit/3fb7217f5f8274e2f11fe6cfeda7cfaf9948a1f5
  prop = property + ':'
  el = document.createElement( 'test' )
  mStyle = el.style

  if !noPrefixes
    mStyle.cssText = prop + [ '-webkit-', '-moz-', '-ms-', '-o-', '' ].join( value + ';' + prop ) + value + ';'
  else
    mStyle.cssText = prop + value

  mStyle[ property ].indexOf( value ) != -1


# This sets the size of the open and closed states of a list
# It's called on page load and whenever the screen size changes
setListOpenData = (includeClosed = false, setHeight = false) ->
  $(".body-list-show-some").each ->
    $list = $(@)
    # We only save height-closed on page load
    if includeClosed
      $list.height $list.height()
      $list.data "height-closed", $list.outerHeight(true)
      $list.find(".list-item").show()
    if $list.data("rows")? and $list.data("row-height")?
      padding = if $list.data("row-padding") then $list.data("row-padding") else 0
      listOpenHeight = ($list.data("rows") * $list.data("row-height")) +
      (($list.data("rows") - 1) * padding)
    else
      listOpenHeight = 0
      $list.find(".list-item").each -> listOpenHeight += $(@).outerHeight(true)
    $list.data "height-open", listOpenHeight
    if setHeight and $list.hasClass "all-items-visible" then $list.height listOpenHeight

# The next two functions make our dates stick to the top on desktop
setDateGroupData = ->
  $(".date-group").each (i) ->
    $(@).data "scroll-top", $(@).offset().top
    $(@).data "scroll-bottom", ($(@).offset().top + $(@).height() - $(@).find(".date-marker").height())

# Keep track of the group that was last active
$lastActiveDateGroup = null
setActiveDateGroup = ->
  if $(window).scrollTop() + $(window).height() <= $("body").height()
    # Tracks if any of our date markers are active
    anyActive = false
    $(".date-group").each (i) ->
      # Is the top of the screen inside a date group?
      # The 45px is to account for the fixed hehader
      if $(@).offset().top < $(window).scrollTop() + wt.navHeight < $(@).data("scroll-bottom") - 14
        anyActive = true
        # Has the active group not been set?
        if not $lastActiveDateGroup? then $lastActiveDateGroup = $(@)
        pinDateMarker $(@)
        # Do we have a new date group?
        if $lastActiveDateGroup? and not $(@).is $lastActiveDateGroup
          $lastActiveDateGroup = $(@)
      else
        $(@).find(".date-marker").removeClass("fixed absolute").css "top", ""
    # Now, what to do if nothing is active
    if $lastActiveDateGroup? and not anyActive
      # Is the group moving out of the viewport at the top
      if $lastActiveDateGroup.data("scroll-top") < $(window).scrollTop()
        pinDateMarker $lastActiveDateGroup, "absolute"
      # We want to make sure we move the previous one to absolute positioning
      pinDateMarker $lastActiveDateGroup.prev(), "absolute", false

pinDateMarker = ($container, position = "fixed", clearClasses = true) ->
  if clearClasses then $(".date-marker").removeClass("fixed absolute").css "top", ""
  $dm = $container.find(".date-marker")
  $dm.addClass position
  if position is "absolute" then $dm.css "top", $container.data "scroll-bottom"

animateContentShift = (state) ->
  # This is called when the menu is opened.
  # We need to move all fixed position elements over 280 pixels
  # Right now, that's the nav, form submits on mobile,
  # app messages, and date markers
  pos = if state is "open" then "280px" else "0"
  selector = ".navbar-default"
  if $(".app-message").length and $("body").hasClass "app-message-visible"
    selector += ", .app-message"
  $(selector).animate(
    left: pos
  , 150
  , -> if pos is "0" then $(selector).css "left", ""
  )
  if $(".date-marker.fixed").length
    leftPos = $(".date-marker.fixed").offset().left
    pos = if state is "open" then "#{leftPos + 280}px" else "#{leftPos - 280}px"
    $(".date-marker.fixed").animate(
      left: pos
    , 150
    , ->
      $(".date-marker.fixed").css "left", ""
    )
  if $(window).width() <= 540
    pos = if state is "open" then "-280px" else "0"
    $(".btn-submit").animate(
      right: pos
    , 150
    )

wt.appMessage =
  paddingChange: wt.navHeight - $(".navbar-default").outerHeight(true)
  create: (message, type = "info") ->
    console.log $('<div/>').html(message).text()
    console.log $(".app-message").text().trim()
    if $(".app-message").text().trim() isnt $('<div/>').html(message).text()
      wt.appMessage.destroy()
      msgClass = "content"
      if type is "warning" then msgClass += " fa-override-before fa-exclamation-triangle"
      if type is "success" then msgClass += " fa-override-before fa-check-circle"
      $el = $("""<div class="app-message app-message-#{type}" style="display: none">
        <div class="#{msgClass}">#{message}</div>
        <a href="#" class="app-message-close"><i class="fa fa-times-circle icon"></i></a>
      </div>""")
      $("#page-content").append($el)
      $(".container").animate({
          paddingTop: "+=#{wt.appMessage.paddingChange}"
        }
        , 150
        , ->
          $(".app-message").fadeIn(
            150
          )
          $("body").addClass "app-message-visible"
          setNavHeight(true) if not $("body").hasClass "account"
      )
  destroy: ->
    $appMessage = $(".app-message")
    $appMessage.fadeOut(150)
    $(".container").animate({
        paddingTop: "+=-#{wt.appMessage.paddingChange}"
      }
      , 150
      , ->
        $appMessage.remove()
        $("body").removeClass "app-message-visible"
        setNavHeight(true) if not $("body").hasClass "account"
    )

setNavHeight = (fixPadding = false)->
  oldHeight = wt.navHeight
  if $(".app-message").length and $("body").hasClass "app-message-visible"
    wt.navHeight = $(".app-message").outerHeight(true) + $(".app-message").offset().top
  else
    wt.navHeight = $(".navbar").outerHeight(true)
  if fixPadding and (oldHeight isnt wt.navHeight) then setFixedPadding()

setFixedPadding = ->
  $(".container").css "padding-top", wt.navHeight
  $(".date-marker").css "top", (wt.navHeight + 14)

timerUsername = null
checkUsername = ($el) ->
  if $el.val() isnt ""
    if timerUsername then clearTimeout timerUsername
    timerUsername = setTimeout(->
      $group = $el.parent()
      if $el.val().match(/^[\w]{3,15}$/gi)?.length isnt 1
        $group.removeClass("form-group-ok").addClass("form-group-warning")
        wt.appMessage.create "Your username must be between 3 - 15 unaccented numbers or letters.", "warning"
      else
        $.getJSON "/user/check.php?un=#{encodeURIComponent $el.val()}", (data) ->
          if not data.available
            $group.removeClass("form-group-ok").addClass("form-group-warning")
            wt.appMessage.create "Sorry, someone already grabbed that name. Please try again.", "warning"
          else
            $group.addClass("form-group-ok").removeClass("form-group-warning")
            $group.find(".help-block").remove()
            wt.appMessage.destroy()
    , 500
    )

checkPasswordFormat = (value) -> value.match(/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$/gi)?
checkEmailFormat    = (value) -> value.match(/^([a-z0-9_\.\+-]+)@([\da-z\.-]+)\.([a-z\.]{2,8})$/)?

timerPassword = null
checkPasswordField = ($el) ->
  if $el.val() isnt ""
    if timerPassword then clearTimeout timerPassword
    timerPassword = setTimeout(->
      $group = $el.parent()
      if checkPasswordFormat $el.val()
        $group.addClass("form-group-ok").removeClass("form-group-warning")
        $group.find(".help-block").remove()
        wt.appMessage.destroy()
      else
        $group.removeClass("form-group-ok").addClass("form-group-warning")
        wt.appMessage.create "Your password must be at least 8 characters and contain both numbers &amp; letters.", "warning"
    , 500
    )

timerEmail = null
checkEmailField = ($el) ->
  if $el.val() isnt ""
    if timerEmail then clearTimeout timerEmail
    timerEmail = setTimeout(->
      $group = $el.parent()
      if checkEmailFormat $el.val()
        $group.addClass("form-group-ok").removeClass("form-group-warning")
        $group.find(".help-block").remove()
        wt.appMessage.destroy()
      else
        $group.removeClass("form-group-ok").addClass("form-group-warning")
        wt.appMessage.create "Please enter a valid email address.", "warning"
    , 500
    )

checkTermsField = ($el) ->
  if $el.is ":checked"
    $el.parent().next(".help-block").remove()
  else
    wt.appMessage.create "Please review and agree to the terms of service.", "warning"

# Focus on the first form field without an empty value or error.
focusField = ($el_array) ->
  for $el in $el_array
    if $el.val() is "" or $el.parent().hasClass "form-group-warning"
      $el.focus()
      break

$ ->
  setListOpenData(true)
  $(window).load -> setDateGroupData()
  setNavHeight()

  isjPMon = false
  jPM = $.jPanelMenu(
    openPosition: "280px"
    keyboardShortcuts: false
    beforeOpen: -> animateContentShift "open"
    beforeClose: -> animateContentShift "close"
    afterOff: ->
      $(".app-message, .navbar-default").stop(true, true).css "left", ""
  )
  if (!$("body").hasClass("menu-open") or $(window).width() < 820) and !$("body").hasClass("menu-off")
    $("#page-content").css({minHeight: $(window).height() - wt.navHeight})
    jPM.on()
    isjPMon = true

  # Change a few things when the user resizes their browser
  $(window).resize ->
    setListOpenData(false, true)
    setNavHeight(true)
    # $("#page-content").css({minHeight: $(window).height() - wt.navHeight})
    if !$("body").hasClass("menu-off") and $("body").hasClass("menu-open") and
    $(window).width() < 820 and (not isjPMon)
      jPM.on()
      isjPMon = true
    if !$("body").hasClass("menu-off") and $("body").hasClass("menu-open") and
    $(window).width() >= 820  and isjPMon
      jPM.off()
      isjPMon = false

  # Test if the browser can use position: sticky.
  # If not, load our sticky dates script.
  # NOTE: We may still get bugs in Android tablets
  if $("body").hasClass "insight-stream"
    if featureTest "position", "sticky"
      $(".date-marker").addClass "sticky"
    else
      $(window).scroll -> setActiveDateGroup()

  $("body").on "click", ".share-button-open", (e) ->
    e.preventDefault()
    $menu = $(@).parent()
    $menu.toggleClass("open")
    rightOffset = $menu.parent().outerWidth(true) - $menu.outerWidth(true) + 4
    $menu.animate(
      right: rightOffset
    , 250
    )

  $("body").on "click", ".share-button-close", (e) ->
    e.preventDefault()
    $menu = $(@).parent()
    $menu.toggleClass("open")
    $menu.animate(
      right: "-275px"
    , 250
    )

  $("body").on "click", ".panel-body .btn-see-all", (e) ->
    $btn = $(@)
    $list = $btn.prev(".body-list")
    listHeight = if $list.hasClass "all-items-visible" then $list.data "height-closed" else $list.data "height-open"
    $list.animate(
      height: listHeight
    , 250
    , ->
      oldText = $btn.find(".btn-text").text()
      $btn.find(".btn-text").text $btn.data "text"
      $btn.data "text", oldText
      $list.toggleClass "all-items-visible"
      $btn.toggleClass "active"
    )

  $(window).load ->
    if $("body").data "app-message-text"
      wt.appMessage.create $("body").data("app-message-text"), $("body").data("app-message-type")
    if app_message? and app_message.msg? and app_message.type?
      wt.appMessage.create app_message.msg, app_message.type

  $("body").on "click", "#msg-action", (e) ->
    e.preventDefault()
    $this = $(@)
    if $this.data("submit-target")? then $($this.data("submit-target")).find(".btn-submit").click()

  $("body").on "click", ".app-message .app-message-close", (e) ->
    e.preventDefault()
    wt.appMessage.destroy()

  $("#username").on "keyup", -> checkUsername $(@)
  $("#pwd").on "keyup",      -> checkPasswordField $(@)
  $("#email").on "keyup",    -> checkEmailField $(@)
  $("#terms").on "click",   -> checkTermsField $(@)
  if $("#form-register")? then focusField [$("#email"),$("#username"),$("#pwd")]

  $("#form-reset").on "submit", (e) ->
    if $(@).find("#password").val().length is 0 or  $(@).find("#password_confirm").val().length is 0
      wt.appMessage.create "You must fill in both fields", "warning"
      e.preventDefault()
    else if !checkPasswordFormat($(@).find("#password").val())
      wt.appMessage.create "Your password must be at least 8 characters, contain both numbers &amp; letters, " +
        "and omit special characters.", "warning"
      e.preventDefault()
    else if $(@).find("#password").val() isnt $(@).find("#password_confirm").val()
      e.preventDefault()
      wt.appMessage.create "Passwords must match", "warning"
    else
      wt.appMessage.destroy()

  $("body").on "click", ".show-section", (e) ->
    $el = $($(@).data("section-selector"))
    if $el.length
      e.preventDefault()
      if $el.length then $el.show()
