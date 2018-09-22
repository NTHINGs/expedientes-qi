function expedientes_qi_imprimir(mode, expedientes) {
    let patient = JSON.parse(window.atob(expedientes))[0];

    let doc = new jsPDF()

    $('<div id="pdf"><p>' + patient.nombre + '</p></div>').hide();

    doc.fromHTML($('#pdf').first());
    // doc.text('Hello world!', 10, 10)
    // doc.text(patient.nombre, 15, 15);
    // doc.save('a4.pdf')
    doc.output("dataurlnewwindow");
}