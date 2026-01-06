<?php
// admin.php - Painel administrativo completo e moderno
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
                <div class="login-logo">🚀</div>
                <h2>Painel Administrativo</h2>
                <p class="login-subtitle">Gerencie todo o conteúdo do seu site</p>
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
                    <button type="submit" name="login" class="btn btn-primary btn-block">Entrar</button>
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
        $success = "✅ Configurações atualizadas com sucesso!";
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
        $success = "✅ Banner adicionado com sucesso!";
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
        $success = "✅ Banner atualizado com sucesso!";
    }

    // Deletar item do carousel
    if (isset($_POST['delete_carousel'])) {
        $stmt = $db->prepare("DELETE FROM carousel_items WHERE id = ?");
        $stmt->execute([$_POST['carousel_id']]);
        $success = "✅ Banner deletado com sucesso!";
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
        $success = "✅ Seção adicionada com sucesso!";
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
        $success = "✅ Seção atualizada com sucesso!";
    }

    // Deletar seção
    if (isset($_POST['delete_section'])) {
        $stmt = $db->prepare("DELETE FROM sections WHERE id = ?");
        $stmt->execute([$_POST['section_id']]);
        $success = "✅ Seção deletada com sucesso!";
    }

    // Gerenciar cards de serviço
    if (isset($_POST['save_service_cards'])) {
        $cards = json_decode($_POST['cards_data'], true);
        $stmt = $db->prepare("UPDATE site_config SET service_cards = ? WHERE id = 1");
        $stmt->execute([json_encode($cards)]);
        $success = "✅ Cards de serviço atualizados!";
    }

    // Gerenciar estatísticas
    if (isset($_POST['save_stats'])) {
        $stats = json_decode($_POST['stats_data'], true);
        $stmt = $db->prepare("UPDATE site_config SET about_stats = ? WHERE id = 1");
        $stmt->execute([json_encode($stats)]);
        $success = "✅ Estatísticas atualizadas!";
    }
}

// Buscar dados atuais
$config = $db->query("SELECT * FROM site_config WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$carousel = $db->query("SELECT * FROM carousel_items ORDER BY ordem")->fetchAll(PDO::FETCH_ASSOC);
$sections = $db->query("SELECT * FROM sections ORDER BY ordem")->fetchAll(PDO::FETCH_ASSOC);

// Dados dos cards de serviço (se existirem no banco)
$serviceCards = isset($config['service_cards']) ? json_decode($config['service_cards'], true) : [
    ['icon' => '🚀', 'title' => 'Consultoria Estratégica', 'description' => 'Desenvolvemos estratégias personalizadas para impulsionar seu negócio.'],
    ['icon' => '💡', 'title' => 'Soluções Inovadoras', 'description' => 'Implementamos tecnologias de ponta e metodologias ágeis.'],
    ['icon' => '🎯', 'title' => 'Marketing Digital', 'description' => 'Criamos campanhas estratégicas que conectam sua marca.'],
    ['icon' => '⚙️', 'title' => 'Automação de Processos', 'description' => 'Automatizamos tarefas repetitivas.'],
    ['icon' => '📊', 'title' => 'Análise de Dados', 'description' => 'Transformamos dados em insights valiosos.'],
    ['icon' => '🛡️', 'title' => 'Suporte Dedicado', 'description' => 'Equipe especializada disponível 24/7.']
];

// Dados das estatísticas
$aboutStats = isset($config['about_stats']) ? json_decode($config['about_stats'], true) : [
    ['number' => '10+', 'label' => 'Anos de Experiência'],
    ['number' => '500+', 'label' => 'Projetos Concluídos'],
    ['number' => '100%', 'label' => 'Satisfação'],
    ['number' => '24/7', 'label' => 'Suporte']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Gerenciamento Completo</title>
    <link rel="stylesheet" href="admin-style.css">
</head>

<body>
    <!-- Header do Admin -->
    <div class="admin-header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <div class="admin-logo">🚀</div>
                    <div>
                        <h1>Painel Administrativo</h1>
                        <p class="header-subtitle">Gerenciamento completo do site</p>
                    </div>
                </div>
                <div class="admin-nav">
                    <a href="index.php" target="_blank" class="btn btn-secondary">
                        <span>👁️</span> Ver Site
                    </a>
                    <a href="?logout" class="btn btn-danger">
                        <span>🚪</span> Sair
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar de Navegação -->
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <nav class="sidebar-nav">
                <a href="#config" class="nav-item active" data-section="config">
                    <span class="nav-icon">⚙️</span>
                    <span>Configurações Gerais</span>
                </a>
                <a href="#carousel" class="nav-item" data-section="carousel">
                    <span class="nav-icon">🎠</span>
                    <span>Carousel/Banners</span>
                </a>
                <a href="#sections" class="nav-item" data-section="sections">
                    <span class="nav-icon">📄</span>
                    <span>Seções</span>
                </a>
                <a href="#services" class="nav-item" data-section="services">
                    <span class="nav-icon">💼</span>
                    <span>Cards de Serviços</span>
                </a>
                <a href="#stats" class="nav-item" data-section="stats">
                    <span class="nav-icon">📊</span>
                    <span>Cards da Seção Sobre</span>
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-container">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <!-- Configurações Gerais -->
                <section id="config" class="admin-section active">
                    <div class="section-header">
                        <div>
                            <h2>⚙️ Configurações Gerais</h2>
                            <p class="section-description">Configure as informações básicas do site</p>
                        </div>
                    </div>
                    <div class="card">
                        <form method="POST" class="admin-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nome do Site</label>
                                    <input type="text" name="site_title" value="<?= htmlspecialchars($config['site_title']) ?>" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>E-mail de Contato</label>
                                    <input type="email" name="contato_email" value="<?= htmlspecialchars($config['contato_email']) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Telefone de Contato</label>
                                    <input type="text" name="contato_telefone" value="<?= htmlspecialchars($config['contato_telefone']) ?>">
                                </div>
                            </div>
                            <button type="submit" name="update_config" class="btn btn-primary">
                                <span>💾</span> Salvar Configurações
                            </button>
                        </form>
                    </div>
                </section>

                <!-- Carousel -->
                <section id="carousel" class="admin-section">
                    <div class="section-header">
                        <div>
                            <h2>🎠 Carousel / Banners</h2>
                            <p class="section-description">Gerencie os banners principais do site</p>
                        </div>
                        <button onclick="showCarouselForm()" class="btn btn-success">
                            <span>➕</span> Novo Banner
                        </button>
                    </div>

                    <div class="items-grid">
                        <?php foreach ($carousel as $item): ?>
                            <div class="item-card">
                                <div class="item-image">
                                    <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="Banner">
                                    <div class="item-order">Ordem: <?= $item['ordem'] ?></div>
                                </div>
                                <div class="item-content">
                                    <h3><?= htmlspecialchars($item['titulo']) ?></h3>
                                    <p><?= htmlspecialchars(substr($item['texto'], 0, 80)) ?>...</p>
                                </div>
                                <div class="item-actions">
                                    <button onclick="editCarousel(<?= htmlspecialchars(json_encode($item)) ?>)" class="btn btn-small btn-primary">
                                        ✏️ Editar
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="carousel_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="delete_carousel" class="btn btn-small btn-danger" onclick="return confirm('Deletar este banner?')">
                                            🗑️ Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Seções -->
                <section id="sections" class="admin-section">
                    <div class="section-header">
                        <div>
                            <h2>📄 Seções do Site</h2>
                            <p class="section-description">Adicione e edite seções de conteúdo</p>
                        </div>
                        <button onclick="showSectionForm()" class="btn btn-success">
                            <span>➕</span> Nova Seção
                        </button>
                    </div>

                    <div class="items-grid">
                        <?php foreach ($sections as $section): ?>
                            <div class="item-card">
                                <?php if ($section['imagem']): ?>
                                    <div class="item-image">
                                        <img src="<?= htmlspecialchars($section['imagem']) ?>" alt="Seção">
                                        <div class="item-order">Ordem: <?= $section['ordem'] ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="item-content">
                                    <h3><?= htmlspecialchars($section['titulo']) ?></h3>
                                    <p class="item-meta">🔗 Slug: <?= htmlspecialchars($section['slug']) ?></p>
                                    <p><?= htmlspecialchars(substr($section['conteudo'], 0, 100)) ?>...</p>
                                </div>
                                <div class="item-actions">
                                    <button onclick="editSection(<?= htmlspecialchars(json_encode($section)) ?>)" class="btn btn-small btn-primary">
                                        ✏️ Editar
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                                        <button type="submit" name="delete_section" class="btn btn-small btn-danger" onclick="return confirm('Deletar esta seção?')">
                                            🗑️ Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Cards de Serviços -->
                <section id="services" class="admin-section">
                    <div class="section-header">
                        <div>
                            <h2>💼 Cards de Serviços</h2>
                            <p class="section-description">Customize os cards exibidos na seção de serviços</p>
                        </div>
                        <button onclick="addServiceCard()" class="btn btn-success">
                            <span>➕</span> Adicionar Card
                        </button>
                    </div>

                    <div id="serviceCardsContainer" class="cards-manager">
                        <?php foreach ($serviceCards as $index => $card): ?>
                            <div class="editable-card" data-index="<?= $index ?>">
                                <div class="card-header">
                                    <span class="drag-handle">⋮⋮</span>
                                    <button type="button" onclick="removeServiceCard(this)" class="btn-icon btn-danger">🗑️</button>
                                </div>
                                <div class="form-group">
                                    <label>Ícone (Emoji)</label>
                                    <input type="text" class="card-icon" value="<?= htmlspecialchars($card['icon']) ?>" placeholder="🚀">
                                </div>
                                <div class="form-group">
                                    <label>Título</label>
                                    <input type="text" class="card-title" value="<?= htmlspecialchars($card['title']) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <textarea class="card-description" rows="3"><?= htmlspecialchars($card['description']) ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form method="POST" id="serviceCardsForm">
                        <input type="hidden" name="cards_data" id="cardsData">
                        <button type="submit" name="save_service_cards" class="btn btn-primary btn-large">
                            <span>💾</span> Salvar Todos os Cards
                        </button>
                    </form>
                </section>

                <!-- Estatísticas -->
                <section id="stats" class="admin-section">
                    <div class="section-header">
                        <div>
                            <h2>📊 Cards da Seção Sobre</h2>
                            <p class="section-description">Edite os números e estatísticas exibidas</p>
                        </div>
                        <button onclick="addStat()" class="btn btn-success">
                            <span>➕</span> Adicionar Estatística
                        </button>
                    </div>

                    <div id="statsContainer" class="cards-manager stats-grid">
                        <?php foreach ($aboutStats as $index => $stat): ?>
                            <div class="editable-card stat-card" data-index="<?= $index ?>">
                                <div class="card-header">
                                    <button type="button" onclick="removeStat(this)" class="btn-icon btn-danger">🗑️</button>
                                </div>
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" class="stat-number" value="<?= htmlspecialchars($stat['number']) ?>" placeholder="10+">
                                </div>
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <input type="text" class="stat-label" value="<?= htmlspecialchars($stat['label']) ?>" placeholder="Anos de Experiência">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form method="POST" id="statsForm">
                        <input type="hidden" name="stats_data" id="statsData">
                        <button type="submit" name="save_stats" class="btn btn-primary btn-large">
                            <span>💾</span> Salvar Todos os Cards
                        </button>
                    </form>
                </section>

            </div>
        </main>
    </div>

    <!-- Modal Carousel -->
    <div id="carouselForm" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3 id="carouselFormTitle">Adicionar Banner</h3>
                <span class="close" onclick="closeCarouselForm()">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="carousel_id" id="carousel_id">

                <div class="form-group">
                    <label>Título do Banner</label>
                    <input type="text" name="carousel_titulo" id="carousel_titulo" required placeholder="Ex: Transforme Seu Negócio">
                </div>

                <div class="form-group">
                    <label>Texto Descritivo</label>
                    <textarea name="carousel_texto" id="carousel_texto" rows="3" placeholder="Texto que aparecerá abaixo do título"></textarea>
                </div>

                <div class="form-group">
                    <label>Imagem do Banner</label>
                    <input type="text" name="carousel_imagem" id="carousel_imagem" placeholder="https://exemplo.com/imagem.jpg">
                    <small>💡 Ou faça upload de uma imagem:</small>
                    <input type="file" name="carousel_imagem_file" accept="image/*">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Texto do Botão (opcional)</label>
                        <input type="text" name="carousel_botao_texto" id="carousel_botao_texto" placeholder="Ex: Saiba Mais">
                    </div>
                    <div class="form-group">
                        <label>Link do Botão</label>
                        <input type="text" name="carousel_botao_link" id="carousel_botao_link" placeholder="#servicos">
                    </div>
                </div>

                <div class="form-group">
                    <label>Ordem de Exibição</label>
                    <input type="number" name="carousel_ordem" id="carousel_ordem" value="1" min="1">
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeCarouselForm()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" name="add_carousel" id="carouselSubmitBtn" class="btn btn-primary">Salvar Banner</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Seção -->
    <div id="sectionForm" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3 id="sectionFormTitle">Adicionar Seção</h3>
                <span class="close" onclick="closeSectionForm()">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="section_id" id="section_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Título da Seção</label>
                        <input type="text" name="section_titulo" id="section_titulo" required placeholder="Ex: Sobre Nós">
                    </div>
                    <div class="form-group">
                        <label>Slug (ID único)</label>
                        <input type="text" name="section_slug" id="section_slug" placeholder="sobre-nos" required>
                        <small>💡 Usado para criar âncoras de navegação (#sobre-nos)</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Conteúdo</label>
                    <textarea name="section_conteudo" id="section_conteudo" rows="6" placeholder="Escreva o conteúdo da seção..."></textarea>
                </div>

                <div class="form-group">
                    <label>Imagem (opcional)</label>
                    <input type="text" name="section_imagem" id="section_imagem" placeholder="https://exemplo.com/imagem.jpg">
                    <small>💡 Ou faça upload de uma imagem:</small>
                    <input type="file" name="section_imagem_file" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Ordem de Exibição</label>
                    <input type="number" name="section_ordem" id="section_ordem" value="1" min="1">
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeSectionForm()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" name="add_section" id="sectionSubmitBtn" class="btn btn-primary">Salvar Seção</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin-script.js"></script>
</body>

</html>