//
// Checks/unchecks a group of checkboxes
//
function setCheckBoxes(setForm, setElement, setStatus)
{
    setObject       = document.forms[setForm].elements[setElement];
    setObjectLength = setObject.length;
    
    if(typeof(setObjectLength) == "undefined")
    {
        setObject.checked = setStatus;
    }
    else
    {
        for(i = 0; i < setObjectLength; i++)
        {
            setObject[i].checked = setStatus;
        }
    }
}

//
// Checks/unchecks a checkbox by its id.
//
function toggleCheckBox(boxId)
{
	document.getElementById(boxId).checked = document.getElementById(boxId).checked ? false : true;
}

//
// Displays/Hides a box
//
function toggleBox(id)
{
    if (document.getElementById(id).style.display == "")
    {
        display = "none";
    }
    else
    {
        display = "";
    }
    document.getElementById(id).style.display = display;
}

//
// Disables/Enables a checkbox or radio button
//
function toggleFormBox(id)
{
    if (document.getElementById(id).disabled)
    {
        disabled = false;
    }
    else
    {
        disabled = true;
    }
    document.getElementById(id).disabled = disabled;
}

//
// Lets an admin test a wikistyle before saving it
//
function testWikiStyle()
{
	var nw = window.open('about:blank', 'styletest', 'height=200,width=400,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
	
	nw.document.write("<html>\n<head>\n<title>Test</title>\n<body>\n<span style=\"");
	
	var styleString  = document.editstyle.style_attribs.value;
	var styleAttribs = styleString.split("\n");
	
	for(i = 0; i < styleAttribs.length; i++)
	{
		var styleAttrib = styleAttribs[i].split(':');
		nw.document.write(styleAttrib[0] + ':' + styleAttrib[1] + ';');
	}
	
	nw.document.write("\">" + document.editstyle.style_name.value + "</span>\n</body>\n</html>");
}

//
// Code for switching a style sheet
//
var xfelement = new Array()	

function xfFetchElement(id)
{
	if(xfelement[id]) {
		return xfelement[id]
	}
	
	if (document.getElementById) {
		xfelement[id] = document.getElementById(id)
	} else if (document.all) {
		xfelement[id] = document.all[id]
	} else if (document.layers) {
		xfelement[id] = document.layers[id]
	}
	
	return xfelement[id]
}

function switchCSS(newCSS) {
	xfFetchElement('cssswitch').href = newCSS;
}