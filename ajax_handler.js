let content_template = document.querySelector("#content_template");
let destination_template = document.querySelector("#destination_template");
let form_template = document.querySelector("#form_template");
let classes_template = document.querySelector("#classes_template");

const source = content_template ? content_template.innerHTML : null;
const destin = destination_template ? destination_template.innerHTML : null;
const formtemp = form_template ? form_template.innerHTML : null;
const classestemp = classes_template ? classes_template.innerHTML : null;
const regex = /(mon_hoc\[[A-Z]{2}\])/
const sortObject = obj => Object.keys(obj).sort().reduce((res, key) => (res[key] = obj[key], res), {})

var getAllFieldMapping = async function() {
    let result;
    try {
        result = await $.ajax({
            type: 'POST',
            url: './resource/field_mapping/get_all.php',
            contentType: false,
            cache: false,
            processData: false,
        });
        // console.log(result);
        return JSON.parse(result);
    } catch (error) {
        console.error(error);
    }
};

var getAllClasses = async function() {
    let result;
    try {
        result = await $.ajax({
            type: 'POST',
            url: './resource/avail_fields/get_distinct_class.php',
            contentType: false,
            cache: false,
            processData: false,
        });
        // console.log(result);
        return JSON.parse(result);
    } catch (error) {
        console.error(error);
    }
}

var Share = { 
    currentFile: []
};

var shortFieldMapping = async () => {
    let result;
    try {
        result = await getAllFieldMapping();
        const mapField = data => {
            const obj = {};
            for(let i = 0; i < data.length; i++){
                const { tag, dinh_danh } = data[i];
                obj[tag] = dinh_danh;
            };
            return obj;
        }
        localStorage.setItem('fieldMapping', JSON.stringify(mapField(result)));
    } catch (e) {
        console.log(e);
    }
}

getAllFieldMapping().then( (data) => {
    const mapField = data => {
        const obj = {};
        for(let i = 0; i < data.length; i++){
            const { tag, dinh_danh } = data[i];
            obj[tag] = dinh_danh;
        };
        return obj;
    }
    localStorage.setItem('fieldMapping', JSON.stringify(mapField(data)));
});

getAllClasses().then( (data) => {
    localStorage.setItem('classes', JSON.stringify(data));
})

function encodeUnicode(str) {
    return btoa(unescape(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function (match, p1) {
            return String.fromCharCode('0x' + p1);
    })));
}

function decodeUnicode(str) {
    // Going backwards: from bytestream, to percent-encoding, to original string.
    return decodeURIComponent(atob(str).split('').map(function (c) {
      return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
}

function equals(arg1, arg2) {
    return arg1 == arg2;
}

Handlebars.registerHelper('getDate', function (val) { 
    val = new Date(val);
    let date = [
        val.getFullYear(), 
        val.getMonth() + 1 < 10 ? `0${val.getMonth() + 1}` : val.getMonth() + 1, 
        val.getDate() < 10 ? `0${val.getDate()}` : val.getDate()
    ];
    return date.join('-');
});

Handlebars.registerHelper('getDateMillis', function (val) { 
    val = new Date(parseInt(val));
    let date = [
        val.getFullYear(), 
        val.getMonth() + 1 < 10 ? `0${val.getMonth() + 1}` : val.getMonth() + 1, 
        val.getDate() < 10 ? `0${val.getDate()}` : val.getDate()
    ];
    return date.join('-');
});

Handlebars.registerHelper('equals', (arg1, arg2) => { 
    return arg1 == arg2;
});

Handlebars.registerHelper('ifEquals', (arg1, arg2, options) => { 
    return (arg1 == arg2) ? options.fn(this) : options.inverse(this);
});

Handlebars.registerHelper('getScoreByKey', (key, subjects) => { 
    return subjects[key];
});

// Handlebars.registerHelper('getPercentage', (value) => { 
//     if (value typeof String) {
//         value = parseFloat(value);
//     }
// });

Handlebars.registerHelper('translateKey', (key) => { 
    const fieldMapping = JSON.parse(localStorage.getItem('fieldMapping'));
    return fieldMapping[key];
});

Handlebars.registerHelper('getPot', (subjects) => { 
    return Object.keys(subjects).join(" - ");
});

window.Handlebars.registerHelper('select', function( value, options ){
    var $el = $('<select />').html( options.fn(this) );
    $el.find('[value="' + value + '"]').attr({'selected':'selected'});
    return $el.html();
});

function ajaxHandler() {}

ajaxHandler.findAllStudent = function() {
    const template = Handlebars.compile(source);
    let classId = $("#class").val();

    let formData = new FormData();
    formData.append("class", classId);

    $.ajax({
        type: 'POST',
        url: './resource/student/find_all_by_class.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            // Empty container before applying data
            $("#content").empty();
            let json;
            try {
                json = JSON.parse(data);
                console.log(json);
            }
            catch (error) {
                console.error(data);
            }
            console.log(json);
            $("#content").append(template(json)).fadeIn();
        },
        error: function() {
            console.log("failed");
        }
    });
}

async function getScoreFields(formData) {
    let result;
    try {
        result = await $.ajax({
            type: 'POST',
            url: './resource/avail_fields/get_score_fields.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false
        });
        return JSON.parse(result);
    } catch (error) {
        console.error(error);
    }
}

ajaxHandler.getAvailableFields = function() {
    const template = Handlebars.compile(source);
    let fields = new Array();
    let classId = $("#class").val();

    let formData = new FormData();
    formData.append("class", classId);
    getScoreFields(formData).then( (data) => {
        // Empty container before applying data
        $("#content").empty();
        if (data.status == 200) {
            $("#content").append(template({ fields: data.data.mon_hoc, lop: classId})).fadeIn();
        } else {
            console.log(data);
        }
    });
}

ajaxHandler.getAvailableFieldsByClass = async function(lop) {
    let result;
    const formData = new FormData();
    formData.append('lop', lop);
    try {
        result = await $.ajax({
            type: 'POST',
            url: './resource/avail_fields/find_by_class.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false
        });
        return JSON.parse(result);
    } catch (error) {
        console.error(error);
    }
}

async function findOneStudent(id) {
    let formData = new FormData();
    formData.append("id", id);
    let result;
    try {
        result = await $.ajax({
            type: 'POST',
            url: './resource/student/find_one_by_id.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false
        });
        return result;
    } catch (error) {
        console.error(error);
        return null;
    }
    
}

ajaxHandler.student = function(id) {
    window.location.assign("./update_student.php?id=" + id);
}

ajaxHandler.getStudent = async function(id) {
    let data = JSON.parse(await findOneStudent(id));

    const template = Handlebars.compile(source);
    // Empty container before applying data
    $("#content").empty();
    $("#content").append(template(data)).fadeIn();
}

ajaxHandler.predictFiveTimes = function() {
    let form = $("form").serializeArray();

    let formData = new FormData();
    let student_id = $("#id").val();
    if (student_id) {
        formData.append("student_id", student_id);
    } else {
        let classId = $("#class").val();
        formData.append("class", classId);
    }

    getScoreFields(formData).then((data) => {
        const template = Handlebars.compile(destin);
        let headers = null;
        if (data.status == 200) {
            header = data.data;

            let json = {};
            $.each(form, function(i, field) {
                if (field.name.match(regex)) {
                    const match = field.name;
                    const key = match.substring(
                        match.lastIndexOf('[') + 1, 
                        match.lastIndexOf(']')
                    );
                    json[key] =  parseFloat(field.value);
                } else {
                    json[field.name] = field.value;
                }
            });

            formData = new FormData();
            formData.append("class", header.lop);
            formData.append("scores", JSON.stringify(json));
            formData.append("n", 5);
            console.log(Array.from(formData.entries()));
            $.ajax({
                type: 'POST',
                url: './resource/student/predict_n_times.php',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    console.log(data);
                    data = JSON.parse(data);
                    for (let key in data.matrix) {
                        data.matrix[key] = sortObject(data.matrix[key]);
                    };

                    let json = {
                        headers: header.mon_hoc.sort(),
                        predictions: data.matrix
                    }
                    $('#destin').empty();
                    $("#destin").append(template(json)).fadeIn();
                    console.log(json);
                },
                error: function() {
                    console.log("failed");
                }
            });
        } else {
            console.log(data);
        }
    });
}

async function getPotential(formData) {
    let result;

    try {
        result = await $.ajax({
            type: 'POST',
            url: './resource/student/get_potential.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false
        });
        console.log(result);
        response = JSON.parse(result);

        if (response.status !== 200) {
            console.log(response);
            return;
        }
        const nk = [];
        const max = Math.max.apply(null, Object.values(response.result));
        for (const [key, value] of Object.entries(response.result)) {
            if (value == max) {
                nk[key] = value;
            }
        }
        return Object.keys(nk).join("-");
    } catch (error) {
        console.error(error);
        return "";
    }
}

ajaxHandler.importFile = async function() {
    const files = document.getElementById('file').files;
    if (files[0]) {
        const template = Handlebars.compile(source);
        const file = files[0];
        let currNk;
        let formData = new FormData();
        formData.append('upload', file);
        let result;
        result = await $.ajax({
            type: 'POST',
            url: './resource/student/import_students.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
        });
        data = JSON.parse(result);

        formData = new FormData();
        let classId = $("#class").val();
        formData.append("lop", classId);
        let fields = await getScoreFields(formData);
        
        header = fields.data;
        console.log(fields);
        for (let i = 0; i < data.length; i++) {
            let elem = data[i];
            let json = {};

            $.each(elem, function(key, field) {
                if (header.mon_hoc.includes(key)) {
                    json[key] = field;
                }
            });
            $.each(json, function(key, value) {
                delete elem[key];
            });
            elem['mon_hoc'] = json;

            formData = new FormData();
            formData.append("lop", header.lop);
            formData.append("scores", JSON.stringify(json));
            let pot;
            pot = await getPotential(formData);
            elem['nang_khieu'] = pot;
            elem['lop'] = classId;
        }
        Share.currentFile = await data;
        $("#content").empty();
        $("#content").append(template({'headers': header, 'ds': Share.currentFile})).fadeIn();
    } else {
        alert('error');
    }
}

async function saveSome(array, overwrite, classId) {
    const formData = new FormData();
    formData.append("students", JSON.stringify(array));
    formData.append("overwrite", overwrite);
    formData.append("class", classId);
    let result;
    try {
        result = await $.ajax({
            type: 'POST',
            url: './resource/student/save_students.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
        });
        try {
            return JSON.parse(result);
        }
        catch {
            return result;
        }
    } catch (error) {
        console.error(error);
    }
}

ajaxHandler.saveStudents = async function() {
    let overwrite = $('#overwrite').val() === "true";
    let classId = $('#class').val();
    console.log(overwrite);
    if (Share.currentFile.length > 0) {
        let result = await saveSome(Share.currentFile, overwrite, classId);
        console.log(result);

    } else {
        console.error("errr");
    }
}

ajaxHandler.navigateLaw = function () {
    let classId = $("#class").val();
    window.location.assign("./show_laws.php?class=" + classId);
}

ajaxHandler.getLawsFromClass = async function (classId) {

    let formData = new FormData();
    formData.append('class', classId);
    try {
        classLaws = await $.ajax({
            type: 'POST',
            url: './resource/class_laws/get_all_by_class.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
        });
        classLaws = JSON.parse(classLaws);
        const template = Handlebars.compile(source);
        console.log(classLaws);
        $("#content").empty();
        $("#content").append(template({ 'classLaws': classLaws})).fadeIn();
    } catch (error) {
        console.error(error);
    }
}

ajaxHandler.saveNewLaws = function(laws, classId, nk) {
    const formData = new FormData();
    formData.append('lop', classId);
    formData.append('bo_luat', JSON.stringify(laws));
    formData.append('nang_khieu', nk);

    $.ajax({
        type: 'POST',
        url: './resource/class_laws/new_class_laws.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            console.log(data);
            window.location.reload();
        },
        error: function() {
            console.log("failed");
        }
    });
}

ajaxHandler.navigateMatrix = function() {
    let classId = $("#class").val();
    window.location.assign("./show_status_matrix.php?class=" + classId);
}

ajaxHandler.getOneStatusMatrix = function (classId) {
    const formData = new FormData();
    formData.append('class', classId);

    $.ajax({
        type: 'POST',
        url: './resource/status_matrix/get_one.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.status == 200 || response.status == 201) {
                    const template = Handlebars.compile(source);
                    console.log(response.body);
                    $("#content").empty();
                    $("#content").append(template(response.body)).fadeIn();
                } else {
                    console.error(response.message);
                    alert(response.message);
                    window.history.back();
                }
            } catch (error) {
                console.error(error);
            }
        },
        error: function() {
            console.log("failed");
        }
    });
        
}

ajaxHandler.upsertStudent = function() {
    const formData = {};
    const inputs = $("#student-form").serializeArray();
    const mh = [];
    $.each(inputs, (i, input) => {
        if (input.name.match(regex)) {
            const match = input.name;
            const obj = {
                key: String,
                score: Number
            };
            const key = match.substring(
                match.lastIndexOf('[') + 1, 
                match.lastIndexOf(']')
                );
            obj.key = key;
            obj.score = parseFloat(input.value)
            mh.push(obj);
        } else {
            formData[input.name] = input.value;
        }
    });
    formData['mon_hoc'] = mh;
    let json = JSON.stringify(formData);
    $.ajax({
        type: 'POST',
        url: './resource/student/save_one.php',
        data: json,
        contentType: 'application/json',
        cache: false,
        processData: false,
        success: function(data) {
            try {
                const url = window.location.href;
                if (url.includes("update_student.php")) {
                    window.location.reload();
                } else {
                    const json = JSON.parse(data);
                    console.log(json);
                    const id = json.data.id.$oid;
                    window.location.assign(`update_student.php?id=${id}`);
                }
                
            } catch (error) {
                console.error(error);
            }
        },
        error: function() {
            console.log("failed");
        }
    });
}

ajaxHandler.saveMatrix = function(id) {
    const matrixHtml = $('#matrix').children();
    const size = matrixHtml.length;
    const inputs = matrixHtml.find(`input`);
    let k = 0;
    let matrix = [];
    for (let i = 0; i < inputs.length / size; i++) {
        let rowSum = 0;
        matrix.push([]);
        for (let j = 0; j < inputs.length / size; j++) {
            const element = parseFloat(inputs[k].value);
            k++;
            if (isNaN(element) || element > 1.0 || element < 0.0) {
                alert(`LỖI\nGiá trị không hợp lệ tại dòng ${i + 1} cột ${j + 1}`);
                return;
            } else {
                rowSum += element;
                matrix[i].push(element);
            }
        }

        if (rowSum != 1.0) {
            alert(`LỖI\nTổng các giá trị tại dòng ${i + 1} phải bằng 1`);
            return;
        }
        
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('ma_tran', JSON.stringify(matrix));
    $.ajax({
        type: 'POST',
        url: './resource/status_matrix/save_one.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
            response = JSON.parse(response);
            console.log(response);
            if (response.status == 200) {
                window.location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            console.log("failed");
        }
    });

    // console.log(matrix);
}

ajaxHandler.saveClass = function(id) {
    const name = $('#lop').val();
    if (name.trim() === '') {
        alert('Tên lớp không hợp lệ');
        return;
    }

    const subjects = $('.subject-checkbox');
    const subArr = [];
    for(let i = 0; i < subjects.length; i++){
        if ($(subjects[i]).prop('checked') == true) {
            subArr.push($(subjects[i]).val());
        }
    }

    if (subArr.length <= 0) {
        alert('Chọn ít nhất 1 môn học');
    } else {
        const formData = new FormData();
        if (id != null) {
            formData.append('id', id);
        }
        formData.append('lop', name);
        formData.append('mon_hoc', JSON.stringify(subArr));

        $.ajax({
            type: 'POST',
            url: './resource/avail_fields/save_one.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                console.log(response);
                response = JSON.parse(response);
                console.log(response);
                if (response.status == 200 || response.status == 201) {
                    getAllClasses().then( (data) => {
                        localStorage.setItem('classes', JSON.stringify(data));
                        window.history.back();
                    });
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                console.log("failed");
            }
        });
    }
}

ajaxHandler.navigateClass = () => {
    const lop = $('#class').val();
    window.location.assign(`./update_class.php?lop=${lop}`);
}

ajaxHandler.insertFieldMapping = (formData) => {
    $.ajax({
        type: 'POST',
        url: './resource/field_mapping/insert_one.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
            try {
                response = JSON.parse(response);
                console.log(response);
                if (response.status == 200 || response.status == 201) {
                    window.location.reload();
                } else {
                    alert(response.message);
                }
            } catch (e) {
                console.error(response);
            }
        },
        error: function() {
            console.log("failed");
        }
    });
}

ajaxHandler.updateFieldMapping = (formData) => {
    $.ajax({
        type: 'POST',
        url: './resource/field_mapping/update_one.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
            try {
                response = JSON.parse(response);
                console.log(response);
                if (response.status == 200 || response.status == 201) {
                    window.location.reload();
                } else {
                    alert(response.message);
                }
            } catch (e) {
                console.error(response);
            }
        },
        error: function() {
            console.log("failed");
        }
    });
}

ajaxHandler.deleteFieldMapping = (formData) => {
    $.ajax({
        type: 'POST',
        url: './resource/field_mapping/delete_one.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
            try {
                response = JSON.parse(response);
                console.log(response);
                if (response.status == 200 || response.status == 201) {
                    window.location.reload();
                } else {
                    alert(response.message);
                }
            } catch (e) {
                console.error(response);
            }
        },
        error: function() {
            console.log("failed");
        }
    });
}