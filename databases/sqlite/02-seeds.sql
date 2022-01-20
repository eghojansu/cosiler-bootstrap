-- target engine: mysql

DELETE FROM "user";
INSERT INTO "user" ("userid", "name", "roles", "created_by", "updated_by", "created_at", "updated_at")
VALUES ('admin', 'Administrator', 'admin', 'seed', 'seed', DATE('NOW'), DATE('NOW'));
