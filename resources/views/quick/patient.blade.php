<dialog id="quickPatient" data-quick-modal>
    <div class="modalhead">
        <h2>নতুন রোগী</h2>
        <button type="button" class="close" data-quick-close aria-label="বন্ধ">&times;</button>
    </div>
    <form data-quick-form data-target="patient_id" method="post" action="{{ route('ajax.patients.store') }}">
        @csrf
        <div class="panelbody">
            <div class="error qa-errors" hidden></div>
            <div class="formgrid">
                <div>
                    <label for="qp_name">রোগীর নাম *</label>
                    <input id="qp_name" name="name" type="text" required>
                </div>
                <div>
                    <label for="qp_phone">মোবাইল *</label>
                    <input id="qp_phone" name="phone" type="text" required>
                </div>
                <div>
                    <label for="qp_gender">লিঙ্গ *</label>
                    <select id="qp_gender" name="gender" required>
                        <option value="female">মহিলা</option>
                        <option value="male">পুরুষ</option>
                        <option value="other">অন্যান্য</option>
                    </select>
                </div>
                <div>
                    <label for="qp_birth_date">জন্মতারিখ</label>
                    <input id="qp_birth_date" name="birth_date" type="date">
                </div>
                <div>
                    <label for="qp_age_years">বয়স (জন্মতারিখ না থাকলে)</label>
                    <input id="qp_age_years" name="age_years" type="number" min="0" step="1">
                </div>
                <div>
                    <label for="qp_emergency_contact">জরুরি যোগাযোগ</label>
                    <input id="qp_emergency_contact" name="emergency_contact" type="text">
                </div>
                <div>
                    <label for="qp_address">ঠিকানা</label>
                    <textarea id="qp_address" name="address"></textarea>
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn" data-quick-close>বাতিল</button>
                <button class="btn primary">সংরক্ষণ করুন</button>
            </div>
        </div>
    </form>
</dialog>
