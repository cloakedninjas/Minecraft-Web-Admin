CREATE TABLE "users" (
"id"  INTEGER NOT NULL,
"username"  TEXT(32) NOT NULL,
"password"  TEXT(64) NOT NULL,
"salt"  TEXT(8) NOT NULL,
"last_login"  INTEGER NOT NULL,
PRIMARY KEY ("id")
);

CREATE TABLE "config" (
"setup_run"  INTEGER NOT NULL,
"warning_time"  INTEGER NOT NULL,
"jar_storage" TEXT(100) NULL
);

INSERT INTO config (setup_run, warning_time) VALUES (
0, 15
);

CREATE TABLE "servers" (
"id"  INTEGER NOT NULL,
"name"  TEXT(32) NOT NULL,
"type"  INTEGER NOT NULL,
"local" INTEGER NOT NULL,
"host"  TEXT(128) NOT NULL,
"port"  INTEGER NOT NULL,
"rcon_port"  INTEGER NOT NULL,
"rcon_pass"  TEXT(16) NOT NULL,
"store"  TEXT(128) NOT NULL,
"built" INTEGER NOT NULL,
"has_changes" INTEGER NOT NULL DEFAULT 0,
PRIMARY KEY ("id")
);