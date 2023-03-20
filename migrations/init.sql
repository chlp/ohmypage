create table pages
(
    id      int auto_increment
        primary key,
    created datetime default CURRENT_TIMESTAMP not null,
    title   varchar(256)                       not null,
    content text                               not null,
    status  tinyint                            null
);

create table images
(
    id       int auto_increment
        primary key,
    page     int          not null,
    filename varchar(256) not null
);
