<?php

namespace App\Support;

class Barcode
{
    /** Pola Code 39: 9 elemen per karakter (bar, space, bar, ...), n = narrow, w = wide. */
    private const TABLE = [
        '0' => 'nnnwwnwnn', '1' => 'wnnwnnnnw', '2' => 'nnwwnnnnw', '3' => 'wnwwnnnnn',
        '4' => 'nnnwwnnnw', '5' => 'wnnwwnnnn', '6' => 'nnwwwnnnn', '7' => 'nnnwnnwnw',
        '8' => 'wnnwnnwnn', '9' => 'nnwwnnwnn',
        'A' => 'wnnnnwnnw', 'B' => 'nnwnnwnnw', 'C' => 'wnwnnwnnn', 'D' => 'nnnnwwnnw',
        'E' => 'wnnnwwnnn', 'F' => 'nnwnwwnnn', 'G' => 'nnnnnwwnw', 'H' => 'wnnnnwwnn',
        'I' => 'nnwnnwwnn', 'J' => 'nnnnwwwnn', 'K' => 'wnnnnnnww', 'L' => 'nnwnnnnww',
        'M' => 'wnwnnnnwn', 'N' => 'nnnnwnnww', 'O' => 'wnnnwnnwn', 'P' => 'nnwnwnnwn',
        'Q' => 'nnnnnnwww', 'R' => 'wnnnnnwwn', 'S' => 'nnwnnnwwn', 'T' => 'nnnnwnwwn',
        'U' => 'wwnnnnnnw', 'V' => 'nwwnnnnnw', 'W' => 'wwwnnnnnn', 'X' => 'nwnnwnnnw',
        'Y' => 'wwnnwnnnn', 'Z' => 'nwwnwnnnn',
        '-' => 'nwnnnnwnw', '.' => 'wwnnnnwnn', ' ' => 'nwwnnnwnn',
        '$' => 'nwnwnwnnn', '/' => 'nwnwnnnwn', '+' => 'nwnnnwnwn', '%' => 'nnnwnwnwn',
        '*' => 'nwnnwnwnn',
    ];

    /** Kode pelanggan stabil dari primary key, mis. C000042. */
    public static function customerCode(int|string $id): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $id) ?: '0';

        return 'C' . str_pad($digits, 6, '0', STR_PAD_LEFT);
    }

    /** Buang karakter yang tidak didukung Code 39. */
    public static function sanitize(string $data): string
    {
        $data = strtoupper(trim($data));
        $data = (string) preg_replace('/[^0-9A-Z\-. $\/+%]/', '', $data);

        return $data === '' ? '0' : $data;
    }

    /**
     * Render barcode Code 39 sebagai SVG inline (tanpa dependensi eksternal).
     * Quiet zone 10 modul di kedua sisi sesuai spesifikasi agar mudah dipindai.
     */
    public static function code39Svg(
        string $data,
        int $height = 52,
        string $color = '#03201a',
        float $narrow = 2.0,
        float $wideRatio = 2.5,
        int $quiet = 10
    ): string {
        $plain   = self::sanitize($data);
        $payload = '*' . $plain . '*';

        $height    = max(16, $height);
        $narrow    = max(0.5, $narrow);
        $wideRatio = max(2.0, $wideRatio);
        $quiet     = max(6, $quiet);
        $color     = preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) ? $color : '#03201a';

        $chars  = str_split($payload);
        $count  = count($chars);
        $x      = $quiet * $narrow;
        $rects  = [];

        foreach ($chars as $i => $ch) {
            $pattern = self::TABLE[$ch] ?? self::TABLE['0'];

            for ($e = 0; $e < 9; $e++) {
                $w = ($pattern[$e] === 'w' ? $wideRatio : 1.0) * $narrow;

                if ($e % 2 === 0) {
                    $rects[] = '<rect x="' . round($x, 2) . '" y="0" width="' . round($w, 2) . '" height="' . $height . '"/>';
                }

                $x += $w;
            }

            if ($i < $count - 1) {
                $x += $narrow;
            }
        }

        $total = round($x + $quiet * $narrow, 2);

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $total . ' ' . $height . '" '
            . 'width="100%" height="' . $height . '" preserveAspectRatio="none" shape-rendering="crispEdges" '
            . 'fill="' . $color . '" role="img" aria-label="Barcode pelanggan ' . $plain . '">'
            . implode('', $rects)
            . '</svg>';
    }
}