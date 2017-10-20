var ONE_UNIX_DAY = 86400;
var now;
var temp;

function splitter(date){
	return date.split("-");
}
function read_server_time(){
	temp2 = document.getElementById("now").value;
	temp = splitter(temp2);
	now = new Date(temp[0],temp[1]-1,temp[2],temp[3],temp[4],temp[5]);
	console.log("read server time: " + now);
}
function clear_checks(){
	checks = document.getElementsByName("choose[]");
	var i;
	for(i = 0; i < checks.length; i++){
		checks[i].checked = false;
	}
	console.log("cleared all checkboxes");
}
function print_remaining_time(idleilao){
	estado = document.getElementById("e" + idleilao).value;
	output = document.getElementById("rem-" + idleilao);
	output.innerHTML = "";
	if(estado == 1){
		output.innerHTML = "Terminado";
		return;
	}
	data_inicio = document.getElementById("dia-" + idleilao).innerHTML;
	data_inicio = splitter(data_inicio);
	data_inicio = (new Date(data_inicio)).getTime() / 1000; // data unix do início do leilão - em JS vem em milisegundos -_- (mas aqui já está em segundos!)
	
	nrdias = document.getElementById("nr" + idleilao).value;
	data_fim = data_inicio + nrdias * ONE_UNIX_DAY;  // data unix do fim do leilão
	
	diff = new Date(data_fim * 1000 - now.getTime()); // diferença entre hoje e a data de encerramento
	var y = diff.getYear() - 70;
	var	m = diff.getMonth();
	var d = diff.getDate() - 1;
	var h = diff.getHours();
	var min = diff.getMinutes();
	var s = diff.getSeconds();
	
	if(y != 0){
		output.innerHTML += y;
		if(y == 1) output.innerHTML += " ano ";
		else output.innerHTML += " anos ";
	}
	if(m != 0){
		output.innerHTML += m;
		if(m == 1) output.innerHTML += " mês ";
		else output.innerHTML += " meses ";
	}
	if(d != 0){
		output.innerHTML += d;
		if(d == 1) output.innerHTML += " dia";
		else output.innerHTML += " dias";
		output.innerHTML += "<br>";
	}
	output.innerHTML += h + ":" + min + ":" + s;
}
function update_times(nrleiloes){
	var i;
	for(i = 1; i <= nrleiloes; i++){
		print_remaining_time(i);
	}
}

function refresh_time(){ // para actualizar a "hora do servidor" localmente
	temp[5]++;
	if(temp[5] >= 60){
		temp[5] = 0;
		temp[4]++;
	}
	if(temp[4] >= 60){
		temp[4] = 0;
		temp[3]++;
	}
	if(temp[3] >= 24){
		temp[3] = 0;
		temp[2]++;
	}
	if((temp[1] == 1 || temp[1] == 3 || temp[1] == 5 || temp[1] == 7 || temp[1] == 8 || temp[1] == 10 || temp[1] == 12) && temp[2] >= 32){ // meses 31 dias
		temp[2] = 0;
		temp[1]++;
	}else if((temp[1] == 4 || temp[1] == 6 || temp[1] == 9 || temp[1] == 11) && temp[2] >= 31){ // meses 30 dias
		temp[2] = 0;
		temp[1]++;
	}else if(temp[1] == 2){ // fevereiro
		if(temp[0] % 4 == 0 && temp[2] == 30){ // ano bissexto
			temp[2] = 0;
			temp[1]++;
		}else if(temp[2] == 29){ // ano comum
			temp[2] = 0;
			temp[1]++;
		}
	}
	if(temp[1] >= 13){
		temp[1] = 0;
		temp[0]++;
	}
	now = new Date(temp[0],temp[1]-1,temp[2],temp[3],temp[4],temp[5]);
}