/**
 * Kokedama Template Scripts
 */

document.addEventListener('DOMContentLoaded', function () {
  // Back to top button
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 400) {
        backToTop.classList.add('visible');
      } else {
        backToTop.classList.remove('visible');
      }
    });

    backToTop.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Cookie banner
  const cookieBanner = document.getElementById('cookieBanner');
  if (cookieBanner && !localStorage.getItem('kokedama_cookies')) {
    cookieBanner.style.display = 'block';
  }

  // Bootstrap validation
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });

  // Navbar scroll effect
  const navbar = document.querySelector('.navbar-kokedama');
  if (navbar) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  }
});

function acceptCookies() {
  localStorage.setItem('kokedama_cookies', 'accepted');
  document.getElementById('cookieBanner').style.display = 'none';
}

function rejectCookies() {
  localStorage.setItem('kokedama_cookies', 'rejected');
  document.getElementById('cookieBanner').style.display = 'none';
}
