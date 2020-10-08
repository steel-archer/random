<?php

/**
 * Drawing code has been taken here: https://boallen.com/random-numbers.html
 *
 * @param string $funcName
 * @param callable|null $seedFunc
 * @param callable $genFunc
 */
function draw(string $funcName, ?callable $seedFunc, callable $genFunc): void
{
    if (is_callable($seedFunc)) {
        $seedFunc();
    }

    $length = 512;
    $im = imagecreatetruecolor($length, $length) or die('Cannot Initialize new GD image stream');
    $white = imagecolorallocate($im, 255, 255, 255);

    for ($y = 0; $y < $length; $y++) {
        for ($x = 0; $x < $length; $x++) {
            if ($genFunc()) {
                imagesetpixel($im, $x, $y, $white);
            }
        }
    }

    ob_start();
    imagepng($im);
    imagedestroy($im);
    $image = base64_encode(ob_get_clean());

    $output = <<<OUTPUT
<b>{$funcName}:</b>
<br>
<img src='data:image/png;base64,{$image}' alt="{$funcName}"/>
<br><br>
OUTPUT;
    echo $output;
}

$allFuncs = [
    'mt_rand' => [
        'seedFunc' => static function () {
            mt_srand(0, MT_RAND_MT19937);
        },
        'genFunc' => static function () {
            return mt_rand(0, 1);
        },
    ],
    'mt_rand (old algorithm)' => [
        'seedFunc' => static function () {
            mt_srand(0, MT_RAND_PHP);
        },
        'genFunc' => static function () {
            return mt_rand(0, 1);
        },
    ],
    'random_int' =>  [
        'seedFunc' => null,
        'genFunc' => static function () {
            return random_int(0, 1);
        },
    ],
    'random_bytes' => [
        'seedFunc' => null,
        'genFunc' => static function () {
            return current(unpack('n', random_bytes(2))) % 2;
        }
    ],
    'openssl_random_pseudo_bytes' => [
        'seedFunc' => null,
        'genFunc' => static function () {
            return current(unpack('n', openssl_random_pseudo_bytes(2))) % 2;
        }
    ],
    'uniqid' => [
        'seedFunc' => null,
        'genFunc' => static function () {
            return hexdec(uniqid('', false)) % 2;
        }
    ],
    'uniqid (more entropy)' => [
        'seedFunc' => null,
        'genFunc' => static function () {
            $string = uniqid('', true); // E.g. 5f7f8002e1f033.79832804
            $parts = explode('.', $string);
            return (hexdec($parts[0]) + $parts[1]) % 2;
        }
    ],
];

foreach ($allFuncs as $funcName => $funcs) {
    draw($funcName, $funcs['seedFunc'], $funcs['genFunc']);
}
