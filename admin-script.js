// admin-script.js - Scripts completos do painel administrativo

// Navegação entre seções
document.addEventListener('DOMContentLoaded', function() {
    // Navegação da sidebar
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.admin-section');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active de todos
            navItems.forEach(nav => nav.classList.remove('active'));
            sections.forEach(section => section.classList.remove('active'));
            
            // Adiciona active ao clicado
            this.classList.add('active');
            const sectionId = this.getAttribute('data-section');
            document.getElementById(sectionId).classList.add('active');
            
            // Salva no localStorage
            localStorage.setItem('activeSection', sectionId);
        });
    });
    
    // Restaurar seção ativa
    const savedSection = localStorage.getItem('activeSection');
    if (savedSection) {
        const activeNav = document.querySelector(`[data-section="${savedSection}"]`);
        const activeSection = document.getElementById(savedSection);
        if (activeNav && activeSection) {
            navItems.forEach(nav => nav.classList.remove('active'));
            sections.forEach(section => section.classList.remove('active'));
            activeNav.classList.add('active');
            activeSection.classList.add('active');
        }
    }
});

// Funções para Carousel
function showCarouselForm() {
    const modal = document.getElementById('carouselForm');
    modal.classList.add('active');
    modal.style.display = 'flex';
    document.getElementById('carouselFormTitle').textContent = 'Adicionar Banner';
    document.getElementById('carousel_id').value = '';
    document.getElementById('carousel_titulo').value = '';
    document.getElementById('carousel_texto').value = '';
    document.getElementById('carousel_imagem').value = '';
    document.getElementById('carousel_botao_texto').value = '';
    document.getElementById('carousel_botao_link').value = '';
    document.getElementById('carousel_ordem').value = '1';
    document.getElementById('carouselSubmitBtn').name = 'add_carousel';
    document.getElementById('carouselSubmitBtn').textContent = 'Salvar Banner';
}

function closeCarouselForm() {
    const modal = document.getElementById('carouselForm');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function editCarousel(item) {
    const modal = document.getElementById('carouselForm');
    modal.classList.add('active');
    modal.style.display = 'flex';
    document.getElementById('carouselFormTitle').textContent = 'Editar Banner';
    document.getElementById('carousel_id').value = item.id;
    document.getElementById('carousel_titulo').value = item.titulo;
    document.getElementById('carousel_texto').value = item.texto;
    document.getElementById('carousel_imagem').value = item.imagem;
    document.getElementById('carousel_botao_texto').value = item.botao_texto || '';
    document.getElementById('carousel_botao_link').value = item.botao_link || '';
    document.getElementById('carousel_ordem').value = item.ordem;
    document.getElementById('carouselSubmitBtn').name = 'update_carousel';
    document.getElementById('carouselSubmitBtn').textContent = 'Atualizar Banner';
}

// Funções para Seções
function showSectionForm() {
    const modal = document.getElementById('sectionForm');
    modal.classList.add('active');
    modal.style.display = 'flex';
    document.getElementById('sectionFormTitle').textContent = 'Adicionar Seção';
    document.getElementById('section_id').value = '';
    document.getElementById('section_titulo').value = '';
    document.getElementById('section_slug').value = '';
    document.getElementById('section_conteudo').value = '';
    document.getElementById('section_imagem').value = '';
    document.getElementById('section_ordem').value = '1';
    document.getElementById('sectionSubmitBtn').name = 'add_section';
    document.getElementById('sectionSubmitBtn').textContent = 'Salvar Seção';
}

function closeSectionForm() {
    const modal = document.getElementById('sectionForm');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function editSection(item) {
    const modal = document.getElementById('sectionForm');
    modal.classList.add('active');
    modal.style.display = 'flex';
    document.getElementById('sectionFormTitle').textContent = 'Editar Seção';
    document.getElementById('section_id').value = item.id;
    document.getElementById('section_titulo').value = item.titulo;
    document.getElementById('section_slug').value = item.slug;
    document.getElementById('section_conteudo').value = item.conteudo;
    document.getElementById('section_imagem').value = item.imagem || '';
    document.getElementById('section_ordem').value = item.ordem;
    document.getElementById('sectionSubmitBtn').name = 'update_section';
    document.getElementById('sectionSubmitBtn').textContent = 'Atualizar Seção';
}

// Auto-gerar slug a partir do título
if (document.getElementById('section_titulo')) {
    document.getElementById('section_titulo').addEventListener('input', function(e) {
        const slugInput = document.getElementById('section_slug');
        if (!slugInput.value || slugInput.dataset.auto !== 'false') {
            const slug = e.target.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });
    
    document.getElementById('section_slug').addEventListener('input', function() {
        this.dataset.auto = this.value ? 'false' : 'true';
    });
}

// Funções para Cards de Serviço
function addServiceCard() {
    const container = document.getElementById('serviceCardsContainer');
    const index = container.children.length;
    
    const cardHTML = `
        <div class="editable-card" data-index="${index}">
            <div class="card-header">
                <span class="drag-handle">⋮⋮</span>
                <button type="button" onclick="removeServiceCard(${index})" class="btn-icon btn-danger">🗑️</button>
            </div>
            <div class="form-group">
                <label>Ícone (Emoji)</label>
                <input type="text" class="card-icon" placeholder="🚀">
            </div>
            <div class="form-group">
                <label>Título</label>
                <input type="text" class="card-title" placeholder="Nome do Serviço">
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea class="card-description" rows="3" placeholder="Descrição do serviço"></textarea>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', cardHTML);
}

function removeServiceCard(index) {
    if (confirm('Remover este card?')) {
        const cards = document.querySelectorAll('#serviceCardsContainer .editable-card');
        if (cards.length > 1) {
            cards[index].remove();
            updateCardIndexes();
        } else {
            alert('Você precisa manter pelo menos um card!');
        }
    }
}

function updateCardIndexes() {
    const cards = document.querySelectorAll('#serviceCardsContainer .editable-card');
    cards.forEach((card, index) => {
        card.dataset.index = index;
        const deleteBtn = card.querySelector('.btn-icon');
        deleteBtn.setAttribute('onclick', `removeServiceCard(${index})`);
    });
}

// Salvar cards de serviço
if (document.getElementById('serviceCardsForm')) {
    document.getElementById('serviceCardsForm').addEventListener('submit', function(e) {
        const cards = [];
        const cardElements = document.querySelectorAll('#serviceCardsContainer .editable-card');
        
        cardElements.forEach(card => {
            const icon = card.querySelector('.card-icon').value;
            const title = card.querySelector('.card-title').value;
            const description = card.querySelector('.card-description').value;
            
            if (icon && title && description) {
                cards.push({ icon, title, description });
            }
        });
        
        document.getElementById('cardsData').value = JSON.stringify(cards);
    });
}

// Funções para Estatísticas
function addStat() {
    const container = document.getElementById('statsContainer');
    const index = container.children.length;
    
    const statHTML = `
        <div class="editable-card stat-card" data-index="${index}">
            <div class="card-header">
                <button type="button" onclick="removeStat(${index})" class="btn-icon btn-danger">🗑️</button>
            </div>
            <div class="form-group">
                <label>Número</label>
                <input type="text" class="stat-number" placeholder="10+">
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <input type="text" class="stat-label" placeholder="Anos de Experiência">
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', statHTML);
}

function removeStat(index) {
    if (confirm('Remover esta estatística?')) {
        const stats = document.querySelectorAll('#statsContainer .stat-card');
        if (stats.length > 1) {
            stats[index].remove();
            updateStatIndexes();
        } else {
            alert('Você precisa manter pelo menos uma estatística!');
        }
    }
}

function updateStatIndexes() {
    const stats = document.querySelectorAll('#statsContainer .stat-card');
    stats.forEach((stat, index) => {
        stat.dataset.index = index;
        const deleteBtn = stat.querySelector('.btn-icon');
        deleteBtn.setAttribute('onclick', `removeStat(${index})`);
    });
}

// Salvar estatísticas
if (document.getElementById('statsForm')) {
    document.getElementById('statsForm').addEventListener('submit', function(e) {
        const stats = [];
        const statElements = document.querySelectorAll('#statsContainer .stat-card');
        
        statElements.forEach(stat => {
            const number = stat.querySelector('.stat-number').value;
            const label = stat.querySelector('.stat-label').value;
            
            if (number && label) {
                stats.push({ number, label });
            }
        });
        
        document.getElementById('statsData').value = JSON.stringify(stats);
    });
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const carouselModal = document.getElementById('carouselForm');
    const sectionModal = document.getElementById('sectionForm');
    
    if (event.target === carouselModal) {
        closeCarouselForm();
    }
    if (event.target === sectionModal) {
        closeSectionForm();
    }
}

// Preview de imagem ao fazer upload
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('Imagem carregada:', file.name);
                // Você pode adicionar um preview aqui se desejar
            };
            reader.readAsDataURL(file);
        }
    });
});

// Confirmação antes de deletar
document.querySelectorAll('button[name="delete_carousel"], button[name="delete_section"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Tem certeza que deseja deletar este item?')) {
            e.preventDefault();
        }
    });
});

// Auto-hide alerts após 5 segundos
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 300);
    }, 5000);
});