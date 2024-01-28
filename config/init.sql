create sequence seq_board_admin_id start with 1 increment by 1;
--
create sequence seq_user_id start with 1 increment by 1;
--
create sequence seq_thread_id start with 1 increment by 1;
--
create sequence seq_response_id start with 1 increment by 1;
--
create sequence seq_ban_id start with 1 increment by 1;
--
create sequence seq_notice_id start with 1 increment by 1;
--
create table board
(
    id                          varchar(10)     not null,
    name                        varchar(50)     not null,
    deleted                     tinyint(1)      not null,
    display_thread              int(0) unsigned not null default 10,
    display_thread_list         int(0) unsigned not null default 30,
    display_response            int(0) unsigned not null default 30,
    display_response_line       int(0) unsigned not null default 30,
    limit_title                 int(0) unsigned not null default 60,
    limit_name                  int(0) unsigned not null default 50,
    limit_content               int(0) unsigned not null default 4096,
    limit_response              int(0) unsigned not null default 1000,
    limit_attachment_type       varchar(1024)   not null default 'jpg,png,gif',
    limit_attachment_size       int(0) unsigned not null default 1073741824,
    limit_attachment_name       int(0) unsigned not null default 100,
    interval_response           int(0) unsigned not null default 1,
    interval_duplicate_response int(0) unsigned not null default 3,
    created_at                  datetime        not null,
    updated_at                  datetime        not null,
    deleted_at                  datetime        null,
    primary key (id),
    index idx_board_name (name)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table user
(
    id       int(0) unsigned not null,
    username varchar(60)     not null,
    password varchar(256)    not null,
    email    varchar(256)    not null,
    admin    tinyint(1)      not null,
    primary key (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table board_admin
(
    id       int(0) unsigned not null,
    board_id varchar(10)     not null,
    user_id  int(0) unsigned not null,
    primary key (id),
    constraint fk_board_admin_board_id
        foreign key (board_id) references board (id)
            on delete cascade
            on update restrict,
    constraint fk_board_admin_user_id
        foreign key (user_id) references user (id)
            on delete cascade
            on update restrict
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table thread
(
    id         int(0) unsigned not null,
    board_id   varchar(10)     not null,
    title      varchar(50)     not null,
    password   varchar(256)    not null,
    username   varchar(60)     not null,
    ended      tinyint(1)      not null,
    deleted    tinyint(1)      not null,
    created_at datetime        not null,
    updated_at datetime        not null,
    deleted_at datetime        null,
    primary key (id),
    constraint fk_thread_board_id
        foreign key (board_id) references board (id)
            on delete cascade
            on update restrict,
    index idx_thread_board (board_id),
    index idx_thread_title (title)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table response
(
    id         int(0) unsigned not null,
    thread_id  int(0) unsigned not null,
    sequence   int(0) unsigned not null,
    username   varchar(60)     not null,
    user_id    varchar(10)     not null,
    ip         varchar(15)     not null,
    content    TEXT(20000)     not null,
    attachment varchar(256)    not null,
    youtube    varchar(100)    not null,
    deleted    tinyint(1)      not null,
    created_at datetime        not null,
    deleted_at datetime        null,
    primary key (id),
    constraint fk_response_thread_id
        foreign key (thread_id) references thread (id)
            on delete cascade
            on update restrict,
    index idx_thread_id (thread_id),
    index idx_user (username),
    index idx_user_name (user_id),
    index idx_ip (ip),
    index idx_create_date (created_at)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table ban
(
    id        int(0) unsigned not null,
    thread_id int(0) unsigned not null,
    user_id   varchar(10)     not null,
    ip        varchar(15)     not null,
    issued_at datetime        not null,
    primary key (id),
    index idx_ban_status (thread_id, user_id, issued_at),
    index idx_user_id (user_id),
    index idx_ip (ip)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table notice
(
    id       int(0) unsigned not null,
    board_id varchar(10)     not null,
    content  text(10000)     not null default '',
    primary key (id),
    constraint fk_notice_board_id
        foreign key (board_id) references board (id)
            on delete cascade
            on update restrict,
    index idx_board_id (board_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;