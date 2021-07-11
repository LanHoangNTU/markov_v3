<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script src="./bootstrap/js/handlebars.js"></script>
    <script src="./bootstrap/js/jquery.validate.min.js"></script>
	<title>Nhập dữ liệu từ file</title>
</head>
<body>
    <div class="container border border-info rounded py-2 mt-5">
        <div class="row">
            <div class="input-group col-6">
                <div class="input-group-prepend">
                    <span class="input-group-text">Chọn lớp</span>
                </div>
                <select name="class" id="class" class="form-control">
                    <script id="classes_template" type="text/x-handlebars-template">
                        {{#each classes}}
                            {{#if (equals '0' @index)}}
                                <option value="{{this}}" selected="selected">Lớp {{this}}</option>
                            {{else}}
                                <option value="{{this}}">Lớp {{this}}</option>
                            {{/if}}
                        {{/each}}
                    </script>
                </select>
                <div class="input-group-append">
                    <button id="find_all" class="btn btn-info">Tìm</button>
                </div>
            </div>
            <div class="col-6 input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id='file' name="file">
                    <label class="custom-file-label" for="file">Chọn tệp...</label>
                </div>
            </div>
        </div>
        
        <div class="row mt-1">
            <div class="input-group col-6">
                <div class="input-group-prepend">
                    <span class="input-group-text">Cách lưu</span>
                </div>
                <select name="overwrite" id="overwrite" class="form-control">
                    <option value="true">Xóa và cập nhật</option>
                    <option value="false">Cập nhật thêm</option>
                </select>
                <div class="input-group-append">
                    <button id="save" class="btn btn-info">Lưu</button>
                </div>
            </div>
            <div class="col-6 text-right">
                <button id="submit_file" class="btn btn-info">Hiển thị tệp</button>
            </div>
        </div>
    </div>
    
	<div id="content" class="container table-container border rounded border-info py-2">
        <script id="content_template" type="text/x-handlebars-template">
        	{{#if headers}}
                <table class="table table-striped">
                    <tr>
                        <th>Họ và tên</th>
                        {{#each ds.0.mon_hoc}}
                            <th>{{translateKey @key}}</th>
                        {{/each}}
                        <th>Năng khiếu</th>
                    </tr>

                    {{#each ds}}
                        <tr>
                            <td>{{ten}}</td>
                            {{#each mon_hoc}}
                                <td>{{getScoreByKey @key ../mon_hoc}}</td>
                            {{/each}}
                            <td>{{translateKey this.nang_khieu}}</td>
                        </tr>
                    {{/each}}
                
                </table>
            {{/if}}
        </script>
    </div>
</body>

<script src="./ajax_handler.js"></script>
<script>
    const template = Handlebars.compile(classestemp);
    const classes = JSON.parse(localStorage.getItem('classes'));
    let vals = classes[0].values;
    $("#class").append(template({'classes': vals})).fadeIn();

    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
    $(document)
    .ready(function () {
        if (Share.currentFile) {
            Share.currentFile = [];
        }
        console.log(Share);

        $("#submit_file").on("click", () => {
            ajaxHandler.importFile();
        });
        $("#save").on("click", () => {
            ajaxHandler.saveStudents();
        });
    });
</script>
</html>