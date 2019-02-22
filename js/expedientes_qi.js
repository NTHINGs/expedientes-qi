function getDataUri(url) {
    if (url) {
        return new Promise((resolve, reject) => {
            let extension = url.split('.').pop();
            let type = 'png';
            switch (extension) {
                case 'jpg':
                case 'jpeg':
                    type = 'jpeg';
                    break;
            }
            let image = new Image();

            image.onload = function () {
                let canvas = document.createElement('canvas');
                canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
                canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size
                canvas.getContext('2d').drawImage(this, 0, 0);
                resolve(canvas.toDataURL('image/' + type));
            };

            image.src = url;
        });
    }
}

function stringValidation(string) {
    return string ? string : '';
}

function createPacientesPDF(pacientes) {
    (async () => {
        let doc = new jsPDF({
            orientation: 'landscape',
            pageFormat: 'a4'
        });
        doc.autoTableSetDefaults({
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255
            }
        });

        doc.addImage(await getDataUri('/wp-content/plugins/expedientes-qi/logo.png'), 10, 10, 30, 20);
        doc.setFontSize(24);
        doc.text('Pacientes', 45, 25);
        doc.autoTable({
            startY: 40,
            columns: [
                { header: 'Fotografía', dataKey: 'fotografia' },
                { header: 'Nombre', dataKey: 'nombre' },
                { header: 'Fecha de Nacimiento', dataKey: 'fechadenacimiento' },
                { header: 'Edad', dataKey: 'edad' },
                { header: 'Teléfono', dataKey: 'telefono' },
                { header: 'Fecha de Creación', dataKey: 'fecha_creacion' },
                { header: 'Fecha de Modificación', dataKey: 'fecha_modificacion' },
                { header: 'Responsables', dataKey: 'responsables' },
            ],
            body: pacientes.map(function (paciente) {
                return {
                    fotografia: '',
                    nombre: paciente.nombre,
                    fechadenacimiento: paciente.fechadenacimiento,
                    edad: paciente.edad,
                    telefono: paciente.telefono,
                    fecha_creacion: paciente.fecha_creacion,
                    fecha_modificacion: paciente.fecha_modificacion,
                    responsables: paciente.responsables
                };
            }),
            bodyStyles: {
                minCellHeight: 25,
                cellWidth: 'wrap'
            },
            didDrawCell: function (data) {
                if (data.row.section === 'body' && data.column.dataKey === 'fotografia') {
                    doc.addImage(pacientes[data.row.index].fotografia, 'PNG', data.cell.x, data.cell.y, 20, 20);
                }
            },
        });
        doc.save('pacientes.pdf');
    })();
}

async function informacionPersonal(doc, paciente, header) {
    doc.addImage(await getDataUri('/wp-content/plugins/expedientes-qi/logo.png'), 10, 10, 30, 20);
    doc.setFontSize(24);
    doc.text(header, 45, 18);
    doc.setFontSize(18);
    doc.text(paciente.nombre, 45, 28);

    if (paciente.fotografia) {
        doc.addImage(await getDataUri(paciente.fotografia), 10, 40, 30, 20);
    } else {
        doc.addImage(await getDataUri('/wp-content/plugins/expedientes-qi/default.png'), 10, 10, 30, 20);
    }

    doc.setFontSize(12);
    doc.text('Nombre: ' + stringValidation(paciente.nombre), 50, 45);
    doc.text('Fecha de Nacimiento: ' + stringValidation(paciente.fechadenacimiento), 50, 52);
    doc.text('Edad: ' + stringValidation(paciente.edad), 150, 52);

    if (header === 'Ficha de Identificación') {
        doc.text('Escolaridad: ' + stringValidation(paciente.escolaridad), 50, 59);
        doc.text('Ocupación: ' + stringValidation(paciente.ocupacion), 150, 59);

        doc.text('Estado Civil: ' + stringValidation(paciente.estadocivil), 50, 66);
        doc.text('Cantidad de Hijos: ' + stringValidation(paciente.cantidadhijos), 150, 66);

        doc.text('Domicilio: ' + stringValidation(paciente.domicilio), 50, 73);

        doc.text('Ciudad de Origen: ' + stringValidation(paciente.ciudaddeorigen), 50, 80);
        doc.text('Ciudad Actual: ' + stringValidation(paciente.ciudadactual), 150, 80);

        doc.text('Teléfono: ' + stringValidation(paciente.telefono), 50, 87);
        doc.text('Email: ' + stringValidation(paciente.email), 150, 87);
    }
}
function createFichaIdentificacion(paciente) {
    (async () => {
        let doc = new jsPDF({
            orientation: 'portrait',
            pageFormat: 'a4'
        });

        doc.autoTableSetDefaults({
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255
            }
        });

        await informacionPersonal(doc, paciente, 'Ficha de Identificación');
        doc.setFontSize(18);
        doc.text('RIESGOS PSICOSOCIALES', 65, 100);
        doc.setFontSize(12);


        const riesgos = {
            individual: [
                { value: 1, text: 'INICIO TEMPRANO AL TRABAJO INFANTIL' },
                { value: 2, text: 'AUSENTISMO ESCOLAR PRESENTE' },
                { value: 3, text: 'CAPACIDADES DIFERENTES' },
                { value: 4, text: 'AISLAMIENTO O TIMIDEZ' },
                { value: 5, text: 'AGRESIVO' },
                { value: 6, text: 'CONSUMO DE DROGAS' },
                { value: 7, text: 'INTENTO SUICIDA' },
            ],
            familiar: [
                { value: 1, text: 'MALTRATO PSICOLOGICO O FISICO DE LOS PADRES' },
                { value: 2, text: 'CONSUMO DE ALCOHOL DE LOS PADRES' },
                { value: 3, text: 'PADRES QUE HUMILLAN Y HACEN SENTIR CULPA A LOS HIJOS' },
                { value: 4, text: 'PADRES AUSENTES' },
                { value: 5, text: 'EMBARAZO ADOLESCENTE' },
            ],
            entorno: [
                { value: 1, text: 'EXPUESTO A PANDILLAS O VENTA DE DROGA EN SU COLONIA' },
                { value: 2, text: 'BAJO NIVEL SOCIO ECONOMICO' },
                { value: 3, text: 'PARTICIPACION EN ACTOS DELICTIVOS' },
            ],
        };

        doc.autoTable({
            startY: 110,
            head: [
                [
                    'INDIVIDUAL',
                    'FAMILIAR',
                    'ENTORNO'
                ]
            ],
            body: [
                [
                    'INICIO TEMPRANO AL TRABAJO INFANTIL',
                    'MALTRATO PSICOLOGICO O FISICO DE LOS PADRES',
                    'EXPUESTO A PANDILLAS O VENTA DE DROGA EN SU COLONIA',
                ],
                [
                    'AUSENTISMO ESCOLAR PRESENTE',
                    'CONSUMO DE ALCOHOL DE LOS PADRES',
                    'BAJO NIVEL SOCIO ECONOMICO',
                ],
                [
                    'CAPACIDADES DIFERENTES',
                    'PADRES QUE HUMILLAN Y HACEN SENTIR CULPA A LOS HIJOS',
                    'PARTICIPACION EN ACTOS DELICTIVOS',
                ],
                [
                    'AISLAMIENTO O TIMIDEZ',
                    'PADRES AUSENTES',
                ],
                [
                    'AGRESIVO',
                    'EMBARAZO ADOLESCENTE',
                ],
                [
                    'CONSUMO DE DROGAS',
                ],
                [
                    'INTENTO SUICIDA',
                ],
            ],
            didParseCell: function (data) {
                if (data.row.section === 'body') {
                    let tipo_riesgo = '';
                    if (data.column.index == 0) {
                        tipo_riesgo = 'individual';
                    }
                    if (data.column.index == 1) {
                        tipo_riesgo = 'familiar';
                    }
                    if (data.column.index == 2) {
                        tipo_riesgo = 'entorno';
                    }
                    if (tipo_riesgo) {
                        for (let riesgo of riesgos[tipo_riesgo]) {
                            if (paciente.riesgos && paciente.riesgos[tipo_riesgo] && riesgo.text === data.cell.raw && paciente.riesgos[tipo_riesgo].split(',').includes('' + riesgo.value)) {
                                data.cell.styles.fillColor = [80, 18, 70];
                                data.cell.styles.textColor = 255;
                            }
                        }
                    }
                }

            },
        });

        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [['Observaciones']],
            body: [[paciente.riesgos.observaciones]],
            showHead: 'firstPage'
        });

        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [['Enfermedades', 'Alergias']],
            body: [[paciente.enfermedades, paciente.alergias]],
            showHead: 'firstPage'
        });


        if (paciente.contactos && paciente.contactos.length > 0) {
            doc.addPage();
            doc.setFontSize(18);
            doc.text('PERSONAS DE CONTACTO', 65, 15);
            doc.setFontSize(12);
            doc.autoTable({
                startY: 20,
                columns: [
                    { header: 'Nombre', dataKey: 'nombre' },
                    { header: 'Relación', dataKey: 'relacion' },
                    { header: 'Domicilio', dataKey: 'domicilio' },
                    { header: 'Teléfono Celular', dataKey: 'telefono_celular' },
                    { header: 'Teléfono Casa', dataKey: 'telefono_casa' },
                    { header: 'Otro Teléfono', dataKey: 'telefono_otro' },
                ],
                body: paciente.contactos,
                showHead: 'firstPage'
            });
        }

        if (paciente.sustancias && paciente.sustancias.length > 0) {
            doc.addPage('a4', 'landscape');
            doc.setFontSize(18);
            doc.text('USO DE SUSTANCIAS', 120, 15);
            doc.setFontSize(12);

            doc.autoTable({
                startY: 20,
                columns: [
                    { header: 'Sustancia', dataKey: 'sustancia' },
                    { header: 'Año Del Primer Uso', dataKey: 'añoprimeruso' },
                    { header: 'Edad', dataKey: 'edadprimeruso' },
                    { header: 'Uso Regular', dataKey: 'usoregular' },
                    { header: 'Periodo', dataKey: 'periodo' },
                    { header: 'Unidad', dataKey: 'unidad' },
                    { header: 'Abstinencia Máxima', dataKey: 'abstinenciamaxima' },
                    { header: 'Abstinencia Actual', dataKey: 'abstinenciaactual' },
                    { header: 'Via de Uso / Administración', dataKey: 'viadeuso' },
                    { header: 'Fecha del Último Consumo', dataKey: 'fechaultimoconsumo' },
                ],
                body: paciente.sustancias,
                headStyles: {
                    cellWidth: 'wrap',
                    fontSize: 8
                },
                showHead: 'firstPage'
            });
        }

        doc.save(paciente.nombre + '_FICHA.pdf');

    })();
}

function createFacesPDF(data) {
    (async () => {
        let doc = new jsPDF({
            orientation: 'portrait',
            pageFormat: 'a4'
        });
        await informacionPersonal(doc, data, 'FACES');

        doc.setFontSize(18);
        doc.text('FACES', 90, 65);
        doc.setFontSize(12);

        doc.autoTable({
            startY: 70,
            columns: [
                { header: 'Adaptabilidad', dataKey: 'adaptabilidad' },
                { header: 'Cohesión', dataKey: 'cohesion' },
                { header: 'Rigidez', dataKey: 'rigidez' },
                { header: 'Apego', dataKey: 'apego' },
                { header: 'Caos', dataKey: 'caos' },
                { header: 'Desapego', dataKey: 'desapego' },
            ],
            body: [
                {
                    adaptabilidad: data.adaptabilidad,
                    cohesion: data.cohesion,
                    rigidez: data.rigidez,
                    apego: data.apego,
                    caos: data.caos,
                    desapego: data.desapego
                }
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            },
            didParseCell: function (data) {
                if (data.row.section === 'body') {
                    try {
                        const array = JSON.parse(data.cell.raw);
                        let cell = '';
                        for (let i = 0; i < array.length; i++) {
                            switch (i) {
                                case 0:
                                    cell += `Papá: ${array[i]}\n`;
                                    break;

                                case 1:
                                    cell += `Mamá: ${array[i]}\n`;
                                    break;

                                case 2:
                                    cell += `Hijo: ${array[i]}\n`;
                                    break;

                                default:
                                    cell += `${array[i].nombre}: ${array[i].valor}\n`;
                                    break;
                            }
                        }

                        data.cell.text = cell;
                    } catch (e) {
                        data.cell.text = '¡Ocurrió un error!';
                    }
                }
            }
        });
        doc.save(data.nombre + '_FACES.pdf');
    })();
}

function createFadPDF(data) {
    (async () => {
        let doc = new jsPDF({
            orientation: 'portrait',
            pageFormat: 'a4'
        });
        await informacionPersonal(doc, data, 'FAD');

        doc.setFontSize(18);
        doc.text('FAD', 90, 60);
        doc.setFontSize(12);

        doc.autoTable({
            startY: 70,
            head: [
                ['Solución De Problemas'],
            ],
            body: [
                [data.solucion_problemas]
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });
        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [
                ['Comunicación'],
            ],
            body: [
                [data.comunicacion]
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });
        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [
                ['Respuesta Afectiva'],
            ],
            body: [
                [data.respuesta_afectiva]
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });
        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [
                ['Involucramiento Afectivo'],
            ],
            body: [
                [data.involucramiento_afectivo]
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });
        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [
                ['Control Del Comportamiento'],
            ],
            body: [
                [data.control_del_comportamiento]
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });
        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [
                ['Funcionamiento General'],
            ],
            body: [
                [data.funcionamiento_general]
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });
        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 5,
            head: [
                ['Interpretación General'],
            ],
            body: [
                [data.interpretacion_general]
            ],
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });

        doc.save(data.nombre + '_FAD.pdf');
    })();
}

function createNotasProgresoPDF(data) {
    (async () => {
        let doc = new jsPDF({
            orientation: 'landscape',
            pageFormat: 'a4'
        });
        await informacionPersonal(doc, data.paciente, 'Notas De Progreso');

        doc.setFontSize(18);
        doc.text('Notas De Progreso', 80, 60);
        doc.setFontSize(12);

        doc.autoTable({
            startY: 70,
            columns: [
                { header: 'ID', dataKey: 'id' },
                { header: 'Nota De Progreso', dataKey: 'nota_progreso' },
                { header: 'Fecha', dataKey: 'fecha' },
                { header: 'Autor', dataKey: 'autor' },
            ],
            body: data.data,
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            },
            willDrawCell: d => {
                if (d.row.section === 'body' && d.column.dataKey === "nota_progreso") {
                    var rowHeigth = d.cell.raw.split(' ')[0];
                    d.cell.text = '';
                    d.row.height = rowHeigth;
                }
            },
            didDrawCell: d => {
                if (d.row.section === 'body' && d.column.dataKey === "nota_progreso") {
                    var rowHeigth = d.cell.raw.split(' ')[0];
                    var base64 = d.cell.raw.split(' ')[1];
                    doc.addImage(base64, 'PNG', d.cell.x, d.cell.y, d.cell.contentWidth, rowHeigth);
                }
            }
        });
        doc.save(data.paciente.nombre + '_NOTAS_PROGRESO.pdf');
    })();
}

function createEvaluacionesPsicologicasPDF(data) {
    (async () => {
        let doc = new jsPDF({
            orientation: 'portrait',
            pageFormat: 'a4'
        });
        await informacionPersonal(doc, data.paciente, 'Evaluaciones Psicológicas');

        doc.setFontSize(18);
        doc.text('Evaluaciones Psicológicas', 80, 60);
        doc.setFontSize(12);

        doc.autoTable({
            startY: 70,
            columns: [
                { header: 'ID', dataKey: 'id' },
                { header: 'Evaluación Psicológica', dataKey: 'evaluacion_psicologica' },
                { header: 'Fecha', dataKey: 'fecha' },
            ],
            body: data.data,
            headStyles: {
                fillColor: [80, 18, 70],
                textColor: 255,
                fontSize: 10
            },
            bodyStyles: {
                fontSize: 10
            }
        });
        doc.save(data.paciente.nombre + '_EVALUACIONES_PSICOLOGICAS.pdf');
    })();
}