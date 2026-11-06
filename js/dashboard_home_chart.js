document.addEventListener("DOMContentLoaded", function () {

/* CATEGORY PIE CHART */

const categoryCanvas = document.getElementById("categoryChart");

if(categoryCanvas){

const ctx1 = categoryCanvas.getContext("2d");

new Chart(ctx1, {

type: "pie",

data: {

labels: [
"Medicines",
"Cosmetics",
"Others"
],

datasets: [{

data: [
medicines,
cosmetics,
others
],

backgroundColor: [
"#2563eb",
"#16a34a",
"#f59e0b"
]

}]

},

options: {

responsive: true,

maintainAspectRatio:false,

plugins: {

legend: {
position: "bottom"
},

title: {
display: true,
text: "Product Categories"
}

}

}

});

}


/* INVENTORY BAR CHART */

const inventoryCanvas = document.getElementById("inventoryChart");

if(inventoryCanvas){

const ctx2 = inventoryCanvas.getContext("2d");

new Chart(ctx2, {

type: "bar",

data: {

labels: [
"Products",
"Stock"
],

datasets: [{

label: "Inventory",

data: [
products,
stock
],

backgroundColor: [
"#2563eb",
"#10b981"
],

borderRadius: 6

}]

},

options: {

responsive: true,

maintainAspectRatio:false,

plugins: {

legend: {
display: false
},

title: {
display: true,
text: "Inventory Overview"
}

},

scales: {
y: {
beginAtZero: true
}
}

}

});

}

});