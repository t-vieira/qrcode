<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Formatar data em português brasileiro
     */
    public static function formatBrazilian($date, $format = 'd/m/Y')
    {
        if (!$date) {
            return '';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $carbon->setLocale('pt_BR');
        
        return $carbon->format($format);
    }

    /**
     * Formatar data com mês em português
     */
    public static function formatWithMonth($date)
    {
        if (!$date) {
            return '';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $carbon->setLocale('pt_BR');
        
        $months = [
            1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
            5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
        ];
        
        $day = $carbon->day;
        $month = $months[$carbon->month];
        $year = $carbon->year;
        
        return "{$day} {$month}, {$year}";
    }

    /**
     * Formatar data completa em português
     */
    public static function formatFull($date)
    {
        if (!$date) {
            return '';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $carbon->setLocale('pt_BR');
        
        return $carbon->format('d \d\e F \d\e Y');
    }
}
