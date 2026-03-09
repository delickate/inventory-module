<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Purchase_Items extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'purchase_items';
    protected $primaryKey = 'id';
    protected $fillable   = ['purchase_id','product_id','quantity','unit_price','total_amount',];

    public function Purchases()

    {

       return $this->belongsTo(Purchases::class,'purchase_id','id');

    }

    public function Products()

    {

       return $this->belongsTo(Products::class,'product_id','id');

    }

}

