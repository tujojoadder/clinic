<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('expenses');
        $query = Expense::with('account');
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['category', 'description', 'amount', 'expense_date'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        // ড্রপডাউন ফিল্টার (সব AND হিসেবে কাজ করবে)
        if ($request->filled('category')) {
            $query->where('category', (string) $request->category);
        }
        if ($request->filled('account_id')) {
            $query->where('account_id', (int) $request->account_id);
        }

        $records = $query->latest('id')->paginate(per_page())->withQueryString();

        return view('expenses.index', [
            'module'     => 'expenses',
            'records'    => $records,
            'categories' => Expense::query()->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function create()
    {
        Registry::authorize('expenses', true);

        return view('expenses.form', ['module' => 'expenses', 'record' => new Expense]);
    }

    public function store(ExpenseRequest $request)
    {
        $expense = DB::transaction(function () use ($request) {
            $expense = Expense::create($request->validated());
            Audit::record('create', $expense->getTable(), $expense->id, ['before' => [], 'after' => $expense->toArray()]);

            return $expense;
        });

        return redirect()->route('expenses.show', $expense)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(Expense $expense)
    {
        Registry::authorize('expenses');
        $expense->load('account');

        return view('expenses.show', ['module' => 'expenses', 'record' => $expense]);
    }

    public function edit(Expense $expense)
    {
        Registry::authorize('expenses', true);

        return view('expenses.form', ['module' => 'expenses', 'record' => $expense]);
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        DB::transaction(function () use ($request, $expense) {
            $expense = Expense::lockForUpdate()->findOrFail($expense->id);
            $before = $expense->toArray();
            $expense->fill($request->validated())->save();
            Audit::record('update', $expense->getTable(), $expense->id, ['before' => $before, 'after' => $expense->toArray()]);
        });

        return redirect()->route('expenses.show', $expense)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Expense $expense)
    {
        Registry::authorize('expenses', true);
        DB::transaction(function () use ($expense) {
            $id = $expense->id;
            $table = $expense->getTable();
            $snapshot = $expense->toArray();
            $expense->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('expenses.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
