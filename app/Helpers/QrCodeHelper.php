<?php

namespace App\Helpers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeHelper
{
    public static function generateSvg(string $data): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_M,
            'scale' => 4,
            'svgViewBox' => true,
            'addQuietzone' => true,
        ]);

        $qrcode = new QRCode($options);
        return $qrcode->render($data);
    }

    public static function generatePngFile(string $data): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_M,
            'scale' => 4,
            'addQuietzone' => true,
        ]);

        $path = tempnam(sys_get_temp_dir(), 'akatabo_qr_') . '.png';
        $qrcode = new QRCode($options);
        $qrcode->render($data, $path);
        return $path;
    }
}
