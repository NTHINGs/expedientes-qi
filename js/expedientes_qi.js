window.load= function(callback) {
    callback();
}
String.prototype.supplant = function (o) {
    return this.replace(/{([^${}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        }
    );
};

function expedientes_qi_imprimir(mode, expedientes) {
    let patient = JSON.parse(window.atob(expedientes))[0];

    parseTemplate('/wp-content/plugins/expedientes-qi/templates/informacion-personal.html', patient)
        .then(function (template) {
            let doc = new jsPDF()
            doc.fromHTML(template, 10, 10);
            doc.save('a4.pdf');
        });
}

function parseTemplate(file, object) {
    return new Promise(function (resolve, reject) {
        var rawFile = new XMLHttpRequest();
        rawFile.open("GET", file, false);
        rawFile.onreadystatechange = function () {
            if (rawFile.readyState === 4) {
                if (rawFile.status === 200 || rawFile.status == 0) {
                    resolve(rawFile.responseText.supplant(object));
                }
            }
        }
        rawFile.send(null);
    });
}