<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('accounts');
        $query = Account::query();
        if ($request->filled('q')) {
            $q = '%' . mb_substr((string) $request->q, 0, 150) . '%';
            $query->where(fn($w) => $w->where('name', 'like', $q)
                ->orWhere('holder_name', 'like', $q)
                ->orWhere('account_number', 'like', $q));
        }
        $records = $query->latest('id')->paginate(per_page())->withQueryString();

        return view('accounts.index', ['module' => 'accounts', 'records' => $records]);
    }

    public function store(AccountRequest $request)
    {
        $account = DB::transaction(function () use ($request) {
            $data = $this->data($request);
            if ($data['is_default']) {
                Account::query()->update(['is_default' => false]); // আগের default বাদ
            }
            $account = Account::create($data);
            Audit::record('create', $account->getTable(), $account->id, ['before' => [], 'after' => $account->toArray()]);

            return $account;
        });

        return response()->json($this->payload($account), 201);
    }

    public function update(AccountRequest $request, Account $account)
    {
        DB::transaction(function () use ($request, $account) {
            $account = Account::lockForUpdate()->findOrFail($account->id);
            $before = $account->toArray();
            $data = $this->data($request);
            if ($data['is_default']) {
                Account::where('id', '!=', $account->id)->update(['is_default' => false]);
            }
            $account->fill($data)->save();
            Audit::record('update', $account->getTable(), $account->id, ['before' => $before, 'after' => $account->toArray()]);
        });

        return response()->json($this->payload($account->refresh()));
    }

    public function destroy(Account $account)
    {
        Registry::authorize('accounts', true);
        DB::transaction(function () use ($account) {
            $id = $account->id;
            $table = $account->getTable();
            $snapshot = $account->toArray();
            $account->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return response()->json(['deleted' => true]);
    }

    private function data(AccountRequest $request): array
    {
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');

        return $data;
    }

    private function payload(Account $a): array
    {
        return [
            'id'             => $a->id,
            'name'           => $a->name,
            'holder_name'    => $a->holder_name,
            'account_number' => $a->account_number,
            'is_default'     => $a->is_default,
        ];
    }
}
