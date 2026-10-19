<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferenceRequest;
use App\Models\ReferencePerson;
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferenceController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('references');
        $query = ReferencePerson::query();
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['name', 'phone', 'commission_percent', 'joining_date'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('references.index', ['module' => 'references', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('references', true);

        return view('references.form', ['module' => 'references', 'record' => new ReferencePerson]);
    }

    public function store(ReferenceRequest $request)
    {
        $reference = $this->createReference($request->validated());

        return redirect()->route('references.show', $reference)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    // Ajax (modal) থেকে তৈরি
    public function quickStore(ReferenceRequest $request)
    {
        $reference = $this->createReference($request->validated());

        return response()->json(['id' => $reference->id, 'label' => $reference->name], 201);
    }

    // Select2 ajax search
    public function search(Request $request)
    {
        Registry::authorize('references');
        $search = mb_substr((string) $request->q, 0, 150);
        $page = ReferencePerson::query()
            ->when($search !== '', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->latest('id')->paginate(10);

        return response()->json([
            'results'    => $page->getCollection()->map(fn($r) => ['id' => $r->id, 'text' => $r->name])->values(),
            'pagination' => ['more' => $page->hasMorePages()],
        ]);
    }
    private function createReference(array $data): ReferencePerson
    {
        return DB::transaction(function () use ($data) {
            $reference = ReferencePerson::create($data);
            Audit::record('create', $reference->getTable(), $reference->id, ['before' => [], 'after' => $reference->toArray()]);

            return $reference;
        });
    }

    public function show(ReferencePerson $reference)
    {
        Registry::authorize('references');

        return view('references.show', ['module' => 'references', 'record' => $reference]);
    }

    public function edit(ReferencePerson $reference)
    {
        Registry::authorize('references', true);

        return view('references.form', ['module' => 'references', 'record' => $reference]);
    }

    public function update(ReferenceRequest $request, ReferencePerson $reference)
    {
        DB::transaction(function () use ($request, $reference) {
            $reference = ReferencePerson::lockForUpdate()->findOrFail($reference->id);
            $before = $reference->toArray();
            $reference->fill($request->validated())->save();
            Audit::record('update', $reference->getTable(), $reference->id, ['before' => $before, 'after' => $reference->toArray()]);
        });

        return redirect()->route('references.show', $reference)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(ReferencePerson $reference)
    {
        Registry::authorize('references', true);
        DB::transaction(function () use ($reference) {
            $id = $reference->id;
            $table = $reference->getTable();
            $snapshot = $reference->toArray();
            $reference->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('references.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
