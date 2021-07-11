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
    <title>Chỉnh sửa môn học</title>
</head>
<body>
    <div class="container table-container border rounded border-info py-2">
        <div id="content" class="">
            <script id="content_template" type="text/x-handlebars-template">
                <div id="subject">
                    {{#each subjs}}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class=" input-group-text">{{key}}</span>
                            </div>
                            <input id="{{key}}" type="text" class="form-control" value="{{sub}}">
                            <div class="input-group-append">
                                <button class="btn btn-info" onclick="updateOne('{{key}}')">Lưu</button>
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-danger" onclick="deleteOne('{{key}}')">Xóa</button>
                            </div>
                        </div>
                    {{/each}}
                </div>
            </script>
        </div>
        <hr>
        <form name="new_mapping" id="new_mapping">
            <div class="input-group mb-3">
                <input type="text" name="tag" id="tag" class="input-group-prepend form-control" placeholder="Tag...">
                <input type="text" name="dinh_danh" class="form-control" placeholder="Định danh...">
                <div class="input-group-append">
                    <button class="btn btn-success">Lưu</button>
                </div>
            </div>
        </form>
    </div>
</body>
<script src="./ajax_handler.js"></script>
<script>

    $(document).ready(async function () {
        await shortFieldMapping();
        const template = Handlebars.compile(source);
        const allSubj = JSON.parse(localStorage.getItem('fieldMapping'));
        console.log(allSubj);
        const mapped = [];
        Object.keys(allSubj).forEach(key => {
            obj = {};
            obj['key'] = key;
            obj['sub'] = allSubj[key];
            mapped.push(obj);
        });
        $('#content').append(template({'subjs': mapped})).fadeIn();

        $('#new_mapping').submit((event) => {
            event.preventDefault();
            const form = $('#new_mapping').serializeArray();
            const request = new FormData();
            form.forEach(input => { 
                request.append(input.name, input.value);
            });
            ajaxHandler.insertFieldMapping(request);
        })
    });
    
    function updateOne(tag) {
        const formData = new FormData();
        const value = $(`input#${tag}`).val();
        formData.append('tag', tag);
        formData.append('dinh_danh', value);
        ajaxHandler.updateFieldMapping(formData);
    }

    function deleteOne(tag) {
        const formData = new FormData();
        formData.append('tag', tag);
        ajaxHandler.deleteFieldMapping(formData);
    }
</script>
</html>