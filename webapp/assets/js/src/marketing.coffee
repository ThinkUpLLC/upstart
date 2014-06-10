wt = window.tu = {}

$(window).load ->
  if $('.container').outerHeight(true) < $(window).height()
    $('body').addClass 'is-not-full-height'
  wt.appMessage.init()

wt.appMessage =
  create: (message, type = "info") ->
    wt.appMessage.destroy()
    msgClass = "content"
    if type is "warning" then msgClass += " fa-override-before fa-exclamation-triangle"
    if type is "success" then msgClass += " fa-override-before fa-check-circle"
    $el = $("""<div class="app-message" style="display: none">
      <div class="#{msgClass}">#{message}</div>
      <a href="#" class="app-message-close"><i class="fa fa-times-circle icon"></i></a>
    </div>""")
    $el.addClass "app-message-#{type}" if type? and type isnt ""
    $("#section-header, #section-navbar").append($el)
    $(".app-message").fadeIn(150)
    $("body").addClass "app-message-visible"
  destroy: ->
    $(".app-message").fadeOut(150)
    $("body").removeClass "app-message-visible"
  init: ->
    $("body").on "click", ".app-message .app-message-close", (e) ->
      e.preventDefault()
      wt.appMessage.destroy()
    if app_message? and app_message.msg? and app_message.type?
      wt.appMessage.create app_message.msg, app_message.type

$ ->
  $.getJSON "https://api.tumblr.com/v2/blog/thinkupapp.tumblr.com/posts/text?api_key=IQYCrVox6Ltyy4IqbbJWoIM9Czw0WzPGgzKWPg69WEFIa5mTtm&limit=3&callback=?", (data) ->
      if data?.response?.posts.length
        mo = [
          'January'
          'February'
          'March'
          'April'
          'May'
          'June'
          'July'
          'August'
          'September'
          'October'
          'November'
          'December'
        ]
        $posts = $("#subsection-blog .blog-posts")
        for post in data.response.posts
          d = new Date(post['timestamp']*1000)
          dateStr = "#{mo[d.getMonth()]} #{d.getDate()}, #{d.getFullYear()}"
          $posts.append """<li class="blog-post"><div class="date">#{dateStr}</div>
            <a href="#{post['post_url']}" class="permalink">#{post['title']}</a></li>"""

  $("#form-contact").submit (e) ->
    e.preventDefault()
    $form = $(@)
    subject = $form.find("#control-subject option:selected").text()
    if subject is "Choose…" then subject = "ThinkUp Help"
    body = $form.find("#control-body").val()

    window.location.href = "mailto:help@thinkup.com?subject=#{encodeURI subject}&body=#{encodeURI body}"