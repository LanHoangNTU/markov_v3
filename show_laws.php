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
    <script src="./bootstrap/js/base64.js"></script>
	<title>Bộ luật</title>
</head>
<body>
	<div id="form">
		<script id="form_template" type="text/x-handlebars-template">
			{{#if availableFiels}}
				{{#if addNew}}
					<div class="form--container">
						<div class="form--body border border-info rounded p-2">
							<div class="row">
								<div class="col-4">Môn học</div>
								<div class="col-3">Toán tử</div>
								<div class="col-3">Điểm</div>
								<!-- <div class="col-3">Thêm vào</div> -->
							</div>
							{{#each laws}}
							<div class="row mt-2">
								<div class="col-4">
									<select id="key-{{@index}}" class="form-control" onchange="changeKey('key-{{@index}}')">
										{{#select this.key}}
											{{#each @root.availableFiels}}
												<option value="{{this}}">{{translateKey this}}</option>
											{{/each}}
										{{/select}}
									</select>
								</div>
								<div class="col-3">
									<select id="operator-{{@index}}" class="form-control" onchange="changeOperator('operator-{{@index}}')">
										{{#select this.operator}}
											<option value=">">&gt;</option>
											<option value=">=">&gt;&equals;</option>
											<option value="<=">&gt;&equals;</option>
											<option value="<">&lt;</option>
											<option value="=">&equals;</option>
										{{/select}}
									</select>
								</div>
								<div class="col-3">
									<input id="score-{{@index}}" class="form-control" step="0.01"
										   type="number" min="0" max="10" value="{{this.score}}"
										   onblur="changeScore('score-{{@index}}')">
								</div>
								<div class="col-2 text-center">
									<button class="btn btn-danger" onclick="removeLaw('{{@index}}')">-</button>
								</div>
							</div>
							{{/each}}
							<hr class="w-100">
							<div class="row">
								<div class="col-8 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Năng khiếu</span>
									</div>
									<select name="nk" id="nk" class="form-control" onchange="changeNK()">
										{{#select nk}}
											{{#each @root.availableFiels}}
												<option value="{{this}}">{{translateKey this}}</option>
											{{/each}}
										{{/select}}
									</select>
								</div>
								<div class="col-2">
									<button class="btn btn-info" id="save" onclick="saveLaws()">Lưu</button>
								</div>
								<div class="col-2 text-center">
									<button class="btn btn-info" id="append" onclick='addLaw()'>+</button>
								</div>
							</div>
						</div>
					</div>
				{{/if}}
			{{/if}}
		</script>
	</div>
	
	<div class="container table-container rounded border border-info rounded">
		<div class="w-100 text-right my-4">
			<button class="btn btn-info" id="add">
				Thêm luật
			</button>
		</div>
		<div id="content">
			<script id="content_template" type="text/x-handlebars-template">
				{{#if classLaws}}
					<table class="table table-bordered mx-auto">
						<thead>
							<tr>
								<th>Bộ luật</th>
								<th>Năng khiếu</th>
							</tr>
						</thead>

						<tbody>
							{{#each classLaws}}
								{{#each bo_luat}}
								<tr>
									<td>{{translateKey key}} {{operator}} {{score}}</td>
									{{#ifEquals '0' @index}}
										<td rowspan="{{../../laws.length}}">{{translateKey ../../nang_khieu}}</td>
									{{/ifEquals}}
								</tr>
								{{/each}}
							{{/each}}
						</tbody>
					</table>
				{{/if}}
			</script>
		</div>
	</div>
</body>
<script src="./ajax_handler.js"></script>
<script>

	const urlParams = new URLSearchParams(window.location.search);
	const template = Handlebars.compile(formtemp);
	const laws = [];
	let classId = urlParams.get('class');
	let availableFiels;
	let addNew = false;
	let nk;

	function addLaw() {
		let law = {
			'key': availableFiels.mon_hoc[0],
			'operator': '=',
			'score': 5.0
		}
		laws.push(law);
		console.log(laws);
		$("#form").empty();
		$("#form").append(template({ 
			'availableFiels': availableFiels.mon_hoc, 
			'laws': laws,
			'addNew': addNew,
			'nk': nk
		})).fadeIn();
	}

	function removeLaw(index) {
		laws.splice(index, 1);
		$("#form").empty();
		$("#form").append(template({ 
			'availableFiels': availableFiels.mon_hoc, 
			'laws': laws,
			'addNew': addNew,
			'nk': nk
		})).fadeIn();
	}

	function changeNK() {
		nk = $('#nk').val();
		console.log(nk);
	}

	function changeKey(id) {
		const value = $(`#${id}`).val();
		console.log(value);
	}

	function changeOperator(id) {
		const value = $(`#${id}`).val();
		console.log(value);
	}

	function changeScore(id) {
		const value = $(`#${id}`).val();
		const index = parseInt(id.substring(id.indexOf('-') + 1));
		const score = parseFloat(value);
		if (!isNaN(score)) {
			if (score > 10) {
				laws[index].score = 10.0;
			} else if (score < 0) {
				laws[index].score = 0.0;
			} else {
				laws[index].score = score;
			}
		} 
		console.log(laws[index].score);
		$(`#${id}`).val(laws[index].score);
	}

	function saveLaws() {
		if (laws.length <= 0) {
			alert('Không hợp lệ');
		} else {
			ajaxHandler.saveNewLaws(laws, classId, nk);
		}
	}


    $(document).ready(async function () {
		async function loadForm(classId) {
			try {
				let formData = new FormData();
				formData.append("class", classId);

				const response = await getScoreFields(formData);
				availableFiels = response.data;
				// console.log(availableFiels);
			} catch (error) {
				console.error(error);
			}
		}

		function toggleAddNew() {
			addNew = !addNew;
			$("#form").empty();
			$("#form").append(template({ 
				'availableFiels': availableFiels.mon_hoc, 
				'laws': laws,
				'addNew': addNew,
				'nk': nk
			})).fadeIn();
		}
        ajaxHandler.getLawsFromClass(classId);

		await loadForm(classId);
		nk = availableFiels.mon_hoc[1];

		$("#add").click(() => {
			toggleAddNew();
		});
    });
</script>
</html>