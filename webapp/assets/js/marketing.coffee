$(window).load ->
  if $('.container').outerHeight(true) < $(window).height()
    $('body').addClass 'is-not-full-height'