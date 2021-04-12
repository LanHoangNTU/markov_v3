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
    <title>Update student</title>
</head>
<body>
    <div class="row">
        <div class="col-6">
            <div id="content">
                <script id="content_template" type="text/x-handlebars-template">
                    <form action="#">
                        <input type="hidden" name="id" id="id" value="{{_id.$oid}}">

                        <label for="name">Họ và tên</label>
                        <input type="text" name="name" id="name" value="{{name}}">
                        <label for="name">Ngày sinh</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{getDate date_of_birth.date}}">
                        {{#each subjects}}
                            <br/>
                            <label for="{{this.key}}">{{this.key}}</label>
                            <input type="text" name="{{this.key}}" id="{{this.key}}" value="{{this.score}}">
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
    $(document).ready(function() {
        ajaxHandler.getStudent();

        $("#get_result").on('click', () => {
            ajaxHandler.predictFiveTimes();
        });
    });
</script>
</html>