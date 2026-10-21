<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('expense_categories');
        $query = ExpenseCategory::withCount('expenses');
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . mb_substr((string) $request->q, 0, 150) . '%');
        }
        $records = $query->latest('id')->paginate(per_page())->withQueryString();

        return view('expense_categories.index', ['module' => 'expense_categories', 'records' => $records]);
    }

    public function store(ExpenseCategoryRequest $request)
    {
        $category = DB::transaction(function () use ($request) {
            $category = ExpenseCategory::create($request->validated());
            Audit::record('create', $category->getTable(), $category->id, ['before' => [], 'after' => $category->toArray()]);

            return $category;
        });

        return response()->json($this->payload($category), 201);
    }

    public function update(ExpenseCategoryRequest $request, ExpenseCategory $expense_category)
    {
        DB::transaction(function () use ($request, $expense_category) {
            $category = ExpenseCategory::lockForUpdate()->findOrFail($expense_category->id);
            $before = $category->toArray();
            $category->fill($request->validated())->save();
            Audit::record('update', $category->getTable(), $category->id, ['before' => $before, 'after' => $category->toArray()]);
        });

        return response()->json($this->payload($expense_category->refresh()));
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        Registry::authorize('expense_categories', true);
        if ($expense_category->expenses()->exists()) {
            return response()->json(['message' => 'এই খাতে খরচ আছে, তাই মোছা যাবে না।'], 422);
        }
        DB::transaction(function () use ($expense_category) {
            $id = $expense_category->id;
            $table = $expense_category->getTable();
            $snapshot = $expense_category->toArray();
            $expense_category->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return response()->json(['deleted' => true]);
    }

    private function payload(ExpenseCategory $c): array
    {
        return ['id' => $c->id, 'name' => $c->name];
    }
}
