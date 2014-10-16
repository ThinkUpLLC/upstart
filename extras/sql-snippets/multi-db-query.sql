-- Sample SQL for running database updates across all ThinkUp user databases.
--
-- To use this code:
-- 1. Substitute your relevant SQL after 'SELECT CONCAT (' using the a.table_schema reference to prefix your table.
-- 2. Run this SQL to PHPMyAdmin to output all the SQL commands that will make the change.
-- 3. Export the results to a Texy! text file via PHPMyAdmin.
-- 4. Clean all comments and pipes from the text file using Find/Replace and tailing UNION, if applicable.
-- 5. Run the resulting output.
--

-- This example sets the auth_error to null in the owner_instances table.
SELECT CONCAT (
	'UPDATE ', a.table_schema,  '.tu_owner_instances SET auth_error = null;'
)
FROM information_schema.tables a
WHERE a.table_schema LIKE 'thinkupstart_%'
GROUP BY a.table_schema;

-- This example sets max_crawl_time to 2 minutes for Facebook
SELECT CONCAT (
	"UPDATE ", a.table_schema,  ".tu_options SET option_value = 2 WHERE namespace='plugin_options-2' AND option_name='max_crawl_time'; "
)
FROM information_schema.tables a
WHERE a.table_schema LIKE 'thinkupstart_%'
GROUP BY a.table_schema;

-- This example checks if the location awareness insight exists in a user database
-- The server will time out on too many of these commands in one shot; run in batches of 400-500
SELECT CONCAT (
	"SELECT * FROM (SELECT '", a.table_schema, "' as installation, count(*) as insight_total FROM ", a.table_schema,
	".tu_insights WHERE slug = 'location_awareness' ) AS t WHERE insight_total > 0 UNION "
)
FROM information_schema.tables a
WHERE a.table_schema LIKE 'thinkupstart_%'
GROUP BY a.table_schema;

-- This example checks if any members whose username starts with a (as a sample) used the word stopgamergate in a post
SELECT CONCAT (
    "SELECT * FROM (SELECT post_text, pub_date, p.author_username, p.network FROM ", a.table_schema,
    ".tu_posts p INNER JOIN ", a.table_schema, ".tu_instances i ON p.author_username = i.network_username
    AND p.network = i.network WHERE post_text LIKE '%stopgamergate%') AS ", a.table_schema,"_posts UNION "
)
FROM information_schema.tables a
WHERE a.table_schema LIKE 'thinkupstart_a%'
GROUP BY a.table_schema;
