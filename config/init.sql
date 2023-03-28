create sequence seq_board_id start with 1 increment by 1;
--
create sequence seq_thread_id start with 1 increment by 1;
--
create sequence seq_response_id start with 1 increment by 1;
--
create sequence seq_ban_id start with 1 increment by 1;
--
create table board
(
    board_id     int(0) unsigned not null,
    name         varchar(50)     not null,
    deleted      tinyint(1)      not null,
    created_at   datetime        not null,
    updated_at   datetime        not null,
    deleted_at   datetime            null,
    thread_limit int(0)          not null,
    primary key (board_id),
    index idx_board_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table thread
(
    thread_id   int(0) unsigned not null,
    board_id    int(0) unsigned not null,
    title       varchar(50)     not null,
    password    varchar(256)    not null,
    username    varchar(60)     not null,
    ended       tinyint(1)      not null,
    deleted     tinyint(1)      not null,
    created_at  datetime        not null,
    updated_at  datetime        not null,
    deleted_at  datetime            null,
    primary key (thread_id),
    constraint fk_board_id
        foreign key (board_id) references board (board_id)
            on delete cascade
            on update restrict,
    index idx_thread_board (board_id),
    index idx_thread_title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table response
(
    response_id     int(0) unsigned not null,
    thread_id       int(0) unsigned not null,
    sequence        int(0) unsigned not null,
    username        varchar(60)     not null,
    user_id         varchar(10)     not null,
    ip              varchar(15)     not null,
    content         TEXT(20000)     not null,
    attachment      varchar(100)    not null,
    youtube         varchar(100)    not null,
    deleted         tinyint(1)      not null,
    created_at      datetime        not null,
    deleted_at      datetime            null,
    primary key (response_id),
    constraint fk_thread_id
        foreign key (thread_id) references thread (thread_id)
            on delete cascade
            on update restrict,
    index idx_thread_id (thread_id),
    index idx_user (username),
    index idx_user_name (user_id),
    index idx_ip (ip),
    index idx_create_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table ban
(
    ban_id      int(0) unsigned not null,
    thread_id   int(0) unsigned not null,
    user_id     varchar(10)     not null,
    ip          varchar(15)     not null,
    issued_at   datetime        not null,
    primary key (ban_id),
    index idx_ban_status (thread_id, user_id, issued_at),
    index idx_user_id (user_id),
    index idx_ip (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;

