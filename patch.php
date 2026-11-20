<?php
$pdo = new PDO('sqlite:'.__DIR__.'/database/database.sqlite');
try { $pdo->exec("ALTER TABLE users ADD COLUMN status VARCHAR(255) DEFAULT 'approved'"); } catch(Exception $e){}
try { $pdo->exec("ALTER TABLE users ADD COLUMN approved_at DATETIME NULL"); } catch(Exception $e){}
try { $pdo->exec("ALTER TABLE users ADD COLUMN approved_by INTEGER NULL"); } catch(Exception $e){}
$pdo->exec("UPDATE users SET status='approved', approved_at=COALESCE(approved_at, datetime('now')) WHERE status IS NULL OR status=''");
echo "ok";
