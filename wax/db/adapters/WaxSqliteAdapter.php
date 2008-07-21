<?php
/**
 * SQLite Adapter class
 *
 * @package PhpWax
 **/
class  WaxSqliteAdapter extends WaxDbAdapter {
  protected $date = 'CURDATE()';
	protected $timestamp = 'NOW()'; 
	public $data_types = array(
	    'AutoField'=>         'INTEGER',
      'BooleanField'=>      'INTEGER',
      'CharField'=>         'TEXT',
      'DateField'=>         'date',
      'DateTimeField'=>     'datetime',
      'DecimalField'=>      'decimal',
      'EmailField'=>        'TEXT',
      'FileField'=>         'TEXT',
      'FilePathField'=>     'TEXT',
      'ForeignKey'=>        'INTEGER',
      'ImageField'=>        'TEXT',
      'IntegerField'=>      'INTEGER',
      'IPAddressField'=>    'TEXT',
      'PasswordField'=>     'TEXT',
      'SlugField'=>         'TEXT',
      'TextField'=>         'TEXT',
      'TimeField'=>         'time',
			'FloatField'=>				'float'
  );
	
	public function connect($db_settings) {
	  $dsn = "{$db_settings['dbtype']}:".WAX_ROOT."{$db_settings['database']}";
	  return new PDO( $dsn );
  }
  
  public function view_table(WaxModel $model) {
    $stmt = $this->db->prepare("SELECT name FROM sqlite_master WHERE type = 'table'");
    return $this->exec($stmt)->fetchAll(PDO::FETCH_NUM);
  }
  
  public function view_columns(WaxModel $model) {
    $stmt = $this->db->prepare("PRAGMA table_info(`{$model->table}`)");
    $stmt = $this->exec($stmt);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function create_table(WaxModel $model) {
    $sql = "CREATE TABLE `{$model->table}` (";
    $sql .= $this->column_sql($model->get_col($model->primary_key), $model);
    $sql.=")";
    $stmt = $this->db->prepare($sql);
    $this->exec($stmt);
    return "Created table {$model->table}";
  }
  
  public function add_column(WaxModelField $field, WaxModel $model, $swallow_errors=false) {
    if(!$field->col_name) return true;
    $sql = "ALTER TABLE `$model->table` ADD ";
    $sql.= $this->column_sql($field, $model);
    $stmt = $this->db->prepare($sql);
    $this->exec($stmt, array(), $swallow_errors);
    return "Added column {$field->col_name} to {$model->table}";
  }
  
  public function alter_column(WaxModelField $field, WaxModel $model, $swallow_errors=false) {

  }
  
  public function column_sql(WaxModelField $field, WaxModel $model) {
    $sql.= "`{$field->col_name}`";
    $sql.=" ".$this->data_types[get_class($field)];
    if($field->null) $sql.=" NULL";
    else $sql.=" NOT NULL";
    if($field->default) $sql.= " DEFAULT '{$field->default}'";
    if($field->primary) $sql.=" PRIMARY KEY";
    if($field->auto) $sql.= " AUTOINCREMENT";
    return $sql;
  }
  
  
  public function syncdb(WaxModel $model) {
    if(in_array(get_class($model), array("WaxModel", "WaxTreeModel"))) return;
    // First check the table for this model exists
    $tables = $this->view_table($model);
    $exists = false;
    foreach($tables as $table) {
      if($table[0]== $model->table) $exists=true;
    }
    if(!$exists) $output .= $this->create_table($model)."\n";
    
    // Then fetch the existing columns from the database
    $db_cols = $this->view_columns($model);
    // Map definitions to database - create or alter if required

    foreach($model->columns as $model_col=>$model_col_setup) {
      $model_field = $model->get_col($model_col);
      $output .= $model_field->before_sync();
      $col_exists = false;
      $col_changed = false;
      foreach($db_cols as $col) {
        if($col["name"]==$model_field->col_name) {
          $col_exists = true;
          if($col["dflt_value"] != $model_field->default) $col_changed = "default";
          if($col["notnull"] && $model_field->null) $col_changed = "now null";
          if(!$col["notnull"] && !$model_field->null) $col_changed = "now not null";
        }
      }
      if($col_exists==false) $output .= $this->add_column($model_field, $model, true)."\n";
      if($col_changed) $output .= $this->alter_column($model_field, $model, true)." ".$col_changed."\n";
    }
    $output .= "Table {$model->table} is now synchronised";
    die();
    return $output;
  }
  
  
	
	
} // END class