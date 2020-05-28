create table seq_thread_uid (sequence int(0) auto_increment primary key) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table seq_response_uid (sequence int(0) auto_increment primary key) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table seq_arc_response_uid (sequence int(0) auto_increment primary key) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table thread
(
    thread_uid int(0) unsigned not null,
    board_uid    varchar(10)        not null,
    title    varchar(50)        not null,
    password varchar(256)       not null,
    user_name varchar(60)       not null,
    create_date datetime           not null,
    update_date datetime           not null,
    primary key (thread_uid),
    index idx_thread_board (board_uid),
    index idx_thread_title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table response
(
    response_uid    int(0) unsigned not null,
    thread_uid    int(0) unsigned not null,
    sequence      int(0) unsigned    not null,
    user_name   varchar(60)        not null,
    user_id     varchar(10)        not null,
    ip          varchar(15)        not null,
    create_date datetime           not null,
    content     TEXT(20000)     not null,
    attachment  varchar(100)       not null,
    primary key (response_uid),
    constraint fk_thread_uid
        foreign key (thread_uid) references thread (thread_uid)
            on delete cascade
            on update restrict,
    index idx_thread_uid (thread_uid),
    index idx_user (user_name),
    index idx_user_name (user_id),
    index idx_ip (ip),
    index idx_create_date (create_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
--
create table arc_response
(
    arc_response_uid int(0) unsigned not null,
    response_uid    int(0) unsigned not null,
    thread_uid    int(0) unsigned not null,
    sequence      int(0) unsigned    not null,
    user_name   varchar(60)        not null,
    user_id     varchar(10)        not null,
    ip          varchar(15)        not null,
    create_date datetime           not null,
    content     TEXT(20000)     not null,
    attachment  varchar(100)       not null,
    archive_date datetime          not null,
    primary key (arc_response_uid),
    index idx_response_uid (response_uid),
    index idx_thread_uid (thread_uid),
    index idx_user (user_name),
    index idx_user_name (user_id),
    index idx_ip (ip),
    index idx_create_date (create_date),
    index idx_archive_date (archive_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_ci;
