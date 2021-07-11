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
    <title>Markov chain</title>
</head>

<body>
    <div class="w-100 pt-5">
        <div class="container m-auto border border-info rounded py-2">
            <div class="input-group">
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

            <div class="">
                <button class="btn btn-info mt-2" id="new_student" onclick="window.location.assign('./new_student.php')">Thêm học sinh</button>
                <button class="btn btn-info mt-2" id="import" onclick="window.location.assign('./import_file.php')">Nhập từ file</button>
                <button class="btn btn-info mt-2" id="laws" onclick="ajaxHandler.navigateLaw()">Bộ luật</button>
                <button class="btn btn-info mt-2" id="matrix" onclick="ajaxHandler.navigateMatrix()">Ma trận chuyển đổi trạng thái</button>
                <button class="btn btn-info mt-2" id="tfields" onclick="window.location.assign('./new_class.php')">Thêm lớp</button>
                <button class="btn btn-info mt-2" id="afields" onclick="ajaxHandler.navigateClass()">Chi tiết lớp</button>
                <button class="btn btn-info mt-2" id="subjs" onclick="window.location.assign('./update_field_mappings.php')">Chỉnh sửa môn học</button>
            </div>
        </div>
    </div>


    <div id="content" class="container table-container border rounded border-info py-2">
        <script id="content_template" type="text/x-handlebars-template">
            {{#if headers}}
                <table class="table table-striped">
                    <tr>
                        <th>Họ và tên</th>
                        <th>Ngày sinh</th>
                        {{#each headers}}
                            <th>{{translateKey this}}</th>
                        {{/each}}
                        <th></th>
                    </tr>

                    {{#each students}}
                        <tr id="{{_id.$oid}}">
                            <td>{{ten}}</td>
                            <td>{{getDateMillis ngay_sinh.$date.$numberLong}}</td>
                            {{#each mon_hoc}}
                                <td>{{score}}</td>
                            {{/each}}
                            <td>
                                <button class="btn btn-info" onclick="ajaxHandler.student('{{_id.$oid}}')">Chi tiết</button>
                                <button class="btn btn-danger" onclick="ajaxHandler.student('{{_id.$oid}}')">Xóa</button>
                            </td>
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
    getAllClasses().then( (data) => {
        localStorage.setItem('classes', JSON.stringify(data));
    });
    $(document).ready(function () {

        $("#find_all").on("click", () => {
            ajaxHandler.findAllStudent();
        });
    });
</script>
</html>