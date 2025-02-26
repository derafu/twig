/* Base JavaScript */

window.copyCode = function(button) {
    let code = button.nextElementSibling.innerText;
    navigator.clipboard.writeText(code).then(() => {
        button.innerText = "Copied!";
        setTimeout(() => button.innerText = "Copy", 2000);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    let colorBoxes = document.querySelectorAll(".color-box");
    colorBoxes.forEach(box => {
        let rgbColor = getComputedStyle(box).backgroundColor;
        let hexColor = rgbToHex(rgbColor);
        box.setAttribute("title", hexColor);
        box.addEventListener("click", function () {
            navigator.clipboard.writeText(hexColor).then(() => {
                //alert("Copied: " + hexColor);
            });
        });
    });

    function rgbToHex(rgb) {
        let result = rgb.match(/\d+/g).map(num => parseInt(num).toString(16).padStart(2, "0"));
        return `#${result.join("")}`;
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
