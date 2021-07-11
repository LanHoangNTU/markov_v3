<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script src="./bootstrap/js/handlebars.js"></script>
    <script src="./bootstrap/js/base64.js"></script>
    <title>Cập nhật lớp</title>
</head>
<body>
<div class="container table-container border rounded border-info py-2">
        <div class="form-group">
            <label for="lop">Tên lớp</label>
            <input id='lop' class="form-control" type="text" name="lop">
        </div>
        <div class="row">
            <div class="d-flex col-6">
                <h6 for="mon_hoc" style="align-self: flex-end;">Các môn học</h6>
            </div>
            <div class="col-6 text-right">
                <button class="btn btn-info" id="save">Lưu</button>
            </div>
        </div>

        <div id="content" class="border border-secondary rounded h-75 mt-2 p-2">
            <script id="content_template" type="text/x-handlebars-template">
                <div id="subject">
                    {{#each subjs}}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{#if picked}}
                                        <input type="checkbox" class="subject-checkbox" name='subject' value="{{key}}" checked>
                                    {{else}}
                                        <input type="checkbox" class="subject-checkbox" name='subject' value="{{key}}">
                                    {{/if}}
                                </div>
                            </div>
                            <span type="text" class="form-control">{{sub}}</span>
                        </div>
                    {{/each}}
                </div>
            </script>
        </div>
    </div>
</body>
<script src="./ajax_handler.js"></script>
<script>
	const template = Handlebars.compile(source);
    const allSubj = JSON.parse(localStorage.getItem('fieldMapping'));
    const mapped = [];
    const urlParams = new URLSearchParams(window.location.search);
    let lop = urlParams.get('lop');
    $('#lop').val(lop);
    ajaxHandler.getAvailableFieldsByClass(lop)
    .then( (response) => { 
        let mh = response.data.mon_hoc;
        let id = response.data._id.$oid;
        const keys = Object.keys(allSubj);
        for (let i = 0; i < keys.length; i++) {
            const key = keys[i];
            obj = {};
            obj['key'] = key;
            obj['sub'] = allSubj[key];
            obj['picked'] = mh.includes(key);
            mapped.push(obj);
        }

        $('#content').append(template({'subjs': mapped})).fadeIn();

        $("#save").on("click", () => {
            ajaxHandler.saveClass(id);
        });
    });
</script>
</html>