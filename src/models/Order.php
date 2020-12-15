<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model{

    protected $primaryKey = 'id';
    public $guid;
    public $id_customer;
    public $id_table;
    public $status;
    public $expectedTime;
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

    public function item(){
        /*
        return $this->belongsToMany(
            Order::class,
            'order_lines',

        )
        */
        return $this->hasMany(Order_line::class);
    }
}

?>