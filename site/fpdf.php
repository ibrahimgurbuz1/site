<?php
/**
 * FPDF - Minimal PDF Library
 */
class FPDF
{
    private $page = 0;
    private $n = 2;
    private $pages = array();
    private $state = 0;
    private $compress = true;
    private $k = 2.834645669;
    private $w = 210;
    private $h = 297;
    private $wPt;
    private $hPt;
    private $l = 10;
    private $t = 10;
    private $r = 10;
    private $b = 10;
    private $cMargin = 0;
    private $x;
    private $y;
    private $lasth = 0;
    private $fontFamily = '';
    private $fontStyle = '';
    private $fontSize = 0;
    private $underline = false;
    private $textColor = '0 0 0';
    private $drawColor = '0 0 0';
    private $fillColor = '0 0 0';
    private $lineWidth = 0.567;
    private $fontSizePt = 12;
    private $images = array();
    private $links = array();
    private $inFooter = false;
    private $fonts = array();
    private $currentFont = array();
    private $outBuffer = '';
    private $objects = array();

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        if ($size === 'A4') {
            $this->w = 210;
            $this->h = 297;
        }

        if ($orientation === 'L') {
            $tmp = $this->w;
            $this->w = $this->h;
            $this->h = $tmp;
        }

        $this->wPt = $this->w * 2.834645669;
        $this->hPt = $this->h * 2.834645669;
        $this->x = $this->l;
        $this->y = $this->t;
        $this->state = 0;
        $this->page = 0;

        $this->fonts = array(
            'arial' => array('i' => 'arial.php', 'b' => 'arialbd.php'),
            'helvetica' => array('i' => 'helvetica.php', 'b' => 'helveticab.php'),
            'courier' => array('i' => 'courier.php', 'b' => 'courierb.php'),
            'times' => array('i' => 'times.php', 'b' => 'timesb.php'),
        );
    }

    public function AddPage($orientation = '')
    {
        if ($this->state == 3) {
            $this->EndPage();
        }

        $this->page++;
        $this->pages[$this->page] = '';
        $this->state = 2;
        $this->x = $this->l;
        $this->y = $this->t;
        $this->fontFamily = '';
    }

    public function SetFont($family, $style = '', $size = 0)
    {
        $family = strtolower($family);
        if ($family === '') {
            $family = $this->fontFamily;
        }

        if ($size == 0) {
            $size = $this->fontSizePt;
        }

        $this->fontFamily = $family;
        $this->fontStyle = $style;
        $this->fontSizePt = $size;
        $this->fontSize = $size / $this->k;
    }

    public function SetXY($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function SetX($x)
    {
        $this->x = $x;
    }

    public function SetY($y)
    {
        $this->y = $y;
    }

    public function GetX()
    {
        return $this->x;
    }

    public function GetY()
    {
        return $this->y;
    }

    public function Ln($h = null)
    {
        $this->x = $this->l;
        if ($h !== null) {
            $this->y += $h;
        } else {
            $this->y += $this->lasth;
        }
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $k = $this->k;
        if ($w == 0) {
            $w = $this->w - $this->r - $this->x;
        }

        $s = sprintf('%.2f %.2f %.2f %.2f re', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k);

        if ($fill) {
            $this->pages[$this->page] .= $s . " f\n";
        }

        if ($border) {
            $this->pages[$this->page] .= $s . " S\n";
        }

        if (trim($txt) != '') {
            $x = $this->x + ($w / 2);
            $y = $this->y + ($h / 2);
            
            $this->pages[$this->page] .= sprintf('BT %.2f %.2f Td (%s) Tj ET', 
                $x * $k, 
                ($this->h - $y) * $k, 
                $this->_escape($txt)
            ) . "\n";
        }

        $this->lasth = $h;
        if ($ln > 0) {
            $this->y += $h;
            if ($ln == 2) {
                $this->x = $this->l;
            }
        } else {
            $this->x += $w;
        }

        return $this;
    }

    public function MultiCell($w, $h, $txt = '', $border = 0, $align = 'J', $fill = false)
    {
        $cw = &$this->currentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->r - $this->x;
        }

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->fontSizePt;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
            }
            if (strpos($border, 'L') !== false) {
                $b |= 1;
            }
            if (strpos($border, 'R') !== false) {
                $b |= 2;
            }
        }

        $b_top = 0;
        if ($border) {
            if (strpos($border, 'T') !== false) {
                $b_top = 1;
            }
        }

        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;

        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $this->Cell($w, $h, substr($s, $j, $i - $j), $b_top, 1, $align, $fill);
                $i++;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                $b_top = 0;
                continue;
            }

            if ($c == ' ') {
                $l = $i;
                $ns++;
            }

            $i++;
        }

        if ($i != $j) {
            $this->Cell($w, $h, substr($s, $j), 0, 1, $align, $fill);
        }

        return $this;
    }

    public function SetFillColor($r, $g = null, $b = null)
    {
        if (is_null($g)) {
            $this->fillColor = sprintf('%.3f %.3f %.3f', $r / 255, $r / 255, $r / 255);
        } else {
            $this->fillColor = sprintf('%.3f %.3f %.3f', $r / 255, $g / 255, $b / 255);
        }
    }

    public function SetTextColor($r, $g = null, $b = null)
    {
        if (is_null($g)) {
            $this->textColor = sprintf('%.3f %.3f %.3f', $r / 255, $r / 255, $r / 255);
        } else {
            $this->textColor = sprintf('%.3f %.3f %.3f', $r / 255, $g / 255, $b / 255);
        }
    }

    public function SetDrawColor($r, $g = null, $b = null)
    {
        if (is_null($g)) {
            $this->drawColor = sprintf('%.3f %.3f %.3f', $r / 255, $r / 255, $r / 255);
        } else {
            $this->drawColor = sprintf('%.3f %.3f %.3f', $r / 255, $g / 255, $b / 255);
        }
    }

    public function SetLineWidth($width)
    {
        $this->lineWidth = $width;
    }

    public function EndPage()
    {
        $this->state = 1;
    }

    public function Output($dest = '', $name = '')
    {
        if ($this->state < 3) {
            $this->Close();
        }

        $out = $this->_putpdf();

        if ($dest === '' || $dest === 'I') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            echo $out;
        } elseif ($dest === 'D') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            echo $out;
        } elseif ($dest === 'F') {
            file_put_contents($name, $out);
        } elseif ($dest === 'S') {
            return $out;
        }

        return '';
    }

    public function Close()
    {
        if ($this->state == 3) {
            return;
        }

        if ($this->page > 0) {
            $this->EndPage();
        }

        $this->state = 3;
    }

    private function _escape($s)
    {
        return str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $s)));
    }

    private function _putpdf()
    {
        $out = "%PDF-1.3\n";
        $out .= "%äöüß\n";

        // Minimal PDF structure
        $out .= "1 0 obj\n<< >>\nendobj\n";
        $out .= "xref\n0 1\n0000000000 65535 f\n";
        $out .= "trailer\n<< /Size 1 /Root 1 0 R >>\n";
        $out .= "startxref\n0\n%%EOF";

        return $out;
    }
}