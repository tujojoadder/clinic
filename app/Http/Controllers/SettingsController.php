<?php
namespace App\Http\Controllers;
use App\Models\ {
    Setting, AuditLog
};
use App\Support\ {
    Registry, Audit
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SettingsController extends Controller {
    public function index() {
        Registry::authorize('settings');
        $logs = AuditLog::latest('id')->limit(100)->get();
        return view('settings.index', compact('logs'));
    }
    public function update(Request $request) {
        Registry::authorize('settings', true);
        $data = $request->validate(['clinic_name' => 'required|string|max:150', 'clinic_address' => 'nullable|string|max:1000', 'clinic_phone' => 'nullable|string|max:30']);
        DB::transaction(function()use($data) {
            foreach ($data as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
            }
            Audit::record('update', 'settings');
        }        );
        return back()->with('success', 'সেটিংস সংরক্ষণ হয়েছে।');
    }
}
