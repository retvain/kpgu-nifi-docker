create table public.base_attributes
(
    id                bigserial
        primary key,
    header_uuid       varchar(255) not null,
    header_created    varchar(255) not null,
    source_uuid       varchar(255) not null,
    income_package_id integer      not null
        constraint base_attributes_income_package_id_foreign
            references public.income_packages
);