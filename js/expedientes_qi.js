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
async function replaceTemplate(template, object) {
    let parsed = template;
    for (let key of Object.keys(object)) {
        let obj = object[key];
        if (key === 'fotografia') {
            obj = await getDataUri(object[key]);
        }
        parsed = parsed.replace(new RegExp('{{' + key + '}}'), obj);
    }
    return parsed;
}

function getTemplate(file) {
    return new Promise(function (resolve, reject) {
        const rawFile = new XMLHttpRequest();
        rawFile.open("GET", file, false);
        rawFile.onreadystatechange = function () {
            if (rawFile.readyState === 4) {
                if (rawFile.status === 200 || rawFile.status == 0) {
                    resolve($.parseHTML(rawFile.responseText.trim()));
                }
            }
        }
        rawFile.send(null);
    });
}

function htmlToElement(html) {
    let template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
}

function createPDF(template, name, heading, orientation, isTable) {
    (async () => {
        let doc = new jsPDF({
            orientation,
        });
        doc.addImage(await getDataUri('/wp-content/plugins/expedientes-qi/logo.png'), 10, 10, 30, 20);
        doc.setFontSize(24);
        doc.text(heading, 45, 25);
        if (isTable) {
            buildTable(doc, template, {
                margin: {
                    top: 40
                }
            });
            doc.save(name + '.pdf');
        }
    })();
}

function buildTable(doc, template, options) {
    let res = doc.autoTableHtmlToJson(htmlToElement(template));
    console.log(res);
    let images = [];
    let i = 0;
    let imgElements = $(template).find('img');
    doc.autoTable(res.columns, res.data, {
        styles: { fillColor: [80, 18, 70] },
        margin: options.margin,
        drawRow: function (row, data) {
            row.height = 24
            if (options.pageBreakOffset) {
                if (row.index % options.pageBreakOffset === 0) {
                    const posY = row.y + row.height * (options.pageBreakOffset + 1) + data.settings.margin.bottom;
                    const pageHeight = doc.internal.pageSize.height || doc.internal.pageSize.getHeight();
                    if (posY > pageHeight) {
                        data.addPage();
                    }
                }
            }
        },
        drawCell: function (cell, opts) {
            if (opts.column.dataKey === 0) {
                images.push({
                    url: imgElements[i].src,
                    x: cell.textPos.x,
                    y: cell.textPos.y
                });
                i++;
            }
        },
        addPageContent: function () {
            for (let i = 0; i < images.length; i++) {
                doc.addImage(images[i].url, images[i].x, images[i].y, 20, 20);
            }
        }
    });
}

async function buildTemplate(template, keys_to_build, data) {
    for (let key of keys_to_build) {
        let elemento_especial = $(template).find('[nth-for="' + key + '"]').removeAttr("nth-for");
        let parent = await replace_logic(elemento_especial, data, true);
        elemento_especial.parent().replaceWith(parent);
    }

    return $(template).prop('outerHTML');
}

async function replace_logic(elemento_especial, data, isDataAnArray) {
    let elemento_especial_html = elemento_especial.prop('outerHTML');
    let parent = $(elemento_especial.parent().prop('outerHTML'));
    parent.find(":first-child").remove();
    for (let index = 0; index < data.length; index++) {
        let parsed_template = await replaceTemplate(elemento_especial_html, data[index]);
        parent.append(parsed_template);
    }

    return parent;
}
function stringValidation(string) {
    return string ? string : '';
}
function createFichaIdentificacion(paciente) {
    (async () => {
        let doc = new jsPDF({
            orientation: 'portrait',
            pageFormat: 'a4'
        });
        doc.addImage(await getDataUri('/wp-content/plugins/expedientes-qi/logo.png'), 10, 10, 30, 20);
        doc.setFontSize(24);
        doc.text('Ficha de Identificación', 45, 18);
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

        doc.text('Escolaridad: ' + stringValidation(paciente.escolaridad), 50, 59);
        doc.text('Ocupación: ' + stringValidation(paciente.ocupacion), 150, 59);

        doc.text('Estado Civil: ' + stringValidation(paciente.estadocivil), 50, 66);
        doc.text('Cantidad de Hijos: ' + stringValidation(paciente.cantidadhijos), 150, 66);

        doc.text('Domicilio: ' + stringValidation(paciente.domicilio), 50, 73);

        doc.text('Ciudad de Origen: ' + stringValidation(paciente.ciudaddeorigen), 50, 80);
        doc.text('Ciudad Actual: ' + stringValidation(paciente.ciudadactual), 150, 80);

        doc.text('Teléfono: ' + stringValidation(paciente.telefono), 50, 87);
        doc.text('Email: ' + stringValidation(paciente.email), 150, 87);

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
        doc.autoTable(
            [
                'INDIVIDUAL',
                'FAMILIAR',
                'ENTORNO'
            ],
            [
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
            ], {
                margin: { top: 105 },
                styles: { overflow: 'linebreak', columnWidth: '50px' },
                headerStyles: {
                    fillColor: [80, 18, 70],
                    textColor: 255
                },
                columnStyles: { text: { columnWidth: '50px' } },
                drawRow: function (row, data) {
                    row.height = 10;
                },
                createdCell: function (cell, data) {
                    let tipo_riesgo = '';
                    switch (data.column.index) {
                        case 0:
                            tipo_riesgo = 'individual';
                            break;
                        case 1:
                            tipo_riesgo = 'familiar';
                            break;
                        case 2:
                            tipo_riesgo = 'entorno';
                            break;
                    }
                    for (let riesgo of riesgos[tipo_riesgo]) {
                        if (riesgo.text === cell.raw && paciente.riesgos[tipo_riesgo].split(',').includes('' + riesgo.value)) {
                            cell.styles.fillColor = [80, 18, 70];
                            cell.styles.textColor = 255;
                        }
                    }
                },
            });

        doc.text('Observaciones:', 15, 190);
        doc.setFontSize(10);
        paciente.riesgos.observaciones = stringValidation(paciente.riesgos.observaciones);
        if (paciente.riesgos.observaciones.length > 1000) {
            paciente.riesgos.observaciones = paciente.riesgos.observaciones.substring(0, 1000) + '... Ver más en sistema.';
        }
        doc.text(doc.splitTextToSize(paciente.riesgos.observaciones, 180), 15, 197);
        doc.setFontSize(12);
        paciente.enfermedades = stringValidation(paciente.enfermedades);
        paciente.alergias = stringValidation(paciente.alergias);
        doc.text('Enfermedades:', 15, 195 + (doc.splitTextToSize(paciente.riesgos.observaciones, 180).length * 4));
        doc.text(doc.splitTextToSize(paciente.enfermedades, 180), 15, 200 + (doc.splitTextToSize(paciente.riesgos.observaciones, 180).length * 4));
        doc.text('Alergias:', 15, 205 + (doc.splitTextToSize(paciente.riesgos.observaciones, 180).length * 4) + (doc.splitTextToSize(paciente.enfermedades, 180).length * 4));
        doc.text(doc.splitTextToSize(paciente.alergias, 180), 15, 210 + (doc.splitTextToSize(paciente.riesgos.observaciones, 180).length * 4) + (doc.splitTextToSize(paciente.enfermedades, 180).length * 4));
        doc.addPage();
        doc.setFontSize(18);
        doc.text('PERSONAS DE CONTACTO', 65, 15);
        doc.setFontSize(12);
        let contactos_table_ref = 0;
        doc.autoTable(
            [
                { title: 'Nombre', dataKey: 'nombre' },
                { title: 'Relación', dataKey: 'relacion' },
                { title: 'Domicilio', dataKey: 'domicilio' },
                { title: 'Teléfono Celular', dataKey: 'telefono_celular' },
                { title: 'Teléfono Casa', dataKey: 'telefono_casa' },
                { title: 'Otro Teléfono', dataKey: 'telefono_otro' },
            ],
            paciente.contactos,
            {
                margin: { top: 20 },
                styles: { overflow: 'linebreak', columnWidth: '50px' },
                headerStyles: {
                    fillColor: [80, 18, 70],
                    textColor: 255
                },
                columnStyles: { text: { columnWidth: '50px' } },
                drawRow: function (row, data) {
                    row.height = 10;
                    contactos_table_ref = data.table;
                },
            });

        doc.autoTable(
            [
                { title: 'Sustancia', dataKey: 'sustancia' },
                { title: 'Año Del Primer Uso', dataKey: 'añoprimeruso' },
                { title: 'Edad', dataKey: 'edadprimeruso' },
                { title: 'Uso Regular', dataKey: 'usoregular' },
                { title: 'Periodo', dataKey: 'periodo' },
                { title: 'Unidad', dataKey: 'unidad' },
                { title: 'Abstinencia Máxima', dataKey: 'abstinenciamaxima' },
                { title: 'Abstinencia Actual', dataKey: 'abstinenciaactual' },
                { title: 'Via de Uso / Administración', dataKey: 'viadeuso' },
                { title: 'Fecha del Último Consumo', dataKey: 'fechaultimoconsumo' },
            ],
            paciente.sustancias,
            {
                margin: { top: contactos_table_ref.finalY },
                styles: { overflow: 'linebreak', columnWidth: '50px' },
                headerStyles: {
                    fillColor: [80, 18, 70],
                    textColor: 255
                },
                columnStyles: { text: { columnWidth: '50px' } },
                drawRow: function (row, data) {
                    row.height = 10;
                },
            });
        doc.save(paciente.nombre + '.pdf');

    })();
}