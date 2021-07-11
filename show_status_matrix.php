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
    <title>Ma trận chuyển đổi trạng thái</title>
</head>
<body>
    <div class="container mt-5">
        <div id="content">
            <script id="content_template" type="text/x-handlebars-template">
                {{#if mon_hoc}}
                <table class="table table-bordered" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th></th>
                        {{#each mon_hoc}}
                            <th>{{translateKey this}}</th>
                        {{/each}}
                        </tr>
                    </thead>

                    <tbody id="matrix">
                    {{#each ma_tran.ma_tran}}
                        <tr id="{{@index}}">
                            <th>{{translateKey (getScoreByKey @index @root.mon_hoc)}}</th>
                            {{#each this}}
                                <td>
                                    <input id="{{@index}}" class="form-control" type="number" value="{{this}}" min="0" max="1" step="0.01">
                                </td>
                            {{/each}}
                        </tr>
                    {{/each}}
                    </tbody>
                </table>
                {{/if}}
                <button class="btn btn-info" id="save" onclick="save('{{ma_tran._id.$oid}}')">Lưu</button>
            </script>
        </div>
    </div>
    
</body>

<script src="./ajax_handler.js"></script>
<script>
	const urlParams = new URLSearchParams(window.location.search);
	let classId = urlParams.get('class');
    ajaxHandler.getOneStatusMatrix(classId);
    function save(id) {
        ajaxHandler.saveMatrix(id);
    }
</script>
</html>