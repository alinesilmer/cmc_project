// Animaciones de entrada y contadores
document.addEventListener("DOMContentLoaded", () => {
  // reveal on scroll
  const reveals = document.querySelectorAll(".reveal");
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add("visible");
          io.unobserve(e.target);
        }
      });
    },
    { threshold: 0.15 }
  );
  reveals.forEach((el) => io.observe(el));

  // contador simple para KPIs
  document.querySelectorAll(".kpi-value").forEach((el) => {
    const target = +el.dataset.count;
    let current = 0;
    const step = Math.max(1, Math.floor(target / 60)); // ~1 seg
    const tick = () => {
      current += step;
      if (current >= target) {
        el.textContent = target;
      } else {
        el.textContent = current;
        requestAnimationFrame(tick);
      }
    };
    requestAnimationFrame(tick);
  });

  // scroll a quick actions
  const btn = document.getElementById("quickStartBtn");
  if (btn) {
    btn.addEventListener("click", () => {
      const qa = document.querySelector(".quick-actions");
      if (qa) qa.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  }
});
