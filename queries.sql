/* вставка существующего списка категорий*/
insert into categories (name)
values ('Доски и лыжи'), ('Крепления'), ('Ботинки'), ('Одежда'), ('Инструменты'), ('Разное');

/*вставка пользователей*/
insert into users (email, name, password_hash, contacts)
values 	
	('racoon@uknown.ru', 'racoon', SHA2('rac123oon!', 256), 'Москва, Кремль'),
	('racoon1@uknown.ru', 'racoon1', SHA2('rac1234oon!', 256), 'Москва, Арбат'),
	('racoon2@uknown.ru', 'racoon2', SHA2('rac12345oon!', 256), 'Москва, Садовническая набережная'),
	('racoon3@uknown.ru', 'racoon3', SHA2('rac123456oon!', 256), 'Москва, Средняя Калитниковская');

/*вставка объявлений*/
insert into lots (name, image, initial_price, close_date, bet_increment, author_id, category_id)
values 
	('2014 Rossignol District Snowboard', 'img/lot-1.jpg', 10999, null, 500, 1, 1),
	('DC Ply Mens 2016/2017 Snowboard', 'img/lot-2.jpg', 159999, null, 300, 1, 1),
	('Крепления Union Contact Pro 2015 года размер L/XL', 'img/lot-3.jpg', 8000, null, 165, 2, 2),
	('Ботинки для сноуборда DC Mutiny Charocal', 'img/lot-4.jpg', 10999, null, 2.5, 3, 3),
	('Куртка для сноуборда DC Mutiny Charocal', 'img/lot-5.jpg', 7500, '2018-05-09 00:00:00', 93, 3, 4),
	('Маска Oakley Canopy', 'img/lot-6.jpg', 5400, null, 1678, 4, 6);
	
/*вставка ставок*/
insert into bets (price, user_id, lot_id)
values
	(10999, 1, 1),
	(11499, 2, 1),
	(159999, 3, 2),
	(160299, 4, 2),
	(160599, 3, 2),
	(8000, 1, 3),
	(8165, 3, 3),
	(8330, 2, 3),
	(10999, 1, 4),
	(11001.5, 4, 4),
	(7500, 3, 5),
	(7593, 2, 5),
	(5400, 4, 6),
	(7078, 1, 6),
	(8756, 4, 6);
	
/*получить все категории*/
select name from categories;

/*получить самые новые, открытые лоты*/
select 
	 l.name, 
	 l.initial_price, 
	 l.image,
	 ifnull((select bi.price from bets bi where l.id = bi.lot_id order by bi.price desc limit 1), l.initial_price) 'actual_price',
	 count(b.id) 'bet_count',
	 c.name
from lots l
left join bets b on l.id = b.lot_id
join categories c on l.category_id = c.id
group by l.name, l.initial_price, l.image, c.name;

/*показать лот по его id*/
select l.*, c.name
from lots l
join categories c on l.category_id = c.id
where l.id = 1;

/*обновить название лота по его идентификатору*/
update lots set name = concat(name, ' 2018') where id = 4;

/*получить список самых свежих ставок для лота по его идентификатору*/
select b.price
from bets b
where b.lot_id = 3 
order by b.create_date desc 
limit 10;
