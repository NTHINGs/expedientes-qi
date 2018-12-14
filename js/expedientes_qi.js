function getDataUri(url) {
    return new Promise((resolve, reject) => {
        let extension = url.split('.').pop();
        let type = 'png';
        switch(extension) {
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
            resolve(canvas.toDataURL('image/'+type));
        };

        image.src = url;
    });
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

function createPDF(template, name, orientation) {
    let doc = new jsPDF({
        orientation,
    });
    let res = doc.autoTableHtmlToJson(htmlToElement(template));
    let images = [];
    let i = 0;
    let imgElements = $(template).find('img');
    doc.autoTable(res.columns, res.data, {
        drawRow: function (row, data) {
            row.height = 24
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
    doc.save(name + '.pdf');
}

async function buildTemplate(template, keys_to_build, data) {
    for (let key of keys_to_build) {
        const elemento_repetible = $(template).find('[nth-for="' + key + '"]').removeAttr("nth-for");
        let elemento_repetible_html = elemento_repetible.prop('outerHTML');
        let parent = $(elemento_repetible.parent().prop('outerHTML'));
        parent.find(":first-child").remove();
        for (let index = 0; index < data.length; index++) {
            let parsed_template = await replaceTemplate(elemento_repetible_html, data[index]);
            parent.append(parsed_template);
        }
        elemento_repetible.parent().replaceWith(parent);
    }
    return $(template).prop('outerHTML');
}