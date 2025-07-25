/* ========= TOASTS ========= */
const toastContainer = document.getElementById("toastContainer");

// ===== centered toast =====
function showToast(message, type = "success") {
  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span class="icon">${
    type === "success" ? "✔️" : "⚠️"
  }</span><span>${message}</span>`;
  toastContainer.appendChild(toast);
  setTimeout(() => toast.remove(), 4000);
}

/* ========= GENERAR ========= */
document.querySelectorAll(".generate-btn").forEach((btn) => {
  btn.addEventListener("click", async () => {
    btn.classList.add("loading");
    const row = btn.closest("tr");
    const id = row.dataset.id;
    const concepto = row.dataset.concepto;

    try {
      // Simulación request
      await new Promise((r) => setTimeout(r, 1500));
      showToast(`Descuento “${concepto}” (${id}) generado.`, "success");
    } catch (err) {
      showToast("Ocurrió un error al generar.", "error");
    } finally {
      btn.classList.remove("loading");
    }
  });
});

/* ========= FILTRO ========= */
const filtroInput = document.getElementById("filtroInput");
if (filtroInput) {
  filtroInput.addEventListener("input", (e) => {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll("#tablaDescuentos tbody tr").forEach((tr) => {
      const text = tr.innerText.toLowerCase();
      tr.style.display = text.includes(term) ? "" : "none";
    });
  });
}

/* ========= MODAL EDIT ========= */
const modal = document.getElementById("editModal");
const modalBackdrop = modal.querySelector(".modal-backdrop");
const modalClose = modal.querySelector(".modal-close");
const modalCancel = document.getElementById("modalCancel");
const modalForm = document.getElementById("modalForm");
const modalPercent = document.getElementById("modalPercent");
const modalConcepto = document.getElementById("modalConcepto");
const modalPrecio = document.getElementById("modalPrecio");

let currentRow = null;

function openModal(row) {
  currentRow = row;
  const concepto = row.dataset.concepto;
  modalPrecio.value = row.dataset.precio;
  const porcentaje = row.dataset.porcentaje;

  modalConcepto.textContent = concepto;
  modalPercent.value = porcentaje;

  modal.hidden = false;
  modal.setAttribute("aria-hidden", "false");
  modalPercent.focus();
  // opcional: scroll lock
  document.body.style.overflow = "hidden";
}

function closeModal() {
  modal.hidden = true;
  modal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
  currentRow = null;
}

document.querySelectorAll(".edit-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    const row = btn.closest("tr");
    openModal(row);
  });
});

modalBackdrop.addEventListener("click", closeModal);
modalClose.addEventListener("click", closeModal);
modalCancel.addEventListener("click", closeModal);

modalForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const precio = parseFloat(modalPrecio.value);
  const porc = parseFloat(modalPercent.value);

  if (isNaN(precio) || precio < 0) {
    showToast("Ingrese un precio válido", "error");
    return;
  }
  if (isNaN(porc) || porc < 0 || porc > 100) {
    showToast("Porcentaje 0‑100 %", "error");
    return;
  }

  const id = currentRow.dataset.id;
  try {
    /* await fetch('actualizar_descuento.php',{method:'POST',body:new FormData(modalForm)}) */

    // actualizar dataset y vistas
    currentRow.dataset.precio = precio;
    currentRow.dataset.porcentaje = porc;
    currentRow.querySelector(".price-view").textContent = `$${Number(
      precio
    ).toLocaleString("es-AR")}`;
    currentRow.querySelector(".percent-view").textContent = `${porc}%`;

    showToast(`Descuento ${id} actualizado`, "success");
    closeModal();
  } catch (err) {
    showToast("No se pudo guardar", "error");
  }
});

// ESC para cerrar
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && !modal.hidden) closeModal();
});
