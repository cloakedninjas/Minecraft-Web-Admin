CREATE TABLE "users" (
"id"  INTEGER NOT NULL,
"username"  TEXT(32) NOT NULL,
"password"  TEXT(64) NOT NULL,
"salt"  TEXT(16) NOT NULL,
"last_login"  INTEGER NOT NULL,
PRIMARY KEY ("id")
);

CREATE TABLE "config" (
"setup_run"  INTEGER NOT NULL,
"warning_time"  INTEGER NOT NULL
);

INSERT INTO config (setup_run, warning_time) VALUES (
0, 15
);

CREATE TABLE "servers" (
"id"  INTEGER NOT NULL,
"name"  TEXT(32) NOT NULL,
"type"  INTEGER NOT NULL,
"status" INTEGER NOT NULL,
PRIMARY KEY ("id")
);