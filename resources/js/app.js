import './bootstrap';

// Menu Mobile - Versão mais robusta
document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initPWA();
});

function initMobileMenu() {
  const menuToggle = document.querySelector('.menu-toggle');
  const sidebar = document.querySelector('.sidebar');

  if (!menuToggle || !sidebar) return;

  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', sidebar.classList.contains('open'));
  });

  // Fechar menu ao clicar em links
  document.querySelectorAll('.sidebar a').forEach(item => {
    item.addEventListener('click', () => {
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
      }
    });
  });
}

// PWA Functions - Versão mais completa
function initPWA() {
  let deferredPrompt;
  const installButton = document.createElement('button');
  
  installButton.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    padding: 10px 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    display: none;
  `;
  installButton.textContent = 'Instalar App';
  document.body.appendChild(installButton);

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    installButton.style.display = 'block';
    
    installButton.addEventListener('click', async () => {
      installButton.style.display = 'none';
      deferredPrompt.prompt();
      const { outcome } = await deferredPrompt.userChoice;
      if (outcome === 'accepted') {
        console.log('Usuário aceitou a instalação');
      }
      deferredPrompt = null;
    });
  });

  window.addEventListener('appinstalled', () => {
    installButton.style.display = 'none';
    console.log('PWA foi instalado');
  });
}