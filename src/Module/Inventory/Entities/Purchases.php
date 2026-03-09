<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Purchases extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'purchases';
    protected $primaryKey = 'id';
    protected $fillable   = ['supplier_id','invoice_no','total_amount','purchase_date'];

    public function Suppliers()

    {

       return $this->belongsTo(Suppliers::class,'supplier_id','id');

    }


    public function items()

    {

       return $this->hasMany(Purchase_Items::class,'purchase_id','id');

    }

}

