# 🚀 Sistema de Gerenciamento de Site Completo

## 📋 Visão Geral

Sistema completo de gerenciamento de site com painel administrativo moderno que permite controlar **ABSOLUTAMENTE TUDO** do site, incluindo:

- ✅ Configurações gerais (nome, email, telefone)
- ✅ Banners do carousel com imagens, textos e botões
- ✅ Seções de conteúdo personalizadas
- ✅ **Cards de serviços** (adicione quantos quiser!)
- ✅ **Estatísticas dinâmicas** (personalize números e labels)
- ✅ Upload de imagens
- ✅ Interface moderna e intuitiva
- ✅ Design responsivo

## 🎯 Recursos Principais

### 1. Painel Administrativo Moderno
- Interface intuitiva com navegação lateral
- Design responsivo para todos os dispositivos
- Feedback visual em todas as ações
- Modais elegantes para edição

### 2. Gerenciamento Completo
- **Carousel/Banners**: Adicione slides ilimitados com imagens, títulos, textos e botões
- **Seções**: Crie seções customizadas com slugs para navegação
- **Cards de Serviço**: Adicione/remova/edite cards com ícones, títulos e descrições
- **Estatísticas**: Personalize os números e labels da seção "Sobre"

### 3. Sistema de Upload
- Upload direto de imagens através do painel
- Suporte para JPG, PNG, GIF
- Limite de 5MB por arquivo

## 📦 Arquivos do Sistema

```
projeto/
│
├── index.php              # Página principal (100% dinâmica)
├── admin.php              # Painel administrativo completo
├── config.php             # Configurações e conexão com BD
├── style.css              # Estilos do site
├── admin-style.css        # Estilos do painel admin
├── script.js              # Scripts do site
├── admin-script.js        # Scripts do painel admin
├── database.db            # Banco de dados SQLite (criado automaticamente)
└── uploads/               # Pasta para imagens (criada automaticamente)
```

## 🔧 Instalação

### Requisitos
- PHP 7.4 ou superior
- SQLite habilitado
- Permissões de escrita na pasta do projeto

### Passo a Passo

1. **Faça upload dos arquivos** para seu servidor

2. **Certifique-se que a pasta tem permissão de escrita:**
```bash
chmod 755 /caminho/do/projeto
```

3. **Acesse o site:**
```
http://seudominio.com/
```

4. **Acesse o painel administrativo:**
```
http://seudominio.com/admin.php
```

5. **Login padrão:**
- **Usuário:** admin
- **Senha:** admin123

⚠️ **IMPORTANTE:** Altere a senha padrão após o primeiro acesso!

## 🎨 Como Usar o Painel Admin

### 1. Configurações Gerais
- Altere o nome do site
- Configure email e telefone de contato
- Clique em "Salvar Configurações"

### 2. Gerenciar Carousel
1. Clique em "Carousel/Banners" no menu lateral
2. Clique em "➕ Novo Banner"
3. Preencha:
   - Título do banner
   - Texto descritivo
   - URL da imagem OU faça upload
   - Texto e link do botão (opcional)
   - Ordem de exibição
4. Clique em "Salvar Banner"

### 3. Gerenciar Seções
1. Clique em "Seções" no menu lateral
2. Clique em "➕ Nova Seção"
3. Preencha:
   - Título da seção
   - Slug (ID único para âncoras)
   - Conteúdo
   - Imagem (opcional)
   - Ordem de exibição
4. Clique em "Salvar Seção"

### 4. Personalizar Cards de Serviços
1. Clique em "Cards de Serviços" no menu lateral
2. **Adicionar novo card:**
   - Clique em "➕ Adicionar Card"
   - Escolha um emoji como ícone
   - Digite título e descrição
3. **Editar card existente:**
   - Modifique diretamente os campos
4. **Remover card:**
   - Clique no ícone 🗑️
5. Clique em "💾 Salvar Todos os Cards"

### 5. Personalizar Estatísticas
1. Clique em "Estatísticas" no menu lateral
2. **Adicionar nova estatística:**
   - Clique em "➕ Adicionar Estatística"
   - Digite o número (ex: "10+", "500+")
   - Digite a descrição
3. **Remover estatística:**
   - Clique no ícone 🗑️
4. Clique em "💾 Salvar Estatísticas"

## 💡 Dicas e Truques

### Ícones para Cards de Serviços
Use emojis para criar ícones visuais atraentes:
- 🚀 Inovação
- 💡 Ideias
- 🎯 Objetivos
- ⚙️ Processos
- 📊 Análise
- 🛡️ Segurança
- 💼 Negócios
- 🌟 Qualidade
- 🔧 Ferramentas
- 📱 Tecnologia

### Imagens Recomendadas
- **Carousel:** 1200x600px
- **Seções:** 600x400px
- Formatos: JPG, PNG
- Peso máximo: 5MB

### Fontes de Imagens Gratuitas
- [Unsplash](https://unsplash.com)
- [Pexels](https://pexels.com)
- [Pixabay](https://pixabay.com)

## 🔐 Segurança

### Alterar Senha do Admin

Edite o arquivo `config.php` e adicione este código após a linha que cria o usuário admin:

```php
// Alterar senha do admin
$nova_senha = password_hash('SUANOVASENH@123', PASSWORD_DEFAULT);
$db->exec("UPDATE admin_users SET password = '$nova_senha' WHERE username = 'admin'");
```

### Proteção Adicional
1. Renomeie o arquivo `admin.php` para algo único
2. Use .htaccess para proteger o painel admin
3. Faça backups regulares do database.db

## 🐛 Solução de Problemas

### Erro: "Banco de dados não inicializado"
- Verifique permissões de escrita na pasta
- Execute: `chmod 755 /caminho/do/projeto`

### Imagens não aparecem
- Verifique se a pasta `uploads/` existe
- Verifique permissões: `chmod 755 uploads/`

### Página em branco
- Ative exibição de erros no PHP
- Verifique logs do servidor

### Não consigo fazer login
- Usuário padrão: `admin`
- Senha padrão: `admin123`
- Verifique se o banco de dados foi criado

## 📱 Responsividade

O site é 100% responsivo e funciona perfeitamente em:
- 📱 Celulares
- 📱 Tablets
- 💻 Notebooks
- 🖥️ Desktops

## 🎨 Personalização Avançada

### Alterar Cores do Site
Edite o arquivo `style.css` na seção `:root`:

```css
:root {
    --primary: #6366f1;        /* Cor primária */
    --secondary: #ec4899;      /* Cor secundária */
    --success: #10b981;        /* Cor de sucesso */
    --dark: #1e293b;          /* Cor escura */
}
```

### Alterar Cores do Admin
Edite o arquivo `admin-style.css` na seção `:root`

## 📈 Próximos Passos

Depois de configurar tudo, você pode:
1. Adicionar Google Analytics
2. Configurar SEO (meta tags)
3. Adicionar formulário de contato
4. Integrar com redes sociais
5. Adicionar chat online

## 💬 Suporte

Para suporte adicional:
1. Verifique a documentação acima
2. Revise os comentários no código
3. Teste em ambiente local primeiro

## 📄 Licença

Este sistema é fornecido como está, sem garantias. Você pode modificá-lo livremente para suas necessidades.

---

**Desenvolvido com ❤️ para facilitar sua vida!**

**Versão:** 2.0
**Última atualização:** Janeiro 2026