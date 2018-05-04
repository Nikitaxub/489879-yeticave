create database yeti_cave character set utf8;
use yeti_cave;
create table items(
  id tinyint unsigned auto_increment primary key,
  name varchar(50)
);
create table lots(
  id int unsigned auto_increment primary key,
  create_date datetime default current_timestamp,
  name varchar(255),
  description varchar(255),
  image text,
  initial_price int unsigned,
  close_date datetime,
  price_increment int unsigned,
  id_author int unsigned,
  id_winner int unsigned,
  id_item tinyint unsigned
);
create table bets(
  id bigint unsigned auto_increment primary key,
  create_date datetime default current_timestamp,
  price int unsigned,
  id_user int unsigned,
  id_lot int unsigned
);
create table users(
  id int unsigned auto_increment primary key,
  registration_date datetime default current_timestamp,
  email varchar(255),
  name varchar(50),
  password varchar(128),
  avatar text,
  contacts varchar(255)
);
create unique index ui_users_email on users(email);
create unique index ui_users_name on users(name);