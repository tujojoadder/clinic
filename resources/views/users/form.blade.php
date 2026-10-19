@extends('layouts.app', ['title' => 'ইউজার'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ $record->exists ? route('users.update', $record) : route('users.store') }}"
                autocomplete="off">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="name">নাম *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $record->name) }}" required>
                    </div>
                    <div>
                        <label for="email">ইমেইল *</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $record->email) }}"
                            required>
                    </div>
                    <div>
                        <label for="password">পাসওয়ার্ড{{ $record->exists ? ' (ফাঁকা রাখলে অপরিবর্তিত)' : ' *' }}</label>
                        <input id="password" name="password" type="password" minlength="12" maxlength="128"
                            autocomplete="new-password" @required(!$record->exists)>
                    </div>
                    <div>
                        <label for="role">রোল *</label>
                        <select id="role" name="role" required>
                            @foreach (['admin' => 'অ্যাডমিন', 'reception' => 'রিসেপশন', 'doctor' => 'ডাক্তার', 'nurse' => 'নার্স', 'lab' => 'ল্যাব', 'cashier' => 'ক্যাশিয়ার'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('role', $record->role) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="active">সক্রিয় *</label>
                        <select id="active" name="active" required>
                            @foreach (['1' => 'হ্যাঁ', '0' => 'না'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('active', $record->exists ? (int) $record->active : 1) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('users.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
