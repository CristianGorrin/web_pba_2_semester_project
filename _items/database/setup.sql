drop database if exists StudentCheckIn;
create database StudentCheckIn;
use StudentCheckIn;

create table tbl_metadata(
	`key` nvarchar(64) primary key,
    `value` nvarchar(128) not null
);

create table tbl_class(
	id int auto_increment primary key,
	class nvarchar(32) unique not null
);

create table tbl_student(
	id int auto_increment primary key,
    firstname nvarchar(128) not null,
    surname nvarchar(128) not null,
    email nvarchar(256) unique not null,
    pass_hass nvarchar(255) unique not null,
    validate bool default false,
    class int not null,
    device_uuid_v4 nvarchar(36) unique not null,
    cache_statistics text not null
);

create table tbl_teacher_class(
	id int auto_increment primary key,
    class int not null,
    teacher int not null,
    unique(class, teacher)
);

create table tbl_subject_class(
	id int auto_increment primary key,
    class int not null,
    `subject`int not null,
    unique(class, `subject`)
);

create table tbl_subject(
	id int auto_increment primary key,
    `subject` nvarchar(128) unique not null
);

create table tbl_class_log(
	id int auto_increment primary key,
    qr_code nvarchar(36) unique not null,
    subject_class int not null,
    teacher_by int not null,
    unix_time int not null,
    weight int default 1
);

create table tbl_teacher(
	id int auto_increment primary key,
    firstname nvarchar(128) not null,
    surname nvarchar(128) not null,
    email nvarchar(256) unique not null,
    hass_pass nvarchar(255) unique not null
);

create table tbl_roll_call(
	id int auto_increment primary key,
    class_log int not null,
    student int not null
);

insert into tbl_metadata (`key`, `value`) values ('database_version', '1.0.2');
insert into tbl_metadata (`key`, `value`) values ('last_update_cache_statistics', convert(unix_timestamp(now()), char));

alter table tbl_student add foreign key (class) references tbl_class(id);
alter table tbl_teacher_class add foreign key (class) references tbl_class(id);
alter table tbl_teacher_class add foreign key (teacher) references tbl_teacher(id);
alter table tbl_subject_class add foreign key (class) references tbl_class(id);
alter table tbl_subject_class add foreign key (`subject`) references tbl_subject(id);
alter table tbl_class_log add foreign key (teacher_by) references tbl_teacher(id);
alter table tbl_class_log add foreign key (subject_class) references tbl_subject_class(id);
alter table tbl_roll_call add foreign key (class_log) references tbl_class_log(id);
alter table tbl_roll_call add foreign key (student) references tbl_student(id);
