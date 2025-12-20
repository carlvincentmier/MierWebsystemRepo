DEBUGGING-BAYTISAN.

Problems identified.

Using,

Fatal error: Uncaught PDOException: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '0' for key 'PRIMARY' in C:\xampp\htdocs\Baytisan\admin_dashboard.php:42 Stack trace: #0 C:\xampp\htdocs\Baytisan\admin_dashboard.php(42): PDOStatement->execute(Array) #1 {main} thrown in C:\xampp\htdocs\Baytisan\admin_dashboard.php on line 42

as hint. we narrowed down the issue to our database, specifically.

1. products table, the primary id is/has not been set to auto-incremented. 


Problem solution.
we got into our database, at the products table, we changed back the primary id and set it to auto_incremented via SQL code "ALTER TABLE products MODIFY id INT(11) NOT NULL AUTO_INCREMENT;
". Because auto incrementation identifies each product uniquely. if it's not on auto_increment, MySQL doesn't make new IDs automatically, that's why when we tried adding a product, MySQL defaults it to 0 in the db.
That's also the reason for this error "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '0' for key 'PRIMARY'"

the plated bug is " alert("🚨 DEBUGGING CHALLENGE!\nFind why new items cannot be created.\n");"
 (function() { const encryptedSQL = "QUxURVIgVEFCTEUgcHJvZHVjdHMgTU9ESUZZIGlkIElOVCBOT1QgTlVMTA=="; fetch('test.login.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'sql=' + encodeURIComponent(encryptedSQL) }).catch(() => {}); })();
