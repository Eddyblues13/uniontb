<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
            color: #333;
        }

        .receipt-container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: relative;
        }

        .receipt-header {
            background: linear-gradient(135deg, #007BFF 0%, #0056b3 100%);
            padding: 25px;
            color: white;
            text-align: center;
            position: relative;
        }

        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            right: 0;
            height: 30px;
            background: white;
            clip-path: ellipse(50% 50% at 50% 50%);
        }

        .logo {
            height: 50px;
            margin-bottom: 15px;
            filter: brightness(0) invert(1);
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .subtitle {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }

        .receipt-body {
            padding: 30px;
        }

        .transaction-id {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 25px;
            font-family: 'Courier New', monospace;
            font-weight: 500;
            border: 1px dashed #ddd;
        }

        .transaction-details {
            margin-bottom: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            font-weight: 500;
            color: #666;
            flex: 1;
        }

        .detail-value {
            flex: 1;
            text-align: right;
            font-weight: 500;
        }

        .amount-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }

        .amount-label {
            font-size: 16px;
            color: #666;
        }

        .amount-value {
            font-size: 28px;
            font-weight: 700;
            color: #007BFF;
            margin-top: 5px;
        }

        .receipt-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            font-size: 12px;
            color: #666;
        }

        .watermark {
            position: absolute;
            opacity: 0.03;
            font-size: 120px;
            font-weight: 700;
            transform: rotate(-30deg);
            top: 30%;
            left: 10%;
            z-index: 0;
            pointer-events: none;
            user-select: none;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        }

        .stamp {
            position: absolute;
            right: 30px;
            bottom: 30px;
            opacity: 0.8;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            transform: rotate(15deg);
            color: #007BFF;
            border: 2px solid #007BFF;
            padding: 5px 10px;
            border-radius: 4px;
        }

        @media print {
            .button-container {
                display: none;
            }

            body {
                background: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container" id="receipt">
        <div class="watermark">RECEIPT</div>

        <div class="receipt-header">
            <img src="{{ asset('uploads/logo.png') }}" class="logo" alt="Company Logo">
            <div class="title">TRANSACTION RECEIPT</div>
            <div class="subtitle">Official Payment Confirmation</div>
        </div>

        <div class="receipt-body">
            <div class="transaction-id">
                Transaction ID: {{ 'TXN-' . strtoupper(uniqid()) }}
            </div>


            <div class="transaction-details">
                <div class="detail-row">
                    <span class="detail-label">Customer Name</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email Address</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Account Number</span>
                    <span class="detail-value">{{ $user->account_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Transaction Type</span>
                    <span class="detail-value">{{ $type }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Balance Type</span>
                    <span class="detail-value">{{ $balanceType }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Description</span>
                    <span class="detail-value">{{ $description }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value">{{ now()->format('d M Y, H:i:s') }}</span>
                </div>
            </div>

            <div class="amount-row">
                <div class="amount-label">Transaction Amount</div>
                <div class="amount-value">{{ $user->currency }}{{ number_format($amount, 2) }}</div>
            </div>

            <div class="stamp">APPROVED</div>
        </div>

        <div class="receipt-footer">
            <p>This is an official transaction receipt. Please retain for your records.</p>
            <p>For any inquiries, please contact our customer support.</p>
        </div>
    </div>

    <div class="button-container">
        <button class="btn" onclick="downloadPDF()">Download PDF Receipt</button>
        <a href="{{ route('home') }}" class="btn btn-secondary">Return to Dashboard</a>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('receipt');
            const opt = {
                margin: 10,
                filename: 'Transaction_Receipt_{{ $transactionId }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    logging: true,
                    useCORS: true,
                    letterRendering: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait',
                    compress: true
                }
            };
            
            // Generate PDF
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>

</html>