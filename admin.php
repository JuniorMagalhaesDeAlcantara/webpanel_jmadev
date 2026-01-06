<?php
// admin.php - Painel administrativo
require_once 'config.php';

// Processar login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Usuário ou senha incorretos";
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Se não estiver logado, mostrar formulário de login
if (!isLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Painel Admin</title>
        <link rel="stylesheet" href="admin-style.css">
    </head>
    <body>
        <div class="login-container">
            <div class="login-box">
                <h2>Painel Administrativo</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Usuário</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Entrar</button>
                </form>
                <p class="login-info">Usuário padrão: admin | Senha: admin123</p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Processar ações do painel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Atualizar configurações gerais
    if (isset($_POST['update_config'])) {
        $stmt = $db->prepare("UPDATE site_config SET site_title = ?, contato_email = ?, contato_telefone = ? WHERE id = 1");
        $stmt->execute([$_POST['site_title'], $_POST['contato_email'], $_POST['contato_telefone']]);
        $success = "Configurações atualizadas!";
    }
    
    // Adicionar item do carousel
    if (isset($_POST['add_carousel'])) {
        $imagem = $_POST['carousel_imagem'];
        if (!empty($_FILES['carousel_imagem_file']['name'])) {
            $uploaded = uploadImage($_FILES['carousel_imagem_file']);
            if ($uploaded) $imagem = $uploaded;
        }
        
        $stmt = $db->prepare("INSERT INTO carousel_items (titulo, texto, imagem, botao_texto, botao_link, ordem) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['carousel_titulo'], $_POST['carousel_texto'], $imagem, $_POST['carousel_botao_texto'], $_POST['carousel_botao_link'], $_POST['carousel_ordem']]);
        $success = "Item do carousel adicionado!";
    }
    
    // Atualizar item do carousel
    if (isset($_POST['update_carousel'])) {
        $imagem = $_POST['carousel_imagem'];
        if (!empty($_FILES['carousel_imagem_file']['name'])) {
            $uploaded = uploadImage($_FILES['carousel_imagem_file']);
            if ($uploaded) $imagem = $uploaded;
        }
        
        $stmt = $db->prepare("UPDATE carousel_items SET titulo = ?, texto = ?, imagem = ?, botao_texto = ?, botao_link = ?, ordem = ? WHERE id = ?");
        $stmt->execute([$_POST['carousel_titulo'], $_POST['carousel_texto'], $imagem, $_POST['carousel_botao_texto'], $_POST['carousel_botao_link'], $_POST['carousel_ordem'], $_POST['carousel_id']]);
        $success = "Item do carousel atualizado!";
    }
    
    // Deletar item do carousel
    if (isset($_POST['delete_carousel'])) {
        $stmt = $db->prepare("DELETE FROM carousel_items WHERE id = ?");
        $stmt->execute([$_POST['carousel_id']]);
        $success = "Item do carousel deletado!";
    }
    
    // Adicionar seção
    if (isset($_POST['add_section'])) {
        $imagem = $_POST['section_imagem'];
        if (!empty($_FILES['section_imagem_file']['name'])) {
            $uploaded = uploadImage($_FILES['section_imagem_file']);
            if ($uploaded) $imagem = $uploaded;
        }
        
        $stmt = $db->prepare("INSERT INTO sections (titulo, slug, conteudo, imagem, ordem) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['section_titulo'], $_POST['section_slug'], $_POST['section_conteudo'], $imagem, $_POST['section_ordem']]);
        $success = "Seção adicionada!";
    }
    
    // Atualizar seção
    if (isset($_POST['update_section'])) {
        $imagem = $_POST['section_imagem'];
        if (!empty($_FILES['section_imagem_file']['name'])) {
            $uploaded = uploadImage($_FILES['section_imagem_file']);
            if ($uploaded) $imagem = $uploaded;
        }
        
        $stmt = $db->prepare("UPDATE sections SET titulo = ?, slug = ?, conteudo = ?, imagem = ?, ordem = ? WHERE id = ?");
        $stmt->execute([$_POST['section_titulo'], $_POST['section_slug'], $_POST['section_conteudo'], $imagem, $_POST['section_ordem'], $_POST['section_id']]);
        $success = "Seção atualizada!";
    }
    
    // Deletar seção
    if (isset($_POST['delete_section'])) {
        $stmt = $db->prepare("DELETE FROM sections WHERE id = ?");
        $stmt->execute([$_POST['section_id']]);
        $success = "Seção deletada!";
    }
}

// Buscar dados atuais
$config = $db->query("SELECT * FROM site_config WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$carousel = $db->query("SELECT * FROM carousel_items ORDER BY ordem")->fetchAll(PDO::FETCH_ASSOC);
$sections = $db->query("SELECT * FROM sections ORDER BY ordem")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <h1>Painel Administrativo</h1>
            <div class="admin-nav">
                <a href="index.php" target="_blank" class="btn btn-secondary">Ver Site</a>
                <a href="?logout" class="btn btn-danger">Sair</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Configurações Gerais -->
        <div class="admin-section">
            <h2>Configurações Gerais</h2>
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label>Nome do Site</label>
                    <input type="text" name="site_title" value="<?= htmlspecialchars($config['site_title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>E-mail de Contato</label>
                    <input type="email" name="contato_email" value="<?= htmlspecialchars($config['contato_email']) ?>">
                </div>
                <div class="form-group">
                    <label>Telefone de Contato</label>
                    <input type="text" name="contato_telefone" value="<?= htmlspecialchars($config['contato_telefone']) ?>">
                </div>
                <button type="submit" name="update_config" class="btn btn-primary">Salvar Configurações</button>
            </form>
        </div>

        <!-- Carousel -->
        <div class="admin-section">
            <h2>Carousel (Banners)</h2>
            <div class="items-list">
                <?php foreach ($carousel as $item): ?>
                <div class="item-card">
                    <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="Banner" class="item-preview">
                    <h3><?= htmlspecialchars($item['titulo']) ?></h3>
                    <button onclick="editCarousel(<?= htmlspecialchars(json_encode($item)) ?>)" class="btn btn-small">Editar</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="carousel_id" value="<?= $item['id'] ?>">
                        <button type="submit" name="delete_carousel" class="btn btn-small btn-danger" onclick="return confirm('Deletar este item?')">Deletar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <button onclick="showCarouselForm()" class="btn btn-success">Adicionar Novo Banner</button>
            
            <div id="carouselForm" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeCarouselForm()">&times;</span>
                    <h3 id="carouselFormTitle">Adicionar Banner</h3>
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <input type="hidden" name="carousel_id" id="carousel_id">
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="carousel_titulo" id="carousel_titulo" required>
                        </div>
                        <div class="form-group">
                            <label>Texto</label>
                            <textarea name="carousel_texto" id="carousel_texto" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>URL da Imagem</label>
                            <input type="text" name="carousel_imagem" id="carousel_imagem" placeholder="https://...">
                            <small>Ou faça upload de uma imagem:</small>
                            <input type="file" name="carousel_imagem_file" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Texto do Botão</label>
                            <input type="text" name="carousel_botao_texto" id="carousel_botao_texto">
                        </div>
                        <div class="form-group">
                            <label>Link do Botão</label>
                            <input type="text" name="carousel_botao_link" id="carousel_botao_link" placeholder="#secao">
                        </div>
                        <div class="form-group">
                            <label>Ordem</label>
                            <input type="number" name="carousel_ordem" id="carousel_ordem" value="1" min="1">
                        </div>
                        <button type="submit" name="add_carousel" id="carouselSubmitBtn" class="btn btn-primary">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Seções -->
        <div class="admin-section">
            <h2>Seções do Site</h2>
            <div class="items-list">
                <?php foreach ($sections as $section): ?>
                <div class="item-card">
                    <?php if ($section['imagem']): ?>
                    <img src="<?= htmlspecialchars($section['imagem']) ?>" alt="Seção" class="item-preview">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($section['titulo']) ?></h3>
                    <p class="item-meta">Slug: <?= htmlspecialchars($section['slug']) ?></p>
                    <button onclick="editSection(<?= htmlspecialchars(json_encode($section)) ?>)" class="btn btn-small">Editar</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                        <button type="submit" name="delete_section" class="btn btn-small btn-danger" onclick="return confirm('Deletar esta seção?')">Deletar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <button onclick="showSectionForm()" class="btn btn-success">Adicionar Nova Seção</button>
            
            <div id="sectionForm" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeSectionForm()">&times;</span>
                    <h3 id="sectionFormTitle">Adicionar Seção</h3>
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <input type="hidden" name="section_id" id="section_id">
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="section_titulo" id="section_titulo" required>
                        </div>
                        <div class="form-group">
                            <label>Slug (ID para âncora)</label>
                            <input type="text" name="section_slug" id="section_slug" placeholder="sobre-nos" required>
                        </div>
                        <div class="form-group">
                            <label>Conteúdo</label>
                            <textarea name="section_conteudo" id="section_conteudo" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label>URL da Imagem (opcional)</label>
                            <input type="text" name="section_imagem" id="section_imagem" placeholder="https://...">
                            <small>Ou faça upload de uma imagem:</small>
                            <input type="file" name="section_imagem_file" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Ordem</label>
                            <input type="number" name="section_ordem" id="section_ordem" value="1" min="1">
                        </div>
                        <button type="submit" name="add_section" id="sectionSubmitBtn" class="btn btn-primary">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="admin-script.js"></script>
</body>
</html>