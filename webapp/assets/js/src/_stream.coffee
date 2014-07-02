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