import './bootstrap';
// Menu Mobile
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.querySelector('.menu-toggle');
  const sidebar = document.querySelector('.sidebar');

  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('open');
    });

    // Fechar menu ao clicar em um item
    document.querySelectorAll('.sidebar a').forEach(item => {
      item.addEventListener('click', () => {
        if(window.innerWidth <= 768) {
          sidebar.classList.remove('open');
        }
      });
    });
  }

  // PWA Install Prompt
  let deferredPrompt;
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showInstallPromotion();
  });
});

function showInstallPromotion() {
  // Implemente sua lógica para mostrar o botão de instalação
}