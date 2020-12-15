<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model{

    protected $primaryKey = 'id';
    public $id_table;
    public $table_value;
    public $restaurant_value;
    public $chef_value;
    public $waiter_value;
    public $comment;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
    protected $guarded = [];

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}

?>