<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Receipt - {{ $receiptData['reference'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .receipt-container {
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .header-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .divider {
            border-color: rgba(0, 0, 0, 0.05);
        }

        .detail-card {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1.25rem;
        }

        .watermark {
            position: absolute;
            opacity: 0.03;
            font-size: 10rem;
            font-weight: 700;
            color: #2563eb;
            z-index: 0;
            transform: rotate(-15deg);
            pointer-events: none;
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="receipt-container bg-white rounded-xl w-full max-w-3xl overflow-hidden relative">
            <!-- Watermark -->
            <div class="watermark hidden md:block" style="top: 20%; left: 10%;">CONFIRMED</div>

            <!-- Header -->
            <div class="header-gradient text-white px-6 py-5 md:px-8 md:py-6">
                <div class="flex justify-between items-start">
                    <div class="relative z-10">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/10 p-2 rounded-lg">
                                <i class="fas fa-receipt text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold tracking-tight">TRANSFER RECEIPT</h1>
                                <p class="text-blue-100 text-sm mt-1">Transaction successfully processed</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right relative z-10">
                        <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">Reference No.</p>
                        <p class="font-mono font-bold text-lg md:text-xl mt-1">{{ $receiptData['reference'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 md:p-8 relative z-10">
                <!-- Status Banner -->
                <div class="bg-green-50 border border-green-100 rounded-lg px-4 py-3 mb-6 flex items-center">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-green-800">Transfer completed successfully</p>
                        <p class="text-green-600 text-sm mt-1">Your funds have been transferred to the recipient.</p>
                    </div>
                </div>

                <!-- Transaction Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="detail-card">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Sent</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $receiptData['currency'] }} {{
                            number_format($receiptData['amount'], 2) }}</p>
                    </div>
                    <div class="detail-card">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{
                            \Carbon\Carbon::parse($receiptData['date'])->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="detail-card">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">From Account</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ ucfirst($receiptData['account_type']) }}
                        </p>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="space-y-6">
                    <!-- Sender Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-full mr-3">
                                <i class="fas fa-user-circle text-sm"></i>
                            </span>
                            Sender Information
                        </h3>
                        <div class="mt-4 pl-9 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Full Name</p>
                                <p class="text-gray-900 mt-1">{{ $receiptData['user']->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Tax Identification</p>
                                <p class="text-gray-900 mt-1">{{ $receiptData['tax_code'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="divider border-t"></div>

                    <!-- Recipient Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="bg-green-100 text-green-600 p-2 rounded-full mr-3">
                                <i class="fas fa-user-tag text-sm"></i>
                            </span>
                            Recipient Information
                        </h3>
                        <div class="mt-4 pl-9 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Recipient Name</p>
                                <p class="text-gray-900 mt-1">{{ $receiptData['recipient'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Account Number</p>
                                <p class="text-gray-900 mt-1">{{ $receiptData['recipient_account'] }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Bank Name</p>
                                <p class="text-gray-900 mt-1">{{ $receiptData['bank_name'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="divider border-t"></div>

                    <!-- Transaction Notes -->
                    <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-full mr-3 flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-blue-800">Important Information</p>
                                <ul class="list-disc list-inside text-blue-600 text-sm mt-2 space-y-1">
                                    <li>This receipt serves as confirmation of your transaction</li>
                                    <li>Please allow 1-2 business days for funds to reflect</li>
                                    <li>Contact support if you have any questions</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t divider">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0">
                    <div class="flex items-center space-x-2">
                        <div class="bg-gray-200 p-2 rounded-full">
                            <i class="fas fa-shield-alt text-gray-600"></i>
                        </div>
                        <p class="text-xs text-gray-600">Secured with 256-bit SSL encryption</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="window.print()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                        <button id="downloadReceipt"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-download mr-2"></i>
                            Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('downloadReceipt').addEventListener('click', function() {
            const element = document.querySelector('.receipt-container');
            const opt = {
                margin: 10,
                filename: 'transfer_receipt_{{ $receiptData["reference"] }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, logging: true, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Show loading state
            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating PDF...';
            button.disabled = true;

            // Generate PDF
            html2pdf().from(element).set(opt).save().then(() => {
                // Restore button state
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });

        // Add animation when page loads
        document.addEventListener('DOMContentLoaded', () => {
            const receipt = document.querySelector('.receipt-container');
            receipt.style.opacity = '0';
            receipt.style.transform = 'translateY(20px)';
            receipt.style.transition = 'all 0.4s ease-out';
            
            setTimeout(() => {
                receipt.style.opacity = '1';
                receipt.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>

</html>