const app=document.getElementById('app');
const diseaseInfo={
 'Fever':'Fever has many possible causes. Rest and fluids may help. Ask a qualified clinician or pharmacist before using medicine.',
 'Common cold':'Usually caused by a virus. Rest and fluids may help. Ask a pharmacist or clinician about suitable symptom relief.',
 'Headache':'Headaches have different causes. Check labels and consult a clinician or pharmacist, especially for repeated or severe headaches.',
 'Cough':'Treatment depends on the cause and the patient’s age. Ask a qualified clinician or pharmacist before taking cough medicine.',
 'Acidity':'A clinician or pharmacist can advise on suitable treatment and warning signs.',
 'Diabetes':'Requires individualised care. Use only medicines prescribed for the patient; do not change doses based on this reference.',
 'Hypertension':'High blood pressure needs monitoring. Use prescribed treatment and consult a clinician before changing medicines.',
 'Allergy':'Symptoms and triggers vary. Ask a qualified pharmacist or clinician about suitable treatment.'
};
const features=[
 ['medicines','💊','Daily Medicine Tracker',"Track today's taken, skipped and pending medicines.",'Open Tracker'],
 ['appointments','🗓️','Family Care Calendar','View medicines, appointments and care tasks.','Open Calendar'],
 ['statistics','📊','Dashboard Statistics','View family care statistics and summary.','View Statistics'],
 ['notifications','🔔','Notifications','View medicine, appointment and care reminders.','View Notifications'],
 ['reports','📄','Medical Reports',"View family members' medical report records.",'View Reports'],
 ['disease','💊','Disease Medicine','Search condition information and medicine guidance.','View Medicines'],
 ['profile','👤','Profile','View and edit your profile information.','View Profile'],
 ['family','👨‍👩‍👧','Family Members','Add and edit family member details.','Manage Family'],
 ['history','📋','Medicine History','View taken and skipped medicine records.','View History'],
 ['tasks','✅','Pending Care Tasks','Create and manage family care tasks.','Manage Tasks']
];
const schemas={
 family:{title:'Family Members',singular:'Family Member',fields:[['name','Full name','text',true],['relation','Relationship','text',false],['age','Age','number',false],['phone','Phone','tel',false],['notes','Notes','textarea',false]]},
 medicines:{title:'Daily Medicine Tracker',singular:'Medicine',fields:[['medicineName','Medicine name','text',true],['memberName','Family member','text',false],['dosage','Dosage (as prescribed)','text',false],['schedule','Time / schedule','text',false],['startDate','Start date','date',false],['endDate','End date','date',false],['status','Status','select',true,['Pending','Taken','Skipped']],['notes','Notes','textarea',false]]},
 appointments:{title:'Family Care Calendar',singular:'Appointment',fields:[['title','Appointment title','text',true],['memberName','Family member','text',false],['date','Date and time','datetime-local',true],['location','Location','text',false],['notes','Notes','textarea',false]]},
 tasks:{title:'Care Tasks',singular:'Care Task',fields:[['taskName','Task name','text',true],['memberName','Family member','text',false],['dueDate','Due date','date',false],['status','Status','select',true,['Pending','Done']],['notes','Notes','textarea',false]]},
 reports:{title:'Medical Reports',singular:'Medical Report',fields:[['title','Report title','text',true],['memberName','Family member','text',false],['date','Report date','date',false],['fileReference','File name / reference','text',false],['notes','Notes','textarea',false]]}
};
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function api(path,options={}){const r=await fetch('/api/'+path,{headers:{'Content-Type':'application/json'},...options});let d;try{d=await r.json()}catch{d={}}if(!r.ok)throw Error(d.error||'Request failed');return d}
function link(route,label,cls='btn'){return `<a class="${cls}" href="#${route}">${label}</a>`}
function pageHead(title,desc=''){return `<h1>${title}</h1>${desc?`<p class="subhead">${desc}</p>`:''}`}
function showMessage(msg,error=false){return `<div class="notice ${error?'error':''}">${esc(msg)}</div>`}
async function render(){
 const route=location.hash.replace(/^#/,'')||'dashboard';
 try{
 if(route==='dashboard')return dashboard();
 if(route==='statistics')return statistics();
 if(route==='notifications')return notifications();
 if(route==='disease')return disease();
 if(route==='profile')return profile();
 if(route==='history')return historyPage();
 if(schemas[route])return recordsPage(route);
 app.innerHTML=pageHead('Page not found')+link('dashboard','Back to Dashboard');
 }catch(e){app.innerHTML=pageHead('Something went wrong')+showMessage(e.message,true)+link('dashboard','Back to Dashboard')}
}
async function dashboard(){
 const [family,medicines,appointments,tasks,reports]=await Promise.all(['family','medicines','appointments','tasks','reports'].map(x=>api(x)));
 app.innerHTML=pageHead('Medicine Reminder & Family Care','Choose a feature below. Add, edit or delete information inside each feature.')+
 `<div class="stats"><div class="stat">Family Members<strong>${family.length}</strong></div><div class="stat">Medicines<strong>${medicines.length}</strong></div><div class="stat">Appointments<strong>${appointments.length}</strong></div><div class="stat">Pending Tasks<strong>${tasks.filter(x=>x.status!=='Done').length}</strong></div></div>
 <section class="grid">${features.map(f=>`<article class="card"><div class="emoji">${f[1]}</div><h2>${esc(f[2])}</h2><p>${esc(f[3])}</p>${link(f[0],f[4]+' →','btn')}</article>`).join('')}</section>`;
}
function formField(field,value=''){
 const [key,label,type,required,opts]=field;let control='';
 if(type==='textarea')control=`<textarea name="${key}">${esc(value)}</textarea>`;
 else if(type==='select')control=`<select name="${key}" ${required?'required':''}>${opts.map(o=>`<option ${value===o?'selected':''}>${o}</option>`).join('')}</select>`;
 else control=`<input name="${key}" type="${type}" value="${esc(value)}" ${required?'required':''} ${type==='number'?'min="0"':''}>`;
 return `<div class="field"><label>${esc(label)}${required?' *':''}</label>${control}</div>`;
}
async function recordsPage(key){
 const s=schemas[key],items=await api(key);app.innerHTML=pageHead(s.title,'Add new records or use Edit and Delete to manage saved information.')+
 `<section class="panel" id="editor"><h2 id="formTitle">Add ${s.singular}</h2><form id="recordForm"><input type="hidden" name="id"><div class="formgrid">${s.fields.map(f=>formField(f)).join('')}</div><div class="form-actions"><button class="primary" type="submit">Save ${s.singular}</button><button type="button" id="cancelEdit" hidden>Cancel edit</button></div></form></section>
 <div class="toolbar"><h2>Saved records (${items.length})</h2>${link('dashboard','← Dashboard')}</div><div id="message"></div>${items.length?`<div class="tablewrap"><table><thead><tr>${s.fields.slice(0,4).map(f=>`<th>${esc(f[1])}</th>`).join('')}<th>Actions</th></tr></thead><tbody>${items.map(it=>`<tr>${s.fields.slice(0,4).map(f=>`<td>${esc(it[f[0]])}</td>`).join('')}<td><div class="actions"><button data-edit="${esc(it.id)}">Edit</button><button class="danger" data-delete="${esc(it.id)}">Delete</button></div></td></tr>`).join('')}</tbody></table></div>`:`<div class="panel empty">No records yet. Use the form above to add your first record.</div>`}`;
 const form=document.getElementById('recordForm'),cancel=document.getElementById('cancelEdit');
 form.onsubmit=async e=>{e.preventDefault();const fd=new FormData(form),data={};for(const [k,v] of fd.entries())if(k!=='id')data[k]=v;const id=fd.get('id');try{await api(key+(id?'/'+encodeURIComponent(id):''),{method:id?'PUT':'POST',body:JSON.stringify(data)});render()}catch(err){document.getElementById('message').innerHTML=showMessage(err.message,true)}};
 cancel.onclick=()=>{form.reset();form.elements.id.value='';document.getElementById('formTitle').textContent='Add '+s.singular;cancel.hidden=true};
 document.querySelectorAll('[data-edit]').forEach(b=>b.onclick=()=>{const it=items.find(x=>x.id===b.dataset.edit);form.reset();form.elements.id.value=it.id;s.fields.forEach(f=>{if(form.elements[f[0]])form.elements[f[0]].value=it[f[0]]??''});document.getElementById('formTitle').textContent='Edit '+s.singular;cancel.hidden=false;document.getElementById('editor').scrollIntoView({behavior:'smooth'})});
 document.querySelectorAll('[data-delete]').forEach(b=>b.onclick=async()=>{if(confirm('Delete this record?')){try{await api(key+'/'+encodeURIComponent(b.dataset.delete),{method:'DELETE'});render()}catch(err){document.getElementById('message').innerHTML=showMessage(err.message,true)}}});
}
async function profile(){
 const p=await api('profile');app.innerHTML=pageHead('Profile','Your profile information can be edited and saved.')+`<section class="panel"><form id="profileForm"><div class="formgrid">${[['name','Full name','text'],['phone','Phone','tel'],['email','Email','email']].map(([k,l,t])=>formField([k,l,t,false],p[k])).join('')}</div><div class="field"><label>Notes</label><textarea name="notes">${esc(p.notes)}</textarea></div><button class="primary">Save Profile</button><div id="msg"></div></form></section>`;
 document.getElementById('profileForm').onsubmit=async e=>{e.preventDefault();const d=Object.fromEntries(new FormData(e.target));try{await api('profile',{method:'PUT',body:JSON.stringify(d)});document.getElementById('msg').innerHTML=showMessage('Profile saved successfully.')}catch(err){document.getElementById('msg').innerHTML=showMessage(err.message,true)}};
}
async function statistics(){
 const [family,medicines,appointments,tasks,reports]=await Promise.all(['family','medicines','appointments','tasks','reports'].map(x=>api(x)));
 app.innerHTML=pageHead('Dashboard Statistics','Live counts from the saved project data.')+`<div class="stats">${[['Family Members',family.length],['Medicine Records',medicines.length],['Appointments',appointments.length],['Care Tasks',tasks.length],['Medical Reports',reports.length],['Pending Medicines',medicines.filter(x=>x.status==='Pending').length],['Taken Medicines',medicines.filter(x=>x.status==='Taken').length],['Skipped Medicines',medicines.filter(x=>x.status==='Skipped').length]].map(([l,n])=>`<div class="stat">${l}<strong>${n}</strong></div>`).join('')}</div>${link('dashboard','← Dashboard')}`;
}
async function notifications(){
 const [meds,apps,tasks]=await Promise.all(['medicines','appointments','tasks'].map(x=>api(x)));
 const upcoming=apps.filter(a=>a.date&&new Date(a.date)>=new Date()).sort((a,b)=>new Date(a.date)-new Date(b.date));
 app.innerHTML=pageHead('Notifications','In-app reminders based on the information saved in your project.')+`<section class="panel"><h2>Pending medicine reminders</h2>${meds.filter(m=>m.status==='Pending').length?`<div class="tablewrap"><table><tr><th>Medicine</th><th>Member</th><th>Schedule</th></tr>${meds.filter(m=>m.status==='Pending').map(m=>`<tr><td>${esc(m.medicineName)}</td><td>${esc(m.memberName)}</td><td>${esc(m.schedule)}</td></tr>`).join('')}</table></div>`:'<p class="muted">No pending medicines.</p>'}<h2 style="margin-top:25px">Upcoming appointments</h2>${upcoming.length?`<div class="tablewrap"><table><tr><th>Appointment</th><th>Date</th><th>Member</th></tr>${upcoming.map(a=>`<tr><td>${esc(a.title)}</td><td>${esc(a.date)}</td><td>${esc(a.memberName)}</td></tr>`).join('')}</table></div>`:'<p class="muted">No upcoming appointments.</p>'}<h2 style="margin-top:25px">Pending care tasks</h2><ul>${tasks.filter(t=>t.status!=='Done').map(t=>`<li>${esc(t.taskName)} — ${esc(t.dueDate)}</li>`).join('')||'<li>There are no pending tasks.</li>'}</ul></section><p class="small">These are website reminders; SMS and mobile push notifications are not enabled.</p>`;
}
async function historyPage(){
 const items=await api('history');app.innerHTML=pageHead('Medicine History','History is recorded when a medicine status is changed.')+link('medicines','Open Medicine Tracker')+`<div style="height:14px"></div>${items.length?`<div class="tablewrap"><table><tr><th>Medicine</th><th>Member</th><th>Status</th><th>Changed at</th></tr>${items.map(x=>`<tr><td>${esc(x.medicineName)}</td><td>${esc(x.memberName)}</td><td>${esc(x.status)}</td><td>${esc(new Date(x.changedAt).toLocaleString())}</td></tr>`).join('')}</table></div>`:'<div class="panel empty">No history yet. Update a medicine status in Daily Medicine Tracker.</div>'}`;
}
function disease(){
 app.innerHTML=pageHead('Disease Medicine','Search condition information and safe medicine guidance.')+`<div class="panel"><form id="diseaseSearch" class="searchrow"><input id="query" placeholder="Search Fever, Cough, Diabetes…" aria-label="Search condition"><button class="primary">Search</button></form><p class="small">Educational information only, not a prescription. Confirm medicines and dosage with a qualified clinician or pharmacist.</p></div><div id="diseaseResults" class="grid" style="margin-top:18px"></div>`;
 const draw=q=>{const rows=Object.entries(diseaseInfo).filter(([name,desc])=>!q||(name+' '+desc).toLowerCase().includes(q.toLowerCase()));document.getElementById('diseaseResults').innerHTML=rows.length?rows.map(([name,desc])=>`<article class="card"><div class="emoji">💊</div><h2>${esc(name)}</h2><p>${esc(desc)}</p></article>`).join(''):'<div class="panel">No matching condition. Try Fever, Common cold, Headache, Cough, Acidity, Diabetes, Hypertension or Allergy.</div>'};
 draw('');document.getElementById('diseaseSearch').onsubmit=e=>{e.preventDefault();draw(document.getElementById('query').value.trim())};
}
window.addEventListener('hashchange',render);render();
