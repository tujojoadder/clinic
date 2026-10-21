@extends('layouts.app', ['title' => 'খরচের খাত'])

@php $canWrite = \App\Support\Registry::can('expense_categories', true); @endphp

@section('heading-action')
    @if ($canWrite)
        <button type="button" class="btn primary" onclick="catOpen()">+ নতুন খাত</button>
    @endif
@endsection

@section('content')
    <x-list-print title="খরচের খাতের তালিকা" :paginator="$records">
        <section class="panel list">
            <div class="filters">
                <form method="get" id="catFilter">
                    @if (request()->filled('per_page'))
                        <input type="hidden" name="per_page" value="{{ per_page() }}">
                    @endif
                    <div class="sbox {{ request()->filled('q') ? 'has' : '' }}">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input name="q" value="{{ request('q') }}" placeholder="খাতের নাম দিয়ে খুঁজুন"
                            oninput="this.parentNode.classList.toggle('has', this.value !== '')">
                        <button type="button" class="sclear" title="মুছুন"
                            onclick="var f=this.form;f.q.value='';@if (request()->filled('q')) f.submit();@else this.parentNode.classList.remove('has');f.q.focus(); @endif"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                    <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> খুঁজুন</button>
                </form>
                <div class="filters-right">
                    <x-per-page :paginator="$records" />
                    <x-print-button />
                </div>
            </div>

            <div class="tablewrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>খাতের নাম</th>
                            <th>খরচের সংখ্যা</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->name }}</td>
                                <td>{{ $record->expenses_count }}</td>
                                <td class="noprint">
                                    @if ($canWrite)
                                        <button type="button" class="btn abtn edit"
                                            onclick="catOpen({{ $record->id }}, @js($record->name))"><i
                                                class="fa-solid fa-pen-to-square"></i> এডিট</button>
                                        <button type="button" class="btn abtn danger"
                                            onclick="catDelete({{ $record->id }}, @js($record->name))"><i
                                                class="fa-solid fa-trash"></i> ডিলেট</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">কোনো খাত নেই। নতুন খাত যোগ করুন।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$records" />
        </section>
    </x-list-print>

    @if ($canWrite)
        <dialog id="catModal" data-quick-modal class="noprint">
            <div class="modalhead">
                <h2 id="catTitle">নতুন খাত</h2>
                <button type="button" class="close" data-quick-close aria-label="বন্ধ"></button>
            </div>
            <form id="catForm" onsubmit="catSave(event)">
                <div class="panelbody">
                    <div class="error qa-errors hidden" id="catError"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="cat_name" class="form-label">খাতের নাম *</label>
                            <input id="cat_name" name="name" type="text" maxlength="100" class="form-control w-100"
                                required>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn" data-quick-close>বাতিল</button>
                        <button class="btn primary" id="catBtn">সংরক্ষণ করুন</button>
                    </div>
                </div>
            </form>
        </dialog>

        @push('scripts')
            <script src="{{ asset('assets/js/quick-add.js') }}?v=1"></script>
            <script>
                (function() {
                    var modal = document.getElementById('catModal'),
                        form = document.getElementById('catForm'),
                        err = document.getElementById('catError'),
                        btn = document.getElementById('catBtn'),
                        editId = null,
                        token = @js(csrf_token()),
                        updateUrl = @js(route('expense_categories.update', '__ID__')),
                        storeUrl = @js(route('expense_categories.store'));

                    function showError(msg) {
                        err.textContent = msg;
                        err.classList.remove('hidden');
                    }

                    window.catOpen = function(id, name) {
                        editId = id || null;
                        document.getElementById('catTitle').textContent = editId ? 'খাত সম্পাদনা' : 'নতুন খাত';
                        form.name.value = name || '';
                        err.classList.add('hidden');
                        modal.showModal();
                        form.name.focus();
                    };

                    window.catSave = function(e) {
                        e.preventDefault();
                        btn.disabled = true;
                        var body = new FormData(form);
                        if (editId) body.append('_method', 'PUT');
                        fetch(editId ? updateUrl.replace('__ID__', editId) : storeUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: body
                            })
                            .then(function(r) {
                                return r.json().then(function(d) {
                                    return {
                                        ok: r.ok,
                                        status: r.status,
                                        d: d
                                    };
                                });
                            })
                            .then(function(x) {
                                if (x.ok) return location.reload();
                                if (x.status === 422 && x.d.errors) return showError(Object.values(x.d.errors).flat()
                                    .join(' '));
                                if (x.status === 419 || x.status === 401) return showError(
                                    'সেশন শেষ হয়েছে, পেজ রিফ্রেশ করুন।');
                                showError(x.d.message || 'কিছু ভুল হয়েছে, আবার চেষ্টা করুন।');
                            })
                            .catch(function() {
                                showError('সংযোগে সমস্যা হয়েছে, আবার চেষ্টা করুন।');
                            })
                            .finally(function() {
                                btn.disabled = false;
                            });
                    };

                    window.catDelete = function(id, name) {
                        confirmDelete('"' + name + '" খাতটি মুছে ফেলবেন?').then(function(yes) {
                            if (!yes) return;
                            fetch(updateUrl.replace('__ID__', id), {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: '_method=DELETE'
                                })
                                .then(function(r) {
                                    return r.json().then(function(d) {
                                        return {
                                            ok: r.ok,
                                            d: d
                                        };
                                    });
                                })
                                .then(function(x) {
                                    if (x.ok) return location.reload();
                                    alertError(x.d.message || 'মোছা যায়নি।');
                                });
                        });
                    };

                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) modal.close();
                    });
                })();
            </script>
        @endpush
    @endif
@endsection
