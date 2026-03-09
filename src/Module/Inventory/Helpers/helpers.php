<?php
use Modules\Accountings\Entities\Chart_Of_Accounts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Schema;


if (!function_exists('generateTreeviewData')) {
    /**
     * Generate hierarchical treeview data from flat array
     *
     * @param array $items
     * @param int $parentId
     * @return array
     */
    function generateTreeviewData(array $items, $parentId = 0)
    {
        $result = [];
        
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = generateTreeviewData($items, $item['id']);
                
                if ($children) {
                    $item['children'] = $children;
                }
                
                $result[] = $item;
            }
        }
        
        return $result;
    }
}

if (!function_exists('renderTreeviewHtml')) 
{
    /**
     * Render HTML for Bootstrap treeview with lines
     *
     * @param array $treeData
     * @return string
     */
function renderTreeviewHtml(array $treeData)
{
    $html = '<ul class="tree">';
    
    foreach ($treeData as $item) {
        $html .= '<li class="tree-item">';

        $html .= '<span class="tree-toggle">' . e($item['name']) . ' -- ('. e($item['code']) .')'. ' </span>';

        // ✅ Show edit/delete if not a group
        if ((int) $item['is_group'] === 0) {
            $editlink = "<a href='".route('chart_of_accounts.editing', ['id' => $item['id']])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
            $deleelink = "<a href='".route('chart_of_accounts.deleting', ['id' => $item['id']])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";

            $html .= $editlink; //. $deleelink;
        }

        // ✅ Recursively render children if they exist
        if (!empty($item['children'])) {
            $html .= renderTreeviewHtml($item['children']);
        }

        $html .= '</li>';
    }

    $html .= '</ul>';
    
    return $html;
}

}


function renderOptions($nodes, $prefix = '', $selectedId = null)
{
    foreach ($nodes as $node) {
        $label = $prefix . $node['code'] . ' - ' . $node['name'];
        
        // Disable if level is 1, 2
        $disabled = in_array($node['level'], [1]) ? 'disabled' : '';

        // Add selected if this is the selected ID
        $selected = ($node['id'] == $selectedId) ? 'selected' : '';

        echo "<option value=\"{$node['id']}\" {$disabled} {$selected}>{$label}</option>";

        if (!empty($node['children'])) {
            renderOptions($node['children'], $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;', $selectedId);
        }
    }
}


function generateVoucherNumber($voucherTypeId, $fiscalYearId)
{
    // Get voucher type prefix (JV, PV, RV, CV)
    $prefix = DB::table('voucher_types')
                ->where('id', $voucherTypeId)
                ->value('prefix'); // Add 'prefix' column to voucher_types table
    
    // If prefix column doesn't exist, use this mapping:
    $prefixes = [
        1 => 'JV', // Journal
        2 => 'PV', // Payment
        3 => 'RV', // Receipt
        4 => 'CV'  // Contra
    ];
    $prefix = $prefixes[$voucherTypeId] ?? 'V';
    
    // Get the last voucher number for this type and fiscal year
    $lastVoucher = DB::table('vouchers')
                    ->where('voucher_type_id', $voucherTypeId)
                    ->where('fiscal_year_id', $fiscalYearId)
                    ->orderBy('id', 'desc')
                    ->first();
    
    // Generate sequential number
    $nextNumber = 1;
    if ($lastVoucher && preg_match('/-(\d+)$/', $lastVoucher->voucher_number, $matches)) {
        $nextNumber = (int)$matches[1] + 1;
    }
    
    return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
}

# return null or company_id
function loggedInUsersCompanyId()
{
    return optional(optional(auth()->user())->offices)->company_id;
}

# return 1 or 2. 1 = single entry, 2 = double entry 
function singleOrDoubleEntry()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_single_entry ?? false;
}


# return 1 or 0. 1 = do not check account balance, 0 = check account balance
function checkAccountBalance()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_allow_overdraft ?? false;
}

function pifraCodeRequired()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_pifra_code_mandatory ?? false;
}


// # return 1 or 2. 1 = public sector, 2 = private sector
// function PublicOrPrivateSector()
// {
//     return optional(optional(optional(auth()->user())->companies)->settings)->is_public_sector ?? false;
// }


# return 1 or 2. 1 = mandatory, 2 = non-mandatory
function PublicOrPrivateSector()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_pifra_code_mandatory ?? false;
}


# return 0 or 1. 1 = attach department in vouchers
function DepartmentWiseBudget()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_department ?? false;
}


# return 0 or 1. 1 = enable assets management module
function AssetsManagementModule()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_assets_management ?? false;
}


# return 0 or 1. 1 = attach cost-centers in vouchers
function CostcenterWiseBudget()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_cost_center ?? false;
}


# return 1 or 2. 1 = public, 2 = private
function AccountCode()
{
    $is_public = optional(optional(optional(auth()->user())->companies)->settings)->is_public_sector ?? false;

    if($is_public == 1)
    {
        return 1;
    }else{
            return 2;
         }
}


# return 0 or 1. 1 = attach inventory
function InventoryModule()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_inventory ?? false;
}


# return 0 or 1. 1 = attach paypall
function PayrollModule()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_payroll ?? false;
}


# return 0 or 1. 1 = attach divisions
function DivisionsAttachment()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_division ?? false;
}

# return 0 or 1. 1 = attach districts
function DistrictsAttachment()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_district ?? false;
}

# return 0 or 1. 1 = attach tehsils
function TehsilsAttachment()
{
    return optional(optional(optional(auth()->user())->companies)->settings)->is_tehsil ?? false;
}

# chart of account heirarchically in dropdown
if (!function_exists('getHierarchicalAccounts')) 
{
 function getHierarchicalAccounts($parentId = null, $prefix = '', $selectedAccountId = null, &$visited = [])
{
    static $deepestLevel = null;

    if (is_null($deepestLevel)) {
        $deepestLevel = Chart_Of_Accounts::where('company_id', optional(auth()->user())->company_id)->max('level');
    }

    $accounts = Chart_Of_Accounts::where('parent_id', $parentId)
        ->where('company_id', optional(auth()->user())->company_id)
        ->orderBy('code')
        ->get();

    $html = '';

    foreach ($accounts as $account) {
        if (in_array($account->id, $visited)) continue;
        $visited[] = $account->id;

        if ($account->level == $deepestLevel) { // && $account->is_group == 0
            $selected = ($selectedAccountId == $account->id) ? ' selected' : '';
            $html .= '<option value="' . $account->id . '"' . $selected . '>' . $prefix . $account->code . ' - ' . $account->name . '</option>';
        } else {
            $html .= '<optgroup label="' . $prefix . $account->code . ' - ' . $account->name . '">';
        }

        $html .= getHierarchicalAccounts($account->id, $prefix . '&nbsp;&nbsp;&nbsp;', $selectedAccountId, $visited);

        if ($account->level != $deepestLevel ) { //|| $account->is_group == 1
            $html .= '</optgroup>';
        }
    }

    return $html;
}




}

# return true if sum of debit is equal to sum of credit
function isDebitSumEqualCreditSum($debitArray, $creditArray)
{
    // Calculate totals
        $totalDebit     = array_sum($debitArray);
        $totalCredit    = array_sum($creditArray);

        # check if sum of debit amount is not equal to sum of credit account
        if (round($totalDebit, 2) != round($totalCredit, 2)) 
        {
            return false;
        }else{
                return true;
             }
}


function hasAccountOpeningBalance($accountId)
{DB::enableQueryLog();
    
    if($accountId > 0)
    {
        $result = DB::table('chart_of_accounts as coa')
                    ->select([
                        'coa.id as account_id',
                        'coa.name',
                        DB::raw('COALESCE(SUM(CASE 
                            WHEN v.voucher_type_id = \'4\' THEN ve.debit - ve.credit
                            ELSE 0
                        END), 0) AS opening_balance'),
                        DB::raw('COALESCE(SUM(CASE 
                            WHEN v.voucher_type_id <> \'4\' THEN ve.debit - ve.credit
                            ELSE 0
                        END), 0) AS net_activity'),
                        DB::raw('COALESCE(SUM(CASE 
                            WHEN v.voucher_type_id = \'4\' THEN ve.debit - ve.credit
                            ELSE 0
                        END), 0)
                        + 
                        COALESCE(SUM(CASE 
                            WHEN v.voucher_type_id <> \'4\' THEN ve.debit - ve.credit
                            ELSE 0
                        END), 0)
                        AS remaining_balance')
                    ])
                    ->leftJoin('voucher_entries as ve', function($join) {
                        $join->on('ve.account_id', '=', 'coa.id');
                    })
                    ->leftJoin('vouchers as v', function($join) {
                        $join->on('v.id', '=', 've.voucher_id')
                             ->whereNull('v.deleted_at');
                    })
                    ->where('coa.id', $accountId)
                    ->groupBy('coa.id', 'coa.name')
                    ->first();
// $queries = DB::getQueryLog();
// $lastQuery = end($queries);

// dd($lastQuery);
                    if(!empty($result))
                    {
                        if(abs($result->opening_balance) > 0)
                        {
                            return true;
                        }
                    }else{
                            return false;
                         }
     }else{
              return false;
          }

}



function hasAccountBalance($accountId, $debit_amount, $credit_amount)
{
    
    if($accountId > 0)
    {
        $result = DB::table('chart_of_accounts as coa')
            ->select([
                'coa.id as account_id',
                'coa.name',
                DB::raw('COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id = \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0) AS opening_balance'),
                DB::raw('COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id <> \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0) AS net_activity'),
                DB::raw('COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id = \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0)
                + 
                COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id <> \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0)
                AS remaining_balance')
            ])
             ->leftJoin('voucher_entries as ve', function($join) {
                        $join->on('ve.account_id', '=', 'coa.id');
                    })
            ->leftJoin('vouchers as v', function($join) {
                $join->on('v.id', '=', 've.voucher_id')
                        ->whereNull('v.deleted_at');
            })
            ->where('coa.id', $accountId)
            ->groupBy('coa.id', 'coa.name')
            ->first();

            if(!empty($result))
            {
                //check if we are checking remaining balance of debit amount
                if($result->opening_balance > 0 && $credit_amount == 0)
                {
                    if($debit_amount <= $result->remaining_balance)
                    {
                        return true;
                    }else{
                            return false;
                         }

                      //check if we are checking remaining balance of credit amount
                }else if($result->opening_balance > 0 && $debit_amount == 0)
                {
                    if($credit_amount <= $result->remaining_balance)
                    {
                        return true;
                    }else{
                            return false;
                         }
                }else{
                        return false;
                     }
            }else{
                    return false;
                 }
    }else{
              return false;
          }
}

function hasSingleEntryAccountBalance($accountId, $amount)
{
    
    if($accountId > 0)
    {
        $result = DB::table('chart_of_accounts as coa')
            ->select([
                'coa.id as account_id',
                'coa.name',
                DB::raw('COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id = \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0) AS opening_balance'),
                DB::raw('COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id <> \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0) AS net_activity'),
                DB::raw('COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id = \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0)
                + 
                COALESCE(SUM(CASE 
                    WHEN v.voucher_type_id <> \'4\' THEN ve.debit - ve.credit
                    ELSE 0
                END), 0)
                AS remaining_balance')
            ])
             ->leftJoin('voucher_entries as ve', function($join) {
                        $join->on('ve.account_id', '=', 'coa.id');
                    })
            ->leftJoin('vouchers as v', function($join) {
                $join->on('v.id', '=', 've.voucher_id')
                        ->whereNull('v.deleted_at');
            })
            ->where('coa.id', $accountId)
            ->groupBy('coa.id', 'coa.name')
            ->first();

       
            if(!empty($result))
            {
                //check if we are checking remaining balance of debit amount
                if($result->opening_balance > 0)
                {
                    if($amount <= $result->remaining_balance)
                    {
                        return true;
                    }else{
                            return false;
                         }

                      //check if we are checking remaining balance of credit amount
                }else{
                        return false;
                     }
            }else{
                    return false;
                 }
    }else{
              return false;
          }
}


function hasDepartmentBudget($departmentId, $debit_amount, $credit_amount)
{
    if ($departmentId > 0) {

        $query = DB::table('department_budgets');
$result = $query->select([
        'department_budgets.department_id',
        'department_budgets.account_id',
        'department_budgets.fiscal_year_id',
    ])
    ->selectRaw("
        (
            COALESCE(
                (SELECT new_amount FROM department_budget_revisions 
                 WHERE department_budget_id = department_budgets.id 
                 ORDER BY id DESC LIMIT 1),
                department_budgets.budget_amount
            )
            +
            COALESCE(
                (SELECT SUM(transfer_amount) FROM department_budget_transfers 
                 WHERE to_department_budget_id = department_budgets.id),
                0
            )
            -
            COALESCE(
                (SELECT SUM(transfer_amount) FROM department_budget_transfers 
                 WHERE from_department_budget_id = department_budgets.id),
                0
            )
            -
            COALESCE(
                (SELECT SUM(ve.debit) 
                 FROM vouchers v
                 JOIN voucher_entries ve ON ve.voucher_id = v.id
                 WHERE ve.account_id = department_budgets.account_id
                 AND v.department_id = department_budgets.department_id
                 AND v.fiscal_year_id = department_budgets.fiscal_year_id
                AND v.voucher_type_id IN (
                    SELECT id FROM voucher_types 
                    WHERE name LIKE 'payment' OR name LIKE 'receipt'
                 )
                 AND v.deleted_at IS NULL),
                0
            )
        ) AS remaining_budget
    ")
    ->where('department_id', $departmentId)
    ->first();


        if (!empty($result) && $result->remaining_budget > 0) {

            $amountToCheck = $debit_amount > 0 ? $debit_amount : $credit_amount;

            if ($amountToCheck <= $result->remaining_budget) {
                return true;
            }
        }

        return false;
    }

    return false;
}


function hasSingleEntryDepartmentBudget($departmentId, $amount)
{
    if ($departmentId > 0) {

        $query = DB::table('department_budgets');
$result = $query->select([
        'department_budgets.department_id',
        'department_budgets.account_id',
        'department_budgets.fiscal_year_id',
    ])
    ->selectRaw("
        (
            COALESCE(
                (SELECT new_amount FROM department_budget_revisions 
                 WHERE department_budget_id = department_budgets.id 
                 ORDER BY id DESC LIMIT 1),
                department_budgets.budget_amount
            )
            +
            COALESCE(
                (SELECT SUM(transfer_amount) FROM department_budget_transfers 
                 WHERE to_department_budget_id = department_budgets.id),
                0
            )
            -
            COALESCE(
                (SELECT SUM(transfer_amount) FROM department_budget_transfers 
                 WHERE from_department_budget_id = department_budgets.id),
                0
            )
            -
            COALESCE(
                (SELECT SUM(ve.debit) 
                 FROM vouchers v
                 JOIN voucher_entries ve ON ve.voucher_id = v.id
                 WHERE ve.account_id = department_budgets.account_id
                 AND v.department_id = department_budgets.department_id
                 AND v.fiscal_year_id = department_budgets.fiscal_year_id
                AND v.voucher_type_id IN (
                    SELECT id FROM voucher_types 
                    WHERE name LIKE 'payment' OR name LIKE 'receipt'
                 )
                 AND v.deleted_at IS NULL),
                0
            )
        ) AS remaining_budget
    ")
    ->where('department_id', $departmentId)
    ->first();


        if (!empty($result) && $result->remaining_budget > 0) {

            $amountToCheck = $amount;

            if ($amountToCheck <= $result->remaining_budget) {
                return true;
            }
        }

        return false;
    }

    return false;
}


function hasCostCenterBudget($costcenterId, $debit_amount, $credit_amount)
{
    if ($costcenterId > 0) {
        $result = DB::table('cost_center_budgets')
            ->select([
                'cost_center_budgets.cost_center_id',
                'cost_center_budgets.account_id',
                'cost_center_budgets.fiscal_year_id',
            ])
            ->selectSub(function ($query) {
                $query->select('new_amount')
                    ->from('cost_center_budget_revisions')
                    ->whereColumn('cost_center_budget_id', 'cost_center_budgets.id')
                    ->latest('id')
                    ->limit(1);
            }, 'current_budget')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(transfer_amount), 0)')
                    ->from('cost_center_budget_transfers')
                    ->whereColumn('to_cost_center_budget_id', 'cost_center_budgets.id');
            }, 'transferred_in')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(transfer_amount), 0)')
                    ->from('cost_center_budget_transfers')
                    ->whereColumn('from_cost_center_budget_id', 'cost_center_budgets.id');
            }, 'transferred_out')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(released_amount), 0)')
                    ->from('cost_center_budget_quaters')
                    ->whereColumn('cost_center_budget_id', 'cost_center_budgets.id');
            }, 'total_released')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(ve.debit), 0)')
                    ->from('vouchers as v')
                    ->join('voucher_entries as ve', 've.voucher_id', '=', 'v.id')
                    ->whereColumn('ve.account_id', 'cost_center_budgets.account_id')
                    ->whereColumn('v.costcenter_id', 'cost_center_budgets.cost_center_id')
                    ->whereColumn('v.fiscal_year_id', 'cost_center_budgets.fiscal_year_id')
                    ->whereIn('v.voucher_type_id', function ($sub) {
                        $sub->select('id')->from('voucher_types')
                            ->where('name', 'like', 'payment')
                            ->orWhere('name', 'like', 'receipt');
                    })
                    ->whereNull('v.deleted_at');
            }, 'used_amount')
            ->selectRaw("
                (
                    COALESCE(
                        (SELECT new_amount FROM cost_center_budget_revisions 
                         WHERE cost_center_budget_id = cost_center_budgets.id 
                         ORDER BY id DESC LIMIT 1),
                        cost_center_budgets.budget_amount
                    )
                    +
                    COALESCE(
                        (SELECT SUM(transfer_amount) FROM cost_center_budget_transfers 
                         WHERE to_cost_center_budget_id = cost_center_budgets.id),
                        0
                    )
                    -
                    COALESCE(
                        (SELECT SUM(transfer_amount) FROM cost_center_budget_transfers 
                         WHERE from_cost_center_budget_id = cost_center_budgets.id),
                        0
                    )
                    -
                    COALESCE(
                        (
                            SELECT SUM(ve.debit)
                            FROM vouchers v
                            JOIN voucher_entries ve ON ve.voucher_id = v.id
                            WHERE ve.account_id = cost_center_budgets.account_id
                              AND v.costcenter_id = cost_center_budgets.cost_center_id
                              AND v.fiscal_year_id = cost_center_budgets.fiscal_year_id
                              AND v.voucher_type_id IN (
                                  SELECT id FROM voucher_types 
                                  WHERE name LIKE 'payment' OR name LIKE 'receipt'
                              )
                              AND v.deleted_at IS NULL
                        ),
                        0
                    )
                ) AS remaining_budget
            ")
            ->where('cost_center_id', $costcenterId)
            ->first();

        if (!empty($result)) {
            if ($result->remaining_budget > 0 && $credit_amount == 0) {
                return $debit_amount <= $result->remaining_budget;
            } elseif ($result->remaining_budget > 0 && $debit_amount == 0) {
                return $credit_amount <= $result->remaining_budget;
            }
        }
    }

    return false;
}

function hasSingleEntryCostCenterBudget($costcenterId, $amount)
{\DB::enableQueryLog();
    if ($costcenterId > 0) {
        $result = DB::table('cost_center_budgets')
            ->select([
                'cost_center_budgets.cost_center_id',
                'cost_center_budgets.account_id',
                'cost_center_budgets.fiscal_year_id',
            ])
            ->selectSub(function ($query) {
                $query->select('new_amount')
                    ->from('cost_center_budget_revisions')
                    ->whereColumn('cost_center_budget_id', 'cost_center_budgets.id')
                    ->latest('id')
                    ->limit(1);
            }, 'current_budget')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(transfer_amount), 0)')
                    ->from('cost_center_budget_transfers')
                    ->whereColumn('to_cost_center_budget_id', 'cost_center_budgets.id');
            }, 'transferred_in')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(transfer_amount), 0)')
                    ->from('cost_center_budget_transfers')
                    ->whereColumn('from_cost_center_budget_id', 'cost_center_budgets.id');
            }, 'transferred_out')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(released_amount), 0)')
                    ->from('cost_center_budget_quaters')
                    ->whereColumn('cost_center_budget_id', 'cost_center_budgets.id');
            }, 'total_released')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(ve.debit), 0)')
                    ->from('vouchers as v')
                    ->join('voucher_entries as ve', 've.voucher_id', '=', 'v.id')
                    ->whereColumn('ve.account_id', 'cost_center_budgets.account_id')
                    ->whereColumn('v.costcenter_id', 'cost_center_budgets.cost_center_id')
                    ->whereColumn('v.fiscal_year_id', 'cost_center_budgets.fiscal_year_id')
                    ->whereIn('v.voucher_type_id', function ($sub) {
                        $sub->select('id')->from('voucher_types')
                            ->where('name', 'like', 'payment')
                            ->orWhere('name', 'like', 'receipt');
                    })
                    ->whereNull('v.deleted_at');
            }, 'used_amount')
            ->selectRaw("
                (
                    COALESCE(
                        (SELECT new_amount FROM cost_center_budget_revisions 
                         WHERE cost_center_budget_id = cost_center_budgets.id 
                         ORDER BY id DESC LIMIT 1),
                        cost_center_budgets.budget_amount
                    )
                    +
                    COALESCE(
                        (SELECT SUM(transfer_amount) FROM cost_center_budget_transfers 
                         WHERE to_cost_center_budget_id = cost_center_budgets.id),
                        0
                    )
                    -
                    COALESCE(
                        (SELECT SUM(transfer_amount) FROM cost_center_budget_transfers 
                         WHERE from_cost_center_budget_id = cost_center_budgets.id),
                        0
                    )
                    -
                    COALESCE(
                        (
                            SELECT SUM(ve.debit)
                            FROM vouchers v
                            JOIN voucher_entries ve ON ve.voucher_id = v.id
                            WHERE ve.account_id = cost_center_budgets.account_id
                              AND v.costcenter_id = cost_center_budgets.cost_center_id
                              AND v.fiscal_year_id = cost_center_budgets.fiscal_year_id
                              AND v.voucher_type_id IN (
                                  SELECT id FROM voucher_types 
                                  WHERE name LIKE 'payment' OR name LIKE 'receipt'
                              )
                              AND v.deleted_at IS NULL
                        ),
                        0
                    )
                ) AS remaining_budget
            ")
            ->where('cost_center_id', $costcenterId)
            ->first();
// dd(\DB::getQueryLog());
       if (!empty($result) && $result->remaining_budget > 0) {

            $amountToCheck = $amount;

            if ($amountToCheck <= $result->remaining_budget) {
                return true;
            }
        }
    }

    return false;
}


//helper function for auto vouching accountings & inventory

function createPurchaseVoucher($purchase, $voucherType)
{

    if (Schema::hasTable('vouchers')) 
    {

    // Get the account mapping for purchases from inventory_accounts_integration
    $accountMapping = DB::table('inventory_accounts_integration')
        ->where('module_name', 'purchase_entry')
        ->where('action_type', 'add')
        ->first();

    if (!$accountMapping) 
    {
        throw new \Exception('Account mapping not found for purchases');
    }

    // Get current fiscal year
    $fiscalYear = DB::table('fiscal_years')
        ->where('is_active', true)
        ->first();

    if (!$fiscalYear) 
    {
        throw new \Exception('No active fiscal year found');
    }

    // Create voucher
    $voucherData = [
                        'voucher_type_id'   => $voucherType, 
                        'fiscal_year_id'    => $fiscalYear->id,
                        'voucher_date'              => $purchase->purchase_date,
                        'voucher_number' => generateVoucherNumber($voucherType, $fiscalYear->id),
                        'department_id' => 1,
                        'costcenter_id' => 1,
                        //'company_id'        => optional(auth()->user())->company_id,
                        'company_id'        => 1,
                        // 'reference_no'      => $purchase->invoice_no,
                        'narration'       => $accountMapping->description . ' - ' . $purchase->invoice_no,
                        'total_debit'            => $purchase->total_amount,
                        'status'            => '1', //1 for approved or 'pending' based on your workflow
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];

    $voucherId = DB::table('vouchers')->insertGetId($voucherData);

    // Create voucher entries (debit and credit)
    
    // Debit entry (Inventory/Stock account)
    DB::table('voucher_entries')->insert([
                                            'voucher_id'            => $voucherId,
                                            'account_id'   => $accountMapping->debit_account_id,
                                            'debit'                 => $purchase->total_amount,
                                            'credit'                => 0,
                                            'narration'           => 'Purchase of inventory - ' . $purchase->invoice_no,
                                            'created_at'            => now(),
                                            'updated_at'            => now(),
                                        ]);

    // Credit entry (Creditors/Payable account)
    DB::table('voucher_entries')->insert([
                                            'voucher_id'            => $voucherId,
                                            'account_id'   => $accountMapping->credit_account_id,
                                            'debit'                 => 0,
                                            'credit'                => $purchase->total_amount,
                                            'narration'           => 'Amount payable to supplier - ' . $purchase->invoice_no,
                                            'created_at'            => now(),
                                            'updated_at'            => now(),
                                        ]);

    return $voucherId;
    }else{
        return false;
    }

}



function updatePurchaseVoucher($purchase, $originalAmount)
{
    // Find the existing voucher for this purchase
    $voucher = DB::table('vouchers')
        ->where('reference_no', $purchase->invoice_no)
        ->where('description', 'like', '%Purchase of inventory%')
        ->first();

    if (!$voucher) {
        // If no existing voucher found, create a new one
        $this->createPurchaseVoucher($purchase);
        return;
    }

    // Get the account mapping for purchases
    $accountMapping = DB::table('inventory_accounts_integration')
        ->where('module_name', 'purchases')
        ->where('action_type', 'create')
        ->first();

    if (!$accountMapping) {
        throw new \Exception('Account mapping not found for purchases');
    }

    // Update the voucher header
    DB::table('vouchers')
        ->where('id', $voucher->id)
        ->update([
            'date' => $purchase->purchase_date,
            'amount' => $purchase->total_amount,
            'updated_at' => now(),
        ]);

    // Delete existing voucher entries
    DB::table('voucher_entries')
        ->where('voucher_id', $voucher->id)
        ->delete();

    // Create new voucher entries with updated amounts
    
    // Debit entry (Inventory/Stock account)
    DB::table('voucher_entries')->insert([
        'voucher_id' => $voucher->id,
        'chart_of_account_id' => $accountMapping->debit_account_id,
        'debit' => $purchase->total_amount,
        'credit' => 0,
        'description' => 'Purchase of inventory - ' . $purchase->invoice_no,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Credit entry (Creditors/Payable account)
    DB::table('voucher_entries')->insert([
        'voucher_id' => $voucher->id,
        'chart_of_account_id' => $accountMapping->credit_account_id,
        'debit' => 0,
        'credit' => $purchase->total_amount,
        'description' => 'Amount payable to supplier - ' . $purchase->invoice_no,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $voucher->id;
}

function navPermission()
{

 $menus = DB::table('navigation_menus')
    ->orderBy('order')
    ->get();

// $role = Role::findByName('admin'); // or whatever role your user has
// dd($role->permissions->pluck('name'));
$navigation = [];
$i = 0;
foreach ($menus as $menu) { $i++;
    
    $items = DB::table('navigation_items')
        ->where('navigation_menu_id', $menu->id)
        ->orderBy('order')
        ->get()
        ->filter(function ($item) {
            // Show item if permission is null OR user has permission
         return is_null($item->permission) || (auth()->check() && auth()->user()->can($item->permission));

        });
     

    if ($items->isNotEmpty()) {
        $navigation[] = [
            'menu' => $menu->title,
            'icon' => $menu->icon,
            'items' => $items,
        ];
    }
}

return $navigation;

}



if (!function_exists('importExcelToTable')) 
{
    /**
     * Import Excel file data to database table
     *
     * @param string $tableName Database table name
     * @param mixed $file Excel file (path, UploadedFile instance, or Laravel Storage file)
     * @param array $options Additional options:
     *   - 'mapping' (array): Column mapping ['excel_column' => 'db_column']
     *   - 'skipRows' (int): Number of rows to skip
     *   - 'validation' (array): Validation rules
     *   - 'batchSize' (int): Number of records to insert at once
     * @return array Result with stats and errors
     */
    function importExcelToTable(string $tableName, $file, array $options = []): array
    {
        try {
            // Verify table exists
            if (!Schema::hasTable($tableName)) {
                throw new \Exception("Table {$tableName} does not exist");
            }

            // Get table columns
            $columns = Schema::getColumnListing($tableName);
            $columns = array_diff($columns, ['id', 'created_at', 'updated_at']);

            // Create dynamic importer class
            $importer = new class($tableName, $columns, $options) implements ToModel, WithHeadingRow {
                private $tableName;
                private $columns;
                private $options;
                private $stats = [
                    'total' => 0,
                    'imported' => 0,
                    'skipped' => 0,
                    'errors' => []
                ];

                public function __construct($tableName, $columns, $options)
                {
                    $this->tableName = $tableName;
                    $this->columns = $columns;
                    $this->options = $options;
                }

                public function model(array $row)
                {
                    $this->stats['total']++;

                    // Apply column mapping if provided
                    if (!empty($this->options['mapping'])) {
                        $mappedRow = [];
                        foreach ($this->options['mapping'] as $excelCol => $dbCol) {
                            if (isset($row[$excelCol])) {
                                $mappedRow[$dbCol] = $row[$excelCol];
                            }
                        }
                        $row = $mappedRow;
                    }

                    // Filter only columns that exist in the table
                    $data = array_intersect_key($row, array_flip($this->columns));

                    // Skip empty rows
                    if (empty(array_filter($data))) {
                        $this->stats['skipped']++;
                        return null;
                    }

                    // Validate if validation rules provided
                    if (!empty($this->options['validation'])) {
                        $validator = Validator::make($data, $this->options['validation']);
                        if ($validator->fails()) {
                            $this->stats['errors'][] = [
                                'row' => $this->stats['total'],
                                'data' => $data,
                                'errors' => $validator->errors()->all()
                            ];
                            $this->stats['skipped']++;
                            return null;
                        }
                    }

                    $this->stats['imported']++;

                    // Return data for insertion
                    return $data;
                }

                public function getStats()
                {
                    return $this->stats;
                }
            };

            // Configure import
            $import = Excel::import($importer, $file);
            
            // Get results
            $stats = $importer->getStats();

            // Batch insert if batchSize option provided
            if (!empty($this->options['batchSize']) && $this->options['batchSize'] > 1) {
                $data = $importer->getData();
                foreach (array_chunk($data, $this->options['batchSize']) as $batch) {
                    DB::table($this->tableName)->insert($batch);
                }
            }

            return [
                'success' => true,
                'message' => 'Import completed',
                'stats' => $stats
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'stats' => $stats ?? []
            ];
        }
    }
}

function getAccountName($accountId) 
{
    //return Modules\Accountings\Entities\Chart_Of_Accounts::find($accountId)?->name ?? 'N/A';
    $account = Modules\Accountings\Entities\Chart_Of_Accounts::find($accountId);
    return $account ? $account->name : 'N/A';
}

function getDepartmentName($departmentId) 
{
    //return Modules\BudgetModule\Entities\Departments::find($departmentId)?->name ?? 'N/A';
    $department = Modules\BudgetModule\Entities\Departments::find($departmentId);
    return $department ? $department->name : 'N/A';
}

function getCostCenterName($costCenterId) 
{
    //return Modules\BudgetModule\Entities\Cost_Centers::find($costCenterId)?->name ?? 'N/A';
    $costcenter = Modules\BudgetModule\Entities\Cost_Centers::find($costCenterId);
    return $costcenter ? $costcenter->name : 'N/A';
}




