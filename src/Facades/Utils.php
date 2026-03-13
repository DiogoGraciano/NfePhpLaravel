<?php

namespace DiogoGraciano\Nfephp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DiogoGraciano\Nfephp\Managers\UtilsManager
 *
 * @method static bool validateXml(string $xml, string $xsd)
 * @method static bool validateCnpj(string $cnpj)
 * @method static bool validateCpf(string $cpf)
 * @method static bool validateCep(string $cep)
 * @method static bool validateEmail(string $email)
 * @method static bool validatePhone(string $phone)
 * @method static bool validateNFeKey(string $key)
 * @method static string cleanString(string $string)
 * @method static string stringToAscii(string $string)
 * @method static \stdClass equilizeParameters(\stdClass $std, array $possible, bool $replaceAccentedChars = false)
 * @method static string formatCnpj(string $cnpj)
 * @method static string formatCpf(string $cpf)
 * @method static string formatCep(string $cep)
 * @method static string formatPhone(string $phone)
 * @method static string unformatDocument(string $document)
 * @method static string unformatCep(string $cep)
 * @method static string unformatPhone(string $phone)
 * @method static string random(int $length = 10, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
 * @method static string upper(string $string)
 * @method static string lower(string $string)
 * @method static string capitalize(string $string)
 * @method static int getUfCode(string $uf)
 * @method static string getUfByCode(int $code)
 * @method static string getTimezoneByUf(string $uf)
 * @method static array getAllUfs()
 * @method static bool isValidUf(string $uf)
 * @method static string generateNFeKey(string $cUF, string $aamm, string $cnpj, string $mod, string $serie, string $nNF, string $tpEmis, string $cNF)
 * @method static string getUfFullName(string $uf)
 * @method static string getUfRegion(string $uf)
 * @method static array|null parseNFeKey(string $key)
 */
class Utils extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'nfe-utils';
    }
}
