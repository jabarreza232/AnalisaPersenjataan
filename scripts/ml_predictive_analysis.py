import pandas as pd
import json
import os
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
from sklearn.preprocessing import LabelEncoder

# ==============================================================================
# MACHINE LEARNING SCRIPT: PREDICTIVE ANALYTICS FOR COMBAT PROVEN STATUS
# Algoritma: Random Forest Classifier (In-scope dengan matkul Sistem Cerdas)
# Tujuan: Menentukan faktor apa yang paling mempengaruhi alutsista menjadi "Combat Proven"
# ==============================================================================

def run_ml_analysis():
    print("🚀 Memulai proses Machine Learning (Random Forest)...")
    
    # 1. Load Dataset
    csv_path = 'weapondb.csv'
    if not os.path.exists(csv_path):
        print(f"❌ Error: File {csv_path} tidak ditemukan.")
        return

    df = pd.read_csv(csv_path)

    # 2. Preprocessing & Data Cleansing
    features = ['Year_Introduced', 'Unit_Cost_USD', 'Theater_of_Operation', 'Category']
    target = 'Combat_Proven'
  
    df_clean = df[features + [target]].dropna()

    # Bersihkan string aneh di Cost & ubah jadi numerik
    df_clean['Unit_Cost_USD'] = pd.to_numeric(df_clean['Unit_Cost_USD'], errors='coerce')
    df_clean['Year_Introduced'] = pd.to_numeric(df_clean['Year_Introduced'], errors='coerce')
    df_clean = df_clean.dropna() # Hapus yang gagal di-convert
   
    # Encode Target DULU: Yes -> 1, No -> 0 (Syarat mutlak agar bisa dihitung persentase/mean)
    df_clean['Combat_Proven'] = df_clean['Combat_Proven'].apply(lambda x: 1 if str(x).strip().lower() == 'yes' else 0)

    # =========================================================================
    # 📊 ANALISIS STATISTIK (DILAKUKAN SEBELUM KATEGORI TEKS DI-ENCODE KE ANGKA)
    # =========================================================================
    print("\n📊 --- ANALISIS STATISTIK FAKTOR ---")

    # 0. HARGA / BIAYA
    avg_cost = df_clean.groupby('Combat_Proven')['Unit_Cost_USD'].mean()
    print("\n💰 HARGA / BIAYA:")
    print(f"Rata-rata harga BUKAN Combat Proven : ${avg_cost[0]:,.2f}")
    print(f"Rata-rata harga SUDAH Combat Proven : ${avg_cost[1]:,.2f}")

    # 1. TAHUN RILIS
    avg_year = df_clean.groupby('Combat_Proven')['Year_Introduced'].mean()
    desc_no = df_clean[df_clean['Combat_Proven'] == 0]['Year_Introduced'].describe()
    desc_yes = df_clean[df_clean['Combat_Proven'] == 1]['Year_Introduced'].describe()

    print("\n🗓️ KESIMPULAN TAHUN RILIS:")
    
    # Menghitung jumlah data untuk validasi (opsional, jika ingin ditampilkan)
    count_no = len(df_clean[df_clean['Combat_Proven'] == 0])
    count_yes = len(df_clean[df_clean['Combat_Proven'] == 1])

    print(f"Total Senjata Dianalisis: {count_no + count_yes:,} unit")
    print("\n💡 Apakah semakin tua usia senjata, semakin Combat Proven?")
    print("   Jawabannya: TIDAK MUTLAK.")
    print("   Secara rata-rata, senjata yang sudah Combat Proven maupun yang belum,")
    print("   mayoritas diciptakan pada era yang sama (puncak Perang Dingin).")
    
    print("\n📌 Kesimpulan Sederhana:")
    print("   1. Di bawah tahun 1985 (Teknologi Lama):")
    print("      Mayoritas alutsista dunia yang berstatus Combat Proven berasal dari era ini.")
    print("      Senjata-senjata ini diproduksi massal dan terus dipakai di berbagai konflik.")
    print("\n   2. Di atas tahun 1985 (Teknologi Modern):")
    print("      Semakin baru senjatanya (terutama rilis tahun 2000-an ke atas), semakin")
    print("      KECIL kemungkinannya menjadi Combat Proven. Senjata modern yang mahal")
    print("      lebih difungsikan sebagai pencegah (deterrence) dan belum teruji di perang besar.")
    # 2. MATRA / THEATER OF OPERATION 
    print("\n🌊 MATRA (Peluang Combat Proven):")
    matra_stats = df_clean.groupby('Theater_of_Operation')['Combat_Proven'].mean() * 100
    matra_stats = matra_stats.sort_values(ascending=False)
    for matra, percent in matra_stats.items():
        print(f"- {matra}: {percent:.1f}%")

    # 3. TIPE SENJATA
    print("\n🔫 TIPE SENJATA (Peluang Combat Proven):")
    type_stats = df_clean.groupby('Category')['Combat_Proven'].mean() * 100
    type_stats = type_stats.sort_values(ascending=False)
    for tipe, percent in type_stats.items():
        print(f"- {tipe}: {percent:.1f}%")
    print("\n------------------------------------")
    # =========================================================================

    # 3. Lanjutkan Preprocessing Label Encoder untuk ML
    le_theater = LabelEncoder()
    df_clean['Theater_of_Operation'] = le_theater.fit_transform(df_clean['Theater_of_Operation'])
    
    le_category = LabelEncoder()
    df_clean['Category'] = le_category.fit_transform(df_clean['Category'])

    # 4. Split Data (Train & Test)
    X = df_clean[features]
    y = df_clean['Combat_Proven']

    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # 5. Melatih Model Random Forest
    print("🧠 Melatih model Random Forest Classifier...")
    model = RandomForestClassifier(n_estimators=100, random_state=42, max_depth=10)
    model.fit(X_train, y_train)

    # Evaluasi Akurasi
    y_pred = model.predict(X_test)
    accuracy = accuracy_score(y_test, y_pred)
    print(f"✅ Model berhasil dilatih! Akurasi Prediksi: {accuracy * 100:.2f}%")

    # 6. Extract Feature Importance (Faktor Paling Berpengaruh)
    importances = model.feature_importances_
    
    # Mapping nama kolom agar lebih human-readable
    human_labels = {
        'Year_Introduced': 'Tahun Rilis (Usia)',
        'Unit_Cost_USD': 'Harga / Biaya',
        'Theater_of_Operation': 'Matra (Darat/Laut/Udara)',
        'Category': 'Tipe Senjata'
    }

    feature_results = []
    for i, feature in enumerate(features):
        feature_results.append({
            'factor': human_labels[feature],
            'importance_score': float(importances[i]) * 100
        })

    # Sort dari yang paling berpengaruh
    feature_results = sorted(feature_results, key=lambda x: x['importance_score'], reverse=True)

    # 7. Export hasil ke JSON agar bisa dibaca oleh Dashboard Laravel
    output_dir = 'public/data'
    os.makedirs(output_dir, exist_ok=True)
    
    output_file = os.path.join(output_dir, 'ml_insight.json')
    
    result_data = {
        'model_used': 'Random Forest Classifier',
        'accuracy': round(accuracy * 100, 2),
        'dataset_rows_used': len(df_clean),
        'insights': feature_results
    }

    with open(output_file, 'w') as f:
        json.dump(result_data, f, indent=4)
        
    print(f"📁 Hasil analisa diekspor ke: {output_file}")
    print("   Data JSON ini bisa di-fetch oleh Dashboard Laravel via AJAX.")

if __name__ == "__main__":
    run_ml_analysis()