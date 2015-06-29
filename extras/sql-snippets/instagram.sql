--
-- Check last login dates
--
SELECT COUNT( * ) AS total_logged_in, last_login
FROM subscribers
GROUP BY DATE( last_login )
ORDER BY last_login DESC
LIMIT 30


--
-- Check if an Instagram instance exists across installations
-- The server will time out on too many of these commands in one shot; run in batches of 400-500
--
SELECT CONCAT (
    "SELECT * FROM (SELECT '", a.table_schema, "' as installation, count(*) as instance_total FROM ", a.table_schema,
    ".tu_instances WHERE network = 'instagram' ) AS t WHERE instance_total > 0 UNION "
)
FROM information_schema.tables a
WHERE a.table_schema LIKE 'thinkupstart_%'
GROUP BY a.table_schema;
