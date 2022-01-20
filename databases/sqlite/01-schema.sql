-- target engine: SQLITE

DROP TABLE IF EXISTS "user";
CREATE TABLE "user" (
  "userid" VARCHAR(8) NOT NULL,
  "name" VARCHAR(64) NOT NULL,
  "password" VARCHAR(64) NULL,
  "roles" VARCHAR(255) NULL,
  "active" SMALLINT(1) NULL,
  "created_by" VARCHAR(8) NULL,
  "updated_by" VARCHAR(8) NULL,
  "deleted_by" VARCHAR(8) NULL,
  "created_at" DATETIME NULL,
  "updated_at" DATETIME NULL,
  "deleted_at" DATETIME NULL,
  PRIMARY KEY ("userid")
);

DROP TABLE IF EXISTS "user_activity";
CREATE TABLE "user_activity" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "userid" VARCHAR(8) NOT NULL,
  "activity" VARCHAR(64) NOT NULL,
  "ip_address" VARCHAR(60) NOT NULL,
  "user_agent" VARCHAR(255) NOT NULL,
  "recorded_at" DATETIME NOT NULL,
  "remark" VARCHAR(255) NULL,
  "created_by" VARCHAR(8) NULL,
  "updated_by" VARCHAR(8) NULL,
  "deleted_by" VARCHAR(8) NULL,
  "created_at" DATETIME NULL,
  "updated_at" DATETIME NULL,
  "deleted_at" DATETIME NULL
);