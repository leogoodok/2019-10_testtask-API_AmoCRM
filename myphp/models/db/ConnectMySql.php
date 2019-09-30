<?php
namespace app\models\db;
use \PDO;

/**
 * Класс соединения с БД и получения экземплята класса PDO
 * находящейся в БД "idwotskill_testtask"
 * @author "BigLeoGood"
 */
  class ConnectMySql
  {
    /**
    * Метод соединения с базой данных, используемое этим классом
    * @return Connection|false
    */
    public static function getDb($NameDB = 'idwotskill_testtask')
    {
      $db_testtask = require 'db-testtask.php';
      $dsn = 'mysql:dbname='.$NameDB.';'.$db_testtask['host'];
      $options = ['charset' => 'utf8'];
      try {
        return new PDO($dsn, $db_testtask['username'], $db_testtask['password'], $options);
      } catch (PDOException $e) {
//      return 'Подключение не удалось: ' . $e->getMessage();
        return false;
      }
    }
  }
?>
