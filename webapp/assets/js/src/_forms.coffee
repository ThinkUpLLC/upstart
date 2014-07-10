timerUsername = null
checkUsername = ($el) ->
  if $el.val() isnt ""
    if timerUsername then clearTimeout timerUsername
    timerUsername = setTimeout(->
      $group = $el.parents(".form-group")
      if $el.val().match(/^[\w]{3,15}$/gi)?.length isnt 1
        $group.removeClass("form-group-ok").addClass("form-group-warning")
        wt.appMessage.create "Your username must be between 3 - 15 unaccented numbers or letters.", "warning"
      else
        $.getJSON "user/check.php?un=#{encodeURIComponent $el.val()}", (data) ->
          if not data.available
            $group.removeClass("form-group-ok").addClass("form-group-warning")
            wt.appMessage.create "Sorry, someone already grabbed that name. Please try again.", "warning"
          else
            $group.addClass("form-group-ok").removeClass("form-group-warning")
            $group.find(".help-block").remove()
            wt.appMessage.destroy()
    , 500
    )

positionUsernameHelper = ($input) ->
  $ul = $("#username-length")
  $ig = $input.parents(".input-with-domain")
  $uh = $ig.find(".domain")

  $ul.text $input.val()
  gWidth  = $ig.width()
  tWidth  = $ul.width()

  if tWidth + 15 + 110 < gWidth and $ul.text().length
    console.log "#{$ul.text()}: #{tWidth}"
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

checkPasswordFormat = (value) -> value.match(/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*).{8,}$/gi)?
checkEmailFormat    = (value) -> value.match(/^([a-z0-9_\.\+-]+)@([\da-z\.-]+)\.([a-z\.]{2,8})$/)?

timerPassword = null
checkPasswordField = ($el) ->
  if $el.val() isnt ""
    if timerPassword then clearTimeout timerPassword
    timerPassword = setTimeout(->
      $group = $el.parents(".form-group")
      if checkPasswordFormat $el.val()
        $group.addClass("form-group-ok").removeClass("form-group-warning")
        $group.find(".help-block").remove()
        wt.appMessage.destroy()
      else
        $group.removeClass("form-group-ok").addClass("form-group-warning")
        wt.appMessage.create "Your password must be at least 8 characters and contain both numbers &amp; letters.", "warning"
    , 500
    )

checkSettingsPasswordField = ($form,e) ->
  $form.find('#control-password-new, #control-password-verify').parent().removeClass('form-group-warning')
  wt.appMessage.destroy()
  if $form.find("#control-password-current").length
    if $form.find("#control-password-current").val().length isnt 0
      if $form.find("#control-password-new").val().length is 0 and
         $form.find("#control-password-verify").val().length is 0
        wt.appMessage.create "You didn't provide a new password in both fields.", "warning"
        $form.find("#control-password-new, #control-password-verify").parent().addClass("form-group-warning")
        e.preventDefault()
       else if !checkPasswordFormat($form.find("#control-password-new").val())
         wt.appMessage.create "Your password must be at least 8 characters, contain both numbers &amp; letters, " +
           "and omit special characters.", "warning"
         $form.find("#control-password-new").parent().addClass("form-group-warning")
         e.preventDefault()
       else if $form.find("#control-password-new").val() isnt $form.find("#control-password-verify").val()
         e.preventDefault()
         wt.appMessage.create "The passwords must match.", "warning"
         $form.find("#control-password-new, #control-password-verify").parent().addClass("form-group-warning")


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
            $group.addClass("form-group-ok").removeClass("form-group-warning")
            $group.find(".help-block").remove()
            wt.appMessage.destroy()
          else
            $group.removeClass("form-group-ok").addClass("form-group-warning")
            wt.appMessage.create "An existing account is using this email address.", "warning"
      else
        $group.removeClass("form-group-ok").addClass("form-group-warning")
        wt.appMessage.create "Please enter a valid email address.", "warning"
    , 500
    )

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
