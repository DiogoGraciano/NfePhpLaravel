<?php

namespace DiogoGraciano\Nfephp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DiogoGraciano\Nfephp\Managers\DanfeManager
 *
 * @method static void setLogo(?string $logoPath)
 * @method static string generateDanfe(string $xml)
 * @method static void saveDanfe(string $xml, string $filePath, string $disk = '')
 * @method static \Illuminate\Http\Response downloadDanfe(string $xml, string $filename = 'danfe.pdf')
 * @method static \Illuminate\Http\Response renderDanfe(string $xml, string $filename = 'danfe.pdf')
 * @method static string generateDanfce(string $xml)
 * @method static void saveDanfce(string $xml, string $filePath, string $disk = '')
 * @method static \Illuminate\Http\Response downloadDanfce(string $xml, string $filename = 'danfce.pdf')
 * @method static \Illuminate\Http\Response renderDanfce(string $xml, string $filename = 'danfce.pdf')
 * @method static string generateDanfeSimples(string $xml)
 * @method static void saveDanfeSimples(string $xml, string $filePath, string $disk = '')
 * @method static \Illuminate\Http\Response downloadDanfeSimples(string $xml, string $filename = 'danfe_simples.pdf')
 * @method static \Illuminate\Http\Response renderDanfeSimples(string $xml, string $filename = 'danfe_simples.pdf')
 * @method static string generateDaevento(string $xml, array $dadosEmitente = [])
 * @method static void saveDaevento(string $xml, string $filePath, array $dadosEmitente = [], string $disk = '')
 * @method static \Illuminate\Http\Response downloadDaevento(string $xml, array $dadosEmitente = [], string $filename = 'evento.pdf')
 * @method static \Illuminate\Http\Response renderDaevento(string $xml, array $dadosEmitente = [], string $filename = 'evento.pdf')
 */
class Danfe extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'danfe';
    }
}
