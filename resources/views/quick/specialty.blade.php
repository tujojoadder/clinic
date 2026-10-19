<dialog id="quickSpecialty" data-quick-modal>
    <div class="modalhead">
        <h2>নতুন বিভাগ</h2>
        <button type="button" class="close" data-quick-close aria-label="বন্ধ">&times;</button>
    </div>
    <form data-quick-form data-target="specialty_id" method="post" action="{{ route('ajax.specialties.quick') }}">
        @csrf
        <div class="panelbody">
            <div class="error qa-errors" hidden></div>
            <div class="row g-3">
                <div class="col-12">
                    <label for="qs_name" class="form-label">বিভাগের নাম *</label>
                    <input id="qs_name" name="name" type="text" maxlength="150" class="form-control w-100"
                        required>
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn" data-quick-close>বাতিল</button>
                <button class="btn primary">সংরক্ষণ করুন</button>
            </div>
        </div>
    </form>
</dialog>
