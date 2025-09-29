document.getElementById("sidebarToggle").addEventListener("click", function () {
    document.getElementById("sidebar").classList.toggle("hidden");
    document.getElementById("content").classList.toggle("sidebar-hidden");
    document.getElementById("navbar").classList.toggle("sidebar-hidden");
});

$(document).ready(function () {
    $("#dataTable").DataTable();
});

// Fullscreen toggle function
document
    .getElementById("fullscreenToggle")
    .addEventListener("click", function (e) {
        e.preventDefault();
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    });

document
    .getElementById("formsDropdownToggle")
    .addEventListener("click", function (e) {
        e.preventDefault();
        var dropdown = document.getElementById("formsDropdown");
        if (
            dropdown.style.display === "none" ||
            dropdown.style.display === ""
        ) {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    });

$(function () {
    $("#dataTable").DataTable();
    $("#dataTableKecamatan").DataTable();
    $("#dataTableKabupaten").DataTable();
    $("#dataTableProvinsi").DataTable();
});

$(document).ready(function () {
    $(".select2").select2({
        theme: "bootstrap4",
        width: "100%",
    });
});
