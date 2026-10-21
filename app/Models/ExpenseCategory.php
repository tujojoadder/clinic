<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';
    public $timestamps = false;
    protected $fillable = ['name', 'created_at'];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }
}
