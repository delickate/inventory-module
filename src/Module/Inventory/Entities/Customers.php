<?php
namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Contracts\Auditable;


class Customers extends Model
{
    #use SoftDeletes;

    //const CREATED_AT = null;
    //const UPDATED_AT = null;
    //const DELETED_AT = null;
    
    public $timestamps    = false;
    protected $table      = 'customers';
    protected $primaryKey = 'id';
    protected $fillable   = ['name','phone','address',];

}

