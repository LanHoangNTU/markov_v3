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
    <script src="./bootstrap/js/jquery.validate.min.js"></script>
    <title>New Student</title>
</head>
<body>
    <select name="class" id="class">
        <option hidden selected value></option>
        <option value="6">Lớp 6</option>
        <option value="7">Lớp 7</option>
    </select>
    <button id="refresh">Refresh</button>

    <div class="row">
        <div class="col-6">
            <div id="content">
                <script id="content_template" type="text/x-handlebars-template">
                    <form action="#">
                        <label for="name">Họ và tên</label>
                        <input type="text" name="name" id="name" required>
                        <br/>
                        <label for="date_of_birth">Ngày sinh</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required>
                        {{#each fields}}
                            <br/>
                            <label for="{{this}}">{{this}}</label>
                            <input type="text" name="{{this}}" id="{{this}}" min="0" max="10" required>
                        {{/each}}
                    </form>
                </script>
            </div>

            <button id="get_result">Dự đoán</button>
            <button id="save">Lưu</button>
        </div>

        <div class="col-6">
            <div id="destin">
                <script id="destination_template" type="text/x-handlebars-template">
                    <table>
                        <tr>
                            <th></th>
                            {{#each headers}}
                                <th>{{this}}</th>
                            {{/each}}
                        </tr>

                        {{#each predictions}}
                            <tr>
                                <td>N{{@index}}</td>
                                {{#each this}}
                                    <td>{{this}}</td>
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
    $(document).ready(function () {
        $("#class").on("change", () => {
            ajaxHandler.getAvailableFields();
        });

        $("#refresh").on("click", () => {
            ajaxHandler.getAvailableFields();
        });

        $("#get_result").on('click', () => {
            ajaxHandler.predictFiveTimes();
        });
    });
</script>
</html>