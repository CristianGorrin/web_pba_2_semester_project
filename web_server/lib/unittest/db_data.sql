/* tbl_metadata */
insert into tbl_metadata (`key`, `value`) values ('test_key_update', 'test_value');
insert into tbl_metadata (`key`, `value`) values ('test_key_delete', 'test_value');
insert into tbl_metadata (`key`, `value`) values ('test_key_select', 'test_value');

/* tbl_class */
insert into tbl_class (class) values ('test_class_update');
insert into tbl_class (class) values ('test_class_delete');
insert into tbl_class (class) values ('test_class_select');
insert into tbl_class (class) values ('test_class_fk');
insert into tbl_class (class) values ('test_class_fk_alt');

/* tbl_student */
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_delete', 'test_surname_delete', 'test_email_delete', '$2y$10$ynnKWXI0p.67/KM0diULQuFNvQ8/p61o0nii2jRo4ft6JPd/u1m8m', false, 4, 'test_device_uuid_v4_delete', 'test_cache_statistics_delete');
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_select', 'test_surname_select', 'test_email_select', 'test_pass_hass_select', false, 4, 'test_device_uuid_v4_select', 'test_cache_statistics_select');
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_update', 'test_surname_update', 'test_email_update', 'test_pass_hass_update', false, 4, 'test_device_uuid_v4_update', 'test_cache_statistics_update');
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_fk', 'test_surname_fk', 'test_email_fk', 'test_pass_hass_fk', false, 4, 'test_device_uuid_v4_fk', 'test_cache_statistics_fk');
insert into tbl_student (firstname, surname, email, pass_hass, validate, class, device_uuid_v4, cache_statistics) values ('test_firstename_fk_alt', 'test_surname_fk_alt', 'test_email_fk_alt', '$2y$10$Z80VWN4O/K4pe7r/0J2OmeN/uVrpZZ.nURv8G7esN8EhVB20ngzaa', false, 4, 'c474cd60-efd8-4f3b-a015-d81f9f7fa87c', 'test_cache_statistics_fk_alt');


/* tbl_teacher */
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_select', 'test_surname_select', 'test_eamil_select', 'test_hass_pass_select');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_udapte', 'test_surname_update', 'test_eamil_update', 'test_hass_pass_update');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_delete', 'test_surname_delete', 'test_eamil_delete', '$2y$10$5/FZikjHed74jGxtVxZ45.fEMFU.uZ45CWgdtrJGijUWLOlXiVrfK');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_fk', 'test_surname_fk', 'test_eamil_fk', 'test_hass_pass_fk');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_firstname_fk_alt', 'test_surname_fk_alt', 'test_eamil_fk_alt', 'test_hass_pass_fk_alt');
insert into tbl_teacher (firstname, surname, email, hass_pass) values ('test_acc_firstname', 'test_acc_surname', 'test_acc_email', '$2y$10$QKmH9mz7vtO6UiL4QO3v6u1idNu05qFauxiWwI32UVm6D8HYhb9/G');

/* tbl_subject */
insert into tbl_subject (`subject`) values ('test_subject_select');
insert into tbl_subject (`subject`) values ('test_subject_update');
insert into tbl_subject (`subject`) values ('test_subject_delete');
insert into tbl_subject (`subject`) values ('test_subject_fk');
insert into tbl_subject (`subject`) values ('test_subject_fk_alt');
insert into tbl_subject (`subject`) values ('test_subject_fk_alt_one_more');

/* tbl_subject_class */
insert into tbl_subject_class (class, `subject`) values (4, 4);
insert into tbl_subject_class (class, `subject`) values (5, 4);
insert into tbl_subject_class (class, `subject`) values (4, 5);
insert into tbl_subject_class (class, `subject`) values (4, 6);

/* tbl_class_log */
insert into tbl_class_log (class_uuid_v4, subject_class, teacher_by, unix_time, weight) values ('dbe9ed21-2c61-4937-95c8-5656975e8c1d', 3, 5, 1509129410, 1);
insert into tbl_class_log (class_uuid_v4, subject_class, teacher_by, unix_time, weight) values ('94cc6e91-dea0-4bbe-8277-035454345397', 3, 5, 1509129411, 2);
insert into tbl_class_log (class_uuid_v4, subject_class, teacher_by, unix_time, weight) values ('39903d35-7f41-474d-a03a-47cbd8fefc2f', 3, 5, 1509129412, 3);
insert into tbl_class_log (class_uuid_v4, subject_class, teacher_by, unix_time, weight) values ('9b2e5417-b8a3-4076-96e6-5c082c0c2e13', 3, 5, 1509129413, 4);
insert into tbl_class_log (class_uuid_v4, subject_class, teacher_by, unix_time, weight) values ('086cdf19-9ac0-44f1-9323-e5ceaaf60348', 3, 5, 1509129414, 5);

/* tbl_teacher_class */
insert into tbl_teacher_class (class, teacher) values (4, 4);
insert into tbl_teacher_class (class, teacher) values (5, 5);
insert into tbl_teacher_class (class, teacher) values (5, 1);

/* tbl_roll_call */
insert into tbl_roll_call (class_log, student, latitude, longitude) values(5, 2, 56.546412, 100.546412);
insert into tbl_roll_call (class_log, student, latitude, longitude) values(5, 4, 86.546412, 101.546412);
insert into tbl_roll_call (class_log, student, latitude, longitude) values(5, 5, 87.546412, 102.546412);
