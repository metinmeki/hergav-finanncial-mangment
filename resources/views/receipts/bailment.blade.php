<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وصل أمانة رقم {{ $tx->receipt_number ? str_pad($tx->receipt_number, 6, '0', STR_PAD_LEFT) : $tx->reference_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tahoma', Arial, sans-serif; background: #f5f5f5; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        .print-btn { margin-bottom: 16px; padding: 8px 28px; background: #1a3c5e; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; font-family: 'Tahoma', Arial, sans-serif; }
        .receipt { background: white; width: 210mm; padding: 12mm; border: 1px solid #ccc; }
        .header { text-align: center; margin-bottom: 8px; }
        .company-name { font-size: 18px; font-weight: 700; color: #1a3c5e; }
        .company-sub { font-size: 12px; color: #555; margin-top: 2px; }
        .receipt-number { margin-top: 6px; font-size: 15px; font-weight: 700; color: #1a3c5e; }
        .badge { display: inline-block; margin-top: 6px; border-radius: 6px; padding: 3px 14px; font-size: 12px; font-weight: 600; }
        .divider { border: none; border-top: 2px solid #d97706; margin: 8px 0; }
        .notice { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px; padding: 6px 10px; margin-bottom: 10px; font-size: 11px; color: #92400e; text-align: center; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 16px; margin-bottom: 10px; font-size: 12px; }
        .info-item { display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px dashed #e0e0e0; }
        .info-label { color: #666; }
        .info-value { font-weight: 600; }
        .tx-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 10px; }
        .tx-table thead tr { background: #d97706; color: white; }
        .tx-table th { padding: 6px 8px; font-weight: 600; text-align: right; }
        .tx-table th:last-child { text-align: left; }
        .tx-table td { padding: 6px 8px; border-bottom: 1px solid #e0e0e0; text-align: right; }
        .tx-table td:last-child { text-align: left; font-weight: 600; }
        .signatures { display: flex; justify-content: space-between; border-top: 1px solid #e0e0e0; padding-top: 14px; margin-top: 10px; }
        .sig-box { text-align: center; }
        .sig-label { font-size: 11px; color: #666; margin-bottom: 24px; }
        .sig-line { border-top: 1px solid #333; width: 110px; font-size: 10px; color: #666; padding-top: 3px; }
        .footer { text-align: center; margin-top: 10px; font-size: 10px; color: #999; border-top: 1px dashed #ccc; padding-top: 8px; line-height: 1.8; }
        .coda { text-align: center; margin-top: 6px; padding-top: 6px; border-top: 1px solid #e0e0e0; font-size: 10px; }
        @media print {
            @page { size: A5 landscape; margin: 8mm; }
            body { background: white; padding: 0; }
            .print-btn { display: none; }
            .receipt { border: none; width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ طباعة</button>
    <div class="receipt">
        <div class="header">
            <div class="company-name">شركة هرگاڤ للصرافة</div>
            <div class="company-sub">رقم البورصة: 84 &nbsp;|&nbsp; <span dir="ltr">+964 750 445 7911</span></div>
            <div class="receipt-number">رقم الوصل: <span dir="ltr">{{ $tx->receipt_number ? str_pad($tx->receipt_number, 6, '0', STR_PAD_LEFT) : '—' }}</span></div>
            @if($tx->type === 'deposit')
                <span class="badge" style="background: #fffbeb; border: 1px solid #fcd34d; color: #d97706;">وصل إيداع أمانة</span>
            @else
                <span class="badge" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">وصل سحب أمانة</span>
            @endif
        </div>
        <hr class="divider">

        <div class="notice">هذه الأموال محفوظة كأمانة وتُرد كاملة عند الطلب</div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">التاريخ</span>
                <span class="info-value" dir="ltr">{{ $tx->created_at->format('Y-m-d') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">الوقت</span>
                <span class="info-value" dir="ltr">{{ $tx->created_at->format('h:i:s A') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">رمز العميل</span>
                <span class="info-value" dir="ltr">{{ $tx->client->code }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">اسم العميل</span>
                <span class="info-value">{{ $tx->client->full_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">هاتف العميل</span>
                <span class="info-value" dir="ltr">{{ $tx->client->phone ?? '—' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">رقم المرجع</span>
                <span class="info-value" dir="ltr" style="color: #d97706; font-size: 10px;">{{ $tx->reference_no }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">اسم المرسل</span>
                <span class="info-value">{{ $tx->sender_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">هاتف المرسل</span>
                <span class="info-value" dir="ltr">{{ $tx->sender_phone ?? '—' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">الصراف</span>
                <span class="info-value">{{ $tx->createdBy->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">ملاحظات</span>
                <span class="info-value">{{ $tx->notes ?? '—' }}</span>
            </div>
        </div>

        <table class="tx-table">
            <thead>
                <tr>
                    <th>البيان</th>
                    <th>العملة</th>
                    <th>المبلغ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>رصيد الأمانة قبل</td>
                    <td>{{ $tx->currency->code }}</td>
                    <td dir="ltr">{{ number_format($tx->balance_before, $tx->currency->code === 'IQD' ? 0 : 2) }}</td>
                </tr>
                <tr style="background: #fffbeb;">
                    <td style="color: #d97706; font-weight: 600;">{{ $tx->type === 'deposit' ? 'المبلغ المودع' : 'المبلغ المسحوب' }}</td>
                    <td style="color: #d97706; font-weight: 600;">{{ $tx->currency->code }}</td>
                    <td style="color: #d97706; font-weight: 700; font-size: 14px;" dir="ltr">
{{ $tx->type === 'deposit' ? '+' : '-' }} {{ number_format($tx->amount, $tx->currency->code === 'IQD' ? 0 : 2) }}                    </td>
                </tr>
                <tr style="border-bottom: 2px solid #d97706;">
                    <td style="font-weight: 600;">رصيد الأمانة بعد</td>
                    <td style="font-weight: 600;">{{ $tx->currency->code }}</td>
                    <td style="font-weight: 700; color: #1a3c5e; font-size: 14px;" dir="ltr">{{ number_format($tx->balance_after, $tx->currency->code === 'IQD' ? 0 : 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <div class="sig-label">توقيع الصراف</div>
                <div class="sig-line">Cashier</div>
            </div>
            <div class="sig-box">
                <div class="sig-label">توقيع العميل</div>
                <div class="sig-line">Client</div>
            </div>
        </div>

        <div class="footer">
            شكراً لتعاملكم معنا &nbsp;|&nbsp; شركة هرگاڤ للصرافة &nbsp;|&nbsp; رقم البورصة: 84 &nbsp;|&nbsp; <span dir="ltr">+964 750 445 7911</span>
        </div>
        <div class="coda">
            <span style="color: #aaa;">Powered & Developed by Coda Agency for ICT Solutions — +964 750 730 8005</span>
        </div>
    </div>
</body>
</html>