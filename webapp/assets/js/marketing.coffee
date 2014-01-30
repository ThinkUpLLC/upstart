$ ->
  # Hide and show levels on pledge page
  $("body").on("tap", ".subscription-levels.is-preselected .section-subscription-level", (e) ->
    console.log 'ya dig'
    $this = $(@)
    if $this.hasClass "is-active"
      true
    else
      $(".subscription-levels .section-subscription-level").removeClass "is-active"
      $this.addClass "is-active"
      e.preventDefault()
  )

$(window).load ->
  if $('.container').outerHeight(true) < $(window).height()
    $('body').addClass 'is-not-full-height'