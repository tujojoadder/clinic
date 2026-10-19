<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Support\{Audit, Registry};
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};

class UserController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('users');
        $query = User::query();
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['name', 'email', 'role', 'active'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('users.index', ['module' => 'users', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('users', true);

        return view('users.form', ['module' => 'users', 'record' => new User]);
    }

    public function store(UserRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            Audit::record('create', $user->getTable(), $user->id, ['before' => [], 'after' => $user->toArray()]);

            return $user;
        });

        return redirect()->route('users.show', $user)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(User $user)
    {
        Registry::authorize('users');

        return view('users.show', ['module' => 'users', 'record' => $user]);
    }

    public function edit(User $user)
    {
        Registry::authorize('users', true);

        return view('users.form', ['module' => 'users', 'record' => $user]);
    }

    public function update(UserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $record = User::lockForUpdate()->findOrFail($user->id);
            $data = $request->validated();
            if ($record->id === auth()->id() && ($data['role'] !== 'admin' || !$data['active'])) {
                throw new DomainException('নিজের অ্যাডমিন অ্যাক্সেস বন্ধ নয়।');
            }
            if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
            else unset($data['password']);
            $before = $record->toArray();
            $record->fill($data)->save();
            Audit::record('update', $record->getTable(), $record->id, ['before' => $before, 'after' => $record->toArray()]);
        });

        return redirect()->route('users.show', $user)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(User $user)
    {
        Registry::authorize('users', true);
        if ($user->id === auth()->id()) throw new DomainException('নিজের ইউজার মোছা যাবে না।');
        DB::transaction(function () use ($user) {
            $id = $user->id;
            $table = $user->getTable();
            $snapshot = $user->toArray();
            $user->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('users.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
