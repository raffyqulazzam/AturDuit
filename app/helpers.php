<?php

if (!function_exists('format_idr')) {
    /**
     * Format number to IDR currency format
     *
     * @param float|int $amount
     * @return string
     */
    function format_idr($amount)
    {
        return 'Rp. ' . number_format($amount, 0, ',', '.');
    }
}
