<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_line extends Model{

    protected $primaryKey = 'id';
    public $order_id;
    public $item_id;
    public $quantity;
    public $status;
    public $expected_time;
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