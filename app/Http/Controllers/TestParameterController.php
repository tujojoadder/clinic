<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestParameterRequest;
use App\Models\{DiagnosticTest, TestParameter};
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestParameterController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('test_parameters');
        $query = TestParameter::with('test');
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['name', 'unit', 'reference_range', 'sort_order'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('test', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('test_parameters.index', ['module' => 'test_parameters', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('test_parameters', true);

        return view('test_parameters.form', [
            'module' => 'test_parameters',
            'record' => new TestParameter,
            'tests' => DiagnosticTest::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(TestParameterRequest $request)
    {
        $parameter = DB::transaction(function () use ($request) {
            $parameter = TestParameter::create($request->validated());
            Audit::record('create', $parameter->getTable(), $parameter->id, ['before' => [], 'after' => $parameter->toArray()]);

            return $parameter;
        });

        return redirect()->route('test_parameters.show', $parameter)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(TestParameter $test_parameter)
    {
        Registry::authorize('test_parameters');
        $test_parameter->load('test');

        return view('test_parameters.show', ['module' => 'test_parameters', 'record' => $test_parameter]);
    }

    public function edit(TestParameter $test_parameter)
    {
        Registry::authorize('test_parameters', true);

        return view('test_parameters.form', [
            'module' => 'test_parameters',
            'record' => $test_parameter,
            'tests' => DiagnosticTest::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(TestParameterRequest $request, TestParameter $test_parameter)
    {
        DB::transaction(function () use ($request, $test_parameter) {
            $record = TestParameter::lockForUpdate()->findOrFail($test_parameter->id);
            $before = $record->toArray();
            $record->fill($request->validated())->save();
            Audit::record('update', $record->getTable(), $record->id, ['before' => $before, 'after' => $record->toArray()]);
        });

        return redirect()->route('test_parameters.show', $test_parameter)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(TestParameter $test_parameter)
    {
        Registry::authorize('test_parameters', true);
        DB::transaction(function () use ($test_parameter) {
            $id = $test_parameter->id;
            $table = $test_parameter->getTable();
            $snapshot = $test_parameter->toArray();
            $test_parameter->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('test_parameters.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
