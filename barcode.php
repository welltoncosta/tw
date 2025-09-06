<?php
// barcode.php - Arquivo para gerar código de barras Code 128
function generateBarcodeImage($code, $width = 2, $height = 80) {
    // Validar o código
    if (empty($code)) {
        return null;
    }
    
    // Calcula o comprimento total da imagem
    $barcodeLength = (strlen($code) * 11 * $width) + 20;
    
    // Cria uma imagem
    $image = imagecreate($barcodeLength, $height);
    
    // Define cores
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    
    // Preenche o fundo com branco
    imagefill($image, 0, 0, $white);
    
    // Tabela de padrões Code 128 (simplificada)
    $code128Patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313112', '311312',
        '311411', '331112', '331211', '341111', '411131', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '233111', '211133', '211331', '213113',
        '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', '314111', '221411',
        '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', '112412', '122114',
        '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', '111242', '121142',
        '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', '214121', '412121',
        '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', '114131', '311141',
        '411131', '211412', '211214', '211232', '233111'
    ];
    
    // Iniciar com o caractere de início (Code 128-B)
    $pattern = $code128Patterns[104];
    $x = 10;
    
    // Adicionar cada caractere do código
    for ($i = 0; $i < strlen($code); $i++) {
        $char = $code[$i];
        $charCode = ord($char);
        
        // Para caracteres ASCII básicos (32-126)
        if ($charCode >= 32 && $charCode <= 126) {
            $patternIndex = $charCode - 32;
            $pattern .= $code128Patterns[$patternIndex];
        } else {
            // Usar padrão padrão para caracteres não suportados
            $pattern .= $code128Patterns[0];
        }
    }
    
    // Calcular checksum (simplificado)
    $checksum = 104; // Iniciar com o valor do caractere de início
    for ($i = 0; $i < strlen($code); $i++) {
        $charCode = ord($code[$i]);
        if ($charCode >= 32 && $charCode <= 126) {
            $checksum += ($charCode - 32) * ($i + 1);
        }
    }
    $checksum = $checksum % 103;
    $pattern .= $code128Patterns[$checksum];
    
    // Adicionar caractere de parada
    $pattern .= $code128Patterns[106];
    
    // Desenhar as barras
    for ($i = 0; $i < strlen($pattern); $i++) {
        $barWidth = $pattern[$i] * $width;
        $color = ($i % 2 == 0) ? $black : $white;
        
        imagefilledrectangle($image, $x, 5, $x + $barWidth - 1, $height - 15, $color);
        $x += $barWidth;
    }
    
    // Adicionar o texto abaixo do código de barras
    $textColor = imagecolorallocate($image, 0, 0, 0);
    $font = 3; // Fonte interna do GD
    $textWidth = imagefontwidth($font) * strlen($code);
    $textX = ($barcodeLength - $textWidth) / 2;
    imagestring($image, $font, $textX, $height - 15, $code, $textColor);
    
    // Captura a imagem como string
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    imagedestroy($image);
    
    // Retorna a imagem em base64
    return 'data:image/png;base64,' . base64_encode($imageData);
}
?>
