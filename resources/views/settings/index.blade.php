@extends('layouts.app',['title'=>'সেটিংস ও অডিট লগ','module'=>'settings'])
@section('content')
<section class="panel">
<div class="panelhead">
<h2>ক্লিনিকের তথ্য</h2>
</div>
<div class="panelbody">
<form method="post" action="{{ route('settings.update') }}">@csrf<div class="formgrid">
@foreach(['clinic_name'=>'ক্লিনিকের নাম','clinic_address'=>'ঠিকানা','clinic_phone'=>'ফোন'] as $name=>$label)
@include('components.field',['name'=>$name,'field'=>[$label,'text',false],'value'=>\App\Support\Registry::setting($name)])
@endforeach</div>
<div class="actions">
<button class="btn primary">সংরক্ষণ</button>
</div>
</form>
</div>
</section>
<div class="quick">
<a class="btn" href="{{ route('entities.index','users') }}">ইউজার</a>
<a class="btn" href="{{ route('entities.index','tests') }}">টেস্ট ও মূল্য</a>
<a class="btn" href="{{ route('entities.index','test_parameters') }}">রিপোর্ট টেমপ্লেট</a>
</div>
<section class="panel">
<div class="panelhead">
<h2>অডিট লগ · সর্বশেষ ১০০</h2>
</div>
<div class="tablewrap">
<table>
<tr>
<th>সময়</th>
<th>ইউজার</th>
<th>কাজ</th>
<th>রেকর্ড</th>
</tr>
@foreach($logs as $log)<tr>
<td>{{ $log->created_at }}</td>
<td>{{ \App\Models\User::find($log->user_id)?->name }}</td>
<td>{{ $log->action }}</td>
<td>{{ $log->entity }} #{{ $log->entity_id }}</td>
</tr>
@endforeach</table>
</div>
</section>
@endsection
