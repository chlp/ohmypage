function loadImage(img) {
    if (!img instanceof HTMLImageElement) {
        return;
    }
    let fullSrc = img.dataset.src;
    let xhr = new XMLHttpRequest();
    xhr.onload = () => {
        img.src = fullSrc;
        img.style.filter = 'blur(0px)'
    };
    xhr.onabort = (ev) => {
        console.log("ohmyimg onabort", img.alt, fullSrc, ev);
    };
    xhr.onerror = (ev) => {
        console.log("ohmyimg onerror", img.alt, fullSrc, ev);
    };
    xhr.onprogress = (ev) => {
        if (ev.lengthComputable) {
            let tenthLoaded = Math.round((ev.loaded / ev.total) * 10);
            img.style.filter = 'blur(' + (10 - tenthLoaded) + 'px)'
        }
    };
    xhr.open("GET", fullSrc);
    xhr.responseType = "blob";
    xhr.send();
}
for (let img of document.getElementsByClassName("ohmyimg")) {
    loadImage(img);
}