<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .receipt-container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .logo {
            width: 120px;
            display: block;
            margin: 0 auto 10px;
            filter: drop-shadow(2px 2px 5px rgba(0, 0, 0, 0.2));
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #007BFF;
            color: white;
            text-transform: uppercase;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #218838;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>

<body>

    <div class="receipt-container" id="receipt">
        <img src="{{ asset('uploads/logo.png') }}" class="logo" alt="Company Logo">
        <div class="title">Transaction Receipt</div>

        <table>
            <tr>
                <th>Name</th>
                <td>{{ $user->name }}</td>
            </tr>

            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Account Number</th>
                <td>{{ $user->account_number }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $description }}</td>
            </tr>
            <tr>
                <th>Transaction Type</th>
                <td>{{ $type }}</td>
            </tr>
            <tr>
                <th>Balance Type</th>
                <td>{{ $balanceType }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td>{{ $user->currency }}{{ number_format($amount, 2) }}</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{ now()->format('d-m-Y H:i:s') }}</td>
            </tr>
        </table>

        <div class="button-container">
            <button class="btn" onclick="downloadPDF()">Download PDF</button>
            <a href="{{ route('home') }}" class="btn btn-secondary">Return Home</a>
        </div>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('receipt');
            html2pdf(element, {
                margin: 10,
                filename: 'Transaction_Receipt.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            });
        }
    </script>

</body>

</html>