<!-- Core JS -->
<script src="{{ asset('modern-assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('modern-assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('modern-assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('modern-assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
<script src="{{ asset('modern-assets/vendor/libs/pickr/pickr.js') }}"></script>
<script src="{{ asset('modern-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('modern-assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('modern-assets/vendor/js/menu.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('modern-assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<script>
  window.config = window.config || {};
  window.config.enableMenuLocalStorage = false;
  window.assetsPath = document.documentElement.getAttribute('data-assets-path') || "{{ rtrim(asset('modern-assets'), '/') }}/";
  window.templateName = document.documentElement.getAttribute('data-template') || "vertical-menu-template-bordered";

  document.addEventListener("DOMContentLoaded", function () {
    var themeToggle = document.getElementById("sneat-theme-toggle");
    var themeIcon = document.getElementById("sneat-theme-toggle-icon");
    var storageKey = "kingsvtu-theme-mode";
    var root = document.documentElement;

    var setIcon = function (theme) {
      if (!themeToggle || !themeIcon) {
        return;
      }

      if (theme === "dark") {
        themeIcon.className = "icon-base bx bx-sun icon-md";
      } else {
        themeIcon.className = "icon-base bx bx-moon icon-md";
      }
    };

    var applyTheme = function (theme) {
      root.setAttribute("data-bs-theme", theme);
      setIcon(theme);
    };

    try {
      applyTheme(localStorage.getItem(storageKey) === "dark" ? "dark" : "light");
    } catch (error) {
      applyTheme("light");
    }

    if (themeToggle) {
      themeToggle.addEventListener("click", function () {
        var currentTheme = root.getAttribute("data-bs-theme") === "dark" ? "dark" : "light";
        var nextTheme = currentTheme === "dark" ? "light" : "dark";

        applyTheme(nextTheme);

        try {
          localStorage.setItem(storageKey, nextTheme);
        } catch (error) {}
      });
    }

    var scrollTopBtn = document.getElementById("sneat-scroll-top");
    if (scrollTopBtn) {
      var toggleScrollTop = function () {
        if (window.scrollY > 400) {
          $(scrollTopBtn).fadeIn();
        } else {
          $(scrollTopBtn).fadeOut();
        }
      };

      toggleScrollTop();
      window.addEventListener("scroll", toggleScrollTop, { passive: true });

      $(scrollTopBtn).on("click", function () {
        $("html, body").animate({ scrollTop: 0 }, 1000);
      });
    }
  });
</script>

<!-- Main JS -->
<script src="{{ asset('modern-assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('modern-assets/js/dashboards-analytics.js') }}"></script>

@yield('page-script')
</body>
</html>
