<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Products extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'products';
    protected $primaryKey = 'id';
    protected $fillable   = ['name','sku','barcode','category_id','unit_id','cost_price','sale_price','description','image','status',];

    public function Categories()

    {

       return $this->belongsTo(Categories::class,'category_id','id');

    }

    public function Units()

    {

       return $this->belongsTo(Units::class,'unit_id','id');

    }

}

