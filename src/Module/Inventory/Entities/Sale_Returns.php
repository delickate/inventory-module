<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Sale_Returns extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'sale_returns';
    protected $primaryKey = 'id';
    protected $fillable   = ['return_date','total_amount','reason','sale_id','customer_id',];

    public function Sales()

    {

       return $this->belongsTo(Sales::class,'sale_id','id');

    }

    public function Customers()

    {

       return $this->belongsTo(Customers::class,'customer_id','id');

    }

}

