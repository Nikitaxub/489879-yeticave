drop database if exists yeti_cave;

create database yeti_cave character set utf8 collate utf8_unicode_ci;

use yeti_cave;

create table categories(
  id tinyint unsigned auto_increment primary key,
  name varchar(50) not null
);

create table lots(
  id int unsigned auto_increment primary key,
  create_date datetime not null default current_timestamp,
  name varchar(255) not null,
  description varchar(255) not null default '',
  image varchar(255) not null default '',
  initial_price decimal(12,4),
  close_date datetime,
  bet_increment decimal(12,4),
  author_id int unsigned not null,
  winner_id int unsigned,
  category_id tinyint unsigned not null
);

create table bets(
  id bigint unsigned auto_increment primary key,
  create_date datetime not null default current_timestamp,
  price decimal(12,4) unsigned not null,
  user_id int unsigned not null,
  lot_id int unsigned not null
);

create table users(
  id int unsigned auto_increment primary key,
  registration_date datetime not null default current_timestamp,
  email varchar(255) not null,
  name varchar(255) not null,
  password_hash varchar(255) not null,
  avatar varchar(255) not null default 'img/unknown-raccoon.v4.svg',
  contacts varchar(255) not null default ''
);

create unique index ui_users_email on users(email);
/*create unique index ui_users_name on users(name);*/