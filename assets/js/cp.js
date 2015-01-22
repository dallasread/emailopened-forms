var a = Math.ceil(Math.random() * 10);
var b = Math.ceil(Math.random() * 10);       
var c = a + b
function DrawBotBoot()
{
    document.write("What is "+ a + " + " + b +"? ");
    document.write("<input id='BotBootInput' type='text' maxlength='2' size='2'/>");
}    
function ValidBotBoot(field, el){
    var d = document.getElementById('BotBootInput').value;
	var text = document.getElementById("cap_text").innerHTML;
	var form = el.form;
	
    if (d == c) 
	{
		document.getElementById("cap_text").innerHTML = '<font color="#01DF01">'+d+' is Correct!</font><br /> Please Click "'+field+'"';
		document.getElementById("button_replace").innerHTML = '<input type="submit" value="'+field+'">';
		form.submit();
	} 
	else if (text.indexOf("Incorrect") == -1)
	{
		document.getElementById("cap_text").innerHTML = text+'<font color="#DF0101">Incorrect!</font>';
	}       
    
}