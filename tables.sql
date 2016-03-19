create table users (
  id int auto_increment primary key,
  email varchar(128) unique not null,
  password varchar(255) not null,
  name varchar(60) not null,
  createdAt timestamp not null default current_timestamp,
  modifiedAt timestamp not null default current_timestamp on update current_timestamp
);

create table posts (
  id integer auto_increment primary key,
  slug varchar(100) unique not null,
  title varchar(255) not null,
  content text not null,
  published tinyint(1) not null default 0,
  createdAt timestamp not null default current_timestamp,
  modifiedAt timestamp not null default current_timestamp on update current_timestamp
);
