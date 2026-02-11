<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staj Başvuru Formu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600&family=Source+Serif+4:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <img class="brand-logo" src="logo.png" alt="Kurum logosu">
        <div class="header-text">
            <h1>Staj Başvuru Formu</h1>
            <p>Sizi daha yakından tanıyabilmek için bazı bilgilere ihtiyaç duyuyoruz. Başvuru aşamasında T.C. kimlik numarası ve ikinci iletişim kişisi bilgisi zorunlu değildir. Değerlendirme sürecimizin sorunsuz ilerleyebilmesi adına diğer alanları eksiksiz doldurmanızı rica ederiz.</p>
        </div>
    </div>
</header>

<div class="container">
    <form id="applicationForm" action="process.php" method="POST" enctype="multipart/form-data">
        
        <!-- ADIM 1: Kişisel Bilgiler -->
        <div class="step active" id="step1">
            <h2>1. Kişisel Bilgiler</h2>
            <div class="form-group">
                <label>Ad Soyad (Tam Adınız)</label>
                <input type="text" name="ad_soyad" required>
            </div>
            <div class="form-group">
                <label>Doğum Tarihi</label>
                <input type="date" name="dogum_tarihi" required>
            </div>
            <div class="form-group">
                <label>TC No (Zorunlu degil)</label>
                <input type="text" name="tc_no" maxlength="11" pattern="\d{11}" title="11 haneli TC No giriniz">
            </div>
            <div class="form-group">
                <label>Okul No</label>
                <input type="text" name="okul_no" required>
            </div>
            <div class="form-group">
                <label>Üniversite</label>
                <input type="text" name="universite" required>
            </div>
            <div class="form-group">
                <label>Bölüm</label>
                <input type="text" name="bolum" required>
            </div>
            <div class="form-group">
                <label>Sınıf</label>
                <select name="sinif" required>
                    <option value="">Seçiniz</option>
                    <option value="1">1. Sınıf</option>
                    <option value="2">2. Sınıf</option>
                    <option value="3">3. Sınıf</option>
                    <option value="4">4. Sınıf</option>
                </select>
            </div>
            <div class="form-group">
                <label>Telefon</label>
                <input type="tel" name="telefon" required>
            </div>
            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Fotograf (JPG/PNG)</label>
                <input type="file" name="foto" accept="image/jpeg,image/png">
            </div>
            <div class="btn-box">
                <button type="button" onclick="nextStep(1)">İleri &raquo;</button>
            </div>
        </div>

        <!-- ADIM 2: İletişim Kişisi -->
        <div class="step" id="step2">
            <h2>2. İletişim Kişisi (Acil Durum - Zorunlu degil)</h2>
            <div class="form-group">
                <label>Ad Soyad (Zorunlu degil)</label>
                <input type="text" name="iletisim_ad">
            </div>
            <div class="form-group">
                <label>Yakınlık Derecesi (Anne, Baba vb.) (Zorunlu degil)</label>
                <input type="text" name="iletisim_yakinlik">
            </div>
            <div class="form-group">
                <label>Telefon (Zorunlu degil)</label>
                <input type="tel" name="iletisim_telefon">
            </div>
            <div class="form-group">
                <label>Adres (İl/İlçe yeterli) (Zorunlu degil)</label>
                <textarea name="iletisim_adres"></textarea>
            </div>
            <div class="btn-box">
                <button type="button" class="btn-prev" onclick="prevStep(2)">&laquo; Geri</button>
                <button type="button" onclick="nextStep(2)">İleri &raquo;</button>
            </div>
        </div>

        <!-- ADIM 3: Staj Detayları -->
        <div class="step" id="step3">
            <h2>3. Staj Detayları</h2>
            <div class="form-group">
                <label>Staj uzaktan oluyor mu?</label>
                <select name="staj_uzaktan" required>
                    <option value="Evet">Evet</option>
                    <option value="Hayır">Hayır</option>
                    <option value="Bilmiyorum">Bilmiyorum</option>
                </select>
            </div>
            <div class="form-group">
                <label>Staj Başlangıç Tarihi</label>
                <input type="date" name="staj_baslangic" required>
            </div>
            <div class="form-group">
                <label>Staj Bitiş Tarihi</label>
                <input type="date" name="staj_bitis" required>
            </div>
            <div class="form-group">
                <label>Staj öncesi çalışabilir misin?</label>
                <select name="staj_oncesi" required>
                    <option value="Evet">Evet</option>
                    <option value="Hayır">Hayır</option>
                </select>
            </div>
            <div class="form-group">
                <label>Staj esnasında hangi ilde olacaksın?</label>
                <input type="text" name="staj_il" required>
            </div>
            <div class="btn-box">
                <button type="button" class="btn-prev" onclick="prevStep(3)">&laquo; Geri</button>
                <button type="button" onclick="nextStep(3)">İleri &raquo;</button>
            </div>
        </div>

        <!-- ADIM 4: Teknik Bilgiler -->
        <div class="step" id="step4">
            <h2>4. Teknik Bilgiler</h2>
            <div class="form-group">
                <label>Bilgisayarın var mı?</label>
                <select name="pc_varmi" id="pc_varmi" onchange="togglePcDetails()" required>
                    <option value="">Seçiniz</option>
                    <option value="Evet">Evet</option>
                    <option value="Hayır">Hayır</option>
                </select>
            </div>

            <div id="pc_details" style="display:none; border-left: 3px solid #3498db; padding-left: 15px; margin-top:10px;">
                <div class="form-group">
                    <label>Tip</label>
                    <select name="pc_tip">
                        <option value="Laptop">Laptop</option>
                        <option value="Masaüstü">Masaüstü</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>CPU (İşlemci)</label>
                    <input type="text" name="pc_cpu">
                </div>
                <div class="form-group">
                    <label>GPU (Ekran Kartı)</label>
                    <input type="text" name="pc_gpu">
                </div>
                <div class="form-group">
                    <label>RAM</label>
                    <input type="text" name="pc_ram">
                </div>
                <div class="form-group">
                    <label>İnternet Durumu</label>
                    <input type="text" name="pc_internet" placeholder="Örn: Sınırsız ev interneti">
                </div>
            </div>

            <div class="btn-box">
                <button type="button" class="btn-prev" onclick="prevStep(4)">&laquo; Geri</button>
                <button type="button" onclick="nextStep(4)">İleri &raquo;</button>
            </div>
        </div>

        <!-- ADIM 5: Ek Bilgiler ve Gönder -->
        <div class="step" id="step5">
            <h2>5. Ek Bilgiler</h2>
            <div class="form-group">
                <label>Dijital Geçmiş / Projeler</label>
                <textarea name="dijital_gecmis" rows="4" placeholder="Tecrübelerinizi yazınız..." required></textarea>
            </div>
            <div class="form-group">
                <label>Yabancı Dil Bilgisi</label>
                <input type="text" name="yabanci_dil" required>
            </div>
            <div class="form-group">
                <label>Staj sonrası devam etmek ister misin?</label>
                <select name="devam_istegi" required>
                    <option value="Evet">Evet</option>
                    <option value="Hayır">Hayır</option>
                    <option value="Belirsiz">Belirsiz</option>
                </select>
            </div>
            <div class="form-group">
                <label>Bizden beklentilerin nelerdir?</label>
                <textarea name="beklentiler" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label></label>
                <div style="display: flex; width: 100% !important; overflow: visible;">
                    <input style="height: 20px; width: 20px;" type="checkbox" id="vehicle2" name="vehicle2" value="Car" required>
                <span style="font-size: 14px">
                Verdigim bilgileri kendi rizamla paylastigimi ve KVKK kapsaminda islenmesini kabul ediyorum. </span></div>
            </div>
                
            
            <div class="btn-box">
                <button type="button" class="btn-prev" onclick="prevStep(5)">&laquo; Geri</button>
                <button type="submit" class="btn-submit">Başvuruyu Tamamla ve İndir</button>
            </div>
        </div>

    </form>
</div>

<script>
    function nextStep(current) {
        const currentDiv = document.getElementById('step' + current);
        const inputs = currentDiv.querySelectorAll('input, select, textarea');
        let valid = true;
        let firstInvalid = null;
        
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                valid = false;
                input.style.borderColor = "red";
                if (!firstInvalid) firstInvalid = input;
            } else {
                input.style.borderColor = "#ddd";
            }
        });

        if (!valid) {
            if (firstInvalid) firstInvalid.reportValidity();
            return;
        }

        document.getElementById('step' + current).classList.remove('active');
        document.getElementById('step' + (current + 1)).classList.add('active');
    }

    function prevStep(current) {
        document.getElementById('step' + current).classList.remove('active');
        document.getElementById('step' + (current - 1)).classList.add('active');
    }

    function togglePcDetails() {
        const val = document.getElementById('pc_varmi').value;
        const details = document.getElementById('pc_details');
        const inputs = details.querySelectorAll('input:not([name="pc_internet"]), select');

        if (val === 'Evet') {
            details.style.display = 'block';
            inputs.forEach(el => el.setAttribute('required', 'required'));
        } else {
            details.style.display = 'none';
            inputs.forEach(el => el.removeAttribute('required'));
        }
    }

    // Sayfa yüklendiğinde durumu kontrol et (tarayıcı geçmişi vb. için)
    togglePcDetails();
</script>

</body>
</html>