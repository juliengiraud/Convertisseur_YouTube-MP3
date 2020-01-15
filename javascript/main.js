function start() {
    navbar();
}

function navbar() {
    var a = window.scrollY,
    currentScrollTop = 0,
    lastPoint = window.scrollY,
    lastHeight;
    const navbar = document.querySelector("nav");

    navbar.style.transform = "translateY(0)";

    window.addEventListener('scroll', function() {
        var b = window.scrollY,
        c = b - lastPoint,
        h = navbar.clientHeight;

        if (h == 0)
            h = lastHeight;
        else
            lastHeight = h;

        if (c > h) {
            navbar.classList.add("display-none");
            lastPoint = b - h;
        }
        else {
            navbar.classList.remove("display-none");
            if (c < 0) {
                lastPoint = b;
                navbar.style.transform = "translateY(0px)";
            }
            else {
                navbar.style.transform = "translateY(" + -c + "px)";
            }
        }

        currentScrollTop = b;
        a = currentScrollTop;
    });
}

document.addEventListener('DOMContentLoaded', start);
