timerUsername = null
checkUsername = ($el) ->
  if timerUsername then clearTimeout timerUsername
  timerUsername = setTimeout(->
    $group = $el.parents(".form-group")
    if $el.val().match(/^[\w]{3,15}$/gi)?.length isnt 1
      wt.inputWarning.create "Must be between 3 - 15 unaccented numbers or letters.", $group
    else
      $.getJSON "user/check.php?un=#{encodeURIComponent $el.val()}", (data) ->
        if not data.available
          wt.inputWarning.create "That URL is already in use. Please try again.", $group
        else
          wt.inputWarning.destroy $group
  , 500
  )

positionUsernameHelper = ($input) ->
  $ul = $("#username-length")
  $ig = $input.parents(".input-with-domain")
  $uh = $ig.find(".domain")

  $ul.text $input.val()
  gWidth  = $ig.width()
  tWidth  = $ul.width()

  if $input.val().length is 0
    $uh.hide()
  else
    $uh.show()
    if tWidth + 15 + 110 < gWidth
      $uh.css("left", tWidth + 15)
    else
      $uh.css("left", "6.95em")

checkEmailAvailability = (email, cb) ->
  isGood = false
  if email? and email isnt ""
    $.getJSON "user/check-email.php?em=#{encodeURIComponent email}", (data) ->
      if data.available then isGood = true
      cb isGood
  else
    cb isGood

checkCouponFormat = (value) -> value.match(/^([0-9a-zA-Z]){12}$/gi)?
checkPasswordFormat = (value) -> value.match(/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$/gi)?
checkEmailFormat    = (value) -> value.match(/^([a-z0-9_\.\+-]+)@([\da-z\.-]+)\.([a-z\.]{2,8})$/)?

timerPassword = null
checkPasswordField = ($el) ->
  if $el.val() isnt ""
    if timerPassword then clearTimeout timerPassword
    timerPassword = setTimeout(->
      $group = $el.parents(".form-group")
      if checkPasswordFormat $el.val()
        wt.inputWarning.destroy $group
      else
        wt.inputWarning.create "Passwords must be at least 8 characters and contain both numbers and letters."
          , $group
    , 500
    )

checkSettingsPasswordField = ($form,e) ->
  $pgc   = $form.find("#control-password-current").parent()
  $pgn   = $form.find("#control-password-new").parent()
  $pgv   = $form.find("#control-password-verify").parent()
  wt.inputWarning.destroy $pgc, false
  wt.inputWarning.destroy $pgn, false
  wt.inputWarning.destroy $pgv, false
  if $form.find("#control-password-current").length
    if $form.find("#control-password-current").val().length isnt 0
      if $form.find("#control-password-new").val().length is 0 and
      $form.find("#control-password-verify").val().length is 0
        wt.inputWarning.destroy $pgc
        wt.inputWarning.create "Please provide a new password in both fields.", $pgn
        wt.inputWarning.create "Please provide a new password in both fields.", $pgv
        e.preventDefault()
      else if !checkPasswordFormat($form.find("#control-password-new").val())
        wt.inputWarning.destroy $pgc
        wt.inputWarning.create "Passwords must be 8+ characters and contain both letters and numbers.", $pgn
        e.preventDefault()
      else if $form.find("#control-password-new").val() isnt $form.find("#control-password-verify").val()
        e.preventDefault()
        wt.inputWarning.destroy $pgc
        wt.inputWarning.destroy $pgn
        wt.inputWarning.create "The passwords must match.", $pgv


timerEmail = null
checkEmailField = ($el) ->
  val = $el.val()
  if $el.val() isnt ""
    if timerEmail then clearTimeout timerEmail
    timerEmail = setTimeout(->
      $group = $el.parents(".form-group")
      if checkEmailFormat $el.val()
        checkEmailAvailability $el.val(), (isGood) ->
          if isGood
            wt.inputWarning.destroy $group
          else
            wt.inputWarning.create "An existing account is using this email address.", $group
      else
        wt.inputWarning.create "Please enter a valid email address.", $group
    , 500
    )

checkCouponField = ($el) ->
  if $el.length
    val = $el.val().replace /\s/g, ""
    $group = $el.parents(".form-group")
    if val isnt "" and checkCouponFormat val
      wt.inputWarning.destroy $group
      true
    else
      wt.inputWarning.create "That code doesn’t seem right. Check it and try again?", $group
      false


checkTermsField = ($el) ->
  if $el.is ":checked"
    $el.parent().next(".help-block").remove()
    if $(".app-message").text().trim() is "Please review and agree to the terms of service."
      wt.appMessage.destroy()
  else
    wt.appMessage.create "Please review and agree to the terms of service.", "warning"

# Focus on the first form field without an empty value or error.
focusField = ($el_array) ->
  for $el in $el_array
    if $el.val() is "" or $el.parents(".form-group").hasClass "form-group-warning"
      $el.focus()
      break

animateLabelIn = ($input) ->
  $label = $input.siblings("label")
  $label.animate(
    top: "50px"
  , 50
  , ->
    $label.css("top", "-50px").addClass("with-focus").animate(
      top: 0
    , 100
    )
  )

$ ->
  if $("#form-register").length
    focusField [$("#email"),$("#username"),$("#pwd")]
    positionUsernameHelper $("#username")
    $("#username, #pwd, #email").on "blur", (e) ->
      if $(@).val().length then $(@).data("do-validate", "1").keyup()
    .on "keyup", ->
      if $(@).val().length > 2 then $(@).data("do-validate", "1")
    .on "keydown", ->
      if $(@).val().length is 0 and !$(@).siblings("label").hasClass "with-focus"
        animateLabelIn $(@)
    .each ->
      if $(@).parents(".form-group-warning").length then $(@).data("do-validate", "1").keyup()

    $("#username").on "keyup", ->
      positionUsernameHelper $(@)
      if $(@).data("do-validate") is "1" then checkUsername $(@)
    $("#pwd").on "keyup",      -> if $(@).data("do-validate") is "1" then checkPasswordField $(@)
    $("#email").on "keyup",    -> if $(@).data("do-validate") is "1" then checkEmailField $(@)
    $("#terms").on "click",    -> if $(@).data("do-validate") is "1" then checkTermsField $(@)

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

  if $("#form-settings").length
    $("#control-password-current, #control-password-new, #control-password-verify").on "keydown", ->
      if $(@).val().length is 0 and !$(@).siblings("label").hasClass "with-focus"
        animateLabelIn $(@)

  $("#form-settings").on "submit", (e) ->
    checkSettingsPasswordField $(@), e

  $(".form-claim-code").on "submit", (e) ->
    unless checkCouponField $(@).find("#claim_code")
      e.preventDefault()