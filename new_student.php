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
    <script src="./bootstrap/js/jquery.validate.min.js"></script>
    <title>New Student</title>
</head>
<body>
    <div class="container mt-5">
        <div class="border border-info rounded p-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Chọn lớp</span>
                </div>
                <select name="class" id="class" class="form-control">
                    <script id="classes_template" type="text/x-handlebars-template">
                        <option hidden selected value>-- Chọn lớp --</option>
                        {{#each classes}}
                            <option value="{{this}}">Lớp {{this}}</option>
                        {{/each}}
                    </script>
                </select>
                <div class="input-group-append">
                    <button id="refresh" class="btn btn-info">Tải lại thông tin</button>
                </div>
            </div>

            <hr>

            <div id="content">
                <script id="content_template" type="text/x-handlebars-template">
                    <form action="#" id="student-form">
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="lop" id="lop" value="{{lop}}">

                        <div class="form-group">
                            <label for="ten">Họ và tên</label>
                            <input class="form-control" type="text" name="ten" id="ten" required>
                        </div>

                        <div class="form-group">
                            <label for="ngay_sinh">Ngày sinh</label>
                            <input class="form-control" type="date" name="ngay_sinh" id="ngay_sinh" required>
                        </div>
                        {{#each fields}}
                            <div class="input-group mt-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{translateKey this}}</span>
                                </div>
                                <input class="form-control" type="text" name="mon_hoc[{{this}}]" id="{{this}}" min="0" max="10" required>
                            </div>
                        {{/each}}
                    </form>
                </script>
            </div>

            <hr>
                
            <div class="row mt-2">
                <div class="col-6">
                    <button class="w-100 btn btn-info" id="get_result">Dự đoán</button>
                </div>
                <div class="col-6">
                    <button class="w-100 btn btn-info" id="save">Lưu</button>
                </div>
            </div>
        </div>

        <div class="table-container smaller rounded border border-info py-2">
            <div id="destin">
                <script id="destination_template" type="text/x-handlebars-template">
                    <table class="table table-striped" style="table-layout: fixed;">
                        <tr>
                            <th></th>
                            {{#each headers}}
                                <th class="text-center">{{translateKey this}}</th>
                            {{/each}}
                        </tr>

                        {{#each predictions}}
                            <tr>
                                <th>N{{@index}}</th>
                                {{#each this}}
                                    <td class="text-center">{{this}}</td>
                                {{/each}}
                            </tr>
                        {{/each}}
                    </table>
                </script>
            </div>
        </div>
    </div>
</body>

<script src="./ajax_handler.js"></script>
<script>
    const template = Handlebars.compile(classestemp);
    const classes = JSON.parse(localStorage.getItem('classes'));
    let vals = classes[0].values;
    $("#class").append(template({'classes': vals})).fadeIn();
    
    $(document).ready(function () {
        $("#class").on("change", () => {
            ajaxHandler.getAvailableFields();
            const lop = $('#class').val();
        });

        $("#refresh").on("click", () => {
            ajaxHandler.getAvailableFields();
            $('#lop').val($('#class').val());
        });

        $("#get_result").on('click', () => {
            ajaxHandler.predictFiveTimes();
        });

        $("#save").on('click', () => {
            ajaxHandler.upsertStudent();
        });
    });
</script>
</html>