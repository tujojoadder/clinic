<?php

namespace App\Http\Controllers;

use App\Http\Requests\BedRequest;
use App\Models\Bed;
use App\Support\{Audit, Registry};
use DomainException;
use Illuminate\Support\Facades\DB;

class BedController extends Controller
{
    public function index()
    {
        Registry::authorize('beds');
        $records = Bed::latest('id')->paginate(25);

        return view('beds.index', ['module' => 'beds', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('beds', true);

        return view('beds.form', ['module' => 'beds', 'record' => new Bed]);
    }

    public function store(BedRequest $request)
    {
        $bed = DB::transaction(function () use ($request) {
            $bed = Bed::create($request->validated());
            Audit::record('create', $bed->getTable(), $bed->id, ['before' => [], 'after' => $bed->toArray()]);

            return $bed;
        });

        return redirect()->route('beds.show', $bed)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(Bed $bed)
    {
        Registry::authorize('beds');

        return view('beds.show', ['module' => 'beds', 'record' => $bed]);
    }

    public function edit(Bed $bed)
    {
        Registry::authorize('beds', true);

        return view('beds.form', ['module' => 'beds', 'record' => $bed]);
    }

    public function update(BedRequest $request, Bed $bed)
    {
        DB::transaction(function () use ($request, $bed) {
            $bed = Bed::lockForUpdate()->findOrFail($bed->id);
            $data = $request->validated();
            if ($bed->stays()->whereNull('ended_at')->exists() && $data['state'] !== 'available') {
                throw new DomainException('দখল বেডের অবস্থা পরিবর্তন নয়।');
            }
            $before = $bed->toArray();
            $bed->fill($data)->save();
            Audit::record('update', $bed->getTable(), $bed->id, ['before' => $before, 'after' => $bed->toArray()]);
        });

        return redirect()->route('beds.show', $bed)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Bed $bed)
    {
        Registry::authorize('beds', true);
        DB::transaction(function () use ($bed) {
            $id = $bed->id;
            $table = $bed->getTable();
            $snapshot = $bed->toArray();
            $bed->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('beds.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
