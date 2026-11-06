/* OPEN POPUP */

function openExportPopup(){
document.getElementById("exportPopup").style.display="flex";
}

/* CLOSE POPUP */

function closeExportPopup(){
document.getElementById("exportPopup").style.display="none";
}


/* CONFIRM EXPORT */

function confirmExport(){

let selected=document.querySelector('input[name="exportType"]:checked');

if(!selected){
alert("Please select export format");
return;
}

let type=selected.value;

closeExportPopup();

/* SAFETY CHECK */

if(typeof exportTable === "undefined" || exportTable === null){
alert("Table not initialized");
return;
}


/* DATATABLE EXPORT */

if(type==="csv"){
exportTable.button('.buttons-csv').trigger();
}

else if(type==="excel"){
exportTable.button('.buttons-excel').trigger();
}

else if(type==="pdf"){
exportTable.button('.buttons-pdf').trigger();
}

else if(type==="word"){
exportWord();
}

else if(type==="json"){
exportJSON();
}

else if(type==="txt"){
exportTXT();
}

}


/* WORD EXPORT */

function exportWord(){

let table=document.getElementById("productTable");

downloadFile(
table.outerHTML,
"products.doc",
"application/msword"
);

}


/* JSON EXPORT */

function exportJSON(){

let data=exportTable.rows().data().toArray();

downloadFile(
JSON.stringify(data,null,2),
"products.json",
"application/json"
);

}


/* TXT EXPORT */

function exportTXT(){

let data=exportTable.rows().data().toArray();

let txt=data.map(r=>r.join(" | ")).join("\n");

downloadFile(txt,"products.txt","text/plain");

}


/* DOWNLOAD FILE */

function downloadFile(content,fileName,type){

let blob=new Blob([content],{type:type});

let link=document.createElement("a");

link.href=URL.createObjectURL(blob);
link.download=fileName;

document.body.appendChild(link);
link.click();
document.body.removeChild(link);

}