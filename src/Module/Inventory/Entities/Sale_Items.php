<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Sale_Items extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'sale_items';
    protected $primaryKey = 'id';
    protected $fillable   = ['quantity','unit_price','subtotal','sale_id','product_id',];

    public function Sales()

    {

       return $this->belongsTo(Sales::class,'sale_id','id');

    }

    public function Products()

    {

       return $this->belongsTo(Products::class,'product_id','id');

    }

}

