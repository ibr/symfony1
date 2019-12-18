<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfInflector
{
  /**
   * Returns a camelized string from a lower case and underscored string by replaceing slash with
   * double-colon and upper-casing each letter preceded by an underscore.
   *
   * @param  string $lower_case_and_underscored_word  String to camelize.
   *
   * @return string Camelized string.
   */
  public static function camelize($lower_case_and_underscored_word)
  {

    return strtr(ucwords(strtr($lower_case_and_underscored_word, array('/' => ':: ', '_' => ' ', '-' => ' '))), array(' ' => ''));
  }

  /**
   * Returns an underscore-syntaxed version or the CamelCased string.
   *
   * @param  string $camel_cased_word  String to underscore.
   *
   * @return string Underscored string.
   */
  public static function underscore($camel_cased_word)
  {
    $tmp = $camel_cased_word;
    $tmp = str_replace('::', '/', $tmp);
    $tmp = sfToolkit::pregtr($tmp, array('/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
                                         '/([a-z\d])([A-Z])/'     => '\\1_\\2'));

    return strtolower($tmp);
  }

  /**
   * Returns classname::module with classname:: stripped off.
   *
   * @param  string $class_name_in_module  Classname and module pair.
   *
   * @return string Module name.
   */
  public static function demodulize($class_name_in_module)
  {
    return preg_replace('/^.*::/', '', $class_name_in_module);
  }

  /**
   * Returns classname in underscored form, with "_id" tacked on at the end.
   * This is for use in dealing with foreign keys in the database.
   *
   * @param string $class_name                Class name.
   * @param bool   $separate_with_underscore  Separate with underscore.
   *
   * @return string Foreign key
   */
  public static function foreign_key($class_name, $separate_with_underscore = true)
  {
    return sfInflector::underscore(sfInflector::demodulize($class_name)).($separate_with_underscore ? "_id" : "id");
  }

  /**
   * Returns corresponding table name for given classname.
   *
   * @param  string $class_name  Name of class to get database table name for.
   *
   * @return string Name of the databse table for given class.
   */
  public static function tableize($class_name)
  {
    return sfInflector::underscore($class_name);
  }

  /**
   * Returns model class name for given database table.
   *
   * @param  string $table_name  Table name.
   *
   * @return string Classified table name.
   */
  public static function classify($table_name)
  {
    return sfInflector::camelize($table_name);
  }
  private static function my_ucwords($str) {
    $exceptions = array();
    $exceptions['Mit'] = 'mit';
    $exceptions['Und'] = 'und';
    $exceptions['An '] = 'an ';
    $exceptions['In '] = 'in ';
    $exceptions['Durch '] = 'durch ';
    $exceptions['Der'] = 'der';
    $exceptions['Die '] = 'die ';
    $exceptions['Das '] = 'das ';
    $exceptions['Von '] = 'von ';
    $exceptions['Zu '] = 'zu ';
    //    etc.
   
    $separator = array(" ","-","+");
   
    $str = strtolower(trim($str));
    foreach($separator as $s){
        $word = explode($s, $str);

        $return = "";
        foreach ($word as $val){
            $return .= $s . strtoupper($val{0}) . substr($val,1,strlen($val)-1);
        }
        $str = substr($return, 1);
    }

    foreach($exceptions as $find=>$replace){
        if (strpos($return, $find) !== false){
            $return = str_replace($find, $replace, $return);
        }
    }
    return substr($return, 1);
}

  /**
   * Returns a human-readable string from a lower case and underscored word by replacing underscores
   * with a space, and by upper-casing the initial characters.
   *
   * @param  string $lower_case_and_underscored_word String to make more readable.
   *
   * @return string Human-readable string.
   */
  public static function humanize($lower_case_and_underscored_word, $html_entities = true)
  {
    //geht nicht / wird als Profilingmerkmal verwendet
    //if ($pos_global_partial = strpos($lower_case_and_underscored_word, '/') ) {
    //  $lower_case_and_underscored_word = substr($lower_case_and_underscored_word, $pos_global_partial+1);
    //}
    if (substr($lower_case_and_underscored_word, -3) === '_id')
    {
      $lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 0, -3);
    }
    if (substr($lower_case_and_underscored_word, 0, 3) === 'id_' || substr($lower_case_and_underscored_word, 0, 3) === 'is_' )
    {
      $lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 3);
    }
    if ($html_entities) {
        $lower_case_and_underscored_word = preg_replace(array('/ae/','/oe/','/([^ae])ue/'),array('&auml;','&ouml;','${1}&uuml;'), $lower_case_and_underscored_word);
    } else {
        $lower_case_and_underscored_word = preg_replace(array('/ae/','/oe/','/([^ae])ue/'),array('�','�','${1}�'), $lower_case_and_underscored_word);
    }
    return self::my_ucwords(str_replace('_', ' ', $lower_case_and_underscored_word));
  }
}
