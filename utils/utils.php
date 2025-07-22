<?php
if (!function_exists('normalizeKey')) {
    function normalizeKey(string $label): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
        $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
        $key   = strtolower(trim($key, '_'));

        if ($key === '') {
            $replacements = ['%' => 'porcentaje', '#' => 'numero'];
            $key = $replacements[$label] ?? 'col_' . dechex(crc32($label));
        }
        return $key;
    }
}
