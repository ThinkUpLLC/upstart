#
# Installation Symlinks
#
SELECT concat('ln -s /local/www/thinkup.com/www/thinkup-user-master/webapp /local/www/thinkup.com/www/doc/user/ ', thinkup_username) FROM subscribers WHERE membership_level != 'Waitlist' and thinkup_username is not null;


#
# Data directories
#
SELECT concat('mkdir /local/www/thinkup.com/www/userdata/', thinkup_username) FROM subscribers WHERE membership_level != 'Waitlist' and thinkup_username is not null;

