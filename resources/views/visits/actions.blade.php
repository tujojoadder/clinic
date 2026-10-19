@if(\App\Support\Registry::can('visits', true))
<form method="post" action="{{ route('workflows.action', ['visits',$record->id,'visit_complete']) }}" class="noprint">@csrf<button class="btn primary">ভিজিট সম্পন্ন</button>
</form>
@endif
@if($record->invoice && \App\Support\Registry::can('invoices'))<a class="btn" href="{{ route('entities.show',['invoices',$record->invoice->id]) }}">ভিজিট বিল</a>
@endif
