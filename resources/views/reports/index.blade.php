@extends('layouts.app',['title'=>'রিপোর্ট','module'=>'reports'])
@section('content')
<form method="get" class="filters noprint">
<input type="date" name="from" value="{{ $from }}">
<input type="date" name="to" value="{{ $to }}">
<button class="btn primary">রিপোর্ট দেখুন</button>
<button type="button" class="btn" data-print>প্রিন্ট</button>
</form>
<section class="panel">
<div class="panelhead">
<h2>আদায় ও ব্যয়</h2>
</div>
<div class="panelbody summary">
<div>
<span>নিট আদায়</span>
<b>{{ \App\Support\Format::money($income) }}</b>
</div>
<div>
<span>ব্যয়</span>
<b>{{ \App\Support\Format::money($expense) }}</b>
</div>
<div>
<span>আদায় বাদ ব্যয় (লাভ নয়)</span>
<b>{{ \App\Support\Format::money($income-$expense) }}</b>
</div>
</div>
</section>
<section class="panel">
<div class="panelhead">
<h2>দৈনিক আদায়</h2>
</div>
<table>
<tr>
<th>তারিখ</th>
<th>নিট আদায়</th>
</tr>
@foreach($daily as $row)<tr>
<td>{{ $row->day }}</td>
<td>{{ \App\Support\Format::money($row->amount) }}</td>
</tr>
@endforeach</table>
</section>
<section class="panel">
<div class="panelhead">
<h2>বর্তমান বকেয়া</h2>
</div>
<table>
<tr>
<th>রোগী</th>
<th>বিল</th>
<th>বকেয়া</th>
</tr>
@foreach($dues as $row)<tr>
<td>{{ $row['invoice']->patient->name }}</td>
<td>
<a href="{{ route('entities.show',['invoices',$row['invoice']->id]) }}">INV-{{ $row['invoice']->id }}</a>
</td>
<td>{{ \App\Support\Format::money($row['due']) }}</td>
</tr>
@endforeach</table>
</section>
<section class="panel">
<div class="panelhead">
<h2>টেস্ট বিক্রি</h2>
</div>
<table>
<tr>
<th>টেস্ট</th>
<th>সংখ্যা</th>
<th>চার্জ</th>
</tr>
@foreach($sales as $row)<tr>
<td>{{ $row->name }}</td>
<td>{{ $row->count }}</td>
<td>{{ \App\Support\Format::money($row->amount) }}</td>
</tr>
@endforeach</table>
</section>
<section class="panel">
<div class="panelhead">
<h2>ভর্তি ও ডিসচার্জ</h2>
</div>
<div class="panelbody">ভর্তি: {{ $admitted }} · ডিসচার্জ: {{ $discharged }}</div>
</section>
<section class="panel">
<div class="panelhead">
<h2>ডাক্তারের প্রাপ্য</h2>
</div>
<table>
<tr>
<th>ডাক্তার</th>
<th>বাকি প্রাপ্য</th>
</tr>
@foreach($doctorBalances as $row)<tr>
<td>{{ $row->name }}</td>
<td>{{ \App\Support\Format::money($row->balance) }}</td>
</tr>
@endforeach</table>
</section>
@endsection
