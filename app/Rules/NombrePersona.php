<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NombrePersona implements ValidationRule
{
    private const PARTICULAS = ['de', 'del', 'la', 'las', 'los', 'y', 'van', 'von', 'da', 'di'];

    public function __construct(private int $maxPalabras = 4)
    {
    }

    /** Quita espacios de más y pone mayúscula inicial ("maría  josé" => "María José"). */
    public static function limpiar(?string $texto): string
    {
        $texto = trim((string) preg_replace('/\s+/u', ' ', (string) $texto));
        $palabras = explode(' ', mb_strtolower($texto));

        foreach ($palabras as $i => $palabra) {
            if ($i > 0 && in_array($palabra, self::PARTICULAS, true)) {
                continue;
            }
            $palabras[$i] = preg_replace_callback(
                '/(^|[\-\'])(\pL)/u',
                fn ($m) => $m[1] . mb_strtoupper($m[2]),
                $palabra
            );
        }

        return implode(' ', $palabras);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $texto = trim((string) $value);

        if (! preg_match('/^\pL+(?:[\'\-]\pL+)*(?: \pL+(?:[\'\-]\pL+)*)*$/u', $texto)) {
            $fail('El campo :attribute solo puede llevar letras, un espacio entre palabras, guion o apóstrofe.');
            return;
        }

        $palabras = explode(' ', $texto);

        if (count($palabras) > $this->maxPalabras) {
            $fail('El campo :attribute puede tener máximo ' . $this->maxPalabras . ' palabras.');
            return;
        }

        foreach ($palabras as $palabra) {
            if (mb_strlen($palabra) > 20) {
                $fail('Cada palabra de :attribute debe tener máximo 20 letras. Separa con un espacio.');
                return;
            }
            if (preg_match('/(\pL)\1{2,}/iu', $palabra)) {
                $fail('El campo :attribute tiene letras repetidas que no parecen un nombre real.');
                return;
            }
            // 5 consonantes seguidas o una palabra larga sin vocales no es un nombre
            if (preg_match('/(?:(?![aeiouyáéíóúü])\pL){5,}/iu', $palabra)
                || (mb_strlen($palabra) >= 3 && ! preg_match('/[aeiouyáéíóúü]/iu', $palabra))) {
                $fail('El campo :attribute no parece un nombre real. Revísalo.');
                return;
            }
        }
    }
}