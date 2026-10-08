const KEY="familyCareGitHubV1";
const defaultData={
 family:[
  {id:1,name:"Pranav",relation:"Father",age:50,contact:"9876543210"},
  {id:2,name:"Tanmay",relation:"Brother",age:21,contact:"9876543210"},
  {id:3,name:"Veda",relation:"Sister",age:15,contact:"9876543210"},
  {id:4,name:"Radhika",relation:"Mother",age:45,contact:"9876543210"}
 ],
 medicines:[
  {id:1,member:"Pranav",name:"Crocin",time:"20:25",frequency:"Twice daily",status:"Pending"},
  {id:2,member:"Radhika",name:"Dolo",time:"03:45",frequency:"Once daily",status:"Taken"}
 ],
 appointments:[{id:1,member:"Pranav",doctor:"National",date:"2026-10-10",time:"01:24",place:"Maruti Mandir",notes:""}],
 tasks:[],
 reports:[
  {id:1,type:"General Health Report",patient:"Pranav",date:"2026-10-06",doctor:"Dr. Deshmukh",hospital:"National Hospital"},
  {id:2,type:"Blood Test Report",patient:"Siddhi",date:"2026-10-06",doctor:"Dr. Deshmukh",hospital:"City Care Hospital"},
  {id:3,type:"Health Checkup Report",patient:"Siddhi",date:"2026-10-06",doctor:"Dr. Kulkarni",hospital:"Life Care Hospital"},
  {id:4,type:"General Checkup",patient:"Family Member",date:"2026-10-04",doctor:"Doctor Name",hospital:"Hospital Name"}
 ],
 feedback:[],
 profile:{name:"Family User",email:"family@example.com",mobile:"",address:""}
};
let data=JSON.parse(localStorage.getItem(KEY)||"null")||structuredClone(defaultData);
function save(){localStorage.setItem(KEY,JSON.stringify(data))}
function id(){return Date.now()+Math.floor(Math.random()*1000)}
function esc(v){return String(v??"").replace(/[&<>"']/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[c]))}
function flash(msg,error=false){const el=document.getElementById("flash");el.innerHTML=`<div class="flash ${error?"error":""}">${esc(msg)}</div>`;setTimeout(()=>el.innerHTML="",2200)}
function setPage(title,html){document.getElementById("app").innerHTML=html;document.title=title;document.querySelectorAll("nav a").forEach(a=>a.classList.toggle("active",a.getAttribute("href")===location.hash))}
function nav() {
    let hash = location.hash.slice(1);
    let page = hash.split("?")[0] || "dashboard";

    const pages = {
        dashboard: dashboard,
        family: family,
        medicines: medicines,
        appointments: appointments,
        tasks: tasks,
        history: history,
        reports: reports,
        disease: disease,
        notifications: notifications,
        feedback: feedback,
        profile: profile
    };

    if (pages[page]) {
        pages[page]();
    } else {
        dashboard();
    }
}

function dashboard(){
 const features=[
 ["👨‍👩‍👧‍👦","Family Members","Manage family members","family","f1"],
 ["💊","Medicine Reminders","Add medicines and track status","medicines","f2"],
 ["🗓️","Doctor Appointments","Manage doctor visits","appointments","f3"],
 ["⏳","Daily Care Tasks","Manage family care tasks","tasks","f4"],
 ["📋","Medicine History","View medicine records","history","f5"],
 ["🩺","Medical Reports","Health and checkup reports","reports","f6"],
 ["🔎💊","Disease Search","Search general care information","disease","f7"],
 ["🔔","Notifications","Pending medicines and care alerts","notifications","f8"],
 ["👤","My Profile","Edit your profile","profile","f9"],
 ["💬","Feedback","Send application feedback","feedback","f10"]
 ];
 setPage("Dashboard - Family Care",`
 <section class="hero"><div><div class="eyebrow">WELCOME TO FAMILY CARE</div><h1>🏠 Family Medicine Tracker</h1><p class="muted">Manage your family's medicines, health records, appointments and daily care from one dashboard.</p></div><a class="btn primary" href="#notifications">🔔 Notifications</a></section>
 <section class="stats">
 <div class="stat"><span>👨‍👩‍👧‍👦</span><b>${data.family.length}</b><small>Family Members</small></div>
 <div class="stat"><span>💊</span><b>${data.medicines.length}</b><small>Total Medicines</small></div>
 <div class="stat"><span>🗓️</span><b>${data.appointments.length}</b><small>Appointments</small></div>
 <div class="stat"><span>⏳</span><b>${data.tasks.filter(x=>x.status==="Pending").length}</b><small>Pending Care Tasks</small></div>
 </section>
 <section class="card"><h2>✨ All Features</h2><p class="muted">All major project features are available directly from the dashboard.</p><div class="feature-grid">
 ${features.map((f,i)=>`<a class="feature ${f[4]}" href="#${f[3]}"><span class="icon">${f[0]}</span><b>${f[1]}</b><small>${f[2]}</small></a>`).join("")}
 </div></section>
 <section class="grid2"><div class="card"><h2>⚡ Quick Actions</h2><div class="quick"><a href="#medicines">💊 Add Medicine</a><a href="#family">👨‍👩‍👧 Add Family Member</a><a href="#appointments">🗓️ Add Appointment</a><a href="#tasks">⏳ Add Care Task</a></div></div>
 <div class="card"><h2>❤️ Family Care Status</h2><ul><li>Medicine reminders can be tracked</li><li>Appointments are organized</li><li>Medical reports are available</li><li>Daily care tasks are manageable</li></ul></div></section>`);
}

function family(){
 setPage("Family Members",`<h1>👨‍👩‍👧 Family Members</h1>
 <section class="card"><h2>➕ Add Family Member</h2><form id="familyForm" class="formgrid">
 <input name="name" placeholder="Name" required><input name="relation" placeholder="Relation" required><input name="age" type="number" placeholder="Age" required><input name="contact" placeholder="Emergency Contact"><button class="btn primary">Add Member</button></form></section>
 <section class="card"><h2>Family Members List</h2>${data.family.length?`<div class="tablewrap"><table><tr><th>Name</th><th>Relation</th><th>Age</th><th>Emergency Contact</th><th>Action</th></tr>${data.family.map(x=>`<tr><td>${esc(x.name)}</td><td>${esc(x.relation)}</td><td>${esc(x.age)}</td><td>${esc(x.contact)}</td><td><button class="btn danger" onclick="delFamily(${x.id})">Delete</button></td></tr>`).join("")}</table></div>`:`<div class="empty">No family members.</div>`}</section>`);
 document.getElementById("familyForm").onsubmit=e=>{e.preventDefault();let f=new FormData(e.target);data.family.push({id:id(),name:f.get("name"),relation:f.get("relation"),age:f.get("age"),contact:f.get("contact")});save();flash("Family member added.");family()}
}
function delFamily(i){data.family=data.family.filter(x=>x.id!==i);save();family()}

function memberOptions(){return data.family.map(x=>`<option>${esc(x.name)}</option>`).join("")}
function medicines(){
 setPage("Medicine Reminders",`<h1>💊 Medicine Reminders</h1><section class="card"><h2>Add Reminder</h2><form id="medForm" class="formgrid">
 <select name="member">${memberOptions()}</select><input name="name" placeholder="Medicine Name" required><input name="time" type="time" required><select name="frequency"><option>Once daily</option><option>Twice daily</option><option>Thrice daily</option><option>As needed</option></select><button class="btn primary">Save Reminder</button></form></section>
 <section class="card"><h2>Medicine List</h2><div class="tablewrap"><table><tr><th>Member</th><th>Medicine</th><th>Time</th><th>Frequency</th><th>Status</th><th>Action</th></tr>${data.medicines.map(x=>`<tr><td>${esc(x.member)}</td><td>${esc(x.name)}</td><td>${esc(x.time)}</td><td>${esc(x.frequency)}</td><td><span class="badge ${x.status==="Taken"?"taken":""}">${esc(x.status)}</span></td><td><button class="btn light" onclick="medStatus(${x.id},'Taken')">Taken</button> <button class="btn light" onclick="medStatus(${x.id},'Skipped')">Skip</button> <button class="btn danger" onclick="delMed(${x.id})">Delete</button></td></tr>`).join("")}</table></div></section>`);
 document.getElementById("medForm").onsubmit=e=>{e.preventDefault();let f=new FormData(e.target);data.medicines.push({id:id(),member:f.get("member"),name:f.get("name"),time:f.get("time"),frequency:f.get("frequency"),status:"Pending"});save();flash("Medicine reminder saved.");medicines()}
}
function medStatus(i,s){let x=data.medicines.find(x=>x.id===i);if(x)x.status=s;save();medicines()}
function delMed(i){data.medicines=data.medicines.filter(x=>x.id!==i);save();medicines()}

function appointments(){
 setPage("Appointments",`<h1>🗓️ Doctor Appointments</h1><section class="card"><h2>Add Appointment</h2><form id="appForm" class="formgrid"><select name="member">${memberOptions()}</select><input name="doctor" placeholder="Doctor / Clinic" required><input name="date" type="date" required><input name="time" type="time" required><input name="place" placeholder="Place"><button class="btn primary">Save Appointment</button></form></section>
 <section class="card"><h2>Appointment List</h2><div class="tablewrap"><table><tr><th>Member</th><th>Doctor/Clinic</th><th>Date</th><th>Time</th><th>Place</th><th>Action</th></tr>${data.appointments.map(x=>`<tr><td>${esc(x.member)}</td><td>${esc(x.doctor)}</td><td>${esc(x.date)}</td><td>${esc(x.time)}</td><td>${esc(x.place)}</td><td><button class="btn danger" onclick="delApp(${x.id})">Delete</button></td></tr>`).join("")}</table></div></section>`);
 document.getElementById("appForm").onsubmit=e=>{e.preventDefault();let f=new FormData(e.target);data.appointments.push({id:id(),member:f.get("member"),doctor:f.get("doctor"),date:f.get("date"),time:f.get("time"),place:f.get("place")});save();flash("Appointment saved.");appointments()}
}
function delApp(i){data.appointments=data.appointments.filter(x=>x.id!==i);save();appointments()}

function tasks(){
 setPage("Daily Care Tasks",`<h1>⏳ Daily Care Tasks</h1><section class="card"><h2>Add Task</h2><form id="taskForm" class="formgrid"><select name="member">${memberOptions()}</select><input name="task" placeholder="Task e.g. Check BP" required><input name="date" type="date" required><input name="time" type="time"><button class="btn primary">Add Task</button></form></section>
 <section class="card"><h2>Task List</h2><div class="tablewrap"><table><tr><th>Member</th><th>Task</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>${data.tasks.map(x=>`<tr><td>${esc(x.member)}</td><td>${esc(x.task)}</td><td>${esc(x.date)}</td><td>${esc(x.time)}</td><td><span class="badge ${x.status==="Completed"?"taken":""}">${x.status}</span></td><td><button class="btn light" onclick="taskDone(${x.id})">Complete</button> <button class="btn danger" onclick="delTask(${x.id})">Delete</button></td></tr>`).join("")}</table></div></section>`);
 document.getElementById("taskForm").onsubmit=e=>{e.preventDefault();let f=new FormData(e.target);data.tasks.push({id:id(),member:f.get("member"),task:f.get("task"),date:f.get("date"),time:f.get("time"),status:"Pending"});save();flash("Care task added.");tasks()}
}
function taskDone(i){let x=data.tasks.find(x=>x.id===i);if(x)x.status="Completed";save();tasks()}
function delTask(i){data.tasks=data.tasks.filter(x=>x.id!==i);save();tasks()}

function history(){
 setPage("Medicine History",`<h1>📋 Medicine History</h1><section class="card"><div class="tablewrap"><table><tr><th>Member</th><th>Medicine</th><th>Time</th><th>Frequency</th><th>Status</th></tr>${data.medicines.map(x=>`<tr><td>${esc(x.member)}</td><td>${esc(x.name)}</td><td>${esc(x.time)}</td><td>${esc(x.frequency)}</td><td><span class="badge ${x.status==="Taken"?"taken":""}">${esc(x.status)}</span></td></tr>`).join("")}</table></div></section>`)
}

function reports(){
 setPage("Medical Reports",`<h1>🩺 Medical Reports</h1><p class="muted">View family medical and health checkup records.</p><div class="report-grid">${data.reports.map(x=>`<section class="report"><h2>${esc(x.type)}</h2><p><b>Patient Name:</b> ${esc(x.patient)}</p><p><b>Report Date:</b> ${esc(x.date)}</p><p><b>Doctor:</b> ${esc(x.doctor)}</p><p><b>Hospital:</b> ${esc(x.hospital)}</p><p><b>Status:</b> <span class="badge available">Available</span></p><button class="btn primary" onclick="alert('Report: ${esc(x.type)}\\nPatient: ${esc(x.patient)}\\nDoctor: ${esc(x.doctor)}')">📝 View Report</button></section>`).join("")}</div>
 <section class="card"><h2>➕ Add Medical Report</h2><form id="reportForm" class="formgrid"><input name="type" placeholder="Report Type" required><input name="patient" placeholder="Patient Name" required><input name="date" type="date" required><input name="doctor" placeholder="Doctor" required><input name="hospital" placeholder="Hospital" required><button class="btn primary">Add Report</button></form></section>`);
 document.getElementById("reportForm").onsubmit=e=>{e.preventDefault();let f=new FormData(e.target);data.reports.push({id:id(),type:f.get("type"),patient:f.get("patient"),date:f.get("date"),doctor:f.get("doctor"),hospital:f.get("hospital")});save();flash("Medical report added.");reports()}
}

function disease() {

    const diseaseData = {
        fever: {
            name: "Fever",
            medicines: ["Paracetamol"],
            info: "Fever may occur due to infection or other causes."
        },

        cold: {
            name: "Common Cold",
            medicines: ["Cetirizine", "Paracetamol"],
            info: "Cold may cause sneezing, runny nose and sore throat."
        },

        cough: {
            name: "Cough",
            medicines: ["Cough medicine"],
            info: "Cough may occur due to cold, allergy or throat irritation."
        },

        headache: {
            name: "Headache",
            medicines: ["Paracetamol"],
            info: "Headache can have different causes such as stress or dehydration."
        },

        acidity: {
            name: "Acidity",
            medicines: ["Antacid"],
            info: "Acidity may cause burning sensation in the stomach."
        },

        allergy: {
            name: "Allergy",
            medicines: ["Cetirizine"],
            info: "Allergy may cause sneezing, itching or runny nose."
        },

        diarrhea: {
            name: "Diarrhea",
            medicines: ["ORS"],
            info: "ORS helps replace fluids and electrolytes."
        }
    };

    const query = location.hash.split("?")[1] || "";
    const params = new URLSearchParams(query);
    const search = (params.get("search") || "").trim().toLowerCase();

    let result = "";

    if (search !== "") {

        const found = diseaseData[search];

        if (found) {

            result = `
                <section class="card">
                    <h2>🩺 ${esc(found.name)}</h2>

                    <h3>💊 Medicine / Care</h3>

                    <ul>
                        ${found.medicines.map(medicine =>
                            `<li>${esc(medicine)}</li>`
                        ).join("")}
                    </ul>

                    <h3>ℹ️ Information</h3>

                    <p>${esc(found.info)}</p>

                    <p class="muted">
                        ⚠️ This is general project information.
                        Consult a doctor or pharmacist before taking medicine.
                    </p>
                </section>
            `;

        } else {

            result = `
                <section class="card">
                    <h2>❌ Disease Not Found</h2>

                    <p>Try searching:</p>

                    <p>
                        <b>Fever</b>,
                        <b>Cold</b>,
                        <b>Cough</b>,
                        <b>Headache</b>,
                        <b>Acidity</b>,
                        <b>Allergy</b>,
                        <b>Diarrhea</b>
                    </p>
                </section>
            `;
        }
    }

    setPage(
        "Disease Medicine Search",
        `
        <h1>🔎 Disease Medicine Search</h1>

        <p class="muted">
            Search a disease to see general medicine information.
        </p>

        <section class="card">

            <form id="diseaseForm" class="formgrid">

                <input
                    id="diseaseSearch"
                    type="text"
                    placeholder="Enter disease e.g. fever"
                    value="${esc(search)}"
                    required
                >

                <button class="btn primary" type="submit">
                    🔎 Search
                </button>

            </form>

        </section>

        ${result}
        `
    );

    const form = document.getElementById("diseaseForm");

    if (form) {
        form.addEventListener("submit", searchDisease);
    }
}


function searchDisease(event) {

    event.preventDefault();

    const input = document.getElementById("diseaseSearch");

    if (!input) {
        return;
    }

    const value = input.value.trim().toLowerCase();

    if (value === "") {
        return;
    }

    location.hash = "#disease?search=" + encodeURIComponent(value);
}

function notifications(){
 const pending=data.medicines.filter(x=>x.status==="Pending");
 setPage("Notifications",`<h1>🔔 Notifications</h1>${pending.map(x=>`<div class="notice medicine"><b>💊 Medicine Reminder</b><p>Pending medicine <strong>${esc(x.name)}</strong> for ${esc(x.member)}.</p><small>Reminder time: ${esc(x.time)}</small></div>`).join("")||'<div class="notice">No pending medicine reminders.</div>'}<div class="notice"><b>🗓️ Upcoming Appointments</b><p>${data.appointments.length?data.appointments.map(x=>esc(x.date+" • "+x.doctor+" • "+x.member)).join("<br>"):"No appointments."}</p></div><div class="notice"><b>⏳ Pending Care Tasks</b><p>${data.tasks.filter(x=>x.status==="Pending").length} pending task(s).</p></div>`)
}

function profile(){
 const p=data.profile;
 setPage("My Profile",`<h1>👤 My Profile</h1><section class="card profile"><div class="avatar">👤</div><div class="profilegrid"><b>Name:</b><span>${esc(p.name)}</span><b>Email:</b><span>${esc(p.email)}</span><b>Mobile:</b><span>${esc(p.mobile)}</span><b>Address:</b><span>${esc(p.address)}</span></div></section><section class="card"><h2>✏️ Edit Profile</h2><form id="profileForm" class="formgrid"><input name="name" value="${esc(p.name)}" placeholder="Name" required><input name="email" type="email" value="${esc(p.email)}" placeholder="Email" required><input name="mobile" value="${esc(p.mobile)}" placeholder="Mobile"><input name="address" value="${esc(p.address)}" placeholder="Address"><button class="btn primary">Save Profile</button></form></section>`);
 document.getElementById("profileForm").onsubmit=e=>{e.preventDefault();let f=new FormData(e.target);data.profile={name:f.get("name"),email:f.get("email"),mobile:f.get("mobile"),address:f.get("address")};save();flash("Profile updated.");profile()}
}
function feedback(){
    setPage("Feedback",`<h1>💬 Feedback</h1><section class="card"><form id="feedbackForm"><label>Rating</label><select name="rating"><option>5</option><option>4</option><option>3</option><option>2</option><option>1</option></select><label>Your Feedback</label><textarea name="message" rows="6" required placeholder="Write your feedback..."></textarea><br><br><button class="btn primary">Submit Feedback</button></form></section>`);
    document.getElementById("feedbackForm").onsubmit=e=>{
        e.preventDefault();
        let f=new FormData(e.target);
        data.feedback.push({
            rating:f.get("rating"),
            message:f.get("message"),
            date:new Date().toLocaleString()
        });
        save();
        flash("Thank you for your feedback.");
        feedback()
    }
}// Start application
window.addEventListener("hashchange", nav);
window.addEventListener("DOMContentLoaded", nav);

// Open dashboard initially
nav();
