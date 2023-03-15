create table public.income_packages
(
    id           bigserial
        primary key,
    message_name text        not null,
    package_uuid uuid        not null
        constraint income_packages_package_uuid_unique
            unique,
    md5_hash     varchar(32) not null
);