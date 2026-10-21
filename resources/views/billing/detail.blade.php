@php $totals=app(\App\Services\BillingService::class)->totals($record); @endphp
<div class="grid">
    <section class="panel">
        <div class="panelhead">
            <h2>INV-{{ $record->id }} · চার্জ</h2>
            <span class="badge">{{ $record->status }}</span>
        </div>
        <div class="tablewrap">
            <table>
                <tr>
                    <th>বিবরণ</th>
                    <th>পরিমাণ</th>
                    <th>দর</th>
                    <th>মোট</th>
                    <th class="noprint" style="width:1%;white-space:nowrap">অ্যাকশন</th>
                </tr>
                @foreach ($record->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ \App\Support\Format::money($item->unit_price) }}</td>
                        <td>{{ \App\Support\Format::money($item->quantity * $item->unit_price) }}</td>
                        <td class="noprint" style="width:1%;white-space:nowrap">
                            @if ($record->status === 'open' && $item->kind === 'service' && \App\Support\Registry::can('invoices', true))
                                <div style="display:flex;align-items:center;gap:6px">
                                    {{-- এডিট --}}
                                    <a href="{{ route('entities.show', ['invoices', $record->id]) . '?item_edit=' . $item->id }}"
                                        title="এডিট" aria-label="এডিট"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1px solid #bfe3df;background:#e8f6f4;color:#087f73;text-decoration:none;font-size:14px;cursor:pointer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- মুছুন --}}
                                    <form method="post"
                                        action="{{ route('workflows.action', ['invoices', $record->id, 'service_delete']) }}"
                                        data-confirm="চার্জ মুছবেন?" style="margin:0">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                                        <button type="submit" title="মুছুন" aria-label="মুছুন"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;padding:0;border-radius:8px;border:1px solid #f3c4c4;background:#fdecec;color:#c0392b;font-size:14px;cursor:pointer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @if ($record->admission_id && $record->status === 'open' && \App\Support\Registry::can('invoices', true))
            <div class="panelbody">
                <form method="post" action="{{ route('workflows.action', ['invoices', $record->id, 'sync_rent']) }}">
                    @csrf<button class="btn">আজ পর্যন্ত বেডভাড়া হিসাব করুন</button>
                </form>
            </div>
        @endif
    </section>
    <section class="panel">
        <div class="panelhead">
            <h2>বিলের সারসংক্ষেপ</h2>
        </div>
        <div class="panelbody">
            <div class="summary">
                @foreach (['subtotal' => 'মোট চার্জ', 'discount' => 'ছাড়', 'total' => 'মোট বিল', 'paid' => 'নিট আদায়', 'due' => 'বকেয়া / অতিরিক্ত অগ্রিম'] as $key => $label)
                    <div>
                        <span>{{ $label }}</span>
                        <b>{{ \App\Support\Format::money($totals[$key]) }}</b>
                    </div>
                @endforeach
            </div>

            <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:15px;">

                {{-- প্রিন্ট --}}
                <button type="button" class="btn noprint" data-print>
                    প্রিন্ট
                </button>

                @if ($record->status === 'open' && !$record->admission_id && \App\Support\Registry::can('invoices', true))

                    {{-- চূড়ান্ত করুন --}}
                    <form method="post"
                        action="{{ route('workflows.action', ['invoices', $record->id, 'invoice_state']) }}"
                        class="noprint" style="margin:0;">
                        @csrf
                        <input type="hidden" name="state" value="finalized">

                        <button type="submit" class="btn">
                            <i class="fa-solid fa-check"></i> চূড়ান্ত করুন
                        </button>
                    </form>

                    @if (auth()->user()->role === 'admin')
                        {{-- বাতিল --}}
                        <form method="post"
                            action="{{ route('workflows.action', ['invoices', $record->id, 'invoice_state']) }}"
                            class="noprint" data-confirm="বিল বাতিল?" style="margin:0;">
                            @csrf
                            <input type="hidden" name="state" value="void">

                            <button type="submit" class="btn">
                                <i class="fa-solid fa-ban"></i> বাতিল
                            </button>
                        </form>
                    @endif
                @endif

            </div>

        </div>
    </section>
</div>
@if (\App\Support\Registry::can('invoices', true) && $record->status !== 'void')
    <section class="panel noprint">
        <div class="panelhead">
            <h2>পেমেন্ট / রিফান্ড</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ route('workflows.action', ['invoices', $record->id, 'payment']) }}">@csrf
                <div class="formgrid">
                    @include('components.field', [
                        'name' => 'amount',
                        'field' => ['পরিমাণ', 'number', true],
                        'value' => null,
                    ])
                    @include('components.field', [
                        'name' => 'account_id',
                        'field' => ['অ্যাকাউন্ট', 'select', true, account_options()->pluck('name', 'id')->all()],
                        'value' => account_options()->firstWhere('is_default', true)?->id,
                    ])
                    @include('components.field', [
                        'name' => 'type',
                        'field' => [
                            'ধরন',
                            'select',
                            true,
                            auth()->user()->role === 'admin'
                                ? ['receipt' => 'আদায়/অগ্রিম', 'refund' => 'রিফান্ড']
                                : ['receipt' => 'আদায়/অগ্রিম'],
                        ],
                        'value' => 'receipt',
                    ])
                    @include('components.field', [
                        'name' => 'transaction_ref',
                        'field' => ['ট্রানজ্যাকশন রেফারেন্স', 'text', false],
                        'value' => '',
                    ])
                    @include('components.field', [
                        'name' => 'reason',
                        'field' => ['কারণ (রিফান্ডে আবশ্যক)', 'text', false],
                        'value' => '',
                    ])
                </div>
                <div class="actions">
                    <button class="btn primary">পেমেন্ট সংরক্ষণ</button>
                </div>
            </form>
        </div>
    </section>
@endif
<section class="panel">
    <div class="panelhead">
        <h2>পেমেন্টের ইতিহাস</h2>
    </div>
    <div class="tablewrap">
        <table>
            <tr>
                <th>রসিদ</th>
                <th>সময়</th>
                <th>ধরন</th>
                <th>অ্যাকাউন্ট</th>
                <th>পরিমাণ</th>
                <th>অ্যাকশন</th>
            </tr>
            @foreach ($record->payments as $payment)
                <tr>
                    <td>RC-{{ $payment->id }}</td>
                    <td>{{ datetime_to_view($payment->paid_at) }}</td>
                    <td>{{ $payment->type }} {{ $payment->voided_at ? '· বাতিল' : '' }}</td>
                    <td>{{ $payment->account?->name }}</td>
                    <td>{{ \App\Support\Format::money($payment->amount) }}</td>
                    <td class="noprint">
                        @if (!$payment->voided_at && auth()->user()->role === 'admin')
                            <form method="post"
                                action="{{ route('workflows.action', ['invoices', $record->id, 'void_payment']) }}"
                                data-confirm="পেমেন্ট বাতিল করবেন?">@csrf<input type="hidden" name="payment_id"
                                    value="{{ $payment->id }}">
                                <input name="reason" required placeholder="কারণ">
                                <button class="btn">বাতিল</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</section>
@if ($record->status === 'open' && \App\Support\Registry::can('invoices', true))
    @php $editing=request('item_edit')?$record->items()->where('kind','service')->findOrFail(request('item_edit')):null; @endphp
    <section class="panel noprint">
        <div class="panelhead">
            <h2>সার্ভিস চার্জ যোগ / সম্পাদনা</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ route('workflows.action', ['invoices', $record->id, 'service_save']) }}">
                @csrf
                @if ($editing)
                    <input type="hidden" name="item_id" value="{{ $editing->id }}">
                @endif
                <div class="formgrid">
                    @include('components.field', [
                        'name' => 'description',
                        'field' => ['সেবার বিবরণ', 'text', true],
                        'value' => $editing?->description,
                    ])
                    @include('components.field', [
                        'name' => 'quantity',
                        'field' => ['পরিমাণ', 'number', true],
                        'value' => $editing?->quantity ?? 1,
                    ])
                    @include('components.field', [
                        'name' => 'unit_price',
                        'field' => ['দর', 'number', true],
                        'value' => $editing?->unit_price,
                    ])
                </div>
                <div class="actions">
                    <button class="btn primary">চার্জ সংরক্ষণ</button>
                </div>
            </form>
        </div>
    </section>
@endif
