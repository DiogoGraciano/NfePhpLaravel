# NfePhp Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/diogo-graciano/nfephp.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp)
[![Total Downloads](https://img.shields.io/packagist/dt/diogo-graciano/nfephp.svg?style=flat-square)](https://packagist.org/packages/diogo-graciano/nfephp)
![GitHub Actions](https://github.com/diogo-graciano/nfephp/actions/workflows/main.yml/badge.svg)

Um pacote Laravel elegante e prático para integração com o NFePHP, facilitando a emissão e gestão de Notas Fiscais Eletrônicas (NFe) e Notas Fiscais de Consumidor Eletrônicas (NFCe) em aplicações Laravel.

## ✨ Características

- 🚀 **Integração simplificada** com o NFePHP
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

## 🚀 Instalação

Instale o pacote via Composer:

```bash
composer require diogo-graciano/nfephp
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

# Configurações opcionais
NFEPHP_TOKEN_IBPT="seu_token_ibpt"
NFEPHP_CSC="seu_csc"
NFEPHP_CSC_ID="seu_csc_id"
NFEPHP_DEBUG=false
NFEPHP_PRODUCTION=false
```

## 📖 Uso

### Uso básico

```php
use DiogoGraciano\Nfephp\Nfephp;

$nfephp = new Nfephp();

// Verificar se o certificado está válido
if ($nfephp->isCertificateValid()) {
    echo "Certificado válido!";
}

// Obter informações do certificado
$certInfo = $nfephp->getCertificateInfo();
```

### Gerenciamento de contingências

```php
// Ativar contingência
$contingencyJson = $nfephp->activateContingency('SP', 'SEFAZ fora do ar', 'SVCAN');

// Verificar se está em contingência
if ($nfephp->isInContingency()) {
    echo "Sistema em modo de contingência";
}

// Desativar contingência
$nfephp->deactivateContingency();
```

### Validações

```php
// Validar CNPJ
if ($nfephp->validateCnpj('12345678000195')) {
    echo "CNPJ válido";
}

// Validar CPF
if ($nfephp->validateCpf('12345678901')) {
    echo "CPF válido";
}

// Validar chave de acesso da NFe
if ($nfephp->validateNFeKey('12345678901234567890123456789012345678901234')) {
    echo "Chave de acesso válida";
}
```

### Formatação de dados

```php
// Formatar CNPJ
$cnpjFormatado = $nfephp->formatCnpj('12345678000195');
// Resultado: 12.345.678/0001-95

// Formatar CPF
$cpfFormatado = $nfephp->formatCpf('12345678901');
// Resultado: 123.456.789-01

// Limpar string
$stringLimpa = $nfephp->cleanString('Texto com caracteres especiais!@#');
```

### Helpers de UF

```php
// Obter código da UF
$codigoUf = $nfephp->getUfCode('SP'); // Retorna: 35

// Obter UF pelo código
$uf = $nfephp->getUfByCode(35); // Retorna: SP

// Obter timezone da UF
$timezone = $nfephp->getTimezoneByUf('SP'); // Retorna: America/Sao_Paulo

// Gerar chave de acesso da NFe
$chave = $nfephp->generateNFeKey('35', '2401', '12345678000195', '55', '1', '1', '1', '12345678');
```

### Uso com Facade

```php
use Nfephp;

// Usar a facade
if (Nfephp::isCertificateValid()) {
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
│   ├── StringHelper.php      # Helpers para manipulação de strings
│   ├── UfHelper.php          # Helpers para códigos de UF
│   └── ValidationHelper.php  # Helpers para validações
├── Managers/
│   ├── CertificateManager.php # Gerenciamento de certificados
│   └── ContingencyManager.php # Gerenciamento de contingências
├── Nfephp.php               # Classe principal
├── NfephpCore.php           # Classe base
├── NfephpFacade.php         # Facade do Laravel
└── NfephpServiceProvider.php # Service Provider
```

## ⚙️ Configuração Avançada

### Configuração de Contingência

```php
// Configuração automática de contingência
'contingency' => [
    'auto_activate' => true,
    'default_motive' => 'SEFAZ fora do ar',
    'default_type' => 'SVCAN', // SVCAN, SVCRS ou vazio para automático
],
```

### Configuração de Timeouts

```php
'timeout' => [
    'connection' => 30, // Timeout de conexão em segundos
    'read' => 60,       // Timeout de leitura em segundos
],
```

### Configuração de Paths

```php
'paths' => [
    'schemes' => storage_path('app/nfephp/schemes'),
    'nfe' => storage_path('app/nfephp/nfe'),
    'nfce' => storage_path('app/nfephp/nfce'),
    'logs' => storage_path('logs/nfephp'),
    // ... outros paths
],
```

## 🔧 Comandos Artisan

O pacote inclui comandos Artisan para facilitar o desenvolvimento:

```bash
# Listar comandos disponíveis
php artisan list nfephp

# Verificar status do certificado
php artisan nfephp:certificate:status

# Verificar configuração
php artisan nfephp:config:check
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

- [Diogo Graciano Comin](https://github.com/diogo-graciano) - Desenvolvedor principal
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
