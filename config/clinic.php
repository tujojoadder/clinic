<?php

return [
    'modules' => [
        'dashboard' => [
            'title' => 'ড্যাশবোর্ড',
            'icon' => 'fa-solid fa-gauge',
            'read' => [
                'reception',
                'doctor',
                'nurse',
                'lab',
                'cashier',
            ],
            'write' => [],
        ],
        'patients' => [
            'title' => 'রোগী',
            'icon' => 'fa-solid fa-user-injured',
            'table' => 'patients',
            'read' => [
                'reception',
                'doctor',
                'nurse',
                'lab',
                'cashier',
            ],
            'write' => [
                'reception',
            ],
            'fields' => [
                'name' => [
                    'রোগীর নাম',
                    'text',
                    true,
                ],
                'birth_date' => [
                    'জন্মতারিখ',
                    'date',
                    false,
                ],
                'age_years' => [
                    'বয়স (জন্মতারিখ না থাকলে)',
                    'number',
                    false,
                ],
                'gender' => [
                    'লিঙ্গ',
                    'select',
                    true,
                    [
                        'female' => 'মহিলা',
                        'male' => 'পুরুষ',
                        'other' => 'অন্যান্য',
                    ],
                ],
                'phone' => [
                    'মোবাইল',
                    'text',
                    true,
                ],
                'address' => [
                    'ঠিকানা',
                    'textarea',
                    false,
                ],
                'emergency_contact' => [
                    'জরুরি যোগাযোগ',
                    'text',
                    false,
                ],
            ],
            'model' => 'App\\Models\\Patient',
        ],
        'doctors' => [
            'title' => 'ডাক্তার',
            'icon' => 'fa-solid fa-user-doctor',
            'table' => 'doctors',
            'read' => [
                'reception',
                'doctor',
                'nurse',
                'cashier',
            ],
            'write' => [],
            'fields' => [
                'name' => [
                    'নাম',
                    'text',
                    true,
                ],
                'specialty_id' => [
                    'স্পেশালিটি',
                    'fk',
                    true,
                    'specialties',
                ],
                'phone' => [
                    'মোবাইল',
                    'text',
                    false,
                ],
                'fee' => [
                    'ভিজিট ফি',
                    'number',
                    true,
                ],
                'followup_fee' => [
                    'ফলোআপ ফি',
                    'number',
                    true,
                ],
                'available_days' => [
                    'কোন দিন বসেন',
                    'text',
                    false,
                ],
                'start_time' => [
                    'শুরুর সময়',
                    'time',
                    false,
                ],
                'end_time' => [
                    'শেষ সময়',
                    'time',
                    false,
                ],
                'active' => [
                    'সক্রিয়',
                    'select',
                    true,
                    [
                        '1' => 'হ্যাঁ',
                        '0' => 'না',
                    ],
                ],
            ],
            'model' => 'App\\Models\\Doctor',
        ],
        'specialties' => [
            'title' => 'স্পেশালিটি ',
            'icon' => 'fa-solid fa-stethoscope',
            'table' => 'specialties',
            'read' => [
                'reception',
                'doctor',
                'nurse',
                'cashier',
            ],
            'write' => [],
            'fields' => [
                'name' => [
                    'স্পেশালিটির নাম',
                    'text',
                    true,
                ],
            ],
            'model' => 'App\\Models\\Specialty',
        ],
        'references' => [
            'title' => 'রেফারেন্স',
            'icon' => 'fa-solid fa-user-tag',
            'table' => 'reference_persons',
            'read' => [
                'reception',
                'cashier',
            ],
            'write' => [
                'reception',
            ],
            'fields' => [
                'name' => [
                    'নাম',
                    'text',
                    true,
                ],
                'phone' => [
                    'মোবাইল',
                    'text',
                    true,
                ],
                'commission_percent' => [
                    'কমিশন (%)',
                    'number',
                    true,
                ],
                'joining_date' => [
                    'জয়েনিং ডেট',
                    'date',
                    false,
                ],
            ],
            'model' => 'App\\Models\\ReferencePerson',
        ],
        'visits' => [
            'title' => 'ডাক্তার ও সিরিয়াল',
            'icon' => 'fa-solid fa-calendar-check',
            'table' => 'visits',
            'read' => [
                'reception',
                'doctor',
                'cashier',
            ],
            'write' => [
                'reception',
                'doctor',
            ],
            'fields' => [
                'patient_id' => [
                    'রোগী',
                    'fk',
                    true,
                    'patients',
                ],
                'doctor_id' => [
                    'ডাক্তার',
                    'fk',
                    true,
                    'doctors',
                ],
                'appointment_at' => [
                    'তারিখ ও সময়',
                    'datetime-local',
                    true,
                ],
                'serial_no' => [
                    'সিরিয়াল নম্বর',
                    'number',
                    true,
                ],
                'visit_type' => [
                    'ভিজিট',
                    'select',
                    true,
                    [
                        'new' => 'নতুন',
                        'followup' => 'ফলোআপ',
                    ],
                ],
                'fee' => [
                    'ভিজিট ফি',
                    'number',
                    true,
                ],
                'status' => [
                    'স্ট্যাটাস',
                    'select',
                    true,
                    [
                        'waiting' => 'অপেক্ষমাণ',
                        'completed' => 'সম্পন্ন',
                        'cancelled' => 'বাতিল',
                    ],
                ],
                'notes' => [
                    'নোট',
                    'textarea',
                    false,
                ],
            ],
            'model' => 'App\\Models\\Visit',
        ],
        'admissions' => [
            'title' => 'রোগী ভর্তি',
            'icon' => 'fa-solid fa-hospital-user',
            'table' => 'admissions',
            'read' => [
                'reception',
                'doctor',
                'nurse',
                'cashier',
            ],
            'write' => [
                'reception',
            ],
            'fields' => [
                'patient_id' => [
                    'রোগী',
                    'fk',
                    true,
                    'patients',
                ],
                'doctor_id' => [
                    'ডাক্তার',
                    'fk',
                    true,
                    'doctors',
                ],
                'admitted_at' => [
                    'ভর্তির তারিখ ও সময়',
                    'datetime-local',
                    true,
                ],
                'reason' => [
                    'ভর্তির কারণ',
                    'textarea',
                    true,
                ],
            ],
            'model' => 'App\\Models\\Admission',
        ],
        'beds' => [
            'title' => 'বেড ও কেবিন',
            'icon' => 'fa-solid fa-bed',
            'table' => 'beds',
            'read' => [
                'reception',
                'doctor',
                'nurse',
            ],
            'write' => [
                'reception',
            ],
            'fields' => [
                'name' => [
                    'বেড / কেবিন নম্বর',
                    'text',
                    true,
                ],
                'ward' => [
                    'ওয়ার্ড',
                    'text',
                    true,
                ],
                'type' => [
                    'ধরন',
                    'select',
                    true,
                    [
                        'ward' => 'সাধারণ',
                        'cabin' => 'কেবিন',
                        'icu' => 'ICU',
                    ],
                ],
                'daily_rate' => [
                    'প্রতি ২৪ ঘণ্টার ভাড়া',
                    'number',
                    true,
                ],
                'state' => [
                    'অবস্থা',
                    'select',
                    true,
                    [
                        'available' => 'খালি',
                        'cleaning' => 'পরিষ্কার',
                        'inactive' => 'নিষ্ক্রিয়',
                    ],
                ],
            ],
            'model' => 'App\\Models\\Bed',
        ],
        'nursing_records' => [
            'title' => 'নার্সিং রেকর্ড',
            'icon' => 'fa-solid fa-heart-pulse',
            'table' => 'nursing_records',
            'read' => [
                'doctor',
                'nurse',
            ],
            'write' => [
                'nurse',
                'doctor',
            ],
            'fields' => [
                'admission_id' => [
                    'ভর্তি নম্বর',
                    'fk',
                    true,
                    'admissions',
                ],
                'recorded_at' => [
                    'পর্যবেক্ষণের সময়',
                    'datetime-local',
                    true,
                ],
                'blood_pressure' => [
                    'রক্তচাপ (mmHg)',
                    'text',
                    false,
                ],
                'temperature' => [
                    'তাপমাত্রা (°F)',
                    'number',
                    false,
                ],
                'pulse' => [
                    'পালস',
                    'number',
                    false,
                ],
                'notes' => [
                    'নার্সিং নোট',
                    'textarea',
                    false,
                ],
            ],
            'model' => 'App\\Models\\NursingRecord',
        ],
        'lab_orders' => [
            'title' => 'টেস্ট ও ল্যাব',
            'icon' => 'fa-solid fa-flask-vial',
            'table' => 'lab_orders',
            'read' => [
                'reception',
                'doctor',
                'lab',
                'cashier',
            ],
            'write' => [
                'reception',
                'lab',
            ],
            'fields' => [
                'patient_id' => [
                    'রোগী',
                    'fk',
                    true,
                    'patients',
                ],
                'admission_id' => [
                    'ভর্তি নম্বর (ঐচ্ছিক)',
                    'fk',
                    false,
                    'admissions',
                ],
                'test_id' => [
                    'টেস্ট',
                    'fk',
                    true,
                    'tests',
                ],
                'ordered_at' => [
                    'অর্ডারের সময়',
                    'datetime-local',
                    true,
                ],
            ],
            'model' => 'App\\Models\\LabOrder',
        ],
        'invoices' => [
            'title' => 'বিলিং ও পেমেন্ট',
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'table' => 'invoices',
            'read' => [
                'reception',
                'cashier',
            ],
            'write' => [
                'reception',
                'cashier',
            ],
            'fields' => [
                'patient_id' => [
                    'রোগী',
                    'fk',
                    true,
                    'patients',
                ],
                'discount' => [
                    'ছাড়',
                    'number',
                    true,
                ],
                'notes' => [
                    'বিলের নোট',
                    'textarea',
                    false,
                ],
            ],
            'model' => 'App\\Models\\Invoice',
        ],
        'discharge' => [
            'title' => 'ডিসচার্জ',
            'icon' => 'fa-solid fa-house-medical',
            'read' => [
                'reception',
                'doctor',
                'nurse',
                'cashier',
            ],
            'write' => [
                'reception',
                'doctor',
            ],
        ],
        'expenses' => [
            'title' => 'খরচ',
            'icon' => 'fa-solid fa-money-bill-transfer',
            'table' => 'expenses',
            'read' => [
                'cashier',
            ],
            'write' => [
                'cashier',
            ],
            'fields' => [
                'category' => [
                    'খাত',
                    'text',
                    true,
                ],
                'description' => [
                    'বিবরণ',
                    'text',
                    true,
                ],
                'amount' => [
                    'পরিমাণ',
                    'number',
                    true,
                ],
                'method' => [
                    'পদ্ধতি',
                    'select',
                    true,
                    [
                        'cash' => 'ক্যাশ',
                        'bkash' => 'bKash',
                        'nagad' => 'Nagad',
                        'bank' => 'ব্যাংক',
                    ],
                ],
                'expense_date' => [
                    'তারিখ',
                    'date',
                    true,
                ],
            ],
            'model' => 'App\\Models\\Expense',
        ],
        'doctor_ledgers' => [
            'title' => 'ডাক্তারের হিসাব',
            'icon' => 'fa-solid fa-book',
            'table' => 'doctor_ledgers',
            'read' => [
                'cashier',
            ],
            'write' => [
                'cashier',
            ],
            'fields' => [
                'doctor_id' => [
                    'ডাক্তার',
                    'fk',
                    true,
                    'doctors',
                ],
                'type' => [
                    'এন্ট্রি',
                    'select',
                    true,
                    [
                        'payable' => 'প্রাপ্য',
                        'settlement' => 'পরিশোধ',
                    ],
                ],
                'amount' => [
                    'পরিমাণ',
                    'number',
                    true,
                ],
                'entry_date' => [
                    'তারিখ',
                    'date',
                    true,
                ],
                'notes' => [
                    'বিবরণ',
                    'text',
                    true,
                ],
            ],
            'model' => 'App\\Models\\DoctorLedger',
        ],

        'accounts' => [
            'title' => 'অ্যাকাউন্ট',
            'icon' => 'fa-solid fa-building-columns',
            'table' => 'accounts',
            'read' => [
                'cashier',
            ],
            'write' => [],
            'fields' => [
                'name' => [
                    'অ্যাকাউন্টের নাম',
                    'text',
                    true,
                ],
                'holder_name' => [
                    'হোল্ডারের নাম',
                    'text',
                    true,
                ],
                'account_number' => [
                    'অ্যাকাউন্ট নম্বর',
                    'text',
                    true,
                ],
            ],
            'model' => 'App\\Models\\Account',
        ],
        'expense_categories' => [
            'title' => 'খরচের খাত',
            'icon' => 'fa-solid fa-tags',
            'table' => 'expense_categories',
            'read' => [
                'cashier',
            ],
            'write' => [],
            'fields' => [
                'name' => [
                    'খাতের নাম',
                    'text',
                    true,
                ],
            ],
            'model' => 'App\\Models\\ExpenseCategory',
        ],

        'reports' => [
            'title' => 'রিপোর্ট',
            'icon' => 'fa-solid fa-chart-line',
            'read' => [
                'cashier',
            ],
            'write' => [],
        ],
        'reports_income' => [
            'title' => 'আদায় ও ব্যয়',
            'icon' => 'fa-solid fa-coins',
            'read' => ['cashier'],
            'write' => [],
        ],
        'reports_daily' => [
            'title' => 'দৈনিক আদায়',
            'icon' => 'fa-solid fa-calendar-day',
            'read' => ['cashier'],
            'write' => [],
        ],
        'reports_dues' => [
            'title' => 'বর্তমান বকেয়া',
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'read' => ['cashier'],
            'write' => [],
        ],
        'reports_sales' => [
            'title' => 'টেস্ট বিক্রি',
            'icon' => 'fa-solid fa-flask-vial',
            'read' => ['cashier'],
            'write' => [],
        ],
        'reports_admissions' => [
            'title' => 'ভর্তি ও ডিসচার্জ',
            'icon' => 'fa-solid fa-hospital-user',
            'read' => ['cashier'],
            'write' => [],
        ],
        'reports_doctors' => [
            'title' => 'ডাক্তারের প্রাপ্য',
            'icon' => 'fa-solid fa-user-doctor',
            'read' => ['cashier'],
            'write' => [],
        ],
        'users' => [
            'title' => 'ইউজার',
            'icon' => 'fa-solid fa-users',
            'table' => 'users',
            'read' => [],
            'write' => [],
            'fields' => [
                'name' => [
                    'নাম',
                    'text',
                    true,
                ],
                'email' => [
                    'ইমেইল',
                    'email',
                    true,
                ],
                'password' => [
                    'পাসওয়ার্ড (সম্পাদনার সময় ফাঁকা রাখলে অপরিবর্তিত)',
                    'password',
                    false,
                ],
                'role' => [
                    'রোল',
                    'select',
                    true,
                    [
                        'admin' => 'অ্যাডমিন',
                        'reception' => 'রিসেপশন',
                        'doctor' => 'ডাক্তার',
                        'nurse' => 'নার্স',
                        'lab' => 'ল্যাব',
                        'cashier' => 'ক্যাশিয়ার',
                    ],
                ],
                'active' => [
                    'সক্রিয়',
                    'select',
                    true,
                    [
                        '1' => 'হ্যাঁ',
                        '0' => 'না',
                    ],
                ],
            ],
            'model' => 'App\\Models\\User',
        ],
        'settings' => [
            'title' => 'সেটিংস',
            'icon' => 'fa-solid fa-gear',
            'read' => [],
            'write' => [],
        ],
        'tests' => [
            'title' => 'টেস্ট ও মূল্য',
            'icon' => 'fa-solid fa-vials',
            'table' => 'tests',
            'read' => [
                'reception',
                'lab',
                'doctor',
                'cashier',
            ],
            'write' => [],
            'fields' => [
                'name' => [
                    'টেস্টের নাম',
                    'text',
                    true,
                ],
                'sample_type' => [
                    'নমুনা',
                    'text',
                    true,
                ],
                'price' => [
                    'মূল্য',
                    'number',
                    true,
                ],
                'turnaround_hours' => [
                    'সময় (ঘণ্টা)',
                    'number',
                    true,
                ],
                'active' => [
                    'সক্রিয়',
                    'select',
                    true,
                    [
                        '1' => 'হ্যাঁ',
                        '0' => 'না',
                    ],
                ],
            ],
            'model' => 'App\\Models\\DiagnosticTest',
        ],
        'test_parameters' => [
            'title' => 'রিপোর্ট টেমপ্লেট',
            'icon' => 'fa-solid fa-clipboard-list',
            'table' => 'test_parameters',
            'read' => [
                'lab',
                'doctor',
            ],
            'write' => [],
            'fields' => [
                'test_id' => [
                    'টেস্ট',
                    'fk',
                    true,
                    'tests',
                ],
                'name' => [
                    'প্যারামিটার',
                    'text',
                    true,
                ],
                'unit' => [
                    'ইউনিট',
                    'text',
                    false,
                ],
                'reference_range' => [
                    'যাচাইকৃত রেফারেন্স রেঞ্জ',
                    'text',
                    false,
                ],
                'sort_order' => [
                    'ক্রম',
                    'number',
                    true,
                ],
            ],
            'model' => 'App\\Models\\TestParameter',
        ],
    ],
    'menu' => [
        ['module' => 'dashboard'],
        [
            'title' => 'রোগী ও সিরিয়াল',
            'icon'  => 'fa-solid fa-user-injured',
            'items' => ['patients', 'visits'],
        ],
        [
            'title' => 'ভর্তি ও ডিসচার্জ',
            'icon'  => 'fa-solid fa-hospital',
            'items' => ['admissions', 'discharge'],
        ],
        [
            'title' => 'বেড ও নার্সিং',
            'icon'  => 'fa-solid fa-bed',
            'items' => ['beds', 'nursing_records'],
        ],
        [
            'title' => 'ল্যাব ও টেস্ট',
            'icon'  => 'fa-solid fa-flask-vial',
            'items' => ['tests', 'lab_orders', 'test_parameters'],
        ],
        [
            'title' => 'ডাক্তার',
            'icon'  => 'fa-solid fa-user-doctor',
            'items' => ['doctors', 'specialties', 'doctor_ledgers'],
        ],
        ['module' => 'references'],
        [
            'title' => 'বিলিং ও আয়-ব্যয়',
            'icon'  => 'fa-solid fa-wallet',
            'items' => [
                'invoices',
                'expenses',
                'expense_categories',
                'accounts',
            ],
        ],
        [
            'title' => 'রিপোর্ট',
            'icon'  => 'fa-solid fa-chart-line',
            'items' => [
                'reports_income',
                'reports_daily',
                'reports_dues',
                'reports_sales',
                'reports_admissions',
                'reports_doctors',
            ],
        ],
        [
            'title' => 'ইউজার ও সেটিংস',
            'icon'  => 'fa-solid fa-gear',
            'items' => ['users', 'settings'],
        ],
    ],
    'bed_rental' => 'started_24_hours',
];
