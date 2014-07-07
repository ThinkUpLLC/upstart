#
# Move a users's database from active to shut down (thinkupstart_NAME to thinkupstop_NAME)
# To use:
# * Replace ... with root password
# * Replace NAME with username
#

mysqldump --user=root --password=... --host=db.x.thinkup.com thinkupstart_NAME > NAME.sql
echo 'CREATE DATABASE thinkupstop_NAME' | mysql --user=root --password=... --host=db.x.thinkup.com
mysql --user=root --password=... --host=db.x.thinkup.com thinkupstop_NAME < NAME.sql
echo 'DROP DATABASE thinkupstart_NAME' | mysqldump --user=root --password=... --host=db.x.thinkup.com