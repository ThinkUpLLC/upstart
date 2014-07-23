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

wt.inputWarning =
  create: (message, $group) ->
    $group.addClass("form-group-warning").removeClass("form-group-ok")
    if $group.find(".warning-block").length
      $elMsg = $group.find(".warning-block")
      $elMsg.text(message)
    else
      $label = $group.find(".control-label")
      $message = $("""<div class="warning-block">#{message}</div>""")
      $group.prepend $message
  destroy: ($group, isOk = true) ->
    $group.removeClass("form-group-warning").find(".warning-block").remove()
    if isOk then $group.addClass("form-group-ok")
