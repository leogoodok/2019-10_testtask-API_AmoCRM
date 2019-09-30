<?php
namespace app\models\db;
use \PDO;
include_once("ConnectMySql.php");

/**
 * Класс прототип для классов ассоциированных с таблицами БД
 * находящейся в БД по умолчанию "idwotskill_testtask" или другой
 * @author "BigLeoGood"
 */

class TabMySql
{
  /**
   * Защищенное статичное свойство - соединение с БД
   */
  protected static $_db;
  /**
   * Защищенное статичное свойство - названия БД
   */
  protected static $_db_name = 'idwotskill_testtask';
  /**
   * Защищенное статичное свойство - названия таблицы
   */
  protected static $_table = 'users';


  /**
   * конструктор класса
   */
  function __construct($NameDB = null) {
    if ($NameDB === null) {
      $NameDB = static::$_db_name;
    }
    static::$_db = ConnectMySql::getDb($NameDB);
  }

  /**
   * деструктор класса
   */
  function __destruct() {
    static::$_db = null;
  }


  /**
  * Метод получения ссылки на класс PDO, используемое этим классом
  * @return Connection|false
  */
  public static function getDb($NameDB = null)
  {
    if (static::$_db === null) {
      if ($NameDB === null) {
        $NameDB = static::$_db_name;
      }
      static::$_db = ConnectMySql::getDb($NameDB);
      return new static();
    }
    return static::$_db;
  }


  /**
  * Метод "динамичного" присвоения названия БД
  */
  public static function useDbName($dbName) {
    static::$_db_name = $dbName;
    return new static();
  }

  /**
  * Метод получения названия БД
  * @return string название таблицы, сопоставленной с этим классом
  */
  public static function dbName()
  {
    return static::$_db_name;
  }


  /**
  * Метод "динамичного" присвоения названия таблицы
  */
  public static function useTableName($table) {
    static::$_table = $table;
    return new static();
  }

  /**
  * Метод получения названия таблицы
  * @return string название таблицы, сопоставленной с этим классом
  */
  public static function tableName()
  {
    return static::$_table;
  }


  /**
  * Метод проверки наличия в таблице строки с заданным "phoneNumber"
  * @return bool
  */
  public function isDataPhoneNumber($phoneNumber)
  {
    if(empty($phoneNumber)) return;
    $sql = 'SELECT * FROM `'.(self::$_table).'` WHERE `'.(self::$_table).'`.`phoneNumber` = :phoneNumber';
    $out = TabMySql::getDb()->prepare($sql);
    $out->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_INT);
    $out->execute();
    $res = $out->fetch(PDO::FETCH_ASSOC);
    return (!empty($res) && is_array($res)) ? true : false;
  }


    /**
    * Метод чтения количества строк в таблице
    * @return int
    */
    public function readCountTotal()
    {
      $sql = 'SELECT count(id) AS total FROM `'.(self::$_table).'`';
      $out = TabMySql::getDb()->prepare($sql);
      $out->execute();
      $res = $out->fetch(PDO::FETCH_COLUMN);
      return $res;
    }


    /**
    * Метод чтения заданного количества строк с заданной позиции в таблице
    * @param $offset
    * @return int
    */
    public function readDataPager($offset = null, $limit = null)
    {
      if(empty($offset)) $offset = 0;
      if(empty($limit)) $limit = 20;
      $sql = 'SELECT * FROM `'.(self::$_table).'` LIMIT :limit OFFSET :offset';
      $out = TabMySql::getDb()->prepare($sql);
      $out->bindParam(':limit', $limit, PDO::PARAM_INT);
      $out->bindParam(':offset', $offset, PDO::PARAM_INT);
      $out->execute();
//    $res = $out->fetch(PDO::FETCH_ASSOC);
      $res = $out->fetchAll(PDO::FETCH_ASSOC);
      return $res;
    }


  /**
  * Метод чтения всех данных из таблицы
  * @return PDOStatement экземпляр класса
  */
  public function readAll()
  {
    return TabMySql::getDb(self::$_table)->query('SELECT * FROM `'.(self::$_table).'`');
  }


  /**
  * Метод чтения всех данных из таблицы
  * @return array массив содержащий все строки таблицы БД
  */
  public function readAllArray()
  {
    return TabMySql::getDb(self::$_table)->query('SELECT * FROM `'.(self::$_table).'`')->fetchAll(PDO::FETCH_ASSOC);
  }


  /**
  * Метод записи одной новой строки в таблицу
  * @return bool
  */
  public function saveOne($data)
  {
    if(empty($data) || !is_array($data)) return;
    $surname = isset($data['surname']) ? $data['surname'] : null;
    $birthday = isset($data['birthday']) ? $data['birthday'] : null;
    $phoneNumber = isset($data['phoneNumber']) ? $data['phoneNumber'] : null;
    $email = isset($data['email']) ? $data['email'] : null;
    $carBrand = isset($data['carBrand']) ? $data['carBrand'] : null;
    $createdAt = time();
    $sql = 'INSERT INTO `'.(self::$_table).'` (`surname`, `birthday`, `phoneNumber`, `email`, `carBrand`, `createdAt`)'.
              ' VALUES (:surname, :birthday, :phoneNumber, :email, :carBrand, :createdAt)';
    $res = TabMySql::getDb()->prepare($sql);
    $res->bindParam(':surname', $surname, PDO::PARAM_STR);
    $res->bindParam(':birthday', $birthday, PDO::PARAM_STR);
    $res->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_INT);
    $res->bindParam(':email', $email, PDO::PARAM_STR | PDO::PARAM_NULL);
    $res->bindParam(':carBrand', $carBrand, PDO::PARAM_STR);
    $res->bindParam(':createdAt', $createdAt, PDO::PARAM_INT);
    return $res->execute();
  }


  /**
  * Метод перезаписи одной строки в таблице по полю "phoneNumber"
  * @return bool
  */
  public function updateOne($data)
  {
    if(empty($data) || !is_array($data)) return;
    $surname = isset($data['surname']) ? $data['surname'] : null;
    $birthday = isset($data['birthday']) ? $data['birthday'] : null;
    $phoneNumber = isset($data['phoneNumber']) ? $data['phoneNumber'] : null;
    $email = isset($data['email']) ? $data['email'] : null;
    $carBrand = isset($data['carBrand']) ? $data['carBrand'] : null;
    $createdAt = time();
    $sql = 'UPDATE `'.(self::$_table).'` SET `surname` = :surname, `birthday` = :birthday, `email` = :email, `carBrand` = :carBrand, `createdAt` = :createdAt WHERE `'.(self::$_table).'`.`phoneNumber` = :phoneNumber';
    $res = TabMySql::getDb()->prepare($sql);
    $res->bindParam(':surname', $surname);
    $res->bindParam(':birthday', $birthday);
    $res->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_INT);
    $res->bindParam(':email', $email);
    $res->bindParam(':carBrand', $carBrand);
    $res->bindParam(':createdAt', $createdAt, PDO::PARAM_INT);
    return $res->execute();
}


  /**
  * Метод удаления одной строки в таблице по полю "phoneNumber"
  * @return bool
  */
  public function deleteOne($phoneNumber)
  {
    if(empty($phoneNumber)) return;
    $sql = 'DELETE FROM `'.(self::$_table).'` WHERE `'.(self::$_table).'`.`phoneNumber` = :phoneNumber';
    $res = TabMySql::getDb()->prepare($sql);
    $res->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_INT);
    return $res->execute();
  }
}
?>
