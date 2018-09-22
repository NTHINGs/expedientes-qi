function expedientes_qi_imprimir(mode, expedientes) {
    let patient = JSON.parse(window.atob(expedientes));

    let doc = new jsPDF()

    doc.text('Hello world!', 10, 10)
    doc.text(patient.nombre, 15, 15);
    doc.save('a4.pdf')
}