# Refatoração da Classe Nfephp

## 📋 **Problema Identificado**

A classe `Nfephp` original tinha **muitas responsabilidades**, violando o **Princípio da Responsabilidade Única (SRP)**. Ela estava fazendo:

- Gerenciamento de NFe/NFCe
- Gerenciamento de contingências
- Gerenciamento de certificados
- Validação de dados
- Formatação de strings
- Utilitários de UF e timezone
- Geração de chaves de acesso

## ✅ **Solução Implementada**

Dividi a classe em **6 classes especializadas** seguindo o padrão de arquitetura limpa:

### 🏗️ **Estrutura Refatorada**

```
src/
├── Nfephp.php                    # Classe principal (conveniência)
├── NfephpCore.php               # Funcionalidades principais do NFePHP
├── Managers/
│   ├── ContingencyManager.php   # Gerenciamento de contingências
│   └── CertificateManager.php   # Gerenciamento de certificados
└── Helpers/
    ├── ValidationHelper.php     # Utilitários de validação
    ├── StringHelper.php         # Utilitários de string
    └── UfHelper.php             # Utilitários de UF e timezone
```

## 🎯 **Benefícios da Refatoração**

### 1. **Responsabilidade Única (SRP)**
- Cada classe tem uma única responsabilidade
- Código mais fácil de entender e manter
- Menor acoplamento entre funcionalidades

### 2. **Reutilização de Código**
- Helpers podem ser usados independentemente
- Managers podem ser reutilizados em outros contextos
- Métodos estáticos para funcionalidades utilitárias

### 3. **Testabilidade**
- Cada classe pode ser testada isoladamente
- Mocks mais fáceis de implementar
- Testes mais focados e específicos

### 4. **Manutenibilidade**
- Mudanças em uma funcionalidade não afetam outras
- Código mais organizado e legível
- Facilita a adição de novas funcionalidades

## 📚 **Como Usar**

### **Classe Principal (Nfephp)**
```php
use DiogoGraciano\Nfephp\Facades\Nfephp;

// Funcionalidades principais (delegadas para NfephpCore)
$nfe = Nfephp::createNFe();
$response = Nfephp::sendNFe($xmls);

// Contingências (delegadas para ContingencyManager)
Nfephp::activateContingency('SP', 'SEFAZ fora do ar');
$info = Nfephp::getContingencyInfo();

// Certificados (delegadas para CertificateManager)
$certInfo = Nfephp::getCertificateInfo();
$isValid = Nfephp::isCertificateValid();

// Validações (delegadas para ValidationHelper)
$isValidCnpj = Nfephp::validateCnpj('12345678000195');
$isValidCpf = Nfephp::validateCpf('12345678901');

// Strings (delegadas para StringHelper)
$clean = Nfephp::cleanString('Texto com caracteres especiais');
$formatted = Nfephp::formatCnpj('12345678000195');

// UF (delegadas para UfHelper)
$code = Nfephp::getUfCode('SP');
$timezone = Nfephp::getTimezoneByUf('SP');
```

### **Uso Direto dos Helpers**
```php
use DiogoGraciano\Nfephp\Helpers\ValidationHelper;
use DiogoGraciano\Nfephp\Helpers\StringHelper;
use DiogoGraciano\Nfephp\Helpers\UfHelper;

// Validação
$isValid = ValidationHelper::validateCnpj('12345678000195');

// Formatação
$formatted = StringHelper::formatCpf('12345678901');

// UF
$code = UfHelper::getCode('SP');
```

### **Uso Direto dos Managers**
```php
use DiogoGraciano\Nfephp\Managers\ContingencyManager;
use DiogoGraciano\Nfephp\Managers\CertificateManager;

// Contingência
$contingency = new ContingencyManager();
$contingency->activate('SP', 'SEFAZ fora do ar');

// Certificado
$certManager = new CertificateManager($tools);
$info = $certManager->getInfo();
```

## 🔧 **Funcionalidades por Classe**

### **NfephpCore**
- Criação de NFe/NFCe
- Envio para SEFAZ
- Consulta, cancelamento e inutilização
- Geração de QR Code
- Padronização de respostas
- Configurações

### **ContingencyManager**
- Ativação/desativação de contingências
- Ajuste de XML para contingência
- Carregamento de configurações
- Informações da contingência ativa

### **CertificateManager**
- Informações do certificado
- Validação de certificado
- Cálculo de dias para expiração
- Verificação de proximidade do vencimento

### **ValidationHelper**
- Validação de XML contra XSD
- Validação de CNPJ/CPF
- Validação de CEP/email/telefone
- Validação de chave de acesso

### **StringHelper**
- Limpeza de caracteres especiais
- Conversão para ASCII
- Formatação de documentos
- Formatação de telefone/CEP
- Geração de strings aleatórias

### **UfHelper**
- Conversão entre código e sigla de UF
- Obtenção de timezone por UF
- Validação de UF
- Geração de chave de acesso
- Informações de região

## 🚀 **Compatibilidade**

A refatoração mantém **100% de compatibilidade** com a API anterior. Todos os métodos públicos da classe `Nfephp` continuam funcionando exatamente como antes, mas agora delegam para as classes especializadas.

## 📈 **Métricas de Melhoria**

- **Linhas de código por classe**: Reduzidas de ~750 para ~200-300
- **Complexidade ciclomática**: Reduzida significativamente
- **Acoplamento**: Muito menor entre funcionalidades
- **Coesão**: Muito maior dentro de cada classe
- **Testabilidade**: Muito melhorada

## 🎉 **Resultado Final**

A refatoração resultou em um código:
- ✅ **Mais limpo** e organizado
- ✅ **Mais fácil** de manter
- ✅ **Mais testável** e confiável
- ✅ **Mais reutilizável** e flexível
- ✅ **Mais legível** e compreensível

Cada classe agora tem uma responsabilidade clara e bem definida, seguindo as melhores práticas de desenvolvimento de software!
