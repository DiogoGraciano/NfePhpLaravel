<?php

namespace DiogoGraciano\Nfephp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DiogoGraciano\Nfephp\Managers\NfeManager
 *
 * @method static \NFePHP\NFe\Make createNFe()
 * @method static \NFePHP\NFe\Make createNFCe()
 * @method static \NFePHP\NFe\Tools getTools()
 * @method static string sendNFe(array $xmls, string $idLote = '', int $indSinc = 0, bool $compactar = false, array &$xmlsSubstitutos = [])
 * @method static string consultNFe(string $chave)
 * @method static string cancelNFe(string $chave, string $justificativa, string $numeroProtocolo)
 * @method static string inutilizeNFe(int $serie, int $numeroInicial, int $numeroFinal, string $justificativa, ?int $tpAmb = null, ?string $ano = null)
 * @method static string distributionDFe(int $ultNSU = 0, int $numNSU = 0, ?string $chave = null, string $fonte = 'AN')
 * @method static string manifestNFe(string $chave, int $tpEvento, string $xJust = '', int $nSeqEvento = 1)
 * @method static string manifestNFeLote(\stdClass $std)
 * @method static string confirmNFe(string $chave, int $nSeqEvento = 1)
 * @method static string acknowledgeNFe(string $chave)
 * @method static string unknownNFe(string $chave, int $nSeqEvento = 1)
 * @method static string notPerformedNFe(string $chave, string $xJust, int $nSeqEvento = 1)
 * @method static string manifestNFeBatch(\stdClass $std)
 * @method static string generateQRCode(\DOMDocument $dom, string $token, string $idToken, string $versao, string $urlqr, string $urichave, ?\NFePHP\Common\Certificate $certificate = null)
 * @method static string generateNFeQRCode(\DOMDocument $dom, string $token, string $idToken, string $versao, string $urlqr, string $urichave, ?\NFePHP\Common\Certificate $certificate = null)
 * @method static \stdClass standardizeResponse(string $response)
 * @method static array getConfig()
 * @method static array getNFeConfig()
 * @method static void setConfig(array $config)
 * @method static void setNFeConfig(array $config)
 */
class Nfe extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'nfe';
    }
}
