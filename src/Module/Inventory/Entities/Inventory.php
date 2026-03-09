<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Inventory extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'inventory';
    protected $primaryKey = 'id';
    protected $fillable   = ['quantity','warehouse_id','product_id',];

    public function Warehouses()

    {

       return $this->belongsTo(Warehouses::class,'warehouse_id','id');

    }

    public function Products()

    {

       return $this->belongsTo(Products::class,'product_id','id');

    }

}

