# NfePhp Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)
[![PHP Version](https://img.shields.io/packagist/php-v/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)
[![License](https://img.shields.io/packagist/l/diogo-graciano/nfephp-laravel.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp-laravel)

Um pacote Laravel elegante e prático para integração com o NFePHP, facilitando a emissão e gestão de Notas Fiscais Eletrônicas (NFe) e Notas Fiscais de Consumidor Eletrônicas (NFCe) em aplicações Laravel.

## ✨ Características

- 🚀 **Integração simplificada** com o NFePHP
- 📄 **Geração de DANFE** (PDF) para NFe, NFCe, DANFE simplificado e eventos
- 🔧 **Gerenciamento de contingências** automático e manual
- 📋 **Validações robustas** para CNPJ, CPF e chaves de acesso
- 🛠️ **Helpers utilitários** para formatação e manipulação de dados
- 📊 **Gerenciamento de certificados** digitais
- 🎯 **Configuração flexível** via arquivo de configuração
- ✅ **Testes abrangentes** com PHPUnit
- 📚 **Documentação completa** e exemplos práticos

## 📋 Requisitos

- PHP 8.2 ou superior
- Laravel 12.0 ou superior
- NFePHP 5.1 ou superior
- SPED-DA 1.0 ou superior (geração de DANFE)

## 🚀 Instalação

Instale o pacote via Composer:

```bash
composer require diogo-graciano/nfephp-laravel
```

### Publicar configurações

```bash
php artisan vendor:publish --provider="DiogoGraciano\Nfephp\NfephpServiceProvider" --tag="config"
```

### Configurar variáveis de ambiente

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# Configurações básicas
NFEPHP_TP_AMB=2
NFEPHP_RAZAO_SOCIAL="Sua Razão Social"
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

# Configurações opcionais
NFEPHP_TOKEN_IBPT="seu_token_ibpt"
NFEPHP_CSC="seu_csc"
NFEPHP_CSC_ID="seu_csc_id"
```

## 📖 Uso

O pacote registra a facade `Nfephp` automaticamente. Use-a nos controllers, commands e onde precisar:

```php
use Nfephp;

// Verificar se o certificado está válido
if (Nfephp::isCertificateValid()) {
    echo "Certificado válido!";
}

// Obter informações do certificado
$certInfo = Nfephp::getCertificateInfo();
```

### Gerenciamento de contingências

```php
use Nfephp;

// Ativar contingência
$contingencyJson = Nfephp::activateContingency('SP', 'SEFAZ fora do ar', 'SVCAN');

// Verificar se está em contingência
if (Nfephp::isInContingency()) {
    echo "Sistema em modo de contingência";
}

// Desativar contingência
Nfephp::deactivateContingency();
```

### Validações

```php
use Nfephp;

// Validar CNPJ
if (Nfephp::validateCnpj('12345678000195')) {
    echo "CNPJ válido";
}

// Validar CPF
if (Nfephp::validateCpf('12345678901')) {
    echo "CPF válido";
}

// Validar chave de acesso da NFe
if (Nfephp::validateNFeKey('12345678901234567890123456789012345678901234')) {
    echo "Chave de acesso válida";
}
```

### Formatação de dados

```php
use Nfephp;

// Formatar CNPJ
$cnpjFormatado = Nfephp::formatCnpj('12345678000195');
// Resultado: 12.345.678/0001-95

// Formatar CPF
$cpfFormatado = Nfephp::formatCpf('12345678901');
// Resultado: 123.456.789-01

// Limpar string
$stringLimpa = Nfephp::cleanString('Texto com caracteres especiais!@#');
```

### Helpers de UF

```php
use Nfephp;

// Obter código da UF
$codigoUf = Nfephp::getUfCode('SP'); // Retorna: 35

// Obter UF pelo código
$uf = Nfephp::getUfByCode(35); // Retorna: SP

// Obter timezone da UF
$timezone = Nfephp::getTimezoneByUf('SP'); // Retorna: America/Sao_Paulo

// Gerar chave de acesso da NFe
$chave = Nfephp::generateNFeKey('35', '2401', '12345678000195', '55', '1', '1', '1', '12345678');
```

### Geração de DANFE (PDF)

```php
use Nfephp;

$xmlAutorizado = '...'; // XML autorizado da NFe

// Gerar PDF do DANFE (retorna string binária do PDF)
$pdf = Nfephp::generateDanfe($xmlAutorizado);

// Salvar DANFE no Storage do Laravel
Nfephp::saveDanfe($xmlAutorizado, 'danfes/nota-001.pdf'); // disco padrão
Nfephp::saveDanfe($xmlAutorizado, 'danfes/nota-001.pdf', 's3'); // disco específico

// Retornar como download HTTP (em controllers)
return Nfephp::downloadDanfe($xmlAutorizado, 'nota-001.pdf');

// Retornar para visualização inline no navegador
return Nfephp::renderDanfe($xmlAutorizado, 'nota-001.pdf');
```

#### DANFE para NFCe

```php
$xmlNfce = '...'; // XML autorizado da NFCe

$pdf = Nfephp::generateDanfce($xmlNfce);
Nfephp::saveDanfce($xmlNfce, 'danfces/cupom-001.pdf');
return Nfephp::downloadDanfce($xmlNfce, 'cupom-001.pdf');
return Nfephp::renderDanfce($xmlNfce, 'cupom-001.pdf');
```

#### DANFE Simplificado

```php
$pdf = Nfephp::generateDanfeSimples($xmlAutorizado);
Nfephp::saveDanfeSimples($xmlAutorizado, 'danfes/simples-001.pdf');
return Nfephp::downloadDanfeSimples($xmlAutorizado, 'simples-001.pdf');
return Nfephp::renderDanfeSimples($xmlAutorizado, 'simples-001.pdf');
```

#### Documento de Evento (Cancelamento, CCe, etc.)

```php
$xmlEvento = '...'; // XML do evento

// Dados do emitente (para o cabeçalho do documento)
$dadosEmitente = [
    'razao' => 'Empresa Teste LTDA',
    'logradouro' => 'Rua Exemplo',
    'numero' => '123',
    'complemento' => 'Sala 1',
    'bairro' => 'Centro',
    'CEP' => '01001000',
    'municipio' => 'São Paulo',
    'UF' => 'SP',
    'telefone' => '1199999999',
    'email' => 'contato@empresa.com',
];

$pdf = Nfephp::generateDaevento($xmlEvento, $dadosEmitente);
Nfephp::saveDaevento($xmlEvento, 'eventos/cancelamento-001.pdf', $dadosEmitente);
return Nfephp::downloadDaevento($xmlEvento, $dadosEmitente, 'cancelamento-001.pdf');
return Nfephp::renderDaevento($xmlEvento, $dadosEmitente, 'cancelamento-001.pdf');
```

#### Definir Logo do DANFE

```php
// Via configuração (.env)
// NFEPHP_DANFE_LOGO_PATH="/path/to/logo.png"

// Ou em tempo de execução
Nfephp::setDanfeLogo('/path/to/logo.png');
Nfephp::setDanfeLogo(null); // remover logo
```

### Uso sem Facade (injeção de dependência)

Se preferir injetar a instância ou usar em classes sem facade:

```php
use DiogoGraciano\Nfephp\Nfephp;

$nfephp = app('nfephp'); // ou new Nfephp()

if ($nfephp->isCertificateValid()) {
    echo "Certificado válido!";
}
```

## 🧪 Testes

Execute os testes com:

```bash
composer test
```

Para executar com cobertura de código:

```bash
composer test-coverage
```

## 📁 Estrutura do Pacote

```
src/
├── Helpers/
│   ├── StringHelper.php       # Helpers para manipulação de strings
│   ├── UfHelper.php           # Helpers para códigos de UF
│   └── ValidationHelper.php   # Helpers para validações
├── Managers/
│   ├── CertificateManager.php # Gerenciamento de certificados
│   ├── ContingencyManager.php # Gerenciamento de contingências
│   ├── DanfeManager.php       # Geração de DANFE (PDF)
│   └── NfephpManager.php     # Classe base (manager NFe/NFCe)
├── Facades/
│   └── Nfephp.php            # Facade do Laravel
├── Nfephp.php                # Classe principal
└── NfephpServiceProvider.php  # Service Provider
```

## 🐛 Troubleshooting

### Problemas Comuns

1. **Erro de certificado inválido**
   - Verifique se o caminho do certificado está correto
   - Confirme se a senha está correta
   - Verifique se o certificado não expirou

2. **Erro de contingência**
   - Verifique se a sigla da UF está correta
   - Confirme se o motivo tem entre 15-255 caracteres
   - Verifique se o tipo de contingência é válido

3. **Erro de validação**
   - Verifique se os dados estão no formato correto
   - Confirme se os CNPJ/CPF são válidos
   - Verifique se as chaves de acesso estão corretas

## 📚 Documentação Adicional

- [NFePHP Oficial](https://github.com/nfephp-org/sped-nfe)
- [SPED-DA (DANFE)](https://github.com/nfephp-org/sped-da)
- [Documentação Laravel](https://laravel.com/docs)
- [Especificações Técnicas da NFe](http://www.nfe.fazenda.gov.br/)

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor, leia o [guia de contribuição](CONTRIBUTING.md) antes de enviar pull requests.

### Processo de Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 🔒 Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um email para diogo.dg691@gmail.com ao invés de usar o issue tracker.

## 📄 Changelog

Veja o [CHANGELOG](CHANGELOG.md) para informações sobre mudanças recentes.

## 👥 Créditos

- [Diogo Graciano Comin](https://github.com/DiogoGraciano) - Desenvolvedor principal
- [NFePHP Community](https://github.com/nfephp-org) - Biblioteca base
- [Todos os Contribuidores](../../contributors)

## 📜 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE.md) para detalhes.

## 🙏 Agradecimentos

- À comunidade NFePHP pelo excelente trabalho na biblioteca base
- À comunidade Laravel pela framework incrível
- A todos os contribuidores e usuários do pacote

---

**⭐ Se este pacote foi útil para você, considere dar uma estrela no GitHub!**
