# php-db

Этот класс позволяет работать с базой данных с помощью PDO,
работа с БД безопасна, это обусловлено тем, что используются placeholders.


В файле с классом меняем конфиг на наши данные для подключения к БД
	
	  $config = array(
					'typeDB' => 'mysql',
					"host" => 'localhost', 
					"dbname" => 'you_db_name', 
					"user" => 'you_db_username', 
					"pass" => 'you_db_password'
				);
				
Также можно поменять тип получения данных и вывод ошибок:
 
 	 const ATTR_ERRMODE = 2;
  	 const ATTR_FETCH = 0;
	
 	ERRMOD: 
				1. PDO::ERRMODE_SILENT
				2. PDO::ERRMODE_WARNING
				3. PDO::ERRMODE_EXCEPTION
	FETCH:
				1. PDO::FETCH_ASSOC
				2. PDO::FETCH_BOTH
				3. PDO::FETCH_BOUND
				4. PDO::FETCH_CLASS
				5. PDO::FETCH_INTO
				6. PDO::FETCH_LAZY
				7. PDO::FETCH_NUM
				8. PDO::FETCH_OBJ

# Подключить файл с классом
	
	include("./fConnectDB.php");
  
# Создание экземпляра

	$DB = new DB(); 
 
# Все доступные методы

	// Выборка
	getCount();
	getData();
	getDataWhere();
	getDataWhereIn();
	
	// Вставка
	insertData();
	
	// Удаление
	deleteFrom();
	deleteFromWhere();
	deleteFromWhereIn();
	
	// Обновление
	update();
	updateWhere();
	updateWhereIn();



# Работа с методами класса


Выборка:

Метод принимает 4 параметра(таблица, замена (*) (необязательно), поле (включить ORDER BY `поле`), 0 ASC | 1 DESC), 3 параметра являются не обязательными, будет выполнено: SELECT * FROM `name_table`

	$n = $DB->getData('name_table'); 
	
Пример получения количества строк в базе, будет выполнено: SELECT COUNT(*) as total FROM `name_table`
		
	var_dump($DB->getData('name_table', 'COUNT(*) as total'));
		
	Получаем Array ( [total] => 478 )

Выполнить запрос на выборку, будет выполнено: SELECT * FROM `name_table` WHERE `id`>=2
	
	$n = $DB->getDataWhere('name_table', array('id'=>2), '>=');
	
	Массив выборки данных из таблицы (1) по ключ(поле)/значение (2) одного операнда (3), если результат одной строки, то получаем массив данных, если более 1 строки, получаем двумерный массив всех данных
	
Выборка, будет выполнено: SELECT * FROM `name_table` WHERE `id` IN (1, 2, 4)

	(1) таблица, (2) по какому полю, (3) искомые значения Array ( ... );
	
	$n = $DB->getDataWhereIN('name_table', array('id'=>1, 2, 4)); 
	
Получаем кол-во строк в таблице name_table, будет выполнено: SELECT COUNT(*) as total WHERE `name_table`
	
	$n = $DB->getCount('name_table'); 
	
Если получать одну запись, то возвращается одномерный, иначе двумерный

У всех методов выборки существует 2 необязательных параметра для ORDER BY `id` A/D, (1) - поле, (2) ASC/DESC
	
# Добавляем данные

Чтобы добавить данные в таблицу, будет выполнено: INSERT INTO `name_table`(`field`, `field2`, `field3`) VALUES (1, 2.5, 'test_insert')
	
	вставляем в таблицу (1) данные из массива пара ключ(поле)/значение (2)
	
	$n = $DB->insertData('name_table',  array('field'=>1, 'field2'=>2.5, 'field3'=>'test_insert')); 
	
# Удаляем данные 

Для удаления всех данных из таблицы, будет выполнено: DELETE FROM `name_table`

	# удаляем все данные из таблицы (1)
	
	$n = $DB->deleteFrom('name_table'); 
	
Удаляем данные по выборке, будет выполнено: DELETE FROM `name_table` WHERE `id`=4
	
	# удаляем данные из таблицы (1) по ключ(поле)/значение (2) одного операнда (3)
	
	$n = $DB->deleteFromWhere('user', array('id'=>'4'), '='); 
	
Удаляем данные по выборке IN, будет выполнено: DELETE FROM `name_table` WHERE `id` IN(1, 2, 3)

	$n = $DB->deleteFromWhereIn('name_table', array('id'=>1, 2, 3));
	
# Обновление данных

Обновляем всю таблицу, будет выполнено: UPDATE `name_table` SET `id`=1, `balance`=0, `name`='username'
	
	# обновляем данные таблицы (1) по ключ(поле)/значение
		
	$n = $DB->update('name_table', array('id'=>1, 'balance'=>0, 'name'=>'username'));
	
Обновляем данные по выборке, будет выполнено: UPDATE `name_table` SET `balance`=0, `name`='username' WHERE `id`=1
	
	# обновляем данные таблицы (1) по ключ(поле)/значение (2) одного операнда (3)
	
	$n = $DB->updateWhere('name_table', array('balance'=>0, 'name'=>'username'), array('id'=>1), '=');

Обновляем данные по выборке IN, будет выполнено: UPDATE `name_table` SET `balance`=0, `name`='username' WHERE `id` IN (1, 2, 3)

	# обновляем данные таблицы (1) по ключ(поле)/значение (2) по полю id в массиве первого ключа и последующих значений (включая первый)
	
	$n = $DB->updateWhereIN('name_table', array('balance'=>0, 'name'=>'username'), array('id'=>1, 2, 3)

# Ручная работа

	$DB = new DB(); # создаем экземпляр класса
	$s = $DB->DBH->query("SELECT * FROM `name_table`"); # используем PDO напрямую
	$r = $s->fetch(); # обрабатываем
	print_r($r); # вывод
	
Также можно получить PDO без создания

	# использовать только объект ПДО для работы с базой (ручная работа)
	
	$DBH = connection::connect(); 
	$s = $DBH->query("SELECT * FROM `name_table`"); # используем PDO напрямую
	$r = $s->fetch(); # обрабатываем
	print_r($r); # вывод
	
Или использовать такой единождый вертолёт)))

	$s = connection::connect()->query("SELECT * FROM `name_table`"); # используем PDO напрямую
	$r = $s->fetch(); # обрабатываем
	print_r($r); # вывод
	
Тут мы не записываем наш PDO объект, с помощью которого работаем с БД, а сразу обрабатываем запрос и забываем о работе с БД.
  
# Работа без создания экземпляра (все параметры аналогичны работы с методами экземпляра)

Создаём подключение к базе данных

	DB::setup(); # инициализация БД

Работаем со всеми методами только с помощью :: (оператор разрешения области видимости)

# Выборка данных

	DB::getCount('name_table'); # Array ( [total] => 484 )
	
	DB::getData('name_table', 'COUNT(*) as total'); # Array ( [total] => 484 )
	
	DB::getData('name_table', 'COUNT(*) as total'); # Двумерный массив данных из таблицы
	
	DB::getDataWhere('name_table', array('id'=>1), '='); # Array ( ... )
	
	DB::getDataWhereIN('name_table', array('id'=>'1', '2', '4')); # Array ( ... )

# Вставка данных

	DB::insertData('name_table', array('id'=>1, 'balance'=>0.25, 'name'=>'username'); # Добавляе данные по ключ(поле)/значение

# Обновление данных

	DB::update('name_table', array('balance'=>33)); # обновляем всю таблицу по массивным полям и значениям
	
	DB::updateWhere('name_table', array('balance'=>10), array('id'=>'1'), '='); # обновляем по условию и операнду выборки
	 
	DB::updateWhereIN('name_table', array('balance'=>'25', 'balance_work'=>10), array('id'=>1, 2, 3)); # обновляем по конструкции в IN поля
	
# Удаляем данные из таблицы
	
	DB::deleteFrom('name_table'); # удаляем все данные из таблицы
	
	DB::deleteFromWhere('user', array('id'=>7), '='); # удаляем все данные из таблицы по конструкции WHERE
	
	DB::deleteFromWhereIN('user', array('id'=>8, 9, 10)); # удаляем все данные из таблицы по конструкции IN
	

