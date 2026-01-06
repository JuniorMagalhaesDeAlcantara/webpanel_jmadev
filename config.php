<?php
// config.php - Configuração do sistema atualizada
session_start();

// Inicializar variável do banco
$db = null;

// Configuração do banco de dados SQLite
try {
    $dbPath = __DIR__ . '/database.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Criar tabelas se não existirem
    $db->exec("CREATE TABLE IF NOT EXISTS site_config (
        id INTEGER PRIMARY KEY,
        site_title TEXT,
        contato_email TEXT,
        contato_telefone TEXT,
        service_cards TEXT,
        about_stats TEXT
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS carousel_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT,
        texto TEXT,
        imagem TEXT,
        botao_texto TEXT,
        botao_link TEXT,
        ordem INTEGER DEFAULT 0
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS sections (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT,
        slug TEXT,
        conteudo TEXT,
        imagem TEXT,
        ordem INTEGER DEFAULT 0
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT
    )");
    
    // Verificar e adicionar novas colunas se não existirem
    try {
        $db->exec("ALTER TABLE site_config ADD COLUMN service_cards TEXT");
    } catch(PDOException $e) {
        // Coluna já existe
    }
    
    try {
        $db->exec("ALTER TABLE site_config ADD COLUMN about_stats TEXT");
    } catch(PDOException $e) {
        // Coluna já existe
    }
    
    // Verificar se existe configuração inicial
    $stmt = $db->query("SELECT COUNT(*) FROM site_config");
    if ($stmt->fetchColumn() == 0) {
        // Dados padrão dos cards de serviço
        $defaultCards = json_encode([
            ['icon' => '🚀', 'title' => 'Consultoria Estratégica', 'description' => 'Desenvolvemos estratégias personalizadas para impulsionar seu negócio e alcançar resultados extraordinários.'],
            ['icon' => '💡', 'title' => 'Soluções Inovadoras', 'description' => 'Implementamos tecnologias de ponta e metodologias ágeis para otimizar seus processos.'],
            ['icon' => '🎯', 'title' => 'Marketing Digital', 'description' => 'Criamos campanhas estratégicas que conectam sua marca ao público certo no momento ideal.'],
            ['icon' => '⚙️', 'title' => 'Automação de Processos', 'description' => 'Automatizamos tarefas repetitivas para que você foque no que realmente importa.'],
            ['icon' => '📊', 'title' => 'Análise de Dados', 'description' => 'Transformamos dados em insights valiosos para tomadas de decisão mais assertivas.'],
            ['icon' => '🛡️', 'title' => 'Suporte Dedicado', 'description' => 'Equipe especializada disponível 24/7 para garantir o sucesso contínuo do seu projeto.']
        ]);
        
        // Dados padrão das estatísticas
        $defaultStats = json_encode([
            ['number' => '10+', 'label' => 'Anos de Experiência'],
            ['number' => '500+', 'label' => 'Projetos Concluídos'],
            ['number' => '100%', 'label' => 'Satisfação'],
            ['number' => '24/7', 'label' => 'Suporte']
        ]);
        
        // Inserir configuração padrão
        $stmt = $db->prepare("INSERT INTO site_config (id, site_title, contato_email, contato_telefone, service_cards, about_stats) 
                   VALUES (1, 'Minha Empresa', 'contato@minhaempresa.com.br', '(11) 99999-9999', ?, ?)");
        $stmt->execute([$defaultCards, $defaultStats]);
        
        // Inserir itens do carousel padrão
        $db->exec("INSERT INTO carousel_items (titulo, texto, imagem, botao_texto, botao_link, ordem) VALUES 
                   ('Transforme Seu Negócio', 'Soluções inovadoras e estratégias personalizadas para levar sua empresa ao próximo nível', 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&h=600&fit=crop', 'Conhecer Serviços', '#servicos', 1),
                   ('Excelência em Resultados', 'Compromisso, qualidade e inovação em cada projeto que desenvolvemos', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&h=600&fit=crop', 'Fale Conosco', '#contato', 2),
                   ('Seu Sucesso é Nossa Missão', 'Junte-se a centenas de empresas que já alcançaram seus objetivos conosco', 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&h=600&fit=crop', 'Sobre Nós', '#sobre', 3)");
        
        // Inserir seções padrão
        $db->exec("INSERT INTO sections (titulo, slug, conteudo, imagem, ordem) VALUES 
                   ('Sobre Nós', 'sobre', 'Somos uma empresa dedicada a transformar desafios em oportunidades. Com mais de 10 anos de experiência no mercado, combinamos expertise técnica com criatividade para entregar soluções que realmente fazem a diferença. Nossa equipe multidisciplinar trabalha com paixão e comprometimento para garantir que cada projeto supere as expectativas. Acreditamos que o sucesso dos nossos clientes é o nosso maior indicador de qualidade.', 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&h=400&fit=crop', 1),
                   ('Nossos Serviços', 'servicos', 'Oferecemos uma ampla gama de serviços personalizados para atender às necessidades específicas de cada cliente. Nossa equipe está sempre pronta para ajudar seu negócio a crescer com soluções inteligentes e inovadoras.', '', 2),
                   ('Entre em Contato', 'contato', 'Estamos prontos para atender você! Entre em contato conosco através dos nossos canais de comunicação e descubra como podemos ajudar seu negócio a crescer.', '', 3)");
        
        // Criar usuário admin padrão (senha: admin123)
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO admin_users (username, password) VALUES ('admin', '$password')");
    }
    
} catch(PDOException $e) {
    die("❌ Erro na conexão com banco de dados: " . $e->getMessage() . "<br><br>Verifique se o PHP tem permissão de escrita na pasta.");
}

// Função para verificar login
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Função para upload de imagens
function uploadImage($file) {
    $target_dir = "uploads/";
    
    // Criar diretório se não existir
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;
    
    // Verificar se é uma imagem real
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Permitir apenas certos formatos
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return false;
    }
    
    // Limitar tamanho (5MB)
    if ($file["size"] > 5000000) {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    
    return false;
}
?>