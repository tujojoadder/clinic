<dialog id="quickReference" data-quick-modal>
    <div class="modalhead">
        <h2>নতুন রেফারেন্স</h2>
        <button type="button" class="close" data-quick-close aria-label="বন্ধ">&times;</button>
    </div>
    <form data-quick-form data-target="reference_id" method="post" action="{{ route('ajax.references.store') }}">
        @csrf
        <div class="panelbody">
            <div class="error qa-errors" hidden></div>
            <div class="formgrid">
                <div>
                    <label for="qr_name">নাম *</label>
                    <input id="qr_name" name="name" type="text" required>
                </div>
                <div>
                    <label for="qr_phone">মোবাইল *</label>
                    <input id="qr_phone" name="phone" type="text" required>
                </div>
                <div>
                    <label for="qr_commission_percent">কমিশন (%) *</label>
                    <input id="qr_commission_percent" name="commission_percent" type="number" min="0"
                        max="100" step="0.01" required>
                </div>
                <div>
                    <label for="qr_joining_date">জয়েনিং ডেট</label>
                    <input id="qr_joining_date" name="joining_date" type="date" value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn" data-quick-close>বাতিল</button>
                <button class="btn primary">সংরক্ষণ করুন</button>
            </div>
        </div>
    </form>
</dialog>
