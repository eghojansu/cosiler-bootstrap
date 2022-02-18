-- target engine: SQLITE

DELETE FROM "user";
INSERT INTO "user" ("userid", "name", "roles", "created_by", "updated_by", "created_at", "updated_at") VALUES
('admin', 'Administrator', 'admin', 'seed', 'seed', DATE('NOW'), DATE('NOW'));

DELETE FROM "menu";
INSERT INTO "menu" ("menuid", "root", "parentid", "path", "title", "roles", "icon", "active", "created_by", "updated_by", "created_at", "updated_at") VALUES
('main', 1, null, null, null, null, null, null, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('MRuPYseS', null, 'main', 'dashboard', null, null, 'speedometer', 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('dPZwZRZd', null, 'main', 'administration', null, 'admin', 'gear', 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('oDOdghbo', null, 'dPZwZRZd', 'users', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('profile', 1, null, null, null, null, null, null, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('MoCNbTYp', null, 'profile', 'account', null, null, 'person-circle', 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('wqDOtmWb', null, 'MoCNbTYp', 'profile', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW')),
('HXRkjYSv', null, 'MoCNbTYp', '#logout#', null, null, null, 1, 'seed', 'seed', DATE('NOW'), DATE('NOW'));