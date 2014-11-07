# window.tu is instantiated in _util.coffee

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


  $("body").on "click", "#btn-claim-code", (e) ->
    e.preventDefault()
    $(".form-claim-code").toggleClass "hidden"
    $(@).hide()

  $("body").on "click", ".show-section", (e) ->
    $el = $($(@).data("section-selector"))
    if $el.length
      e.preventDefault()
      if $el.length then $el.show()

  $("#form-membership-contact").submit (e) ->
    e.preventDefault()
    $form = $(@)
    subject = $form.find("#control-subject option:selected").text()
    if subject is "Choose…" then subject = "ThinkUp Help"
    body = $form.find("#control-body").val()

    window.location.href = "mailto:help@thinkup.com?subject=#{encodeURI subject}&body=#{encodeURI body}"

  $("#modal-close-account").on "shown.bs.modal", ->
    $backdrop = $(".modal-backdrop").clone()
    if $(".jPanelMenu-panel").length
      $("body > .modal-backdrop").remove()
      $(".jPanelMenu-panel").append $backdrop

  $("#modal-close-account").on "hidden.bs.modal", ->
    $(".modal-backdrop").remove()
