function expedientes_qi_imprimir(mode, expedientes) {
    let patient = JSON.parse(window.atob(expedientes))[0];

    let doc = new jsPDF()

    parseTemplate('/wp-content/plugins/expedientes-qi/templates/informacion-personal.html')
        .then(function (template) {
            doc.fromHTML(template, 10, 10);
            doc.save('a4.pdf');
        });
}

function parseTemplate(file) {
    return new Promise(function (resolve, reject) {
        var rawFile = new XMLHttpRequest();
        rawFile.open("GET", file, false);
        rawFile.onreadystatechange = function () {
            if (rawFile.readyState === 4) {
                if (rawFile.status === 200 || rawFile.status == 0) {
                    resolve(rawFile.responseText);
                }
            }
        }
        rawFile.send(null);
    });
}