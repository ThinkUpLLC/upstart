#
# Installation Symlinks
#
SELECT concat('ln -s /local/www/thinkup.com/www/thinkup-user-master/webapp /local/www/thinkup.com/www/doc/user/', thinkup_username) FROM subscribers WHERE thinkup_username is not null;
SELECT 'chown -R www-data.www-data /local/www/thinkup.com/www/doc/user/*';

#
# Data directories
#
SELECT concat('mkdir /local/www/thinkup.com/www/userdata/', thinkup_username) FROM subscribers WHERE thinkup_username is not null;

