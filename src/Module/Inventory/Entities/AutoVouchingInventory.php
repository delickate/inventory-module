<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutoVouchingInventory extends Model
{
    use HasFactory;

    public $timestamps    = false;
    protected $table      = 'inventory_accounts_integration';
    protected $primaryKey = 'id';
    protected $fillable = [
    'module_name',
    'action_type',
    'debit_account_id',
    'credit_account_id',
    'description'
];

    
    
}
