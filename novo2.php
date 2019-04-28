<style type="text/css">
	
	.text-tiny {
    	min-width: 0;
    	width: 85px;
    	display: inline;
	}

</style>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="jquery-3.3.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="resources/css/estilo.css">
<link rel="stylesheet" type="text/css"   href="resources/bootstrap/css/bootstrap.css">
<script type="text/javascript" scr="resources/js/jquery-3.1.0.min.js"> </script>
<script type="text/javascript" scr="resources/bootstrap/js/bootstrap.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>

<script type="text/javascript">

	$(function () {
		$("#addVar").on("click", function(){
			var clone = $("#var").clone();
			$("#trem").append(clone);
		});

		$("#addVar").on("click", function(){
			var clone = $("#var").clone();
			$("#trem").append(clone);
		});

		var url = window.location.href;  
		var urlGet = url.split("?");
		urlGet = urlGet[1].split("&");

		var v = urlGet[0];

		v = v.split("=");
		v = v[1];

		var r = urlGet[1];
		r = r.split("=");
		r = r[1];

		var i = 0;
		for (i = 1; i <= v; i++) {
			if (i != v) {
	  			$("#var").append("Var " + i + "<input type='text' class='text-tiny' name='variavel[var" + i + "]'> + ");
			} else {
				$("#var").append("Var " + i + "<input type='text' class='text-tiny' name='variavel[var" + i + "]'>");
			}
		}

		var i = 0;
		var j = 0;
		for (i = 1; i <= r; i++) {
			for (j = 1; j <= v; j++) {
	  			$("#restr").append("Var " + i + "<input class='text-tiny' type='text' name='restr[" + i + "][vars][" + j + "]'>");	
			}
			$("#restr").append(" >= <input class='text-tiny' type='text' name='restr[" + i + "][total][" + i + "]'>");
			$("#restr").append("<br><br>");
		}
		
				
	});


</script>
<div class="container">
	<form action="/TutorialPesquisaOperacional/novo3.php" type="GET">
		<label for="exampleSelect1">Escolhe um ae: </label>
    	<select class="custom-select col-sm" id="exampleSelect1">
			<option value="max">Minimizar</option>
			<option value="min">Maximizar</option>
		</select>
		<div class="container">
			<div class="form-group">
			  	Variaveis: <br>
				<div id="var">
				</div>
			  	Restrições: <br>
			  	<div id="restr">
			  	</div>
			</div>
			x,y != 0 <br>
			<button type="submit" class="btn btn-primary">SHAZAM</button>
		</div>
	</form>
</div>