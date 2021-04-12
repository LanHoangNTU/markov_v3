let content_template = document.querySelector("#content_template");
let destination_template = document.querySelector("#destination_template");

const source = content_template.innerHTML;
const destin = destination_template ? destination_template.innerHTML : null;
const sortObject = obj => Object.keys(obj).sort().reduce((res, key) => (res[key] = obj[key], res), {})

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

Handlebars.registerHelper('getDate', function (val) { 
    val = new Date(val);
    let date = [
        val.getFullYear(), 
        String(val.getMonth()).padStart(2, 0), 
        String(val.getDate()).padStart(2, 0)
    ];
    return date.join('-');
});

function ajaxHandler() {
}

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
            let json = JSON.parse(data);
            console.log(json);
            $("#content").append(template(json)).fadeIn();
        },
        error: function() {
            console.log("failed");
        }
    });
}

function getScoreFields(formData, callback) {
    $.ajax({
        type: 'POST',
        url: './resource/avail_fields/get_score_fields.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            let json = JSON.parse(data);
            callback(json);
        },
        error: function() {
            console.log("failed");
        }
    });
}

ajaxHandler.getAvailableFields = function() {
    const template = Handlebars.compile(source);
    let fields = new Array();
    let classId = $("#class").val();

    let formData = new FormData();
    formData.append("class", classId);
    getScoreFields(formData, (data) => {
        // Empty container before applying data
        $("#content").empty();
        if (data.status == 200) {
            $("#content").append(template({ fields: data.data.fields})).fadeIn();
        } else {
            console.log(data);
        }
    });
}

function findOneStudent(id, callback) {
    let formData = new FormData();
    formData.append("id", id);

    $.ajax({
        type: 'POST',
        url: './resource/student/find_one_by_id.php',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            callback(JSON.parse(data));
        },
        error: function() {
            console.log("failed");
        }
    });
}

ajaxHandler.student = function(id) {
    findOneStudent(id, function(data) {
        let encode = encodeUnicode(JSON.stringify(data));
        window.location.assign("./update_student.php#" + encode);
    });
}

ajaxHandler.getStudent = function() {
    console.log("hash: " + window.location.hash);
    let data = decodeUnicode(window.location.hash.substring(1));
    data = JSON.parse(data);
    console.log(data);

    const template = Handlebars.compile(source);
    // Empty container before applying data
    $("#content").empty();
    $("#content").append(template(data)).fadeIn();
}

ajaxHandler.predictFiveTimes = function() {
    let form = $("form").serializeArray();
    console.log(form);

    let formData = new FormData();
    let student_id = $("#id").val();
    if (student_id) {
        formData.append("student_id", student_id);
    } else {
        let classId = $("#class").val();
        formData.append("class", classId);
    }

    getScoreFields(formData, (data) => {
        const template = Handlebars.compile(destin);
        let headers = null;
        if (data.status == 200) {
            header = data.data;

            let json = {};
            $.each(form, function(i, field) {
                if (header.fields.includes(field.name)) {
                    json[field.name] = field.value;
                    console.log(field.name + ": " + field.value + "\n");
                }
            });

            console.log(json);

            formData = new FormData();
            formData.append("class", header.class);
            formData.append("scores", JSON.stringify(json));
            formData.append("n", 5);

            $.ajax({
                type: 'POST',
                url: './resource/student/predict_n_times.php',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    data = JSON.parse(data);
                    for (let key in data.matrix) {
                        data.matrix[key] = sortObject(data.matrix[key]);
                    };

                    let json = {
                        headers: header.fields.sort(),
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