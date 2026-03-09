<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Purchase_Returns extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'purchase_returns';
    protected $primaryKey = 'id';
    protected $fillable   = ['return_date','total_amount','reason','purchase_id','supplier_id', 'created_by'];

    public function Purchases()

    {

       return $this->belongsTo(Purchases::class,'purchase_id','id');

    }

    public function Products()

    {

       return $this->belongsTo(Products::class,'supplier_id','id');

    }


    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }

    public function items()
    {
        return $this->hasMany(Purchase_Return_Items::class);
    }

    public function creator()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

}

