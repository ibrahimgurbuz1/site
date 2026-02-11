<?php
session_start();

require_once '/usr/share/php/tcpdf/tcpdf.php';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pdf_section($pdf, $title, $width)
{
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell($width, 7, $title, 1, 1, 'L', true);
    $pdf->SetFont('dejavusans', '', 10);
}

function pdf_row($pdf, $label, $value, $labelWidth, $valueWidth)
{
    $lineHeight = 6;
    $pdf->MultiCell($labelWidth, $lineHeight, $label, 1, 'L', false, 0);
    $pdf->MultiCell($valueWidth, $lineHeight, $value, 1, 'L', false, 1);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tempDir = 'temp/';
    
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }
    
    $data = $_POST;
    $photoNote = 'Fotoğraf: Yüklenmedi';
    $photoFileName = '';
    $photoPath = '';
    $allowedPhotoTypes = array('image/jpeg', 'image/png');
    $maxPhotoSize = 2 * 1024 * 1024;
    if (!empty($_FILES['foto']) && is_array($_FILES['foto'])) {
        $photoError = $_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($photoError === UPLOAD_ERR_OK) {
            $photoType = $_FILES['foto']['type'] ?? '';
            $photoSize = $_FILES['foto']['size'] ?? 0;
            if (in_array($photoType, $allowedPhotoTypes, true) && $photoSize > 0 && $photoSize <= $maxPhotoSize) {
                $photoExt = $photoType === 'image/png' ? 'png' : 'jpg';
                $photoFileName = 'foto_' . date('Ymd_His') . '_' . uniqid() . '.' . $photoExt;
                $photoPath = $tempDir . $photoFileName;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $photoPath)) {
                    $photoNote = 'Fotoğraf: Eklendi';
                } else {
                    $photoNote = 'Fotoğraf: Kaydedilemedi';
                }
            } else {
                $photoNote = 'Fotoğraf: Geçersiz tür veya boyut (maks 2MB, JPG/PNG)';
            }
        } elseif ($photoError !== UPLOAD_ERR_NO_FILE) {
            $photoNote = 'Fotoğraf: Yükleme hatası';
        }
    }
    $fileName = 'basvuru_' . date('Ymd_His') . '_' . uniqid() . '.pdf';
    $filePath = $tempDir . $fileName;
    
    $pcVarMi = $data['pc_varmi'] ?? '';
    $pcTip = $pcVarMi === 'Evet' ? ($data['pc_tip'] ?? '') : '';
    $pcCpu = $pcVarMi === 'Evet' ? ($data['pc_cpu'] ?? '') : '';
    $pcGpu = $pcVarMi === 'Evet' ? ($data['pc_gpu'] ?? '') : '';
    $pcRam = $pcVarMi === 'Evet' ? ($data['pc_ram'] ?? '') : '';
    $pcInternet = $pcVarMi === 'Evet' ? ($data['pc_internet'] ?? '') : '';

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Staj Başvuru Sistemi');
    $pdf->SetAuthor('Staj Başvuru');
    $pdf->SetTitle('Staj Başvuru Formu');
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetAutoPageBreak(true, 18);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 11);

    $applicationId = date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    $photoWidth = 26;
    $photoHeight = 30;

    $left = $pdf->getMargins()['left'];
    $right = $pdf->getMargins()['right'];
    $pageWidth = $pdf->getPageWidth();
    $usableWidth = $pageWidth - $left - $right;

    $startY = $pdf->GetY();
    $headerHeight = 36;
    $pdf->Rect($left, $startY, $usableWidth, $headerHeight);

    $photoX = $left + 2;
    $photoY = $startY + 3;
    $pdf->Rect($photoX, $photoY, $photoWidth, $photoHeight);
    if ($photoPath && file_exists($photoPath)) {
        $pdf->Image($photoPath, $photoX, $photoY, $photoWidth, $photoHeight);
    }

    $titleX = $left + $photoWidth + 6;
    $titleWidth = $usableWidth - $photoWidth - 6;
    $pdf->SetXY($titleX, $startY + 5);
    $pdf->SetFont('dejavusans', 'B', 11);
    $pdf->MultiCell($titleWidth, 5, "STAJYER OGRENCI BASVURU FORMU", 0, 'C', false, 1, $titleX, $startY + 5);

    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetXY($left, $startY + $headerHeight + 2);
    $pdf->Cell($usableWidth / 2, 5, 'Basvuru No: ' . $applicationId, 0, 0, 'L');
    $pdf->Cell($usableWidth / 2, 5, 'Tarih: ' . date('d.m.Y'), 0, 1, 'R');

    $labelWidth = 60;
    $valueWidth = $usableWidth - $labelWidth;

    pdf_section($pdf, 'Kisisel Bilgiler', $usableWidth);
    pdf_row($pdf, 'Ad Soyad', $data['ad_soyad'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Dogum Tarihi', $data['dogum_tarihi'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'T.C. Kimlik No', $data['tc_no'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Okul No', $data['okul_no'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Universite', $data['universite'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Bolum', $data['bolum'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Sinif', ($data['sinif'] ?? '') . '. Sinif', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Telefon', $data['telefon'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'E-posta', $data['email'] ?? '', $labelWidth, $valueWidth);

    pdf_section($pdf, 'Iletisim Kisisi', $usableWidth);
    pdf_row($pdf, 'Ad Soyad', $data['iletisim_ad'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Yakinlik', $data['iletisim_yakinlik'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Telefon', $data['iletisim_telefon'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Adres', $data['iletisim_adres'] ?? '', $labelWidth, $valueWidth);

    pdf_section($pdf, 'Staj Bilgileri', $usableWidth);
    pdf_row($pdf, 'Uzaktan', $data['staj_uzaktan'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Baslangic', $data['staj_baslangic'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Bitis', $data['staj_bitis'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Oncesi Calisma', $data['staj_oncesi'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Il', $data['staj_il'] ?? '', $labelWidth, $valueWidth);

    pdf_section($pdf, 'Teknik Bilgiler', $usableWidth);
    pdf_row($pdf, 'PC Var mi', $pcVarMi, $labelWidth, $valueWidth);
    pdf_row($pdf, 'Tip', $pcTip, $labelWidth, $valueWidth);
    pdf_row($pdf, 'CPU', $pcCpu, $labelWidth, $valueWidth);
    pdf_row($pdf, 'GPU', $pcGpu, $labelWidth, $valueWidth);
    pdf_row($pdf, 'RAM', $pcRam, $labelWidth, $valueWidth);
    pdf_row($pdf, 'Internet', $pcInternet, $labelWidth, $valueWidth);

    pdf_section($pdf, 'Ek Bilgiler', $usableWidth);
    pdf_row($pdf, 'Dijital Gecmis', $data['dijital_gecmis'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Yabanci Dil', $data['yabanci_dil'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Devam Istegi', $data['devam_istegi'] ?? '', $labelWidth, $valueWidth);
    pdf_row($pdf, 'Beklentiler', $data['beklentiler'] ?? '', $labelWidth, $valueWidth);

    $pdf->Ln(2);
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->MultiCell($usableWidth, 5, 'Kisisel ve staj ile ilgili bilgilerin tarafimca dogru beyan edildigini kabul ederim.', 0, 'L', false, 1);

    $pdfContent = $pdf->Output($filePath, 'S');
    file_put_contents($filePath, $pdfContent);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . strlen($pdfContent));
    echo $pdfContent;
    exit;
}
?>
