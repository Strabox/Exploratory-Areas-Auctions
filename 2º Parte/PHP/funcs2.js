var now;
var temp;
var nr;
var disabled_counts = [];
function splitter(date){
	return date.split("-");
}
function read_html_time(){
	temp2 = document.getElementById("now").value;
	temp = splitter(temp2);
	now = new Date(temp[0],temp[1]-1,temp[2],temp[3],temp[4],temp[5]);
}
function get_remaining_time(target_time){
	target_time = splitter(target_time);
	target = new Date(target_time[0],target_time[1]-1,target_time[2],target_time[3],target_time[4],target_time[5]);
	diff = target-now;
	if(diff <= 0) return -1;
	diff = new Date(diff);
	var arr = [diff.getYear()-70, diff.getMonth(), diff.getDate()-1, diff.getHours(), diff.getMinutes(), diff.getSeconds()];
	return arr;
}
function print_remaining_time(idleilao){
	target_auc = document.getElementById("dia-" + idleilao).innerHTML;
	target_auc = target_auc + "-23-59-59"; // o leilão termina no fim do dia indicado
	rem = get_remaining_time(target_auc);
	string = "rem-" + idleilao;
	local = document.getElementById(string);
	local.innerHTML = "";
	if(rem[0] != 0){
		local.innerHTML += rem[0]; 
		if(rem[0] == 1) local.innerHTML += " ano ";
		else local.innerHTML += " anos ";
	}
	if(rem[1] != 0){
		local.innerHTML += rem[1];
		if(rem[1] == 1) local.innerHTML += " mês ";
		else local.innerHTML += " meses ";
	}
	if(rem[2] != 0){
		local.innerHTML += rem[2];
		if(rem[2] == 1) local.innerHTML += " dia";
		else local.innerHTML += " dias";
		local.innerHTML += "<br>";
	}
	local.innerHTML += rem[3] + ":";
	local.innerHTML += rem[4] + ":";
	local.innerHTML += rem[5];
}
function update_counts(){
	for(i = 1; i <= nr; i++){
		if(disabled_counts.indexOf(i) != -1) continue;
		still_running = print_remaining_time(i);
		if(!still_running) disabled_counts.push(i);
	}
}
function refresh_time(){
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
	//document.getElementById("curr_time").innerHTML = now.toLocaleDateString()+", "+now.toLocaleTimeString();
}
function clear_checks(){
	checks = document.getElementsByName("choose[]");
	var i;
	for(i = 0; i < checks.length; i++){
		checks[i].checked = false;
	}
}
function print_from_status(leilao){
	estado = document.getElementById("e" + leilao).value;
	if(estado == 1){
		document.getElementById("rem-" + leilao).innerHTML = "Terminado";
		return false;
	}
	print_remaining_time(leilao);
	return true;
}