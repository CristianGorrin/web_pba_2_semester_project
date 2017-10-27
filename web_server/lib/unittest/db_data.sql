/* tbl_metadata */
insert into tbl_metadata (`key`, `value`) values ('test_key_update', 'test_value');
insert into tbl_metadata (`key`, `value`) values ('test_key_delete', 'test_value');
insert into tbl_metadata (`key`, `value`) values ('test_key_select', 'test_value');

/* tbl_class */
insert into tbl_class (class) values ('test_class_update');
insert into tbl_class (class) values ('test_class_delete');
insert into tbl_class (class) values ('test_class_select');
insert into tbl_class (class) values ('test_class_fk');

/* tbl_student */
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_delete', 'test_surname_delete', 'test_email_delete', 'test_pass_hass_delete', false, 4, 'test_device_uuid_v4_delete', 'test_cache_statistics_delete');
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_select', 'test_surname_select', 'test_email_select', 'test_pass_hass_select', false, 4, 'test_device_uuid_v4_select', 'test_cache_statistics_select');
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_update', 'test_surname_update', 'test_email_update', 'test_pass_hass_update', false, 4, 'test_device_uuid_v4_update', 'test_cache_statistics_update');
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_fk', 'test_surname_fk', 'test_email_fk', 'test_pass_hass_fk', false, 4, 'test_device_uuid_v4_fk', 'test_cache_statistics_fk');

/* tbl_teacher */
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_select', 'test_surname_select', 'test_eamil_select', 'test_hass_pass_select');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_udapte', 'test_surname_update', 'test_eamil_update', 'test_hass_pass_update');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_delete', 'test_surname_delete', 'test_eamil_delete', 'test_hass_pass_delete');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_fk', 'test_surname_fk', 'test_eamil_fk', 'test_hass_pass_fk');

/* tbl_subject */
insert into tbl_subject (`subject`) values ('test_subject_select');
insert into tbl_subject (`subject`) values ('test_subject_update');
insert into tbl_subject (`subject`) values ('test_subject_delete');
insert into tbl_subject (`subject`) values ('test_subject_fk');
