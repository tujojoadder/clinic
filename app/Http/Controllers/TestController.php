<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestRequest;
use App\Models\DiagnosticTest;
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('tests');
        $query = DiagnosticTest::query();
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['name', 'sample_type', 'price', 'turnaround_hours', 'active'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('tests.index', ['module' => 'tests', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('tests', true);

        return view('tests.form', ['module' => 'tests', 'record' => new DiagnosticTest]);
    }

    public function store(TestRequest $request)
    {
        $test = DB::transaction(function () use ($request) {
            $test = DiagnosticTest::create($request->validated());
            Audit::record('create', $test->getTable(), $test->id, ['before' => [], 'after' => $test->toArray()]);

            return $test;
        });

        return redirect()->route('tests.show', $test)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(DiagnosticTest $test)
    {
        Registry::authorize('tests');

        return view('tests.show', ['module' => 'tests', 'record' => $test]);
    }

    public function edit(DiagnosticTest $test)
    {
        Registry::authorize('tests', true);

        return view('tests.form', ['module' => 'tests', 'record' => $test]);
    }

    public function update(TestRequest $request, DiagnosticTest $test)
    {
        DB::transaction(function () use ($request, $test) {
            $record = DiagnosticTest::lockForUpdate()->findOrFail($test->id);
            $before = $record->toArray();
            $record->fill($request->validated())->save();
            Audit::record('update', $record->getTable(), $record->id, ['before' => $before, 'after' => $record->toArray()]);
        });

        return redirect()->route('tests.show', $test)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(DiagnosticTest $test)
    {
        Registry::authorize('tests', true);
        DB::transaction(function () use ($test) {
            $id = $test->id;
            $table = $test->getTable();
            $snapshot = $test->toArray();
            $test->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('tests.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
