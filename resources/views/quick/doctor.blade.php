<dialog id="quickDoctor" data-quick-modal>
    <div class="modalhead">
        <h2>নতুন ডাক্তার</h2>
        <button type="button" class="close" data-quick-close aria-label="বন্ধ">&times;</button>
    </div>
    <form data-quick-form data-target="doctor_id" method="post" action="{{ route('ajax.doctors.store') }}">
        @csrf
        <div class="panelbody">
            <div class="error qa-errors" hidden></div>
            <div class="formgrid">
                <div>
                    <label for="qd_name">নাম *</label>
                    <input id="qd_name" name="name" type="text" required>
                </div>
                <div>
                    <label for="qd_specialty_id">বিভাগ *</label>
                    <select id="qd_specialty_id" name="specialty_id" required>
                        <option value="">নির্বাচন করুন</option>
                        @foreach (\App\Models\Specialty::orderBy('name')->get() as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="qd_phone">মোবাইল</label>
                    <input id="qd_phone" name="phone" type="text">
                </div>
                <div>
                    <label for="qd_fee">ভিজিট ফি *</label>
                    <input id="qd_fee" name="fee" type="number" min="0" step="0.01" required>
                </div>
                <div>
                    <label for="qd_followup_fee">ফলোআপ ফি *</label>
                    <input id="qd_followup_fee" name="followup_fee" type="number" min="0" step="0.01"
                        required>
                </div>
                <div class="daysfield">
                    <label>কোন দিন বসেন</label>
                    <div class="daychecks">
                        @foreach (\App\Models\Day::orderBy('id')->get() as $day)
                            <label class="daycheck">
                                <input type="checkbox" name="days[]" value="{{ $day->id }}">
                                {{ $day->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label for="qd_start_time">শুরুর সময়</label>
                    <input id="qd_start_time" name="start_time" type="time">
                </div>
                <div>
                    <label for="qd_end_time">শেষ সময়</label>
                    <input id="qd_end_time" name="end_time" type="time">
                </div>
                <div>
                    <label for="qd_active">সক্রিয় *</label>
                    <select id="qd_active" name="active" required>
                        <option value="1">হ্যাঁ</option>
                        <option value="0">না</option>
                    </select>
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn" data-quick-close>বাতিল</button>
                <button class="btn primary">সংরক্ষণ করুন</button>
            </div>
        </div>
    </form>
</dialog>
