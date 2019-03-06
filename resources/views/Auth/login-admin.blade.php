<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Login - {{config("app.name")}}</title>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<style>
		body{
			background-color: #efefef;
			font-family: 'Open Sans', sans-serif;
			font-size: 16px;
			color: #333333;

		}
		.box{
			background-color: #ffffff;
			width: 430px;
			 position: fixed;
			top: 50%;
			left: 50%;
			/* bring your own prefixes */
			transform: translate(-50%, -50%);
		    -webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 3px 1px -2px rgba(0,0,0,0.12), 0 1px 5px 0 rgba(0,0,0,0.2);
		    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 3px 1px -2px rgba(0,0,0,0.12), 0 1px 5px 0 rgba(0,0,0,0.2);
		}
		.header{
			background-color:  #d50000;
			padding: 40px 40px;
			position: relative;
		}
		.body{
			padding: 50px 40px 30px 40px;
		}
		h1{
			text-align: left;
			margin: 0px;
			font-size: 28px;
			color: #ffffff;
			font-weight: 300;
			text-transform: uppercase;

		}
		label{
			font-size: 14px;
			font-weight: 400;
			margin-bottom: 10px;
			display: block;
		}
		input[type="email"],input[type="password"],input[type="email"]:hover,input[type="email"]:focus{
			width: 100%;
			border: 1px solid #dddfe2;
			font-size: 18px;
			outline: none;
			padding: 10px 17px;
			box-sizing: border-box;
			font-weight: normal;
			border-radius: 20px;
			font-size: 16px;
			font-weight: 300;
			color: #a09d9d;
		}
		.btn-div{
			text-align: center;
			margin-top: 30px;
		}
		button{
			background-color: #d50000;
			color: #ffffff;
			outline: none;
			padding: 12px 40px;
			border: none;
			font-size: 18px;
			margin-top: 20px;
			border-radius: 20px;
			font-weight: 300;
			font-size: 14px;
		}
		.footer{
			margin-top: 30px;
		}
		.footer ul{
			list-style: none;
			text-transform: uppercase;
			font-size: 14px;
			color: #cccccc;
			padding: 0px;
			margin: 0px;
		}
		.footer ul:after{
			content: "";
			display: table;
			clear: both;
		}
		.footer ul li{
			display: inline-block;
			width: 50%;
			float: left;
			text-align: center;
		}
		.footer ul li a{
			color: #cccccc;
		}
		.footer ul li a:hover{
			color: #d50000;
		}
		.footer>ul>li>div, .footer label{
			display: inline-block;
		}
		.keep_sign-in{
			position: relative;
		}
		input[type="checkbox"]{
			opacity: 0;
		}
		.keep_sign-in label{
			position: absolute;
			max-width: 256px;
			max-height: 256px;
			max-width: 256px;max-height: 256px;
			background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAAGXcA1uAAAACXBIWXMAAA3XAAAN1wFCKJt4AAAF8WlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDAgNzkuMTYwNDUxLCAyMDE3LzA1LzA2LTAxOjA4OjIxICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgKE1hY2ludG9zaCkiIHhtcDpDcmVhdGVEYXRlPSIyMDE4LTA1LTE3VDE2OjM5OjQxKzA3OjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAxOC0wNS0xN1QxNjo0MTo1MSswNzowMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAxOC0wNS0xN1QxNjo0MTo1MSswNzowMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5YmRiNjFmYy1lZjQ3LTRkMmQtODRlMy04NGU5NzU3MjZhOTQiIHhtcE1NOkRvY3VtZW50SUQ9ImFkb2JlOmRvY2lkOnBob3Rvc2hvcDo1ODUwMmFkYy05ZThjLTE3NDgtOTRhMi0zYTIyNWI5ZWQ3MTAiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpkMGI4ZjFkZS05YTM0LTQxYjktODdmYi01NDA0YjVmNGMxYzQiPiA8eG1wTU06SGlzdG9yeT4gPHJkZjpTZXE+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJjcmVhdGVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOmQwYjhmMWRlLTlhMzQtNDFiOS04N2ZiLTU0MDRiNWY0YzFjNCIgc3RFdnQ6d2hlbj0iMjAxOC0wNS0xN1QxNjozOTo0MSswNzowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIENDIChNYWNpbnRvc2gpIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJzYXZlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDo5YmRiNjFmYy1lZjQ3LTRkMmQtODRlMy04NGU5NzU3MjZhOTQiIHN0RXZ0OndoZW49IjIwMTgtMDUtMTdUMTY6NDE6NTErMDc6MDAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCBDQyAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz58/GwuAAACnElEQVRIia2VT4sVVxDFf+feZpQQhHGh4sK4SASFMZPgxrjzG7jKOovssnMTyGY2fhe/g+AqOu4mzIALnT++JxgRRBBCQt7rOln07Xm35/WMDuRA0X27btWpqlt1W68mk/U2tAWQ2tCWQISV6JGE9g6mLks3Alkgs5JaJdTp/tXewdQCMcS5pl71jojm40BxaJbmaO/gdYCHrswvjfAucG3wXTgBTVngsBwdUwIaiw0MJJmsnV7xNEgbiCQAe43eTXYARBeZMKC9g9cGzwR/Mo7N8lwx3GvACKbA14fRRlPoZ533jvwL4K++7IPCkOeQ50EWZN0omf1Tb3zWliNLhGTshflzOlUseS5JukuyxmKVbB4YfkwOG04UAO3uT7ck1o+pUM2y26S40UisCyZLUSywCdj47jzSZp/D1U/6h0fGa2lM6WhwNJf7RimYAWeWDCz6c3gDXKqMhgbG3yxRZb0Ffign/2VtIIKLQDdFtSRtAV/RtU5KAK2SI+ffHb5ZEqzxN10VA2CQg5K2HV7tjXT4fc6SQWkLlPRh4GTxujDoN38Ci+ZzdQecBJtt7e5P/5D49nMMTomPNr+m3rnghUD/g5wXPAbOSfxWz9toGy6lHb3JrL8ajmJWBI606rEE1kJKG2dg1WF6qTA/NcEIWrI+kJWAm0BNcjKBo7lisePuVgTofzJjCLJ2yNoA7pTytcCtol+phlMvWiVFjmmQ1gh/Z3G9VaJVGs7fUIzZIPGObg5M6WmoLlXh93WskfMTgNS2l5T09mjoI0m9LCNJTXBs3bOD7KB37mgu13otyaCjljOoHY9Baf7muGBGUBPoHfiC4XZ2PBzZ/D1wls+8Huh+OSVboclksjprdV/Sz+ALp4jyBMi2t5vsn/4DzNwyWrAd2SQAAAAASUVORK5CYII=) no-repeat;
			width: 16px;
			height: 16px;
			background-size: 100%;
			top: 0px;
			left: 0px;
		}
		input[type="checkbox"]:checked + label {
		 	background: url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDQ1OSA0NTkiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ1OSA0NTk7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8ZyBpZD0iY2hlY2stYm94LW91dGxpbmUiPgoJCTxwYXRoIGQ9Ik0xMjQuOTUsMTgxLjA1bC0zNS43LDM1LjdMMjA0LDMzMS41bDI1NS0yNTVsLTM1LjctMzUuN0wyMDQsMjYwLjFMMTI0Ljk1LDE4MS4wNXogTTQwOCw0MDhINTFWNTFoMjU1VjBINTEgICAgQzIyLjk1LDAsMCwyMi45NSwwLDUxdjM1N2MwLDI4LjA1LDIyLjk1LDUxLDUxLDUxaDM1N2MyOC4wNSwwLDUxLTIyLjk1LDUxLTUxVjIwNGgtNTFWNDA4eiIgZmlsbD0iI0Q4MDAyNyIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=) no-repeat;
		 	background-size: 100%;
		}
		.avatar{
			width: 100px;
			height: 100px;
			border-radius: 50%;
			background-color: #ffffff;
			position: absolute;
			right: 20px;
			bottom: -50px;
			border: solid 3px #d50000;
		}
		.avatar img{
			width: 100px;
			height: 100px;
			border-radius: 50%;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="box">
					<div class="header">
						<h1>SIGN IN</h1>
						<div class="avatar">
							<img src="data:image/png;base64,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">
						</div>
					</div>
					<div class="body">
					<form action="{{url('/login_admin')}}" method="POST" role="form">
					@if($errors->has('errorlogin'))
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							{{$errors->first('errorlogin')}}
						</div>
					@endif
					<div class="form-group">
						<label>Email</label>
						<input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" autocomplete="off">
					</div>
					<div class="form-group">
						<label for="">Password</label>
						<input type="password" class="form-control" id="password" name="password">
					</div>
					@if($errors->has('email'))
							<p style="color:red; font-size:14px;">{{$errors->first('email')}}</p>
					@endif
					@if($errors->has('password'))
						<p style="color:red; font-size:14px;"">{{$errors->first('password')}}</p>
					@endif
					{!! csrf_field() !!}
					<div class="btn-div">
					<button type="submit" class="btn btn-primary">SIGN IN</button>
					</div>
					<div class="footer">
						<ul>
							<li>
								<div class="keep_sign-in">
									<input id="keep_sign-in" type="checkbox" name="remember"> 
									<label for="keep_sign-in"></label>
								</div>
								<label>Keep me sign in</label>
							</li>
							<li>
								<a href="#"><label>Forget Password?</label></a>
							</li>
						</ul>
					</div>
					</form>
					</div>
			</div>
		</div>
	</div>
</body>
</html>