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