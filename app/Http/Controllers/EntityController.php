<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntityRequest;
use App\Services\EntityService;
use App\Support\Registry;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index(Request $request, string $module)
    {
        Registry::authorize($module);
        $definition = Registry::definition($module);
        $query = Registry::model($module)->newQuery();
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $columns = array_keys($definition['fields']);
            $query->where(function ($query) use ($columns, $search) {
                foreach ($columns as $column) if ($column !== 'password') $query->orWhere($column, 'like', '%'.$search.'%');
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();
        if ($module === 'beds') return view('beds.index', compact('module', 'definition', 'records'));
        return view('entities.index', compact('module', 'definition', 'records'));
    }

    public function create(string $module)
    {
        Registry::authorize($module, true);
        return view('entities.form', ['module' => $module, 'definition' => Registry::definition($module), 'record' => Registry::model($module)]);
    }

    public function edit(string $module, int $record)
    {
        Registry::authorize($module, true);
        return view('entities.form', ['module' => $module, 'definition' => Registry::definition($module), 'record' => Registry::model($module)->findOrFail($record)]);
    }

    public function show(string $module, int $record)
    {
        Registry::authorize($module);
        return view('entities.show', ['module' => $module, 'definition' => Registry::definition($module), 'record' => Registry::model($module)->findOrFail($record)]);
    }

    public function store(EntityRequest $request, string $module, EntityService $service)
    {
        $record = $service->save($module, $request->validated());
        return redirect()->route('entities.show', [$module, $record->id])->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function update(EntityRequest $request, string $module, int $record, EntityService $service)
    {
        $record = $service->save($module, $request->validated(), Registry::model($module)->findOrFail($record));
        return redirect()->route('entities.show', [$module, $record->id])->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(string $module, int $record, EntityService $service)
    {
        Registry::authorize($module, true);
        $service->delete($module, Registry::model($module)->findOrFail($record));
        return redirect()->route('entities.index', $module)->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
