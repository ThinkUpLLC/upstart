# document.addEventListener('touchmove', (e) ->
#     e.preventDefault()
# , false)

$ ->
  # For mobile, show the answer to each question when the user taps the title.
  $("body").on "tap", ".fundraiser-qa-item header", (e) ->
    if $(window).width() <= 640 then $(@).parent().toggleClass "active"
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

  # Let users swipe images in carousel
  $("body").on "swipeleft", ".carousel", -> $(@).carousel 'next'
  $("body").on "swiperight", ".carousel", -> $(@).carousel 'prev'