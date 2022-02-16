-- target engine: SQLITE

DELETE FROM "user";
INSERT INTO "user" ("userid", "name", "roles", "created_by", "updated_by", "created_at", "updated_at") VALUES
('admin', 'Administrator', 'admin', 'seed', 'seed', DATE('NOW'), DATE('NOW'));

DELETE FROM "menu";
INSERT INTO "menu" ("menuid", "grp", "parentid", "path", "title", "roles", "icon", "active", "created_by", "updated_by", "created_at", "updated_at") VALUES
('MRuPYseS', null, null, 'dashboard', null, null, 'speedometer', 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('dPZwZRZd', null, null, 'administration', null, 'admin', 'gear', 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('oDOdghbo', null, 'dPZwZRZd', 'users', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('MoCNbTYp', 'profile', null, 'account', null, null, 'person-circle', 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('wqDOtmWb', 'profile', 'MoCNbTYp', 'profile', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('HXRkjYSv', 'profile', 'MoCNbTYp', '#logout#', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW'));

INSERT INTO "menu" ("menuid", "grp", "parentid", "path", "title", "roles", "icon", "active", "created_by", "updated_by", "created_at", "updated_at") VALUES
('aVqZJhaQ', null, 'oDOdghbo', 'download', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW'));

INSERT INTO "menu" ("menuid", "grp", "parentid", "path", "title", "roles", "icon", "active", "created_by", "updated_by", "created_at", "updated_at") VALUES
('rISTqMmp', null, 'aVqZJhaQ', 'buff', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW'));