(function (global) {
  function initSidebarToggle(options = {}) {
    const { parentSelector = ".nav-parent", submenuSelector = ".sub-menu" } =
      options;

    document.querySelectorAll(parentSelector).forEach((parent) => {
      parent.addEventListener("click", () => {
        parent.classList.toggle("open");
        parent.setAttribute("aria-expanded", parent.classList.contains("open"));

        const submenu = parent.nextElementSibling;
        if (submenu && submenu.matches(submenuSelector)) {
          submenu.classList.toggle("open");
        }
      });
    });
  }

  /*----- exportaciones -----*/
  if (typeof module !== "undefined" && module.exports) {
    module.exports = initSidebarToggle;
  } else if (typeof define === "function" && define.amd) {
    define(() => initSidebarToggle);
  } else {
    global.initSidebarToggle = initSidebarToggle;
  }
})(this);
