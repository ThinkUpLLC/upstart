module.exports = (grunt) ->
  grunt.initConfig(
    pkg: grunt.file.readJSON('package.json')
    project:
      app: 'webapp'
      css_path: '<%= project.app%>/assets/css'
      js_path: '<%= project.app%>/assets/js'
      premails: 'extras/dev/precompiled-emails'
      prod_emails: '<%= project.app %>/libs/view'
      test_emails: '<%= project.app %>'
    less:
      marketing:
        options:
          paths: ['../']
          sourceMap: true
          sourceMapFilename: '<%= project.css_path %>/marketing.css.map'
          sourceMapURL: 'marketing.css.map'
        files:
          '<%= project.css_path %>/marketing.css': '<%= project.css_path %>/src/marketing.less'
          '<%= project.css_path %>/marketing.v2.css': '<%= project.css_path %>/src/marketing.v2.less'
      app:
        options:
          paths: ['../']
          sourceMap: true
          sourceMapFilename: '<%= project.css_path %>/thinkup.css.map'
          sourceMapURL: 'thinkup.css.map'
        files:
          '<%= project.css_path %>/thinkup.css': '<%= project.css_path %>/src/thinkup.less'
    coffee:
      app:
        options:
          join: true
        files: [
          '<%= project.js_path %>/thinkup.js':[
            '<%= project.js_path %>/src/_util.coffee'
            '<%= project.js_path %>/src/_constants.coffee'
            '<%= project.js_path %>/src/_stream.coffee'
            '<%= project.js_path %>/src/_forms.coffee'
            '<%= project.js_path %>/src/thinkup.coffee'
          ]
        ]
      marketing:
        files: [
          '<%= project.js_path %>/marketing.js':'<%= project.js_path %>/src/marketing.coffee'
        ]
    uglify:
      app:
        files:
          '<%= project.js_path %>/thinkup.min.js':'<%= project.js_path %>/thinkup.js'
      marketing:
        files:
          '<%= project.js_path %>/marketing.min.js':'<%= project.js_path %>/marketing.js'

    premailer:
      system:
        options:
          css: [
            '<%= project.app %>/assets/css/vendor/zurb-ink.css'
            '<%= project.app %>/assets/css/email-system-messages.css'
          ]
        files:
          '<%= project.prod_emails %>/_email.system_message.tpl': ['<%= project.premails %>/_email.system_message.tpl']
      reminders:
        options:
          css: [
            '<%= project.app %>/assets/css/vendor/zurb-ink.css'
            '<%= project.app %>/assets/css/email-reminders.css'
          ]
        files:
          '<%= project.prod_emails %>/_email.payment-reminder-trial-1.tpl': ['<%= project.premails %>/_email.payment-reminder-trial-1.tpl']
          '<%= project.prod_emails %>/_email.payment-reminder-trial-2.tpl': ['<%= project.premails %>/_email.payment-reminder-trial-2.tpl']
          '<%= project.prod_emails %>/_email.payment-reminder-trial-3.tpl': ['<%= project.premails %>/_email.payment-reminder-trial-3.tpl']
          '<%= project.prod_emails %>/_email.payment-reminder-trial-4.tpl': ['<%= project.premails %>/_email.payment-reminder-trial-4.tpl']
    watch:
      email:
        files: '<%= project.premails %>/*'
        tasks: ['email']
      css:
        files: '<%= project.css_path %>/src/*'
        tasks: ['less']
      js:
        files: '<%= project.js_path %>/src/*'
        tasks: ['app_js', 'marketing_js']

  )
  grunt.loadNpmTasks('grunt-contrib-watch')
  grunt.loadNpmTasks('grunt-contrib-less')
  grunt.loadNpmTasks('grunt-contrib-coffee')
  grunt.loadNpmTasks('grunt-contrib-uglify')
  grunt.loadNpmTasks('grunt-premailer')

  grunt.registerTask('fix_styles', 'This fixes the stuff premailer breaks', ->
    styles = (path) ->
      html = grunt.file.read path
      html = html.replace('{literal}', '').replace('{/literal}','')
      html = html.replace('<style type="text/css">','<style type="text/css">{literal}')
      html = html.replace('</style>','{/literal}</style>')
      grunt.file.write path, html

    styles('webapp/libs/view/_email.system_message.tpl')
  )

  grunt.registerTask('default', ['premailer:system', 'fix_styles'])
  grunt.registerTask('email', ['premailer:system', 'premailer:reminders', 'fix_styles'])
  grunt.registerTask('app_js', ['coffee:app', 'uglify:app'])
  grunt.registerTask('marketing_js', ['coffee:marketing', 'uglify:marketing'])