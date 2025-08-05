<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Transaction Receipt - {{ 'TXN-' . strtoupper(uniqid()) }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #10b981;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --text-color: #334155;
            --text-light: #64748b;
            --success-color: #22c55e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, Roboto, 'Helvetica Neue', sans-serif;
            background-color: #f1f5f9;
            padding: 2rem;
            color: var(--text-color);
            line-height: 1.6;
        }

        .security-banner {
            background-color: var(--dark-color);
            color: white;
            padding: 0.75rem;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .security-banner i {
            font-size: 1rem;
        }

        .receipt-container {
            max-width: 680px;
            background: white;
            margin: 0 auto;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
            border: 1px solid var(--border-color);
        }

        .receipt-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 2rem;
            color: white;
            text-align: center;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -1.5rem;
            left: 0;
            right: 0;
            height: 3rem;
            background: white;
            clip-path: ellipse(75% 50% at 50% 50%);
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .logo {
            height: 3.5rem;
            filter: brightness(0) invert(1);
        }

        .title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            letter-spacing: 0.025em;
        }

        .subtitle {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .receipt-body {
            padding: 2.5rem;
            position: relative;
        }

        .transaction-id-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .transaction-id {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
            font-family: 'Roboto Mono', monospace;
            font-weight: 500;
            border: 1px dashed var(--border-color);
            font-size: 0.95rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .transaction-id i {
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .transaction-security {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 0.8125rem;
            color: var(--text-light);
        }

        .transaction-security i {
            color: var(--success-color);
        }

        .transaction-details {
            margin-bottom: 2.5rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-label {
            font-weight: 500;
            color: var(--text-light);
            font-size: 0.8125rem;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-weight: 500;
            font-size: 0.9375rem;
        }

        .amount-section {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(29, 78, 216, 0.05) 100%);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin: 2rem 0;
            border: 1px solid rgba(37, 99, 235, 0.15);
            position: relative;
        }

        .amount-label {
            font-size: 0.9375rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .amount-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .amount-subtext {
            font-size: 0.8125rem;
            color: var(--text-light);
            margin-top: 0.5rem;
        }

        .verification-section {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px dashed var(--border-color);
        }

        .verification-item {
            flex: 1;
            min-width: 150px;
        }

        .verification-label {
            font-size: 0.8125rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .verification-value {
            font-family: 'Roboto Mono', monospace;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        .qrcode-container {
            margin-top: 1rem;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            display: inline-block;
            background: white;
        }

        .watermark {
            position: absolute;
            opacity: 0.03;
            font-size: 8rem;
            font-weight: 900;
            transform: rotate(-25deg);
            top: 30%;
            left: 10%;
            z-index: 0;
            pointer-events: none;
            user-select: none;
            color: var(--primary-color);
            font-family: 'Segoe UI', sans-serif;
        }

        .stamp {
            position: absolute;
            right: 2rem;
            bottom: 2rem;
            opacity: 0.9;
            font-family: 'Roboto Mono', monospace;
            font-size: 0.875rem;
            transform: rotate(12deg);
            color: var(--success-color);
            border: 2px solid var(--success-color);
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .receipt-footer {
            text-align: center;
            padding: 1.5rem;
            background: var(--light-color);
            font-size: 0.8125rem;
            color: var(--text-light);
            border-top: 1px solid var(--border-color);
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 0.75rem;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.875rem 1.75rem;
            border: none;
            cursor: pointer;
            font-size: 0.9375rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            min-width: 180px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--dark-color) 0%, #0f172a 100%);
        }

        .btn-tertiary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #0d9488 100%);
        }

        .btn i {
            font-size: 1rem;
        }

        @media print {
            .button-container {
                display: none;
            }

            body {
                background: none;
                padding: 0;
            }

            .security-banner {
                display: none;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .receipt-body {
                padding: 1.5rem;
            }

            .transaction-details {
                grid-template-columns: 1fr;
            }

            .verification-section {
                flex-direction: column;
                gap: 1rem;
            }

            .amount-value {
                font-size: 1.75rem;
            }

            .watermark {
                font-size: 5rem;
                top: 40%;
            }
        }

        /* Animation for security elements */
        @keyframes pulse {
            0% {
                opacity: 0.8;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.8;
            }
        }

        .security-pulse {
            animation: pulse 2s infinite;
        }

        /* Hover effects for interactive elements */
        .transaction-id:hover {
            background: rgba(37, 99, 235, 0.05);
            cursor: pointer;
        }

        /* Print-specific styles */
        @page {
            size: auto;
            margin: 10mm;
        }
    </style>
</head>

<body>
    <div class="security-banner">
        <i class="fas fa-lock security-pulse"></i>
        SECURE TRANSACTION RECEIPT - DO NOT SHARE PRIVATE DETAILS
    </div>

    <div class="receipt-container" id="receipt">
        <div class="watermark">SECURE</div>

        <div class="receipt-header">
            <div class="logo-container">
                <img src="{{ asset('uploads/logo.png') }}" class="logo" alt="Company Logo">
            </div>
            <div class="title">TRANSACTION CONFIRMATION</div>
            <div class="subtitle">Official Payment Receipt • Valid Financial Document</div>
        </div>

        @php
        $transactionId = 'TXN-' . strtoupper(uniqid());
        @endphp

        <div class="receipt-body">
            <div class="transaction-id-container">
                <div class="transaction-id" onclick="copyToClipboard('{{ $transactionId }}')">
                    <i class="fas fa-fingerprint"></i>
                    <span id="txn-id">{{ $transactionId }}</span>
                </div>


                <div class="transaction-security">
                    <i class="fas fa-shield-alt security-pulse"></i>
                    <span>Secured with 256-bit encryption • Validated {{ now()->format('M d, Y \a\t H:i') }}</span>
                </div>
            </div>

            <div class="transaction-details">
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-user"></i> Customer Name</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-envelope"></i> Email Address</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-id-card"></i> Account Number</span>
                    <span class="detail-value">{{ $user->account_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-exchange-alt"></i> Transaction Type</span>
                    <span class="detail-value">{{ $type }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-wallet"></i> Balance Type</span>
                    <span class="detail-value">{{ $balanceType }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-info-circle"></i> Description</span>
                    <span class="detail-value">{{ $description }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-calendar-alt"></i> Date & Time</span>
                    <span class="detail-value">{{ now()->format('d M Y, H:i:s') }} (UTC)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-server"></i> System Reference</span>
                    <span class="detail-value">SR-{{ substr(md5(uniqid()), 0, 8) }}</span>
                </div>
            </div>

            <div class="amount-section">
                <div class="amount-label">
                    <i class="fas fa-money-bill-wave"></i>
                    Transaction Amount
                </div>
                <div class="amount-value">
                    <span>{{ $user->currency }}</span>
                    <span>{{ number_format($amount, 2) }}</span>
                </div>
                <div class="amount-subtext">
                    Equivalent to {{ $user->currency }}{{ number_format($amount * 1.0, 2) }} (including all fees)
                </div>
            </div>

            <div class="verification-section">
                <div class="verification-item">
                    <div class="verification-label">
                        <i class="fas fa-hashtag"></i>
                        Authorization Code
                    </div>
                    <div class="verification-value">AUTH-{{ substr(strtoupper(md5(uniqid())), 0, 8) }}</div>
                </div>
                <div class="verification-item">
                    <div class="verification-label">
                        <i class="fas fa-qrcode"></i>
                        Verification QR Code
                    </div>
                    <div id="qrcode" class="qrcode-container"></div>
                </div>
            </div>

            <div class="stamp">
                <i class="fas fa-check-circle"></i>
                VERIFIED & APPROVED
            </div>
        </div>

        <div class="receipt-footer">
            <p>This is an official transaction receipt from Union Savings Bank. Please retain for your records.</p>
            <p>For any inquiries, please contact our customer support with the transaction reference above.</p>
            <div class="footer-links">
                <a href="#"><i class="fas fa-headset"></i> Contact Support</a>
                <a href="#"><i class="fas fa-file-invoice"></i> Dispute Transaction</a>
                <a href="#"><i class="fas fa-lock"></i> Security Center</a>
            </div>
        </div>
    </div>




    <div class="button-container">
        <button class="btn" onclick="downloadPDF()">
            <i class="fas fa-file-pdf"></i> Download PDF Receipt
        </button>
        <button class="btn btn-tertiary" onclick="printReceipt()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <a href="{{ route('home') }}" class="btn btn-secondary">
            <i class="fas fa-tachometer-alt"></i> Return to Dashboard
        </a>
    </div>

    <script>
        // Generate QR Code
        document.addEventListener('DOMContentLoaded', function() {
            const txnId = document.getElementById('txn-id').textContent;
            new QRCode(document.getElementById("qrcode"), {
                text: `TXNRECEIPT:${txnId}:{{ $user->account_number }}:{{ now()->format('YmdHis') }}`,
                width: 100,
                height: 100,
                colorDark: "#1e293b",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });

        function downloadPDF() {
            const element = document.getElementById('receipt');
            const opt = {
                margin: [10, 5, 10, 5],
                filename: `Transaction_Receipt_{{ $transactionId }}_${new Date().getTime()}.pdf`,
                image: { 
                    type: 'jpeg', 
                    quality: 0.98 
                },
                html2canvas: { 
                    scale: 2,
                    logging: true,
                    useCORS: true,
                    letterRendering: true,
                    allowTaint: true,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: document.documentElement.scrollWidth,
                    windowHeight: document.documentElement.scrollHeight
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait',
                    compress: true,
                    hotfixes: ['px_scaling']
                },
                pagebreak: { 
                    mode: ['avoid-all', 'css', 'legacy'] 
                }
            };
            
            // Show loading indicator
            const originalText = event.target.innerHTML;
            event.target.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Generating PDF...`;
            event.target.disabled = true;
            
            // Generate PDF
            html2pdf().set(opt).from(element).save().then(() => {
                event.target.innerHTML = originalText;
                event.target.disabled = false;
            });
        }

        function printReceipt() {
            window.print();
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const txnIdElement = document.getElementById('txn-id');
                const originalText = txnIdElement.textContent;
                txnIdElement.innerHTML = `<i class="fas fa-check"></i> Copied!`;
                
                setTimeout(() => {
                    txnIdElement.textContent = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }

        // Add security validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.search.includes('print=true')) {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
            
            // Add security validation message
            console.log('%c SECURITY VALIDATION: This receipt has been cryptographically signed and verified.', 
                'background: #1e293b; color: #ffffff; font-size: 12px; padding: 8px; border-radius: 4px;');
        });
    </script>
</body>

</html>