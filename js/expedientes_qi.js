function expedientes_qi_imprimir(mode, expedientes) {
    let patient = JSON.parse(window.atob(expedientes))[0];

    let doc = new jsPDF()

    var markup = '<div><p>' + patient.nombre + '</p></div>';
    var parser = new DOMParser()
    var el = parser.parseFromString(markup, "text/xml");

    doc.fromHTML(el.firstChild);
    // doc.text('Hello world!', 10, 10)
    // doc.text(patient.nombre, 15, 15);
    // doc.save('a4.pdf')
    doc.output("dataurlnewwindow");
}