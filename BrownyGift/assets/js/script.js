// ====== BROWNYGIFT SCRIPT ======

// Konfirmasi hapus data
function confirmDelete(nama) {
    return confirm("Apakah kamu yakin ingin menghapus buket '" + nama + "'?");
}

// Efek fade-in untuk container saat halaman dimuat
document.addEventListener("DOMContentLoaded", () => {
    const container = document.querySelector(".container");
    if (container) {
        container.style.opacity = "0";
        container.style.transition = "opacity 0.5s ease-in";
        setTimeout(() => {
            container.style.opacity = "1";
        }, 100);
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search");
    const tableContainer = document.getElementById("table-container");

    searchInput.addEventListener("keyup", function() {
        const keyword = this.value;

        const xhr = new XMLHttpRequest();
        xhr.open("GET", "search.php?keyword=" + encodeURIComponent(keyword), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                tableContainer.innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });
});

function confirmDelete(nama) {
    return confirm(`Yakin ingin menghapus buket "${nama}"?`);
}