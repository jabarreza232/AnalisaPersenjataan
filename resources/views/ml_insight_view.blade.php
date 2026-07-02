<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis Machine Learning</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; }
        .error-box { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; }
        .success-box { background: #dcfce7; color: #166534; padding: 15px; border-radius: 5px; }
        .terminal { background: #1e293b; color: #38bdf8; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        th { background-color: #f1f5f9; }
    </style>
</head>
<body>

    <h2>🧠 Dashboard Analisis Prediktif (Combat Proven)</h2>

    {{-- Tampilan Jika Error --}}
    @if($status === 'failed')
        <div class="error-box">
            <h3>❌ Terjadi Kesalahan!</h3>
            <p>{{ $error_message }}</p>
            @if($python_error)
                <h4>Detail Error dari Python:</h4>
                <div class="terminal">{{ $python_error }}</div>
            @endif
        </div>
    @endif

    {{-- Tampilan Jika Sukses --}}
    @if($status === 'success' && $ml_data)
        <div class="success-box">
            <h3>✅ Model Berhasil Dilatih!</h3>
            <p><strong>Algoritma:</strong> {{ $ml_data['model_used'] }}</p>
            <p><strong>Akurasi Prediksi:</strong> {{ $ml_data['accuracy'] }}%</p>
            <p><strong>Jumlah Data Diproses:</strong> {{ $ml_data['dataset_rows_used'] }} baris</p>
        </div>

        <h3>Tingkat Pengaruh Faktor (Feature Importance)</h3>
        <table>
            <thead>
                <tr>
                    <th>Faktor / Variabel</th>
                    <th>Skor Pengaruh (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ml_data['insights'] as $insight)
                    <tr>
                        <td>{{ $insight['factor'] }}</td>
                        <td>{{ number_format($insight['importance_score'], 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Log Terminal Proses Python</h3>
        <div class="terminal">{{ $terminal_output }}</div>
    @endif

</body>
</html>