<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Sales extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'sales';
    protected $primaryKey = 'id';
    protected $fillable   = ['date','total_amount','customer_id',];

    public function Customers()

    {

       return $this->belongsTo(Customers::class,'customer_id','id');

    }


    public function Items()

    {

       return $this->hasMany(Sale_Items::class,'sale_id','id');

    }


    public function Returns()

    {

       return $this->hasMany(Sale_Returns::class,'sale_id','id');

    }

}

