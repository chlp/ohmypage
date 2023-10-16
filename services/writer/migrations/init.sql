create table pages
(
    id      VARCHAR(40) DEFAULT (uuid()) primary key,
    created datetime    default CURRENT_TIMESTAMP not null,
    title   varchar(256)                          not null,
    content text                                  not null,
    status  tinyint                               not null,
    theme   varchar(32)                           not null,
    lang    varchar(16)                           not null
);

create table images
(
    id       int auto_increment primary key,
    page     int          not null,
    filename varchar(256) not null
);

CREATE TABLE users
(
    uuid       VARCHAR(36) DEFAULT (UUID()) PRIMARY KEY,
    name       VARCHAR(64),
    telegramId int unsigned
);
CREATE INDEX telegramId ON users (telegramId);