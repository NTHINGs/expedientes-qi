function expedientes_qi_imprimir(mode, expedientes) {
    let patient = JSON.parse(window.atob(expedientes))[0];

    let doc = new jsPDF()

    let div = document.createElement('div');
    div.setAttribute("id", "pdf");
    div.innerHTML = '<p>' + patient.nombre + '</p>';
    // div.style.display = 'none';

    doc.fromHTML('<p>' + patient.nombre + '</p>', 10, 10);
    // doc.text('Hello world!', 10, 10)
    // doc.text(patient.nombre, 15, 15);
    // doc.save('a4.pdf')
    doc.output("dataurlnewwindow");
}