<?php

namespace Acms\Plugins\Form2FieldGroup;

use DB;
use SQL;
use Field;
use Field_Validation;

class Engine
{
    /**
     * Engine constructor.
     * @param string $module
     */
    public function __construct($code, $module)
    {
      $formField = $this->loadFrom($code);
      if (empty($formField)) {
          throw new \RuntimeException('Not Found Form');
      }
      $this->module = $module;
      $this->config = $formField->getChild('mail');
    }

    /**
     * Send
     */
    public function send()
    {
      if ($this->config->get('form2fieldgroup_open') !== 'true' ) {
        return;
      }
      
      // フィールドを取得
      $postField = $this->module->Post->getChild('field');
      
      // エントリーIDを取得
      $eid = intval($postField->get('entry_id'));
      if( empty($eid) ){
        throw new \RuntimeException('Not Found Entry ID.');
      }
      
      // 対象のフィールドを取得
      $targetFieldGroup = '@' . $this->config->get('form2fieldgroup_fieldgroup');
      $targetFieldKey = $postField->getArray($targetFieldGroup);
      
      // フィールドグループがDBになければ定義
      $DB = DB::singleton(dsn());
      $SQL = SQL::newSelect('field');
      $SQL->setSelect('count(`field_key`)');
      $SQL->addWhereOpr('field_eid', $eid);
      $SQL->addWhereOpr('field_key', $targetFieldGroup);
      $one = intval($DB->query($SQL->get(dsn()), 'one'));
      if( $one == 0 ){
        $i = 1;
        foreach ($targetFieldKey as $key => $value) {
          $SQL = SQL::newInsert('field');
          $SQL->addInsert('field_key', $targetFieldGroup);
          $SQL->addInsert('field_value', $value);
          $SQL->addInsert('field_sort', $i);
          $SQL->addInsert('field_search', 'off');
          $SQL->addInsert('field_eid', $eid);
          $SQL->addInsert('field_blog_id', BID);
          $DB->query($SQL->get(dsn()), 'exec');
          $i++;
        }
      }
      
      // フィールドグループの並び順を取得
      $SQL = SQL::newSelect('field');
      $SQL->setSelect('max(`field_sort`)');
      $SQL->addWhereOpr('field_eid', $eid);
      $SQL->addWhereOpr('field_key', $targetFieldKey[0]);
      $field_sort = intval($DB->query($SQL->get(dsn()), 'one')) + 1;
      
      // 各ユニットの値を取得    
      foreach ($targetFieldKey as $key => $value) {
        $SQL = SQL::newInsert('field');
        $SQL->addInsert('field_key', $value);
        $SQL->addInsert('field_value', $postField->get($value));
        $SQL->addInsert('field_sort', $field_sort);
        $SQL->addInsert('field_search', 'on');
        $SQL->addInsert('field_eid', $eid);
        $SQL->addInsert('field_blog_id', BID);
        $DB->query($SQL->get(dsn()), 'exec');
      }
    }
    
    /**
     * @param string $code
     * @return bool|Field
     */
    protected function loadFrom($code)
    {
        $DB = DB::singleton(dsn());
        $SQL = SQL::newSelect('form');
        $SQL->addWhereOpr('form_blog_id', BID);
        $SQL->addWhereOpr('form_code', $code);
        $row = $DB->query($SQL->get(dsn()), 'row');
        if (!$row) {
            return false;
        }
        $Form = new Field();
        $Form->set('code', $row['form_code']);
        $Form->set('name', $row['form_name']);
        $Form->set('scope', $row['form_scope']);
        $Form->set('log', $row['form_log']);
        $Form->overload(unserialize($row['form_data']), true);
        return $Form;
    }
}
