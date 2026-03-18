# NfePhp Laravel

[![Tests](https://github.com/DiogoGraciano/NfePhpLaravel/actions/workflows/main.yml/badge.svg)](https://github.com/DiogoGraciano/NfePhpLaravel/actions/workflows/main.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)
[![PHP Version](https://img.shields.io/packagist/php-v/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)
[![License](https://img.shields.io/packagist/l/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)

Um pacote Laravel elegante e pratico para integracao com o NFePHP e NFSe Nacional, facilitando a emissao e gestao de Notas Fiscais Eletronicas (NFe/NFCe) e Notas Fiscais de Servico Eletronicas (NFSe) em aplicacoes Laravel.

## Caracteristicas

- Integracao simplificada com o NFePHP e NFSe Nacional
- **6 Facades independentes** para separacao clara de responsabilidades
- Geracao de DANFE (PDF) para NFe, NFCe, DANFE simplificado e eventos
- Gerenciamento de contingencias automatico e manual
- Validacoes robustas para CNPJ, CPF e chaves de acesso (compartilhado NFe/NFSe)
- Helpers utilitarios para formatacao e manipulacao de dados
- Gerenciamento de certificados digitais (compartilhado NFe/NFSe)
- Operacoes NFSe (envio DPS, consulta, cancelamento, DANFSE)
- Configuracao flexivel via arquivo de configuracao
- Testes abrangentes com PHPUnit

## Requisitos

- PHP 8.2 ou superior
- Laravel 12.0 ou 13.0
- NFePHP 5.1 ou superior
- SPED-DA 1.0 ou superior (geracao de DANFE)
- NFSe Nacional 1.0 ou superior (para NFSe)

## Instalação

Instale o pacote via Composer:

```bash
composer require diogo-graciano/nfephp-laravel
```

### Publicar configuracoes

```bash
php artisan vendor:publish --provider="DiogoGraciano\Nfephp\NfephpServiceProvider" --tag="config"
```

### Configurar variaveis de ambiente

Adicione as seguintes variaveis ao seu arquivo `.env`:

```env
# Configuracoes basicas
NFEPHP_TP_AMB=2
NFEPHP_RAZAO_SOCIAL="Sua Razao Social"
NFEPHP_CNPJ="12345678000195"
NFEPHP_IE="123456789"
NFEPHP_SIGLA_UF="SP"
NFEPHP_SCHEMES="PL_009_V4"
NFEPHP_VERSAO="4.00"

# Certificado digital
NFEPHP_CERTIFICATE_PATH="/path/to/your/certificate.pfx"
NFEPHP_CERTIFICATE_PASSWORD="sua_senha"

# DANFE (logo opcional)
NFEPHP_DANFE_LOGO_PATH="/path/to/logo.png"

# Configuracoes opcionais
NFEPHP_TOKEN_IBPT="seu_token_ibpt"
NFEPHP_CSC="seu_csc"
NFEPHP_CSC_ID="seu_csc_id"
```

## Arquitetura

O pacote utiliza 6 facades independentes, cada uma com responsabilidade bem definida:

| Facade | Binding | Classe | Responsabilidade |
|--------|---------|--------|------------------|
| `Nfe` | `'nfe'` | `NfeManager` | Operacoes NFe (criar, enviar, consultar, cancelar, inutilizar, manifestar) |
| `Danfe` | `'danfe'` | `DanfeManager` | Geracao de PDF (DANFE, DANFCe, DANFESimples, DAEvento) |
| `Contingency` | `'contingency'` | `ContingencyManager` | Ativar/desativar/verificar contingencia |
| `Certificate` | `'certificate'` | `CertificateManager` | Info, validade e expiracao do certificado (compartilhado NFe/NFSe) |
| `Utils` | `'nfe-utils'` | `UtilsManager` | Validacao, formatacao, helpers de UF (compartilhado NFe/NFSe) |
| `Nfse` | `'nfse'` | `Nfse` | Operacoes NFSe (envio DPS, consulta, cancelamento, DANFSE) |

### Contexto compartilhado (NfeContext)

O `NfeContext` e um singleton que gerencia o estado compartilhado entre as facades NFe:

```
NfeContext (singleton)
├── config[]
├── Certificate ──compartilhado──> CertificateManager (facade Certificate)
├── Tools (NFePHP\NFe\Tools) ──usado por──> NfeManager
├── ContingencyManager ──callback──> NfeContext.reinitialize()
```

Quando a contingencia e ativada/desativada, o `NfeContext` reinicializa automaticamente o Tools.

## Uso

### Facade Nfe - Operacoes NFe/NFCe

```php
use DiogoGraciano\Nfephp\Facades\Nfe;

// Criar XML da NFe
$make = Nfe::createNFe();

// Criar XML da NFCe
$make = Nfe::createNFCe();

// Enviar NFe (lote)
$response = Nfe::sendNFe([$xmlAssinado1, $xmlAssinado2]);

// Consultar NFe
$response = Nfe::consultNFe('12345678901234567890123456789012345678901234');

// Cancelar NFe
$response = Nfe::cancelNFe(
    '12345678901234567890123456789012345678901234',
    'Justificativa do cancelamento',
    '123456789012345'
);

// Inutilizar numeracao
$response = Nfe::inutilizeNFe(1, 1, 10, 'Justificativa da inutilizacao');

// Distribuicao DFe
$response = Nfe::distributionDFe(0);

// Manifestacao do destinatario
Nfe::confirmNFe($chaveNFe);         // Confirmacao da operacao
Nfe::acknowledgeNFe($chaveNFe);     // Ciencia da operacao
Nfe::unknownNFe($chaveNFe);         // Desconhecimento da operacao
Nfe::notPerformedNFe($chaveNFe, 'Justificativa'); // Operacao nao realizada

// Manifestacao em lote
Nfe::manifestNFeBatch($std);

// Gerar QR Code
Nfe::generateQRCode($dom, $token, $id, $versao, $urlQR, $urlChave);
Nfe::generateNFeQRCode($dom, $token, $id, $versao, $urlQR, $urlChave);

// Padronizar resposta XML
$response = Nfe::standardizeResponse($xmlResponse);

// Configuracao
$config = Nfe::getConfig();
$nfeConfig = Nfe::getNFeConfig();
Nfe::setConfig(['chave' => 'valor']);
Nfe::setNFeConfig(['tpAmb' => 1]);

// Obter Tools diretamente
$tools = Nfe::getTools();
```

### Facade Danfe - Geracao de PDF

```php
use DiogoGraciano\Nfephp\Facades\Danfe;

$xmlAutorizado = '...'; // XML autorizado da NFe

// Gerar PDF do DANFE (retorna string binaria do PDF)
$pdf = Danfe::generateDanfe($xmlAutorizado);

// Salvar DANFE no Storage do Laravel
Danfe::saveDanfe($xmlAutorizado, 'danfes/nota-001.pdf');         // disco padrao
Danfe::saveDanfe($xmlAutorizado, 'danfes/nota-001.pdf', 's3');   // disco especifico

// Retornar como download HTTP
return Danfe::downloadDanfe($xmlAutorizado, 'nota-001.pdf');

// Retornar para visualizacao inline no navegador
return Danfe::renderDanfe($xmlAutorizado, 'nota-001.pdf');
```

#### DANFE para NFCe

```php
$pdf = Danfe::generateDanfce($xmlNfce);
Danfe::saveDanfce($xmlNfce, 'danfces/cupom-001.pdf');
return Danfe::downloadDanfce($xmlNfce, 'cupom-001.pdf');
return Danfe::renderDanfce($xmlNfce, 'cupom-001.pdf');
```

#### DANFE Simplificado

```php
$pdf = Danfe::generateDanfeSimples($xmlAutorizado);
Danfe::saveDanfeSimples($xmlAutorizado, 'danfes/simples-001.pdf');
return Danfe::downloadDanfeSimples($xmlAutorizado, 'simples-001.pdf');
return Danfe::renderDanfeSimples($xmlAutorizado, 'simples-001.pdf');
```

#### Documento de Evento (Cancelamento, CCe, etc.)

```php
$xmlEvento = '...';

$dadosEmitente = [
    'razao' => 'Empresa Teste LTDA',
    'logradouro' => 'Rua Exemplo',
    'numero' => '123',
    'complemento' => 'Sala 1',
    'bairro' => 'Centro',
    'CEP' => '01001000',
    'municipio' => 'Sao Paulo',
    'UF' => 'SP',
    'telefone' => '1199999999',
    'email' => 'contato@empresa.com',
];

$pdf = Danfe::generateDaevento($xmlEvento, $dadosEmitente);
Danfe::saveDaevento($xmlEvento, 'eventos/cancelamento-001.pdf', $dadosEmitente);
return Danfe::downloadDaevento($xmlEvento, $dadosEmitente, 'cancelamento-001.pdf');
return Danfe::renderDaevento($xmlEvento, $dadosEmitente, 'cancelamento-001.pdf');
```

#### Logo do DANFE

```php
// Via .env: NFEPHP_DANFE_LOGO_PATH="/path/to/logo.png"

// Ou em tempo de execucao
Danfe::setLogo('/path/to/logo.png');
Danfe::setLogo(null); // remover logo
```

### Facade Contingency - Gerenciamento de Contingencia

```php
use DiogoGraciano\Nfephp\Facades\Contingency;

// Ativar contingencia
$json = Contingency::activate('SP', 'SEFAZ fora do ar', 'SVCAN');

// Verificar se esta em contingencia
if (Contingency::isActive()) {
    echo "Sistema em modo de contingencia";
}

// Obter informacoes da contingencia ativa
$info = Contingency::getInfo();
// Retorna: ['type' => 'SVCAN', 'motive' => '...', 'timestamp' => '...', 'tpEmis' => '...']

// Ajustar XML para contingencia
$xmlAjustado = Contingency::adjustXml($xml);

// Carregar contingencia de JSON salvo
Contingency::load($jsonContingencia);

// Desativar contingencia
Contingency::deactivate();
```

### Facade Certificate - Gerenciamento de Certificado

```php
use DiogoGraciano\Nfephp\Facades\Certificate;

// Verificar se o certificado esta valido
if (Certificate::isValid()) {
    echo "Certificado valido!";
}

// Obter informacoes do certificado
$info = Certificate::getInfo();
// Retorna: ['cnpj', 'cpf', 'name', 'valid_from', 'valid_to', 'icp', 'ca_url', 'csp']

// Dias para expirar
$dias = Certificate::getDaysToExpire();

// Verificar se esta proximo do vencimento
if (Certificate::isNearExpiration(30)) {
    echo "Certificado expira em menos de 30 dias!";
}

// Obter dados especificos
$cnpj = Certificate::getCnpj();
$cpf = Certificate::getCpf();
$empresa = Certificate::getCompanyName();
```

### Facade Utils - Validacoes e Helpers (NFe + NFSe)

```php
use DiogoGraciano\Nfephp\Facades\Utils;

// Validacoes
Utils::validateCnpj('12345678000195');
Utils::validateCpf('12345678901');
Utils::validateNFeKey('12345678901234567890123456789012345678901234');
Utils::validateXml($xmlString);

// Formatacao
$cnpj = Utils::formatCnpj('12345678000195');  // 12.345.678/0001-95
$cpf = Utils::formatCpf('12345678901');         // 123.456.789-01

// Manipulacao de strings
$limpa = Utils::cleanString('Texto com acentos!@#');
$ascii = Utils::stringToAscii('Texto com acentuacao');
$params = Utils::equilizeParameters($string, $length);

// Helpers de UF
$codigo = Utils::getUfCode('SP');              // 35
$uf = Utils::getUfByCode(35);                  // SP
$timezone = Utils::getTimezoneByUf('SP');       // America/Sao_Paulo

// Gerar chave de acesso
$chave = Utils::generateNFeKey('35', '2401', '12345678000195', '55', '1', '1', '1', '12345678');
```

### Facade Nfse - Operacoes NFSe

```php
use DiogoGraciano\Nfephp\Facades\Nfse;

// Enviar DPS
$response = Nfse::sendDps($xmlDps);

// Consultar NFSe por chave
$response = Nfse::consultNfseByKey($chave);

// Consultar DPS por chave
$response = Nfse::consultDpsByKey($chave);

// Consultar eventos da NFSe
$response = Nfse::consultNfseEvents($chave);

// Obter DANFSE (PDF)
$pdf = Nfse::getDanfse($chave);

// Cancelar NFSe
$std = new \stdClass();
$std->chave = '12345678901234567890123456789012345678901234567890';
$response = Nfse::cancelNfse($std);

// Configuracao
$config = Nfse::getConfig();
```

**Nota:** Para operacoes de certificado com NFSe, use a facade `Certificate` que e compartilhada.

### Uso sem Facade (injecao de dependencia)

```php
// Via container
$nfe = app('nfe');           // NfeManager
$danfe = app('danfe');       // DanfeManager
$contingency = app('contingency'); // ContingencyManager
$certificate = app('certificate'); // CertificateManager
$utils = app('nfe-utils');   // UtilsManager
$nfse = app('nfse');         // Nfse

// Ou via type-hint no construtor/metodo
use DiogoGraciano\Nfephp\NfeContext;

public function __construct(NfeContext $context)
{
    $tools = $context->getTools();
    $cert = $context->getCertificate();
}
```

## Testes

Execute os testes com:

```bash
composer test
```

Para executar com cobertura de codigo:

```bash
composer test-coverage
```

## Estrutura do Pacote

```
src/
├── Helpers/
│   ├── StringHelper.php        # Helpers para manipulacao de strings
│   ├── UfHelper.php            # Helpers para codigos de UF
│   └── ValidationHelper.php    # Helpers para validacoes
├── Managers/
│   ├── CertificateManager.php  # Gerenciamento de certificados
│   ├── ContingencyManager.php  # Gerenciamento de contingencias
│   ├── DanfeManager.php        # Geracao de DANFE (PDF)
│   ├── NfeManager.php          # Operacoes NFe/NFCe
│   ├── NfseManager.php         # Operacoes NFSe
│   └── UtilsManager.php        # Validacoes e helpers compartilhados
├── Facades/
│   ├── Nfe.php                 # Facade NFe
│   ├── Danfe.php               # Facade DANFE
│   ├── Contingency.php         # Facade Contingencia
│   ├── Certificate.php         # Facade Certificado
│   ├── Utils.php               # Facade Utils (NFe + NFSe)
│   └── Nfse.php                # Facade NFSe
├── NfeContext.php               # Contexto compartilhado (singleton)
├── Nfse.php                     # Classe principal NFSe
└── NfephpServiceProvider.php    # Service Provider
```

## Migracao da versao anterior

Se voce usava a facade `Nfephp` unica, atualize para as novas facades:

| Antes (Nfephp::) | Agora |
|-------------------|-------|
| `Nfephp::createNFe()` | `Nfe::createNFe()` |
| `Nfephp::sendNFe()` | `Nfe::sendNFe()` |
| `Nfephp::generateDanfe()` | `Danfe::generateDanfe()` |
| `Nfephp::saveDanfe()` | `Danfe::saveDanfe()` |
| `Nfephp::activateContingency()` | `Contingency::activate()` |
| `Nfephp::isInContingency()` | `Contingency::isActive()` |
| `Nfephp::deactivateContingency()` | `Contingency::deactivate()` |
| `Nfephp::isCertificateValid()` | `Certificate::isValid()` |
| `Nfephp::getCertificateInfo()` | `Certificate::getInfo()` |
| `Nfephp::validateCnpj()` | `Utils::validateCnpj()` |
| `Nfephp::formatCnpj()` | `Utils::formatCnpj()` |
| `Nfephp::getUfCode()` | `Utils::getUfCode()` |
| `Nfephp::setDanfeLogo()` | `Danfe::setLogo()` |

## Troubleshooting

### Problemas Comuns

1. **Erro de certificado invalido**
   - Verifique se o caminho do certificado esta correto
   - Confirme se a senha esta correta
   - Verifique se o certificado nao expirou
   - Use `Certificate::getDaysToExpire()` para verificar a validade

2. **Erro de contingencia**
   - Verifique se a sigla da UF esta correta
   - Confirme se o motivo tem entre 15-255 caracteres
   - Verifique se o tipo de contingencia e valido (SVCAN, SVCRS)

3. **Tools nao inicializado**
   - O certificado precisa estar configurado para usar operacoes NFe
   - Verifique as variaveis `NFEPHP_CERTIFICATE_PATH` e `NFEPHP_CERTIFICATE_PASSWORD`

4. **NFSe Tools nao inicializado**
   - Verifique se o certificado esta configurado para NFSe tambem

## Documentacao Adicional

- [NFePHP Oficial](https://github.com/nfephp-org/sped-nfe)
- [SPED-DA (DANFE)](https://github.com/nfephp-org/sped-da)
- [NFSe Nacional](https://github.com/Rainzart/nfse-nacional)
- [Documentacao Laravel](https://laravel.com/docs)
- [Especificacoes Tecnicas da NFe](http://www.nfe.fazenda.gov.br/)

## Contribuindo

Contribuicoes sao bem-vindas! Por favor, leia o [guia de contribuicao](CONTRIBUTING.md) antes de enviar pull requests.

### Processo de Contribuicao

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudancas (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Seguranca

Se voce descobrir alguma vulnerabilidade de seguranca, por favor envie um email para diogo.dg691@gmail.com ao inves de usar o issue tracker.

## Changelog

Veja o [CHANGELOG](CHANGELOG.md) para informacoes sobre mudancas recentes.

## Creditos

- [Diogo Graciano Comin](https://github.com/DiogoGraciano) - Desenvolvedor principal
- [NFePHP Community](https://github.com/nfephp-org) - Biblioteca base
- [Todos os Contribuidores](../../contributors)

## Licenca

Este projeto esta licenciado sob a Licenca MIT - veja o arquivo [LICENSE](LICENSE.md) para detalhes.

---

**Se este pacote foi util para voce, considere dar uma estrela no GitHub!**
