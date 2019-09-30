<?php
namespace app\models\db;

use \PDO;

include_once("TabMySql.php");

/**
 * Класс ассоциированный с таблицей "users"
 * находящейся в БД "idwotskill_testtask"
 * @author "BigLeoGood"
 */
class TabUsers extends TabMySql
{
  /**
   * Защищенное статичное свойство - названия БД
   */
  protected static $_db_name = 'idwotskill_testtask';
  /**
   * Защищенное статичное свойство - названия таблицы
   */
  protected static $_table = 'users';

}
?>
