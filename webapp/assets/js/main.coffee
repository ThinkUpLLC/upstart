$ ->
  # For mobile, show the answer to each question when the user taps the title.
  $("body").on "tap", ".fundraiser-qa-item header", (e) ->
    if $(window).width() <= 640 then $(@).parent().toggleClass "active"

  # Same idea for the FAQ, but everywhere.
  $("body").on "tap", "#qa-item-faq .faq-question", (e) ->
    $(@).parent().toggleClass "active"

  # Make the width of the video fluid
  $theVideo = $(".video-wrapper iframe")
  $fluidVideoContainer = $(".video-wrapper")
  $theVideo.data "aspect-ratio", ($theVideo.height() / $theVideo.width())
  $theVideo.removeAttr("height").removeAttr("width")

  $(window).resize ->
    $newWidth = $fluidVideoContainer.width()
    $theVideo.width($newWidth).height($newWidth * $theVideo.data "aspect-ratio")
  $(window).resize()

  # Submit the newsletter form without refreshing
  $("body").on("submit", ".newsletter-signup-form", (e) ->
    e.preventDefault()
    $email = $(@).children(".email").val()
    if $email?
      $form = $(@)
      $form.children(".button").attr "disabled", "disabled"
      $.getJSON(
        "list-subscribe.php"
        { email: $email }
        (data) ->
          console.log data
          if data?.code? and data.code isnt 200
            alert "You need to use a valid email address."
            $form.children(".button").removeAttr "disabled"
          else
            $(".newsletter-signup-wrapper .content").html("""<p>Thanks for signing up! Please check your email address to confirm your subscription.</p>""")
      )
  )

  # Hide and show levels on pledge page
  $("body.pledge").on("tap", ".funding-levels .level", (e) ->
    $this = $(@)
    if not $this.parent().hasClass "level-selected" then $this.parent().addClass "level-selected"
    if $this.hasClass "selected"
      true
    else
      $(".funding-levels .level").removeClass "selected"
      $this.addClass "selected"
      $(".funding-levels-header .payment-details").html """You selected the <strong>#{$this.data("name").charAt(0).toUpperCase() + $this.data("name").slice(1)}</strong> level<br><a href="#{$this.children().attr("href")}">Pay with Amazon</a>"""
      e.preventDefault()
  )