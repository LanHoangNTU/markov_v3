<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script src="./bootstrap/js/handlebars.js"></script>
    <script src="./bootstrap/js/base64.js"></script>
    <title>Markov chain</title>
</head>

<body>
    <label for="class">Chọn lớp</label>
    <select name="class" id="class">
        <option value="6">Lớp 6</option>
        <option value="7">Lớp 7</option>
    </select>

    <button id="find_all">Tìm</button>
    <button id="new_student" onclick="window.location.assign('./new_student.php')">Thêm học sinh</button>

    <div id="content">
        <script id="content_template" type="text/x-handlebars-template">
            {{#if headers}}
                <table class="table table-striped">
                    <tr>
                        <th>OID</th>
                        <th>Họ và tên</th>
                        {{#each headers}}
                            <th>{{this}}</th>
                        {{/each}}
                        <th></th>
                    </tr>

                    {{#each students}}
                        <tr id="{{_id.$oid}}">
                            <td>{{_id.$oid}}</td>
                            <td>{{name}}</td>

                            {{#each subjects}}
                                <td>{{score}}</td>
                            {{/each}}
                            <td>
                                <p onclick="ajaxHandler.student('{{_id.$oid}}')">a</p>
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
    $(document).ready(function () {
        $("#find_all").on("click", () => {
            ajaxHandler.findAllStudent();
        });
    });
</script>
</html>