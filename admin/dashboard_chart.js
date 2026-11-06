document.addEventListener("DOMContentLoaded", function () {

/* USER DISTRIBUTION PIE CHART */

const ctx = document.getElementById('userChart');

new Chart(ctx, {

type: 'pie',

data: {

labels: [
'Medical Stores',
'Clinics',
'NGOs'
],

datasets: [{

label: 'User Distribution',

data: [
medicals,
clinics,
ngos
],

backgroundColor: [
'#2563eb',
'#16a34a',
'#dc2626'
],

borderWidth: 1

}]

},

options: {

responsive: true,

plugins: {

legend: {
position: 'bottom'
},

title: {
display: true,
text: 'User Distribution in EXPIROCHAIN'
}

}

}

});



/* USER VERIFICATION PIE CHART */

const verifyCtx = document.getElementById("verifyChart");

new Chart(verifyCtx, {

type: "pie",

data: {

labels: ["Verified Users", "Unverified Users"],

datasets: [{

data: [
verified,
unverified
],

backgroundColor: [
"#22c55e",
"#ef4444"
]

}]

},

options: {

responsive: true,

plugins: {

legend: {
position: "bottom"
},

title: {
display: true,
text: "User Verification Status"
}

}

}

});

});